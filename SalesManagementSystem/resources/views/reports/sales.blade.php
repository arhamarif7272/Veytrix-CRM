@extends('layouts.app')

@section('title', 'Sales Performance Report')
@section('page-title', 'Sales Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Sales</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Sales & Pipeline Analytics</h4>
        <p class="text-muted mb-0">Closed revenue, win rates, pipeline value, and deal size metrics</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> All Reports</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Total Closed Won Value</div>
            <h3 class="fw-bold text-success mb-0">${{ number_format($metrics['won_value'], 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Active Pipeline Value</div>
            <h3 class="fw-bold text-primary mb-0">${{ number_format($metrics['pipeline_value'], 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Win Rate Ratio</div>
            <h3 class="fw-bold text-dark mb-0">{{ $metrics['win_rate'] }}%</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Average Deal Size</div>
            <h3 class="fw-bold text-info mb-0">${{ number_format($metrics['avg_deal_size'], 2) }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark">Deal Volume Distribution</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="fw-semibold text-success"><i class="fas fa-trophy me-2"></i> Closed Won</span>
                        <span class="badge bg-success px-3 py-2 fs-6">{{ $metrics['won_count'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="fw-semibold text-primary"><i class="fas fa-spinner me-2"></i> Open in Pipeline</span>
                        <span class="badge bg-primary px-3 py-2 fs-6">{{ $metrics['open_count'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="fw-semibold text-danger"><i class="fas fa-times-circle me-2"></i> Closed Lost</span>
                        <span class="badge bg-danger px-3 py-2 fs-6">{{ $metrics['lost_count'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-top">
                        <span class="fw-bold text-dark">Total Created Deals</span>
                        <span class="badge bg-dark px-3 py-2 fs-6">{{ $metrics['total_deals'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
