<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 13px; line-height: 1.5; padding: 25px; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 25px; }
        .header table { width: 100%; }
        .company-title { font-size: 24px; font-weight: bold; color: #4f46e5; margin: 0; }
        .doc-title { font-size: 26px; font-weight: bold; text-align: right; margin: 0; color: #1e293b; }
        .doc-meta { text-align: right; font-size: 12px; color: #64748b; }
        .client-info { margin-bottom: 25px; width: 100%; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; text-align: left; font-size: 11px; text-transform: uppercase; color: #475569; }
        .items-table td { border: 1px solid #e2e8f0; padding: 10px; }
        .totals-table { width: 45%; margin-left: auto; margin-bottom: 30px; border-collapse: collapse; }
        .totals-table td { padding: 6px 10px; }
        .total-row { border-top: 2px solid #1e293b; font-weight: bold; font-size: 15px; color: #0f172a; }
        .balance-row { font-weight: bold; font-size: 15px; color: #dc2626; border-top: 1px solid #e2e8f0; }
        .notes-section { margin-top: 25px; padding: 15px; background: #f8fafc; border-radius: 6px; font-size: 12px; color: #475569; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="company-title">Veytrix</div>
                    <div style="color: #64748b; font-size: 12px;">Enterprise Customer Relationship &amp; Workflow Management System</div>
                    <div style="color: #64748b; font-size: 12px;">billing@veytrix.com &bull; +1 (800) 555-0199</div>
                </td>
                <td>
                    <div class="doc-title">INVOICE</div>
                    <div class="doc-meta" style="font-weight: bold; color: #10b981; font-size: 14px;">#{{ $invoice->number }}</div>
                    <div class="doc-meta">Date: {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : '' }}</div>
                    <div class="doc-meta">Due Date: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="client-info">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div style="font-weight: bold; color: #64748b; font-size: 11px; text-transform: uppercase;">Billed To:</div>
                <div style="font-size: 16px; font-weight: bold; color: #0f172a;">{{ $customer?->name ?? 'Customer' }}</div>
                @if($customer?->company)<div style="color: #475569;">{{ $customer->company }}</div>@endif
                <div style="color: #64748b;">{{ $customer?->email ?? '' }}</div>
                <div style="color: #64748b;">{{ $customer?->phone ?? '' }}</div>
                <div style="color: #64748b;">{{ $customer?->address ?? '' }} {{ $customer?->city ? ', ' . $customer->city : '' }}</div>
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                <div style="font-weight: bold; color: #64748b; font-size: 11px; text-transform: uppercase;">Invoice Status:</div>
                <div style="font-size: 14px; font-weight: bold; text-transform: uppercase; color: {{ $invoice->status === 'paid' ? '#10b981' : '#dc2626' }};">{{ $invoice->status }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Item / Description</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 15%;">Unit Price</th>
                <th class="text-right" style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items ?? [] as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $item['name'] ?? '' }}</strong>
                    @if(!empty($item['description']))<br><small style="color: #64748b;">{{ $item['description'] }}</small>@endif
                </td>
                <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                <td class="text-right">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                <td class="text-right">${{ number_format($item['total'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td style="color: #64748b;">Subtotal:</td>
            <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->discount_amount > 0)
        <tr>
            <td style="color: #dc2626;">Discount:</td>
            <td class="text-right" style="color: #dc2626;">-${{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td style="color: #64748b;">Tax ({{ $invoice->tax_rate }}%):</td>
            <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Grand Total:</td>
            <td class="text-right">${{ number_format($invoice->total, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #10b981;">Amount Paid:</td>
            <td class="text-right" style="color: #10b981; font-weight: bold;">${{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        <tr class="balance-row">
            <td>Balance Due:</td>
            <td class="text-right">${{ number_format($invoice->amount_due, 2) }}</td>
        </tr>
    </table>

    @if($invoice->notes || $invoice->terms)
    <div class="notes-section">
        @if($invoice->notes)
            <div style="font-weight: bold; margin-bottom: 4px;">Notes:</div>
            <div style="margin-bottom: 10px;">{{ $invoice->notes }}</div>
        @endif
        @if($invoice->terms)
            <div style="font-weight: bold; margin-bottom: 4px;">Terms:</div>
            <div>{{ $invoice->terms }}</div>
        @endif
    </div>
    @endif
</body>
</html>
