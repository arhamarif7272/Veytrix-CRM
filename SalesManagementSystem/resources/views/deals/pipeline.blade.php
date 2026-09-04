@extends('layouts.app')

@section('title', 'Sales Pipeline Kanban')
@section('page-title', 'Pipeline Kanban')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('deals.index') }}">Deals</a></li>
    <li class="breadcrumb-item active">Pipeline</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Visual Sales Pipeline</h4>
        <p class="text-muted mb-0">Drag and monitor opportunities across qualification and closing stages</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('deals.pipeline') }}" method="GET" class="d-inline-flex align-items-center gap-2">
            <select name="assigned_to" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Team Deals</option>
                @foreach($salesReps as $rep)
                    <option value="{{ $rep->id }}" {{ $assignedTo == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-list me-1"></i> Table View
        </a>
        <a href="{{ route('deals.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Deal
        </a>
    </div>
</div>

<!-- Pipeline Kanban Board -->
<div class="kanban-wrapper d-flex gap-3 overflow-x-auto pb-4" style="min-height: calc(100vh - 240px);">
    @foreach($pipeline as $column)
    @php
        $stage = $column['stage'];
        $deals = $column['deals'];
    @endphp
    <div class="kanban-column flex-shrink-0" style="width: 320px;" data-stage-id="{{ $stage->id }}">
        <!-- Column Header -->
        <div class="card border-0 shadow-sm mb-3" style="border-top: 4px solid {{ $stage->color ?? '#4f46e5' }} !important;">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $stage->name }}</h6>
                    <span class="text-muted small">${{ number_format($column['total_value'], 0) }} &bull; {{ $column['count'] }} deals</span>
                </div>
                <span class="badge bg-light text-dark border rounded-pill">{{ $column['count'] }}</span>
            </div>
        </div>

        <!-- Deal Cards Container -->
        <div class="kanban-cards d-flex flex-column gap-2" id="stage-{{ $stage->id }}" style="min-height: 200px;">
            @forelse($deals as $deal)
            @php
                $cust = $customers->get((string) $deal->customer_id);
            @endphp
            <div class="card border-0 shadow-sm kanban-card" data-deal-id="{{ $deal->id }}" style="cursor: grab;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-dark border small">${{ number_format($deal->value, 0) }}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="{{ route('deals.show', $deal->id) }}"><i class="far fa-eye text-primary me-2"></i> View Details</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header small text-uppercase">Move Stage:</li>
                                @foreach($stages as $otherStage)
                                    @if($otherStage->id !== $stage->id)
                                    <li>
                                        <form action="{{ route('deals.stage', $deal->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="stage_id" value="{{ $otherStage->id }}">
                                            <button type="submit" class="dropdown-item small">
                                                <i class="fas fa-arrow-right me-2 text-muted"></i> {{ $otherStage->name }}
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <a href="{{ route('deals.show', $deal->id) }}" class="fw-bold text-dark text-decoration-none d-block mb-1">
                        {{ $deal->title }}
                    </a>
                    @if($cust)
                        <div class="small text-muted mb-2"><i class="far fa-building me-1"></i>{{ $cust->name }}</div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                        <div><i class="far fa-clock me-1"></i>{{ $deal->expected_close_date ? $deal->expected_close_date->format('M d') : 'No date' }}</div>
                        <span class="fw-semibold text-primary">{{ $deal->probability ?? 0 }}%</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted small bg-light rounded border border-dashed">
                Empty stage
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endsection
