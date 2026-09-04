@extends('layouts.app')

@section('title', 'Edit ' . $lead->title)
@section('page-title', 'Edit Lead')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item"><a href="{{ route('leads.show', $lead->id) }}">{{ $lead->title }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('leads.update', $lead->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Edit Lead: {{ $lead->title }}</h4>
                    <p class="text-muted mb-0">Modify contact points, deal size expectations, and sales ownership</p>
                </div>
                <div>
                    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Lead</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-bullseye me-2"></i> Lead Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Lead Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $lead->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $lead->first_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $lead->last_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $lead->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company', $lead->company) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sliders-h me-2"></i> Pipeline & Status</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lead Source <span class="text-danger">*</span></label>
                            <select name="source" class="form-select" required>
                                <option value="website" {{ old('source', $lead->source) === 'website' ? 'selected' : '' }}>Website</option>
                                <option value="referral" {{ old('source', $lead->source) === 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="social_media" {{ old('source', $lead->source) === 'social_media' ? 'selected' : '' }}>Social Media</option>
                                <option value="email_campaign" {{ old('source', $lead->source) === 'email_campaign' ? 'selected' : '' }}>Email Campaign</option>
                                <option value="cold_call" {{ old('source', $lead->source) === 'cold_call' ? 'selected' : '' }}>Cold Call</option>
                                <option value="event" {{ old('source', $lead->source) === 'event' ? 'selected' : '' }}>Conference / Event</option>
                                <option value="other" {{ old('source', $lead->source) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ old('priority', $lead->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $lead->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $lead->priority) === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="new" {{ old('status', $lead->status) === 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ old('status', $lead->status) === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="qualified" {{ old('status', $lead->status) === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                <option value="unqualified" {{ old('status', $lead->status) === 'unqualified' ? 'selected' : '' }}>Unqualified</option>
                                <option value="converted" {{ old('status', $lead->status) === 'converted' ? 'selected' : '' }}>Converted</option>
                                <option value="lost" {{ old('status', $lead->status) === 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estimated Value ($)</label>
                            <input type="number" step="0.01" name="value_estimate" class="form-control" value="{{ old('value_estimate', $lead->value_estimate) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Next Follow-Up Date</label>
                            <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date', $lead->follow_up_date ? $lead->follow_up_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assign Sales Executive</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Select Rep</option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ old('assigned_to', $lead->assigned_to) == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Lead Notes</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Lead</button>
            </div>
        </form>
    </div>
</div>
@endsection
