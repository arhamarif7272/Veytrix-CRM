@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'User Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Profile & Security</h4>
                <p class="text-muted mb-0">Manage your personal information and credentials</p>
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-user me-2"></i> Personal Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <small class="text-muted">Contact administrator to change account email.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <input type="text" class="form-control bg-light text-capitalize" value="{{ str_replace('_', ' ', $user->role) }}" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Office Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-danger"><i class="fas fa-lock me-2"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-danger px-4"><i class="fas fa-key me-1"></i> Update Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
