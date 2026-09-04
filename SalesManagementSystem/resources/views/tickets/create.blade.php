@extends('layouts.app')

@section('title', 'Open Ticket')
@section('page-title', 'Create Support Ticket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
    <li class="breadcrumb-item active">New Ticket</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Submit Support Ticket</h4>
                    <p class="text-muted mb-0">Log a new issue, technical incident, or billing question</p>
                </div>
                <div>
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Submit Ticket</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-ticket-alt me-2"></i> Issue Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Subject / Issue Summary <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Unable to access reporting dashboard API after credentials rotation" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @if(!auth()->user()->isCustomer())
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Account</label>
                            <select name="customer_id" class="form-select">
                                <option value="">Select Customer (Optional)</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign Support Agent</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned (Queue)</option>
                                @foreach($agents as $ag)
                                    <option value="{{ $ag->id }}" {{ old('assigned_to') == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="technical">Technical Support</option>
                                <option value="billing">Billing & Invoicing</option>
                                <option value="general">General Inquiry</option>
                                <option value="feature_request">Feature Request</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical (System Down)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Detailed Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="6" placeholder="Steps to reproduce, error codes observed, affected users..." required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-1"></i> Submit Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
