@extends('layouts.app')

@section('title', 'Support Metrics Report')
@section('page-title', 'Support Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Support Desk</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Support Desk Analytics</h4>
        <p class="text-muted mb-0">Ticket volume, resolution rate, and SLA performance</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> All Reports</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Total Tickets Opened</div>
            <h3 class="fw-bold text-dark mb-0">{{ $metrics['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Active Open Cases</div>
            <h3 class="fw-bold text-warning mb-0">{{ $metrics['open'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Resolved / Closed</div>
            <h3 class="fw-bold text-success mb-0">{{ $metrics['resolved'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Resolution Success Rate</div>
            <h3 class="fw-bold text-primary mb-0">{{ $metrics['resolution_rate'] }}%</h3>
        </div>
    </div>
</div>
@endsection
