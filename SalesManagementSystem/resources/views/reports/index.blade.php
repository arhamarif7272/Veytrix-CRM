@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Enterprise Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Executive Intelligence & Reporting</h4>
        <p class="text-muted mb-0">Deep analytics on lead conversion, revenue velocity, team output, and customer retention</p>
    </div>
</div>

<!-- Report Navigation Cards -->
<div class="row g-4 mb-4">
    <!-- Lead Conversion -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="avatar-circle bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-funnel-dollar fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Lead Conversion Report</h5>
                    <p class="text-muted small">Analyze marketing channels, acquisition sources, qualification velocity, and prospect-to-customer conversion rates.</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary-subtle text-primary">{{ $leads['conversion_rate'] ?? 0 }}% Conv. Rate</span>
                    <a href="{{ route('reports.leads') }}" class="btn btn-sm btn-outline-primary">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales & Deals -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="avatar-circle bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-handshake fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Sales & Pipeline Analytics</h5>
                    <p class="text-muted small">Monitor closed-won contract volumes, average deal size, win/loss ratios, and open pipeline distributions.</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="badge bg-success-subtle text-success">${{ number_format($sales['won_value'] ?? 0) }} Won</span>
                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-success">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Billing -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="avatar-circle bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-chart-line fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Revenue & Cashflow</h5>
                    <p class="text-muted small">Assess realized revenue, billed invoices, outstanding customer receivables, and overdue accounts aging.</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="badge bg-info-subtle text-info">${{ number_format($revenue['total_collected'] ?? 0) }} Collected</span>
                    <a href="{{ route('reports.revenue') }}" class="btn btn-sm btn-outline-info">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Performance -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="avatar-circle bg-warning-subtle text-warning-emphasis rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-trophy fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Employee Leaderboard</h5>
                    <p class="text-muted small">Compare sales quotas, closed deals, assigned leads, and individual representative contributions across the firm.</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Team Quota Tracking</span>
                    <a href="{{ route('reports.performance') }}" class="btn btn-sm btn-outline-warning">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Health & Growth -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="avatar-circle bg-secondary-subtle text-secondary rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-users-cog fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Customer Acquisition</h5>
                    <p class="text-muted small">Track active customer accounts, prospects, enterprise churn rates, and account retention metrics.</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Account Health</span>
                    <a href="{{ route('reports.customers') }}" class="btn btn-sm btn-outline-secondary">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Desk SLAs -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="avatar-circle bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="fas fa-headset fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Support Desk Metrics</h5>
                    <p class="text-muted small">Review ticket resolution velocity, backlog cases, agent SLAs, and customer satisfaction performance.</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="badge bg-success text-white">{{ $support['resolution_rate'] ?? 0 }}% Resolved</span>
                    <a href="{{ route('reports.support') }}" class="btn btn-sm btn-outline-danger">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
