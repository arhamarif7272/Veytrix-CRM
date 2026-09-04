@extends('layouts.app')

@section('title', $lead->title)
@section('page-title', 'Lead Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active">{{ $lead->title }}</li>
@endsection

@section('content')
<!-- Conversion Alert Banner if converted -->
@if($lead->isConverted())
<div class="alert alert-success border-0 shadow-sm d-flex justify-content-between align-items-center mb-4 p-3">
    <div class="d-flex align-items-center">
        <div class="fs-2 me-3">🎉</div>
        <div>
            <h5 class="alert-heading mb-1 fw-bold">This Lead Has Been Converted!</h5>
            <p class="mb-0 small">
                Converted into Customer
                @if($convertedCustomer)
                    <a href="{{ route('customers.show', $convertedCustomer->id) }}" class="fw-bold text-success text-decoration-underline">{{ $convertedCustomer->name }}</a>
                @endif
                @if($convertedDeal)
                    and Deal <a href="{{ route('deals.show', $convertedDeal->id) }}" class="fw-bold text-success text-decoration-underline">{{ $convertedDeal->title }}</a>
                @endif
                on {{ $lead->converted_at ? $lead->converted_at->format('M d, Y h:i A') : '' }}.
            </p>
        </div>
    </div>
    <div>
        @if($convertedCustomer)
            <a href="{{ route('customers.show', $convertedCustomer->id) }}" class="btn btn-sm btn-success">View Customer 360°</a>
        @endif
    </div>
</div>
@endif

<!-- Lead Overview Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary text-capitalize">{{ str_replace('_', ' ', $lead->source) }}</span>
                    @php
                        $priorityColors = [
                            'high'   => 'bg-danger-subtle text-danger',
                            'medium' => 'bg-warning-subtle text-warning-emphasis',
                            'low'    => 'bg-info-subtle text-info',
                        ];
                    @endphp
                    <span class="badge {{ $priorityColors[$lead->priority] ?? 'bg-light text-dark' }} text-capitalize">
                        {{ $lead->priority }} Priority
                    </span>
                    <span class="badge bg-light text-dark border text-capitalize">{{ $lead->status }}</span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $lead->title }}</h3>
                <p class="text-muted mb-0">
                    @if($lead->full_name) <i class="far fa-user me-1"></i>{{ $lead->full_name }} &bull; @endif
                    @if($lead->company) <i class="far fa-building me-1"></i>{{ $lead->company }} &bull; @endif
                    Added {{ $lead->created_at ? $lead->created_at->format('M d, Y') : '' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(!$lead->isConverted())
                    <button type="button" class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#convertLeadModal">
                        <i class="fas fa-magic me-1"></i> Convert to Customer
                    </button>
                @endif
                <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Delete this lead?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"><i class="far fa-trash-alt"></i></button>
                </form>
            </div>
        </div>

        <!-- Quick Status Update Strip -->
        <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">Quick Status:</span>
                @foreach(['new', 'contacted', 'qualified', 'lost'] as $st)
                    <form action="{{ route('leads.status', $lead->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="status" value="{{ $st }}">
                        <button type="submit" class="btn btn-sm {{ $lead->status === $st ? 'btn-dark' : 'btn-outline-secondary' }} text-capitalize py-0 px-2" style="font-size: 12px;">
                            {{ $st }}
                        </button>
                    </form>
                @endforeach
            </div>
            <div>
                <form action="{{ route('leads.assign', $lead->id) }}" method="POST" class="d-flex align-items-center gap-2">
                    @csrf
                    <span class="text-muted small fw-semibold">Assignee:</span>
                    <select name="assigned_to" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" {{ $lead->assigned_to == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Lead Info -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Lead Specification</h5>
            </div>
            <div class="card-body pt-0">
                <ul class="list-unstyled mb-0">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Contact Name</span>
                        <span class="fw-semibold text-dark">{{ $lead->full_name ?: '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Email Address</span>
                        @if($lead->email)
                            <a href="mailto:{{ $lead->email }}" class="fw-semibold text-primary">{{ $lead->email }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Phone Number</span>
                        <span class="fw-semibold text-dark">{{ $lead->phone ?: '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Company</span>
                        <span class="fw-semibold text-dark">{{ $lead->company ?: '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Estimated Deal Value</span>
                        <span class="fw-bold text-success">{{ $lead->value_estimate ? '$' . number_format($lead->value_estimate, 2) : '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Next Follow Up</span>
                        <span class="fw-semibold {{ $lead->isOverdue() ? 'text-danger' : 'text-dark' }}">
                            {{ $lead->follow_up_date ? $lead->follow_up_date->format('M d, Y') : '—' }}
                        </span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Created By</span>
                        <span class="text-dark">{{ $createdUser?->name ?? 'System' }}</span>
                    </li>
                </ul>

                @if($lead->notes)
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Lead Notes</div>
                    <p class="small mb-0 text-dark">{{ $lead->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Timeline & Activities -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i> Lead History & Activities</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($activities as $act)
                    <div class="d-flex mb-3">
                        <div class="timeline-icon me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                            <i class="fas fa-check small"></i>
                        </div>
                        <div class="bg-light p-3 rounded flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">{{ $act->subject }}</strong>
                                <small class="text-muted">{{ $act->occurred_at ? $act->occurred_at->diffForHumans() : '' }}</small>
                            </div>
                            <p class="small text-muted mb-1">{{ $act->description }}</p>
                            <small class="text-secondary"><i class="far fa-user me-1"></i>{{ $act->performed_by_name ?? 'System' }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No interactions logged yet for this lead.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Convert Lead to Customer -->
<div class="modal fade" id="convertLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('leads.convert', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-magic me-2"></i> Convert Lead to Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted">
                        Converting this lead will automatically create a new <strong>Customer account</strong> with the prospect's details and lock this lead as converted.
                    </p>

                    <div class="card bg-light border-0 p-3 mb-4">
                        <h6 class="fw-bold mb-2 text-dark">Customer Details to be Created:</h6>
                        <div class="row small g-2">
                            <div class="col-6"><strong>Name:</strong> {{ $lead->full_name ?: $lead->title }}</div>
                            <div class="col-6"><strong>Email:</strong> {{ $lead->email ?: 'N/A' }}</div>
                            <div class="col-6"><strong>Phone:</strong> {{ $lead->phone ?: 'N/A' }}</div>
                            <div class="col-6"><strong>Company:</strong> {{ $lead->company ?: 'N/A' }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-handshake me-1"></i> Deal Pipeline Generation (Optional)</h6>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="create_deal" value="1" id="createDealSwitch" checked>
                        <label class="form-check-label fw-bold" for="createDealSwitch">Also create an active Deal in sales pipeline</label>
                    </div>

                    <div id="dealFields">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Deal Title</label>
                                <input type="text" name="deal_title" class="form-control" value="{{ ($lead->company ?: $lead->title) . ' - Initial Deal' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Deal Value ($)</label>
                                <input type="number" step="0.01" name="deal_value" class="form-control" value="{{ $lead->value_estimate ?? 10000 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pipeline Stage</label>
                                <select name="stage_id" class="form-select">
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expected Close Date</label>
                                <input type="date" name="expected_close_date" class="form-control" value="{{ now()->addDays(30)->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold"><i class="fas fa-check-circle me-1"></i> Complete Conversion</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
