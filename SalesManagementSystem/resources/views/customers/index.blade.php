@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Customers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Customer Directory</h4>
        <p class="text-muted mb-0">Manage accounts, corporate details, and multi-contact relations</p>
    </div>
    <div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Customer
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('customers.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Search by name, company, email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="churned" {{ request('status') === 'churned' ? 'selected' : '' }}>Churned</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="assigned_to" class="form-select bg-light">
                    <option value="">All Sales Reps</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}" {{ request('assigned_to') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'status', 'assigned_to']))
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Customer Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Customer / Company</th>
                        <th>Contact Info</th>
                        <th>Industry / Location</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-primary-subtle text-primary fw-bold" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('customers.show', $customer->id) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                        {{ $customer->name }}
                                    </a>
                                    @if($customer->company)
                                        <small class="text-muted"><i class="far fa-building me-1"></i>{{ $customer->company }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($customer->email)
                                <div><i class="far fa-envelope text-muted me-1 small"></i>{{ $customer->email }}</div>
                            @endif
                            @if($customer->phone)
                                <div class="text-muted small"><i class="fas fa-phone-alt text-muted me-1 small"></i>{{ $customer->phone }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $customer->industry ?? '—' }}</div>
                            <small class="text-muted">{{ $customer->city ? $customer->city . ', ' : '' }}{{ $customer->country ?? '' }}</small>
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'active'   => 'bg-success-subtle text-success border border-success-subtle',
                                    'prospect' => 'bg-info-subtle text-info border border-info-subtle',
                                    'inactive' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                    'churned'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$customer->status] ?? 'bg-light text-dark' }} px-2 py-1 text-capitalize">
                                {{ $customer->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $rep = $salesReps->firstWhere('_id', $customer->assigned_to) ?? $salesReps->firstWhere('id', $customer->assigned_to);
                            @endphp
                            <span class="small text-dark">{{ $rep?->name ?? 'Unassigned' }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ $customer->created_at ? $customer->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('customers.show', $customer->id) }}"><i class="far fa-eye text-primary me-2"></i> View 360°</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customers.edit', $customer->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="far fa-trash-alt me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                                <h5>No customers found</h5>
                                <p class="small mb-3">Get started by adding your first enterprise client</p>
                                <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add Customer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
