@extends('layouts.app')

@section('title', 'Leads')
@section('page-title', 'Lead Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Leads</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Sales Leads</h4>
        <p class="text-muted mb-0">Track prospect qualification, follow-ups, and customer conversions</p>
    </div>
    <div>
        <a href="{{ route('leads.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Lead
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('leads.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search leads, names, company..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="qualified" {{ request('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="unqualified" {{ request('status') === 'unqualified' ? 'selected' : '' }}>Unqualified</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select bg-light">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
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
                @if(request()->anyFilled(['search', 'status', 'priority', 'assigned_to']))
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></a>
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
                        <th class="ps-4">Lead / Contact</th>
                        <th>Source</th>
                        <th>Estimated Value</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Follow Up</th>
                        <th>Assigned To</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td class="ps-4">
                            <div>
                                <a href="{{ route('leads.show', $lead->id) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $lead->title }}
                                </a>
                                <div class="small text-muted">
                                    @if($lead->full_name) {{ $lead->full_name }} @endif
                                    @if($lead->company) &bull; {{ $lead->company }} @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize">{{ str_replace('_', ' ', $lead->source) }}</span>
                        </td>
                        <td class="fw-bold text-dark">
                            {{ $lead->value_estimate ? '$' . number_format($lead->value_estimate, 2) : '—' }}
                        </td>
                        <td>
                            @php
                                $priorityColors = [
                                    'high'   => 'bg-danger-subtle text-danger',
                                    'medium' => 'bg-warning-subtle text-warning-emphasis',
                                    'low'    => 'bg-info-subtle text-info',
                                ];
                            @endphp
                            <span class="badge {{ $priorityColors[$lead->priority] ?? 'bg-light text-dark' }} text-capitalize px-2 py-1">
                                {{ $lead->priority }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusBadges = [
                                    'new'         => 'bg-primary-subtle text-primary',
                                    'contacted'   => 'bg-info-subtle text-info',
                                    'qualified'   => 'bg-warning-subtle text-warning-emphasis',
                                    'converted'   => 'bg-success text-white',
                                    'unqualified' => 'bg-secondary-subtle text-secondary',
                                    'lost'        => 'bg-danger-subtle text-danger',
                                ];
                            @endphp
                            <span class="badge {{ $statusBadges[$lead->status] ?? 'bg-light text-dark' }} text-capitalize px-2 py-1">
                                {{ $lead->status }}
                            </span>
                        </td>
                        <td>
                            @if($lead->follow_up_date)
                                <span class="small {{ $lead->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                    <i class="far fa-calendar-alt me-1"></i>{{ $lead->follow_up_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $assigned = $salesReps->firstWhere('_id', $lead->assigned_to) ?? $salesReps->firstWhere('id', $lead->assigned_to);
                            @endphp
                            <span class="small text-dark">{{ $assigned?->name ?? 'Unassigned' }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('leads.show', $lead->id) }}"><i class="far fa-eye text-primary me-2"></i> View Lead</a></li>
                                    @if(!$lead->isConverted())
                                        <li><a class="dropdown-item text-success fw-semibold" href="{{ route('leads.show', $lead->id) }}#convert"><i class="fas fa-magic me-2"></i> Convert to Customer</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('leads.edit', $lead->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Delete this lead?');">
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
                            <i class="fas fa-funnel-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>No leads found</h5>
                            <p class="small mb-3">Add prospective leads to build your sales funnel</p>
                            <a href="{{ route('leads.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Lead
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($leads->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $leads->links() }}
    </div>
    @endif
</div>
@endsection
