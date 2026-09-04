@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->number)
@section('page-title', 'Invoice Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
    <li class="breadcrumb-item active">{{ $invoice->number }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Actions Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                @php
                    $statusBadges = [
                        'draft'     => 'bg-secondary text-white',
                        'sent'      => 'bg-info text-dark',
                        'partial'   => 'bg-warning text-dark',
                        'paid'      => 'bg-success text-white',
                        'overdue'   => 'bg-danger text-white',
                        'cancelled' => 'bg-dark text-white',
                    ];
                @endphp
                <span class="badge {{ $statusBadges[$invoice->status] ?? 'bg-light text-dark' }} text-capitalize px-3 py-2 fs-6">
                    {{ $invoice->status }}
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="btn btn-outline-dark">
                    <i class="fas fa-print me-1"></i> Print / PDF
                </a>
                @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                    <button type="button" class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                        <i class="fas fa-credit-card me-1"></i> Record Payment
                    </button>
                @endif
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>

        <!-- Printable Invoice Layout Card -->
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
                        <div class="small text-muted">billing@veytrix.com &bull; +1 (800) 555-0199</div>
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold text-dark mb-1">INVOICE</h3>
                        <div class="fw-bold text-primary fs-5 mb-1">{{ $invoice->number }}</div>
                        <div class="text-muted small">Date: {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : '—' }}</div>
                        <div class="small {{ $invoice->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                            Payment Due: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}
                        </div>
                    </div>
                </div>

                <!-- Recipient Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase small fw-bold mb-2">Billed To:</h6>
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
                        <div class="p-3 bg-light rounded text-start text-md-end d-inline-block">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Amount Due</span>
                            <span class="fs-4 fw-bold {{ $invoice->amount_due > 0 ? 'text-danger' : 'text-success' }}">
                                ${{ number_format($invoice->amount_due, 2) }}
                            </span>
                            @if($invoice->status === 'paid')
                                <div class="badge bg-success text-white mt-1 d-block">Fully Settled on {{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y') : 'Record' }}</div>
                            @endif
                        </div>
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
                            @foreach($invoice->items ?? [] as $index => $item)
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

                <!-- Financial Calculation -->
                <div class="row justify-content-end mb-4">
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-semibold">${{ number_format($invoice->subtotal, 2) }}</span>
                            </div>
                            @if($invoice->discount_amount > 0)
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Discount:</span>
                                <span class="text-danger fw-semibold">-${{ number_format($invoice->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Tax:</span>
                                <span class="fw-semibold">${{ number_format($invoice->tax_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-top">
                                <strong class="text-dark">Grand Total:</strong>
                                <strong class="text-dark">${{ number_format($invoice->total, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 small text-success">
                                <span>Amount Paid:</span>
                                <span class="fw-bold">${{ number_format($invoice->amount_paid, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-top fs-5">
                                <strong class="text-danger">Balance Due:</strong>
                                <strong class="text-danger">${{ number_format($invoice->amount_due, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                @if($invoice->notes || $invoice->terms)
                <div class="row pt-3 border-top g-3">
                    @if($invoice->notes)
                    <div class="col-md-6">
                        <h6 class="small fw-bold text-uppercase text-muted">Notes:</h6>
                        <p class="small text-muted mb-0">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                    @if($invoice->terms)
                    <div class="col-md-6">
                        <h6 class="small fw-bold text-uppercase text-muted">Terms:</h6>
                        <p class="small text-muted mb-0">{{ $invoice->terms }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Record Payment -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('invoices.payment', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-credit-card me-2"></i> Record Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-3">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Total Invoice:</span>
                            <strong>${{ number_format($invoice->total, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Already Paid:</span>
                            <span class="text-success">${{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fs-6 border-top pt-1 mt-1">
                            <strong class="text-danger">Current Balance:</strong>
                            <strong class="text-danger">${{ number_format($invoice->amount_due, 2) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control form-control-lg" value="{{ $invoice->amount_due }}" max="{{ $invoice->amount_due }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer">Bank Wire Transfer</option>
                            <option value="credit_card">Credit Card (Stripe)</option>
                            <option value="paypal">PayPal</option>
                            <option value="check">Company Check</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction / Check Reference #</label>
                        <input type="text" name="payment_reference" class="form-control" placeholder="e.g. WIRE-8492049 or Check #1042">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-check me-1"></i> Apply Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
