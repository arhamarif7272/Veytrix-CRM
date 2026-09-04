@extends('layouts.app')

@section('title', 'Team Performance Report')
@section('page-title', 'Employee Performance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Performance</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Representative Quota & Leaderboard</h4>
        <p class="text-muted mb-0">Compare deal volumes, won revenue, and sales productivity</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> All Reports</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Sales Representative</th>
                        <th>Role</th>
                        <th>Assigned Leads</th>
                        <th>Deals Won</th>
                        <th>Total Revenue Closed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($performance as $p)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-primary-subtle text-primary fw-bold" style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($p['user']->name, 0, 2)) }}
                                </div>
                                <span class="fw-semibold text-dark">{{ $p['user']->name }}</span>
                            </div>
                        </td>
                        <td class="small text-muted text-capitalize">{{ str_replace('_', ' ', $p['user']->role) }}</td>
                        <td class="fw-semibold">{{ $p['leads'] }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success fs-6 px-3 py-1">{{ $p['deals_won'] }} Deals</span>
                        </td>
                        <td class="fw-bold text-dark fs-6">${{ number_format($p['revenue'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No representative metrics available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
