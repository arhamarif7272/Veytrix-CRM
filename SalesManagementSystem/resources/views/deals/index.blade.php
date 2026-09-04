@extends('layouts.app')

@section('title', 'Deals')
@section('page-title', 'Deals Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Deals</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Sales Opportunities</h4>
        <p class="text-muted mb-0">Track pipeline stages, closing probabilities, and deal values</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('deals.pipeline') }}" class="btn btn-outline-primary">
            <i class="fas fa-columns me-1"></i> Kanban Pipeline
        </a>
        <a href="{{ route('deals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Deal
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('deals.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search deals..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="won" {{ request('status') === 'won' ? 'selected' : '' }}>Won</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="stage_id" class="form-select bg-light">
                    <option value="">All Stages</option>
                    @foreach($stages as $stg)
                        <option value="{{ $stg->id }}" {{ request('stage_id') == $stg->id ? 'selected' : '' }}>{{ $stg->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_to" class="form-select bg-light">
                    <option value="">All Reps</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}" {{ request('assigned_to') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'status', 'stage_id', 'assigned_to']))
                    <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></a>
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
                        <th class="ps-4">Deal Title / Opportunity</th>
                        <th>Customer</th>
                        <th>Stage</th>
                        <th>Value</th>
                        <th>Probability</th>
                        <th>Expected Close</th>
                        <th>Assigned Rep</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deals as $deal)
                    @php
                        $stg = $stages->firstWhere('_id', $deal->stage_id) ?? $stages->firstWhere('id', $deal->stage_id);
                        $cust = $customers->firstWhere('_id', $deal->customer_id) ?? $customers->firstWhere('id', $deal->customer_id);
                        $rep = $salesReps->firstWhere('_id', $deal->assigned_to) ?? $salesReps->firstWhere('id', $deal->assigned_to);
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('deals.show', $deal->id) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $deal->title }}
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
                        <td>
                            @php
                                $statusBadge = $deal->status === 'won' ? 'bg-success' : ($deal->status === 'lost' ? 'bg-danger' : 'bg-primary');
                            @endphp
                            <span class="badge {{ $statusBadge }} text-white">
                                {{ $stg?->name ?? 'Stage' }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">
                            ${{ number_format($deal->value, 2) }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                    <div class="progress-bar bg-info" style="width: {{ $deal->probability ?? 0 }}%"></div>
                                </div>
                                <span class="small text-muted">{{ $deal->probability ?? 0 }}%</span>
                            </div>
                        </td>
                        <td class="text-muted small">
                            {{ $deal->expected_close_date ? $deal->expected_close_date->format('M d, Y') : '—' }}
                        </td>
                        <td>
                            <span class="small text-dark">{{ $rep?->name ?? 'Unassigned' }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('deals.show', $deal->id) }}"><i class="far fa-eye text-primary me-2"></i> View Deal</a></li>
                                    <li><a class="dropdown-item" href="{{ route('quotations.create', ['customer_id' => $deal->customer_id, 'deal_id' => $deal->id]) }}"><i class="fas fa-file-invoice text-info me-2"></i> Create Quote</a></li>
                                    <li><a class="dropdown-item" href="{{ route('deals.edit', $deal->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('deals.destroy', $deal->id) }}" method="POST" onsubmit="return confirm('Delete this deal?');">
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
                            <i class="fas fa-handshake fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>No deals found</h5>
                            <p class="small mb-3">Create your first deal opportunity</p>
                            <a href="{{ route('deals.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Deal
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($deals->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $deals->links() }}
    </div>
    @endif
</div>
@endsection
