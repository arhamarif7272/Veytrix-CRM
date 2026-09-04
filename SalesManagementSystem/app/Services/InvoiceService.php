<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Invoice;
use App\Models\User;

class InvoiceService
{
    public function generateNumber(): string
    {
        $count = Invoice::count() + 1;
        return 'INV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function recordPayment(Invoice $invoice, float $amount, string $paymentMethod, ?string $reference, User $user): Invoice
    {
        $newAmountPaid = ($invoice->amount_paid ?? 0) + $amount;
        $total = $invoice->total ?? 0;
        $amountDue = max(0, $total - $newAmountPaid);

        $status = $invoice->status;
        if ($amountDue <= 0) {
            $status = Invoice::STATUS_PAID;
        } elseif ($newAmountPaid > 0) {
            $status = Invoice::STATUS_PARTIAL;
        }

        $invoice->update([
            'amount_paid'       => $newAmountPaid,
            'amount_due'        => $amountDue,
            'status'            => $status,
            'paid_at'           => $status === Invoice::STATUS_PAID ? now() : $invoice->paid_at,
            'payment_method'    => $paymentMethod,
            'payment_reference' => $reference,
        ]);

        Activity::create([
            'type'              => Activity::TYPE_STATUS,
            'subject'           => 'Payment Received',
            'description'       => "Payment of \${$amount} received for Invoice #{$invoice->number} via {$paymentMethod}",
            'related_type'      => 'customer',
            'related_id'        => (string) $invoice->customer_id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        AuditService::log(
            action: 'invoice.payment',
            module: 'invoices',
            entityType: 'Invoice',
            entityId: (string) $invoice->id,
            entityLabel: $invoice->number,
            description: "Payment of \${$amount} recorded for Invoice #{$invoice->number}"
        );

        return $invoice;
    }
}
