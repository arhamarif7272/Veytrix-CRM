@extends('layouts.app')

@section('title', 'Add Lead')
@section('page-title', 'Create Sales Lead')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active">New Lead</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('leads.store') }}" method="POST">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Create Sales Lead</h4>
                    <p class="text-muted mb-0">Record prospect contact info, qualification status, and pipeline potential</p>
                </div>
                <div>
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Lead</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-bullseye me-2"></i> Lead Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Lead Title / Opportunity Summary <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Enterprise Cloud Migration for Zenith Corp" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="John">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="john@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 555-0182">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="Acme International">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sliders-h me-2"></i> Lead Pipeline & Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lead Source <span class="text-danger">*</span></label>
                            <select name="source" class="form-select" required>
                                <option value="website" {{ old('source') === 'website' ? 'selected' : '' }}>Website</option>
                                <option value="referral" {{ old('source') === 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="social_media" {{ old('source') === 'social_media' ? 'selected' : '' }}>Social Media</option>
                                <option value="email_campaign" {{ old('source') === 'email_campaign' ? 'selected' : '' }}>Email Campaign</option>
                                <option value="cold_call" {{ old('source') === 'cold_call' ? 'selected' : '' }}>Cold Call</option>
                                <option value="event" {{ old('source') === 'event' ? 'selected' : '' }}>Conference / Event</option>
                                <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="new" {{ old('status', 'new') === 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="qualified" {{ old('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                <option value="unqualified" {{ old('status') === 'unqualified' ? 'selected' : '' }}>Unqualified</option>
                                <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estimated Value ($)</label>
                            <input type="number" step="0.01" name="value_estimate" class="form-control" value="{{ old('value_estimate') }}" placeholder="15000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Next Follow-Up Date</label>
                            <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assign Sales Executive</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Assign to Me</option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ old('assigned_to') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Lead Notes & Context</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Pain points, requirements, conversation history...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Lead</button>
            </div>
        </form>
    </div>
</div>
@endsection
