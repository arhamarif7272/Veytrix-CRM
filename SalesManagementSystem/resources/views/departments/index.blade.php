@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Company Departments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Departments</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Organizational Structure</h4>
        <p class="text-muted mb-0">Manage business divisions, head managers, and teams</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newDeptModal">
        <i class="fas fa-plus me-1"></i> Add Department
    </button>
</div>

<div class="row g-4">
    @forelse($departments as $dept)
    @php
        $mgr = $managers->get((string) $dept->manager_id);
    @endphp
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold text-dark mb-0">{{ $dept->name }}</h5>
                        <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Delete department?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm text-danger p-0"><i class="far fa-trash-alt"></i></button>
                        </form>
                    </div>
                    <p class="text-muted small mb-3">{{ $dept->description ?? 'No description provided.' }}</p>
                </div>
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Manager:</span>
                    <span class="badge bg-primary-subtle text-primary">{{ $mgr?->name ?? 'Unassigned' }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="fas fa-sitemap fa-3x mb-3 text-secondary opacity-50"></i>
        <h5>No departments defined</h5>
        <p class="small">Add departments like Sales, Engineering, Customer Success</p>
    </div>
    @endforelse
</div>

<!-- Modal: New Dept -->
<div class="modal fade" id="newDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Enterprise Sales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Head of Department</label>
                        <select name="manager_id" class="form-select">
                            <option value="">Select Manager</option>
                            @foreach($managers as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Mission, functions, responsibilities..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
