@extends('layouts.app')

@section('title', 'Revenue & Billing Report')
@section('page-title', 'Revenue Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Revenue</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Revenue & Billing Analysis</h4>
        <p class="text-muted mb-0">Financial collections, outstanding accounts receivable, and aging debts</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> All Reports</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <div class="text-muted small">Total Realized Revenue</div>
            <h2 class="fw-bold text-success mb-0">${{ number_format($metrics['total_collected'], 2) }}</h2>
            <small class="text-muted mt-1">{{ $metrics['paid_count'] }} paid invoices</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <div class="text-muted small">Outstanding Balance (Pending)</div>
            <h2 class="fw-bold text-primary mb-0">${{ number_format($metrics['pending'], 2) }}</h2>
            <small class="text-muted mt-1">Pending customer settlement</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <div class="text-muted small">Overdue Receivables</div>
            <h2 class="fw-bold text-danger mb-0">${{ number_format($metrics['overdue'], 2) }}</h2>
            <small class="text-muted mt-1">Past due payment grace period</small>
        </div>
    </div>
</div>
@endsection
