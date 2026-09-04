@extends('layouts.app')

@section('title', 'Quotations')
@section('page-title', 'Quotations & Proposals')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Quotations</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Quotations & Commercial Estimates</h4>
        <p class="text-muted mb-0">Prepare price quotes, discount structures, and convert to billing invoices</p>
    </div>
    <div>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Quotation
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('quotations.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search quote #..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></a>
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
                        <th class="ps-4">Quotation #</th>
                        <th>Client / Account</th>
                        <th>Subtotal</th>
                        <th>Tax / Discount</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Valid Until</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                    @php
                        $cust = $customers->get((string) $quotation->customer_id);
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('quotations.show', $quotation->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $quotation->number }}
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
                        <td class="text-dark small">${{ number_format($quotation->subtotal, 2) }}</td>
                        <td class="text-muted small">
                            Tax: ${{ number_format($quotation->tax_amount, 2) }}
                            @if($quotation->discount_amount > 0)
                                <br><span class="text-danger">- ${{ number_format($quotation->discount_amount, 2) }}</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark fs-6">${{ number_format($quotation->total, 2) }}</td>
                        <td>
                            @php
                                $statusBadges = [
                                    'draft'    => 'bg-secondary text-white',
                                    'sent'     => 'bg-info text-dark',
                                    'accepted' => 'bg-success text-white',
                                    'rejected' => 'bg-danger text-white',
                                    'expired'  => 'bg-dark text-white',
                                ];
                            @endphp
                            <span class="badge {{ $statusBadges[$quotation->status] ?? 'bg-light text-dark' }} text-capitalize px-2 py-1">
                                {{ $quotation->status }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : '—' }}</td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('quotations.show', $quotation->id) }}"><i class="far fa-eye text-primary me-2"></i> View Quote</a></li>
                                    @if($quotation->status !== 'accepted')
                                        <li>
                                            <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST" onsubmit="return confirm('Convert this quotation to an Invoice?');">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success fw-semibold"><i class="fas fa-receipt me-2"></i> Convert to Invoice</button>
                                            </form>
                                        </li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('quotations.pdf', $quotation->id) }}" target="_blank"><i class="fas fa-print text-secondary me-2"></i> Print / PDF</a></li>
                                    <li><a class="dropdown-item" href="{{ route('quotations.edit', $quotation->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" onsubmit="return confirm('Delete quotation?');">
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>No quotations found</h5>
                            <p class="small mb-3">Create custom itemized price quotes for your customers</p>
                            <a href="{{ route('quotations.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> New Quotation
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($quotations->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $quotations->links() }}
    </div>
    @endif
</div>
@endsection
