@extends('layouts.app')

@section('title', 'Audit Log #' . substr($log->id, -6))
@section('page-title', 'Audit Inspection')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('audit-logs.index') }}">Audit Logs</a></li>
    <li class="breadcrumb-item active">Inspect</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Audit Event: {{ $log->action }}</h4>
                <p class="text-muted mb-0">Occurred on {{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : '' }}</p>
            </div>
            <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-info-circle me-2"></i> Event Context</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <span class="text-muted small d-block">Actor:</span>
                        <strong>{{ $log->actor_name }}</strong> ({{ $log->actor_role }})
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block">Target Entity:</span>
                        <strong>{{ $log->entity_type }} ({{ $log->entity_label ?: $log->entity_id }})</strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block">IP / Client:</span>
                        <code>{{ $log->ip_address }}</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Value Differences -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="fw-bold mb-0 text-muted">Prior Values (Old)</h6>
                    </div>
                    <div class="card-body p-0">
                        <pre class="p-3 mb-0 bg-light rounded-bottom text-dark small" style="max-height: 350px; overflow-y: auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="fw-bold mb-0 text-success">Mutated Values (New)</h6>
                    </div>
                    <div class="card-body p-0">
                        <pre class="p-3 mb-0 bg-light rounded-bottom text-dark small" style="max-height: 350px; overflow-y: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
