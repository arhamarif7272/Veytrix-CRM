@extends('layouts.app')

@section('title', 'Lead Conversion Report')
@section('page-title', 'Lead Conversion Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Lead Conversion</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Lead Conversion Analytics</h4>
        <p class="text-muted mb-0">Conversion funnels, status distributions, and acquisition channel effectiveness</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> All Reports</a>
</div>

<!-- KPI Summary Strip -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Total Captured Leads</div>
            <h3 class="fw-bold text-dark mb-0">{{ $metrics['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Converted to Customers</div>
            <h3 class="fw-bold text-success mb-0">{{ $metrics['converted'] }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Overall Conversion Rate</div>
            <h3 class="fw-bold text-primary mb-0">{{ $metrics['conversion_rate'] }}%</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Status Breakdown -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark">Leads by Qualification Status</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($metrics['by_status'] as $status => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="text-capitalize fw-semibold">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $count }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Source Breakdown -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark">Leads by Inbound Source</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($metrics['by_source'] as $source => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="text-capitalize fw-semibold">{{ str_replace('_', ' ', $source) }}</span>
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $count }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
