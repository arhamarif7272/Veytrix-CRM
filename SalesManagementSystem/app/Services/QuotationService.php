<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\User;

class QuotationService
{
    public function generateNumber(): string
    {
        $count = Quotation::count() + 1;
        return 'QT-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals(array $items, float $taxRate = 0, string $discountType = 'percentage', float $discountValue = 0): array
    {
        $subtotal = 0;
        $calculatedItems = [];

        foreach ($items as $item) {
            $qty = floatval($item['quantity'] ?? 1);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $total = $qty * $unitPrice;
            $subtotal += $total;

            $calculatedItems[] = [
                'name'        => $item['name'] ?? 'Item',
                'description' => $item['description'] ?? '',
                'quantity'    => $qty,
                'unit_price'  => $unitPrice,
                'total'       => round($total, 2),
            ];
        }

        // Discount
        $discountAmount = 0;
        if ($discountType === 'percentage') {
            $discountAmount = ($subtotal * ($discountValue / 100));
        } else {
            $discountAmount = $discountValue;
        }
        $discountAmount = min($discountAmount, $subtotal);

        // Tax
        $taxable = $subtotal - $discountAmount;
        $taxAmount = ($taxable * ($taxRate / 100));

        $total = $taxable + $taxAmount;

        return [
            'items'           => $calculatedItems,
            'subtotal'        => round($subtotal, 2),
            'discount_type'   => $discountType,
            'discount_value'  => $discountValue,
            'discount_amount' => round($discountAmount, 2),
            'tax_rate'        => $taxRate,
            'tax_amount'      => round($taxAmount, 2),
            'total'           => round($total, 2),
        ];
    }

    public function createQuotation(array $data, User $user): Quotation
    {
        $calculated = $this->calculateTotals(
            items: $data['items'] ?? [],
            taxRate: floatval($data['tax_rate'] ?? 0),
            discountType: $data['discount_type'] ?? 'fixed',
            discountValue: floatval($data['discount_value'] ?? 0)
        );

        $quotationData = array_merge($data, [
            'number'          => $data['number'] ?? $this->generateNumber(),
            'created_by'      => $user->id,
            'status'          => $data['status'] ?? Quotation::STATUS_DRAFT,
            'currency'        => $data['currency'] ?? 'USD',
            'items'           => $calculated['items'],
            'subtotal'        => $calculated['subtotal'],
            'tax_rate'        => $calculated['tax_rate'],
            'tax_amount'      => $calculated['tax_amount'],
            'discount_type'   => $calculated['discount_type'],
            'discount_value'  => $calculated['discount_value'],
            'discount_amount' => $calculated['discount_amount'],
            'total'           => $calculated['total'],
        ]);

        $quotation = Quotation::create($quotationData);

        if (!empty($quotation->customer_id)) {
            Activity::create([
                'type'              => Activity::TYPE_CREATED,
                'subject'           => 'Quotation Created',
                'description'       => "Quotation #{$quotation->number} created for \${$quotation->total}",
                'related_type'      => 'customer',
                'related_id'        => (string) $quotation->customer_id,
                'performed_by'      => $user->id,
                'performed_by_name' => $user->name,
                'occurred_at'       => now(),
            ]);
        }

        AuditService::log(
            action: 'quotation.created',
            module: 'quotations',
            entityType: 'Quotation',
            entityId: (string) $quotation->id,
            entityLabel: $quotation->number,
            newValues: $quotation->toArray()
        );

        return $quotation;
    }

    public function updateQuotation(Quotation $quotation, array $data, User $user): Quotation
    {
        $calculated = $this->calculateTotals(
            items: $data['items'] ?? $quotation->items ?? [],
            taxRate: floatval($data['tax_rate'] ?? $quotation->tax_rate ?? 0),
            discountType: $data['discount_type'] ?? $quotation->discount_type ?? 'fixed',
            discountValue: floatval($data['discount_value'] ?? $quotation->discount_value ?? 0)
        );

        $updateData = array_merge($data, [
            'items'           => $calculated['items'],
            'subtotal'        => $calculated['subtotal'],
            'tax_rate'        => $calculated['tax_rate'],
            'tax_amount'      => $calculated['tax_amount'],
            'discount_type'   => $calculated['discount_type'],
            'discount_value'  => $calculated['discount_value'],
            'discount_amount' => $calculated['discount_amount'],
            'total'           => $calculated['total'],
        ]);

        $quotation->update($updateData);

        AuditService::log(
            action: 'quotation.updated',
            module: 'quotations',
            entityType: 'Quotation',
            entityId: (string) $quotation->id,
            entityLabel: $quotation->number,
            newValues: $quotation->toArray()
        );

        return $quotation;
    }

    public function convertToInvoice(Quotation $quotation, User $user): Invoice
    {
        $invoiceService = app(InvoiceService::class);

        $invoice = Invoice::create([
            'number'          => $invoiceService->generateNumber(),
            'customer_id'     => $quotation->customer_id,
            'quotation_id'    => (string) $quotation->id,
            'deal_id'         => $quotation->deal_id,
            'created_by'      => $user->id,
            'status'          => Invoice::STATUS_SENT,
            'items'           => $quotation->items,
            'subtotal'        => $quotation->subtotal,
            'tax_rate'        => $quotation->tax_rate,
            'tax_amount'      => $quotation->tax_amount,
            'discount_type'   => $quotation->discount_type,
            'discount_value'  => $quotation->discount_value,
            'discount_amount' => $quotation->discount_amount,
            'total'           => $quotation->total,
            'amount_paid'     => 0,
            'amount_due'      => $quotation->total,
            'currency'        => $quotation->currency ?? 'USD',
            'due_date'        => now()->addDays(30),
            'notes'           => $quotation->notes,
            'terms'           => $quotation->terms,
            'sent_at'         => now(),
        ]);

        $quotation->update([
            'status'     => Quotation::STATUS_ACCEPTED,
            'invoice_id' => (string) $invoice->id,
        ]);

        Activity::create([
            'type'              => Activity::TYPE_CONVERTED,
            'subject'           => 'Quotation Converted to Invoice',
            'description'       => "Quotation #{$quotation->number} converted into Invoice #{$invoice->number}",
            'related_type'      => 'customer',
            'related_id'        => (string) $quotation->customer_id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        AuditService::log(
            action: 'quotation.converted',
            module: 'quotations',
            entityType: 'Quotation',
            entityId: (string) $quotation->id,
            entityLabel: $quotation->number,
            description: "Quotation #{$quotation->number} converted to Invoice #{$invoice->number}"
        );

        return $invoice;
    }
}
