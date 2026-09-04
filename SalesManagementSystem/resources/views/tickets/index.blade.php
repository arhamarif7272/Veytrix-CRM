@extends('layouts.app')

@section('title', 'Support Tickets')
@section('page-title', 'Customer Support Desk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tickets</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Helpdesk & Support Cases</h4>
        <p class="text-muted mb-0">Customer inquiries, bug reports, and technical assistance resolution</p>
    </div>
    <div>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Open Ticket
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('tickets.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control bg-light" placeholder="Search ticket # or subject..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting_customer" {{ request('status') === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select bg-light">
                    <option value="">All Priorities</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select bg-light">
                    <option value="">All Categories</option>
                    <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Billing</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                    <option value="feature_request" {{ request('category') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'status', 'priority', 'category']))
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
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
                        <th class="ps-4">Ticket #</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned Agent</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    @php
                        $agent = $agents->firstWhere('_id', $ticket->assigned_to) ?? $agents->firstWhere('id', $ticket->assigned_to);
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $ticket->ticket_number }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-light text-muted border text-capitalize">{{ str_replace('_', ' ', $ticket->category) }}</span>
                        </td>
                        <td>
                            @php
                                $priorityBadges = [
                                    'critical' => 'bg-danger text-white',
                                    'high'     => 'bg-danger-subtle text-danger',
                                    'medium'   => 'bg-warning-subtle text-warning-emphasis',
                                    'low'      => 'bg-info-subtle text-info',
                                ];
                            @endphp
                            <span class="badge {{ $priorityBadges[$ticket->priority] ?? 'bg-light text-dark' }} text-capitalize px-2 py-1">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusBadges = [
                                    'open'             => 'bg-primary text-white',
                                    'in_progress'      => 'bg-info text-dark',
                                    'waiting_customer' => 'bg-warning text-dark',
                                    'resolved'         => 'bg-success text-white',
                                    'closed'           => 'bg-secondary text-white',
                                ];
                            @endphp
                            <span class="badge {{ $statusBadges[$ticket->status] ?? 'bg-light text-dark' }} text-capitalize px-2 py-1">
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </span>
                        </td>
                        <td class="small text-dark">{{ $agent?->name ?? 'Unassigned' }}</td>
                        <td class="small text-muted">{{ $ticket->created_at ? $ticket->created_at->format('M d, Y') : '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                View Case
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-headset fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>No tickets found</h5>
                            <p class="small mb-3">Customer support cases and inquiries will appear here</p>
                            <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Open Ticket
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tickets->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
