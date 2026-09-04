@extends('layouts.app')

@section('title', 'Customer Growth Report')
@section('page-title', 'Customer Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Customer Health</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Customer Account Analytics</h4>
        <p class="text-muted mb-0">Active enterprise clients, prospects, and churned accounts</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> All Reports</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Total Customer Records</div>
            <h3 class="fw-bold text-dark mb-0">{{ $totalCustomers }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Active Accounts</div>
            <h3 class="fw-bold text-success mb-0">{{ $activeCustomers }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Prospect Accounts</div>
            <h3 class="fw-bold text-info mb-0">{{ $prospects }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted small">Churned Accounts</div>
            <h3 class="fw-bold text-danger mb-0">{{ $churned }}</h3>
        </div>
    </div>
</div>
@endsection
