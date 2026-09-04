@extends('layouts.app')

@section('title', 'Add Customer')
@section('page-title', 'Create New Customer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">New Customer</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Add New Customer</h4>
                    <p class="text-muted mb-0">Record account information, business profile, and sales assignment</p>
                </div>
                <div>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Customer</button>
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
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. John Doe or Acme Corp" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="e.g. Acme Technologies Inc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="client@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 555-0199">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://company.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Industry</label>
                            <input type="text" name="industry" class="form-control" value="{{ old('industry') }}" placeholder="e.g. SaaS, Finance, Healthcare">
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
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="123 Business Boulevard, Suite 400">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="New York">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country') }}" placeholder="United States">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="prospect" {{ old('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="churned" {{ old('status') === 'churned' ? 'selected' : '' }}>Churned</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assigned Sales Executive</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Assign to Me</option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ old('assigned_to') == $rep->id ? 'selected' : '' }}>{{ $rep->name }} ({{ ucfirst($rep->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lead Source</label>
                            <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="e.g. Website, Referral, Cold outreach">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Annual Revenue ($)</label>
                            <input type="number" step="0.01" name="annual_revenue" class="form-control" value="{{ old('annual_revenue') }}" placeholder="e.g. 500000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee Count</label>
                            <input type="number" name="employee_count" class="form-control" value="{{ old('employee_count') }}" placeholder="e.g. 50">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Internal Notes</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Background details, special account requirements, preferences...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
