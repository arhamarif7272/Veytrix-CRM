@extends('layouts.app')

@section('title', 'Edit ' . $ticket->ticket_number)
@section('page-title', 'Edit Ticket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tickets.show', $ticket->id) }}">{{ $ticket->ticket_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Edit: {{ $ticket->ticket_number }}</h4>
                    <p class="text-muted mb-0">Update ticket metadata, assignee, and priority</p>
                </div>
                <div>
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Ticket</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-ticket-alt me-2"></i> Ticket Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $ticket->title) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="open" {{ old('status', $ticket->status) === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ old('status', $ticket->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="waiting_customer" {{ old('status', $ticket->status) === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                                <option value="resolved" {{ old('status', $ticket->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ old('status', $ticket->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ old('priority', $ticket->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $ticket->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $ticket->priority) === 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('priority', $ticket->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="technical" {{ old('category', $ticket->category) === 'technical' ? 'selected' : '' }}>Technical</option>
                                <option value="billing" {{ old('category', $ticket->category) === 'billing' ? 'selected' : '' }}>Billing</option>
                                <option value="general" {{ old('category', $ticket->category) === 'general' ? 'selected' : '' }}>General</option>
                                <option value="feature_request" {{ old('category', $ticket->category) === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Support Agent</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($agents as $ag)
                                    <option value="{{ $ag->id }}" {{ old('assigned_to', $ticket->assigned_to) == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SLA Resolution Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $ticket->due_date ? $ticket->due_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
