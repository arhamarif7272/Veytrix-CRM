@extends('layouts.app')

@section('title', 'My Invoices')
@section('page-title', 'My Invoices')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Invoices</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-dark">Billing Invoices</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-muted text-uppercase">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Total Amount</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $inv->number }}</td>
                        <td class="fw-bold text-dark">${{ number_format($inv->total, 2) }}</td>
                        <td class="fw-semibold {{ $inv->amount_due > 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($inv->amount_due, 2) }}
                        </td>
                        <td>
                            @php
                                $statusBadges = [
                                    'draft'   => 'bg-secondary text-white',
                                    'sent'    => 'bg-info text-dark',
                                    'partial' => 'bg-warning text-dark',
                                    'paid'    => 'bg-success text-white',
                                    'overdue' => 'bg-danger text-white',
                                ];
                            @endphp
                            <span class="badge {{ $statusBadges[$inv->status] ?? 'bg-light text-dark' }} text-capitalize">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $inv->due_date ? $inv->due_date->format('M d, Y') : '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('invoices.my.show', $inv->id) }}" class="btn btn-sm btn-outline-primary">View Invoice</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No invoices available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
