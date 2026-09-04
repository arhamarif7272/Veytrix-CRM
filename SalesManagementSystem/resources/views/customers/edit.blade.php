@extends('layouts.app')

@section('title', 'Edit ' . $customer->name)
@section('page-title', 'Edit Customer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer->id) }}">{{ $customer->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Edit: {{ $customer->name }}</h4>
                    <p class="text-muted mb-0">Update customer records and commercial profile</p>
                </div>
                <div>
                    <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Customer</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-user-circle me-2"></i> Primary Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer / Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company', $customer->company) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $customer->website) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Industry</label>
                            <input type="text" name="industry" class="form-control" value="{{ old('industry', $customer->industry) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-map-marker-alt me-2"></i> Location & Commercial Info</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Street Address</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $customer->country) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="prospect" {{ old('status', $customer->status) === 'prospect' ? 'selected' : '' }}>Prospect</option>
                                <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="churned" {{ old('status', $customer->status) === 'churned' ? 'selected' : '' }}>Churned</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assigned Sales Executive</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Select Rep</option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ old('assigned_to', $customer->assigned_to) == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lead Source</label>
                            <input type="text" name="source" class="form-control" value="{{ old('source', $customer->source) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Annual Revenue ($)</label>
                            <input type="number" step="0.01" name="annual_revenue" class="form-control" value="{{ old('annual_revenue', $customer->annual_revenue) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee Count</label>
                            <input type="number" name="employee_count" class="form-control" value="{{ old('employee_count', $customer->employee_count) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Internal Notes</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $customer->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
