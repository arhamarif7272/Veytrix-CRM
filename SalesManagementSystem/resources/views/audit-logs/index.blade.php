@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Security & Audit Trail')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Audit Logs</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Enterprise Audit Trail</h4>
        <p class="text-muted mb-0">Immutable compliance logs of record creations, data mutations, and administrative overrides</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('audit-logs.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="action" class="form-control bg-light" placeholder="Search action (e.g. customer.created)..." value="{{ request('action') }}">
            </div>
            <div class="col-md-3">
                <select name="module" class="form-select bg-light">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="actor" class="form-control bg-light" placeholder="Actor name..." value="{{ request('actor') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['action', 'module', 'actor']))
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
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
                        <th class="ps-4">Timestamp</th>
                        <th>User / Actor</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Entity</th>
                        <th>IP Address</th>
                        <th class="text-end pe-4">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 small text-muted">
                            {{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : '—' }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $log->actor_name ?? 'System' }}</div>
                            <small class="text-muted text-capitalize">{{ str_replace('_', ' ', $log->actor_role ?? '') }}</small>
                        </td>
                        <td>
                            <code class="text-primary">{{ $log->action }}</code>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize">{{ $log->module }}</span>
                        </td>
                        <td class="small text-muted">
                            {{ $log->entity_label ?: ($log->entity_type . ' #' . substr($log->entity_id, -6)) }}
                        </td>
                        <td class="small text-muted">
                            <code>{{ $log->ip_address ?? '127.0.0.1' }}</code>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-secondary">Inspect</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No audit events recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
