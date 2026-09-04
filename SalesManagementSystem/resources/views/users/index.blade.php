@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'Team & User Administration')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">User Administration</h4>
        <p class="text-muted mb-0">Manage system access, roles, account status, and department assignments</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Add User
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('users.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control bg-light" placeholder="Search name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select bg-light">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="sales_executive" {{ request('role') === 'sales_executive' ? 'selected' : '' }}>Sales Executive</option>
                    <option value="support_agent" {{ request('role') === 'support_agent' ? 'selected' : '' }}>Support Agent</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
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
                        <th class="ps-4">User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Phone</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3 bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleBadges = [
                                    'admin'           => 'bg-danger text-white',
                                    'manager'         => 'bg-primary text-white',
                                    'sales_executive' => 'bg-success text-white',
                                    'support_agent'   => 'bg-info text-dark',
                                    'customer'        => 'bg-secondary text-white',
                                ];
                            @endphp
                            <form action="{{ route('users.role', $user->id) }}" method="POST" class="d-inline-flex align-items-center">
                                @csrf
                                <select name="role" 
                                        class="form-select form-select-sm border-0 fw-semibold {{ $roleBadges[$user->role] ?? 'bg-light text-dark' }} shadow-none" 
                                        onchange="if(confirm('Reassign role of {{ addslashes($user->name) }} to ' + this.options[this.selectedIndex].text + '?')) { this.form.submit(); } else { this.value = '{{ $user->role }}'; }"
                                        style="cursor: pointer; width: auto; font-size: 11.5px; padding: 3px 26px 3px 10px; border-radius: 20px; font-weight: 600;"
                                        title="Click to quickly reassign user role">
                                    <option value="customer" class="bg-white text-dark" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                    <option value="sales_executive" class="bg-white text-dark" {{ $user->role === 'sales_executive' ? 'selected' : '' }}>Sales Executive</option>
                                    <option value="manager" class="bg-white text-dark" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="support_agent" class="bg-white text-dark" {{ $user->role === 'support_agent' ? 'selected' : '' }}>Support Agent</option>
                                    <option value="admin" class="bg-white text-dark" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <span class="badge {{ $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-capitalize">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $user->phone ?? '—' }}</td>
                        <td class="small text-muted">{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('users.edit', $user->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    @if($user->id !== auth()->id())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="far fa-trash-alt me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
