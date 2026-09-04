@extends('layouts.app')

@section('title', 'Tasks')
@section('page-title', 'Task Calendar & Schedule')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tasks</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Scheduled Tasks & Reminders</h4>
        <p class="text-muted mb-0">Follow-ups, customer calls, demos, and administrative deadlines</p>
    </div>
    <div>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Task
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('tasks.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <select name="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select bg-light">
                    <option value="">All Task Types</option>
                    <option value="call" {{ request('type') === 'call' ? 'selected' : '' }}>Call</option>
                    <option value="meeting" {{ request('type') === 'meeting' ? 'selected' : '' }}>Meeting</option>
                    <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="follow_up" {{ request('type') === 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                    <option value="demo" {{ request('type') === 'demo' ? 'selected' : '' }}>Product Demo</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select bg-light">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled(['status', 'type', 'priority']))
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Task Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th style="width: 40px;" class="ps-4"></th>
                        <th>Task Title</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr>
                        <td class="ps-4">
                            @if($task->status !== 'completed')
                                <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;" title="Mark Completed">
                                        <i class="fas fa-check" style="font-size: 10px;"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-success"><i class="fas fa-check-circle"></i></span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark {{ $task->status === 'completed' ? 'text-decoration-line-through text-muted' : '' }}">
                                {{ $task->title }}
                            </div>
                            @if($task->description)
                                <small class="text-muted d-block text-truncate" style="max-width: 300px;">{{ $task->description }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeIcons = [
                                    'call'      => 'fas fa-phone text-primary',
                                    'meeting'   => 'fas fa-users text-info',
                                    'email'     => 'fas fa-envelope text-warning',
                                    'follow_up' => 'fas fa-redo text-secondary',
                                    'demo'      => 'fas fa-desktop text-success',
                                ];
                            @endphp
                            <span class="small text-capitalize"><i class="{{ $typeIcons[$task->type] ?? 'fas fa-tasks text-muted' }} me-1"></i> {{ str_replace('_', ' ', $task->type) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $task->priority === 'high' ? 'bg-danger' : ($task->priority === 'medium' ? 'bg-warning text-dark' : 'bg-info text-dark') }} text-capitalize">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="small {{ $task->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $task->status === 'completed' ? 'bg-success' : 'bg-light text-dark border' }} text-capitalize">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('tasks.edit', $task->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="far fa-trash-alt me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-tasks fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>All Caught Up!</h5>
                            <p class="small mb-3">No pending tasks or meetings currently scheduled</p>
                            <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Task
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tasks->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endsection
