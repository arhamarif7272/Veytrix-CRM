@extends('layouts.app')

@section('title', 'Quotation ' . $quotation->number)
@section('page-title', 'Quotation Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
    <li class="breadcrumb-item active">{{ $quotation->number }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Action Header Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                @php
                    $statusBadges = [
                        'draft'    => 'bg-secondary text-white',
                        'sent'     => 'bg-info text-dark',
                        'accepted' => 'bg-success text-white',
                        'rejected' => 'bg-danger text-white',
                        'expired'  => 'bg-dark text-white',
                    ];
                @endphp
                <span class="badge {{ $statusBadges[$quotation->status] ?? 'bg-light text-dark' }} text-capitalize px-3 py-2 fs-6">
                    {{ $quotation->status }}
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('quotations.pdf', $quotation->id) }}" target="_blank" class="btn btn-outline-dark">
                    <i class="fas fa-print me-1"></i> Print / PDF
                </a>
                @if($quotation->status === 'draft')
                    <form action="{{ route('quotations.send', $quotation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-info">
                            <i class="far fa-paper-plane me-1"></i> Mark Sent
                        </button>
                    </form>
                @endif
                @if($quotation->status !== 'accepted')
                    <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST" onsubmit="return confirm('Convert this accepted quotation to an Invoice?');">
                        @csrf
                        <button type="submit" class="btn btn-success fw-semibold">
                            <i class="fas fa-receipt me-1"></i> Convert to Invoice
                        </button>
                    </form>
                @else
                    <a href="{{ route('invoices.show', $quotation->invoice_id) }}" class="btn btn-success">
                        <i class="fas fa-receipt me-1"></i> View Converted Invoice
                    </a>
                @endif
                <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>

        <!-- Printable Document Style Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start pb-4 border-bottom mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Veytrix" style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid #10b981;">
                            <span class="fs-4 fw-bold text-dark">Veytrix</span>
                        </div>
                        <p class="text-muted small mb-0">Enterprise Customer Relationship &amp; Workflow Management System</p>
                        <div class="small text-muted">support@veytrix.com &bull; +1 (800) 555-0199</div>
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold text-dark mb-1">QUOTATION</h3>
                        <div class="fw-bold text-primary fs-5 mb-1">{{ $quotation->number }}</div>
                        <div class="text-muted small">Date: {{ $quotation->created_at ? $quotation->created_at->format('M d, Y') : '—' }}</div>
                        <div class="text-muted small">Valid Until: {{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : '—' }}</div>
                    </div>
                </div>

                <!-- Recipient Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Quote Prepared For:</h6>
                        @if($customer)
                            <h5 class="fw-bold text-dark mb-1">{{ $customer->name }}</h5>
                            @if($customer->company)<div class="text-muted">{{ $customer->company }}</div>@endif
                            <div class="text-muted small">{{ $customer->email ?? '' }}</div>
                            <div class="text-muted small">{{ $customer->phone ?? '' }}</div>
                            <div class="text-muted small">{{ $customer->address ?? '' }} {{ $customer->city ? ', ' . $customer->city : '' }}</div>
                        @else
                            <div class="text-muted">Account details not found.</div>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        @if($deal)
                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Opportunity Reference:</h6>
                            <div class="fw-semibold text-dark">{{ $deal->title }}</div>
                            <div class="small text-muted">Ref Deal ID: #{{ substr($deal->id, -6) }}</div>
                        @endif
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light text-uppercase small fw-bold">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 45%;">Item / Description</th>
                                <th class="text-center" style="width: 15%;">Qty</th>
                                <th class="text-end" style="width: 15%;">Unit Price</th>
                                <th class="text-end" style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->items ?? [] as $index => $item)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item['name'] ?? 'Item' }}</div>
                                    @if(!empty($item['description']))
                                        <div class="small text-muted">{{ $item['description'] }}</div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                                <td class="text-end">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                <td class="text-end fw-semibold">${{ number_format($item['total'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)), 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Calculation Section -->
                <div class="row justify-content-end mb-4">
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-semibold">${{ number_format($quotation->subtotal, 2) }}</span>
                            </div>
                            @if($quotation->discount_amount > 0)
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Discount ({{ $quotation->discount_type === 'percentage' ? $quotation->discount_value . '%' : '$' . $quotation->discount_value }}):</span>
                                <span class="text-danger fw-semibold">-${{ number_format($quotation->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Tax ({{ $quotation->tax_rate }}%):</span>
                                <span class="fw-semibold">${{ number_format($quotation->tax_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-top fs-5">
                                <strong class="text-dark">Total:</strong>
                                <strong class="text-primary">${{ number_format($quotation->total, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                @if($quotation->notes || $quotation->terms)
                <div class="row pt-3 border-top g-3">
                    @if($quotation->notes)
                    <div class="col-md-6">
                        <h6 class="small fw-bold text-uppercase text-muted">Notes:</h6>
                        <p class="small text-muted mb-0">{{ $quotation->notes }}</p>
                    </div>
                    @endif
                    @if($quotation->terms)
                    <div class="col-md-6">
                        <h6 class="small fw-bold text-uppercase text-muted">Terms & Conditions:</h6>
                        <p class="small text-muted mb-0">{{ $quotation->terms }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
