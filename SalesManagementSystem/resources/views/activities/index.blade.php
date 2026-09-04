@extends('layouts.app')

@section('title', 'Activity Stream')
@section('page-title', 'Enterprise Activity Stream')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activity Stream</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Live Activity Stream</h4>
        <p class="text-muted mb-0">Audited timeline of sales calls, lead conversions, deals won, and customer updates</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('activities.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <select name="type" class="form-select bg-light">
                    <option value="">All Activity Types</option>
                    <option value="created" {{ request('type') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="status_change" {{ request('type') === 'status_change' ? 'selected' : '' }}>Status Change</option>
                    <option value="assignment" {{ request('type') === 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="converted" {{ request('type') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="call" {{ request('type') === 'call' ? 'selected' : '' }}>Call</option>
                    <option value="meeting" {{ request('type') === 'meeting' ? 'selected' : '' }}>Meeting</option>
                    <option value="note" {{ request('type') === 'note' ? 'selected' : '' }}>Note</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="related_type" class="form-select bg-light">
                    <option value="">All Modules</option>
                    <option value="lead" {{ request('related_type') === 'lead' ? 'selected' : '' }}>Leads</option>
                    <option value="customer" {{ request('related_type') === 'customer' ? 'selected' : '' }}>Customers</option>
                    <option value="deal" {{ request('related_type') === 'deal' ? 'selected' : '' }}>Deals</option>
                    <option value="ticket" {{ request('related_type') === 'ticket' ? 'selected' : '' }}>Support Tickets</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="user_id" class="form-select bg-light">
                    <option value="">All Team Members</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['type', 'related_type', 'user_id']))
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Timeline Stream -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="timeline">
            @forelse($activities as $act)
            <div class="d-flex mb-4">
                @php
                    $actIcons = [
                        'created'       => 'fas fa-plus bg-primary text-white',
                        'status_change' => 'fas fa-exchange-alt bg-info text-dark',
                        'assignment'    => 'fas fa-user-tag bg-warning text-dark',
                        'converted'     => 'fas fa-magic bg-success text-white',
                        'call'          => 'fas fa-phone bg-primary text-white',
                        'meeting'       => 'fas fa-users bg-indigo text-white',
                        'note'          => 'fas fa-sticky-note bg-secondary text-white',
                    ];
                @endphp
                <div class="timeline-badge rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm {{ $actIcons[$act->type] ?? 'fas fa-check bg-light text-dark' }}" style="width: 40px; height: 40px; flex-shrink: 0;">
                </div>
                <div class="flex-grow-1 bg-light p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <strong class="text-dark">{{ $act->subject }}</strong>
                            <span class="badge bg-white text-muted border text-capitalize ms-2">{{ $act->related_type ?? 'Activity' }}</span>
                        </div>
                        <small class="text-muted">{{ $act->occurred_at ? $act->occurred_at->diffForHumans() : '' }}</small>
                    </div>
                    <p class="small text-muted mb-2">{{ $act->description }}</p>
                    <div class="d-flex align-items-center small text-secondary">
                        <i class="far fa-user me-1"></i>
                        <span>{{ $act->performed_by_name ?? 'System' }}</span>
                        <span class="mx-2">&bull;</span>
                        <span>{{ $act->occurred_at ? $act->occurred_at->format('M d, Y h:i A') : '' }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-stream fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No activities recorded yet</h5>
                <p class="small">Workflows, updates, and interactions will be logged automatically</p>
            </div>
            @endforelse
        </div>
    </div>
    @if($activities->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $activities->links() }}
    </div>
    @endif
</div>
@endsection
