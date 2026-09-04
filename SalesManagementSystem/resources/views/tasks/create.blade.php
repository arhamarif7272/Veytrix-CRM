@extends('layouts.app')

@section('title', 'Schedule Task')
@section('page-title', 'Create Task')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
    <li class="breadcrumb-item active">New Task</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Schedule New Activity</h4>
                    <p class="text-muted mb-0">Set deadlines, reminder alerts, and customer associations</p>
                </div>
                <div>
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Task</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-calendar-check me-2"></i> Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Follow up on Enterprise Proposal with CTO" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="call">Phone Call</option>
                                <option value="meeting">Meeting / Demo</option>
                                <option value="email">Email</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="demo">Product Presentation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Due Date & Time</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign Task To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Assign to Me</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Related Record Type</label>
                            <select name="related_type" class="form-select">
                                <option value="">None / General Task</option>
                                <option value="lead">Lead</option>
                                <option value="customer">Customer</option>
                                <option value="deal">Deal</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description & Objective</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Agenda, conference call URL, talking points...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Task</button>
            </div>
        </form>
    </div>
</div>
@endsection
