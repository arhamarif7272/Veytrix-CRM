@extends('layouts.app')

@section('title', 'My Quotations')
@section('page-title', 'My Quotations')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Quotations</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-dark">Price Quotations Received</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-muted text-uppercase">
                    <tr>
                        <th class="ps-4">Quotation #</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Valid Until</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $q->number }}</td>
                        <td class="fw-bold text-primary">${{ number_format($q->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $q->status === 'accepted' ? 'bg-success' : 'bg-info' }} text-capitalize">
                                {{ $q->status }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $q->valid_until ? $q->valid_until->format('M d, Y') : '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('quotations.my.show', $q->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No quotations currently available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
