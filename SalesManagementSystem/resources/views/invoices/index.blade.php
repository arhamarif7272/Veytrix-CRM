@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices & Billing')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Invoices</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Invoice Management</h4>
        <p class="text-muted mb-0">Track billed revenues, outstanding debts, and payment receipts</p>
    </div>
    <div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Invoice
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('invoices.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search invoice #..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent / Unpaid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Paid / Balance</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    @php
                        $cust = $customers->get((string) $invoice->customer_id);
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $invoice->number }}
                            </a>
                        </td>
                        <td>
                            @if($cust)
                                <a href="{{ route('customers.show', $cust->id) }}" class="text-primary text-decoration-none small">
                                    <i class="far fa-building me-1"></i>{{ $cust->name }}
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark fs-6">${{ number_format($invoice->total, 2) }}</td>
                        <td class="small">
                            <span class="text-success fw-semibold">${{ number_format($invoice->amount_paid, 2) }}</span> /
                            <span class="text-danger fw-semibold">${{ number_format($invoice->amount_due, 2) }}</span>
                        </td>
                        <td>
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
                            <span class="badge {{ $statusBadges[$invoice->status] ?? 'bg-light text-dark' }} text-capitalize px-2 py-1">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="small {{ $invoice->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}"><i class="far fa-eye text-primary me-2"></i> View Invoice</a></li>
                                    @if($invoice->status !== 'paid')
                                        <li><a class="dropdown-item text-success fw-semibold" href="{{ route('invoices.show', $invoice->id) }}#recordPayment"><i class="fas fa-credit-card me-2"></i> Record Payment</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank"><i class="fas fa-print text-secondary me-2"></i> Print / PDF</a></li>
                                    <li><a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Delete invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="far fa-trash-alt me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>No invoices found</h5>
                            <p class="small mb-3">Create billing invoices or convert accepted quotations</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> New Invoice
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection
