@extends('layouts.app')

@section('title', $deal->title)
@section('page-title', 'Deal Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('deals.index') }}">Deals</a></li>
    <li class="breadcrumb-item active">{{ $deal->title }}</li>
@endsection

@section('content')
<!-- Deal Stage Visual Progress Tracker -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge {{ $deal->status === 'won' ? 'bg-success' : ($deal->status === 'lost' ? 'bg-danger' : 'bg-primary') }} text-capitalize px-3 py-1 fs-6">
                        {{ $deal->status }}
                    </span>
                    @if($deal->customer_id && $customer)
                        <a href="{{ route('customers.show', $customer->id) }}" class="badge bg-light text-primary border text-decoration-none px-2 py-1">
                            <i class="far fa-building me-1"></i>{{ $customer->name }}
                        </a>
                    @endif
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $deal->title }}</h3>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($deal->isOpen())
                    <form action="{{ route('deals.won', $deal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark deal as WON? 🎉');">
                        @csrf
                        <button type="submit" class="btn btn-success fw-semibold"><i class="fas fa-trophy me-1"></i> Mark as Won</button>
                    </form>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#markLostModal">
                        <i class="fas fa-times me-1"></i> Mark as Lost
                    </button>
                @endif
                <a href="{{ route('quotations.create', ['customer_id' => $deal->customer_id, 'deal_id' => $deal->id]) }}" class="btn btn-outline-info">
                    <i class="fas fa-file-invoice me-1"></i> Create Quote
                </a>
                <a href="{{ route('deals.edit', $deal->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>

        <!-- Stages Progress Steps -->
        <div class="deal-stages-wrapper overflow-x-auto py-2">
            <div class="d-flex justify-content-between position-relative gap-2" style="min-width: 600px;">
                @php
                    $currentIndex = $stages->search(fn($s) => (string)$s->id === (string)$deal->stage_id);
                @endphp
                @foreach($stages as $index => $stg)
                @php
                    $isPast = $index < $currentIndex;
                    $isCurrent = $index === $currentIndex;
                @endphp
                <form action="{{ route('deals.stage', $deal->id) }}" method="POST" class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="stage_id" value="{{ $stg->id }}">
                    <button type="submit" class="btn w-100 text-truncate {{ $isCurrent ? 'btn-primary shadow-sm' : ($isPast ? 'btn-light border-primary text-primary' : 'btn-light text-muted') }}" style="font-size: 13px; font-weight: {{ $isCurrent ? '700' : '500' }};">
                        @if($isPast)<i class="fas fa-check-circle me-1"></i>@endif
                        {{ $stg->name }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Metrics & Summary -->
    <div class="col-lg-4">
        <!-- Deal Financial Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-dollar-sign text-success me-2"></i> Deal Economics</h5>
            </div>
            <div class="card-body pt-0">
                <div class="p-3 bg-light rounded text-center mb-3">
                    <span class="text-muted small text-uppercase fw-semibold">Contract Value</span>
                    <h2 class="fw-bold text-success mb-0">${{ number_format($deal->value, 2) }}</h2>
                </div>

                <ul class="list-unstyled mb-0">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Win Probability</span>
                        <span class="fw-bold text-dark">{{ $deal->probability ?? 0 }}%</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Weighted Value</span>
                        <span class="fw-semibold text-dark">${{ number_format($deal->value * (($deal->probability ?? 0) / 100), 2) }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Target Close Date</span>
                        <span class="fw-semibold text-dark">{{ $deal->expected_close_date ? $deal->expected_close_date->format('M d, Y') : '—' }}</span>
                    </li>
                    @if($deal->actual_close_date)
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Actual Close Date</span>
                        <span class="fw-semibold text-dark">{{ $deal->actual_close_date->format('M d, Y') }}</span>
                    </li>
                    @endif
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Deal Owner</span>
                        <span class="badge bg-primary-subtle text-primary">{{ $assignedUser?->name ?? 'Unassigned' }}</span>
                    </li>
                </ul>

                @if($deal->lost_reason)
                <div class="alert alert-danger mt-3 mb-0">
                    <strong class="d-block small text-uppercase">Reason for Loss:</strong>
                    <span class="small">{{ $deal->lost_reason }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Summary Card -->
        @if($customer)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-building text-primary me-2"></i> Client Overview</h5>
                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-light">View 360°</a>
            </div>
            <div class="card-body pt-0">
                <h6 class="fw-bold mb-1">{{ $customer->name }}</h6>
                <div class="small text-muted mb-2">{{ $customer->company ?? 'No company' }}</div>
                <div class="small text-muted mb-1"><i class="far fa-envelope me-1"></i>{{ $customer->email ?? '—' }}</div>
                <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $customer->phone ?? '—' }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Quotations & Activities -->
    <div class="col-lg-8">
        <!-- Quotations Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-info me-2"></i> Associated Quotations ({{ $quotations->count() }})</h5>
                <a href="{{ route('quotations.create', ['customer_id' => $deal->customer_id, 'deal_id' => $deal->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus me-1"></i> Create Quote
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">Quotation #</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Valid Until</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $quote)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('quotations.show', $quote->id) }}" class="fw-semibold text-dark text-decoration-none">
                                        {{ $quote->number }}
                                    </a>
                                </td>
                                <td class="fw-bold text-dark">${{ number_format($quote->total, 2) }}</td>
                                <td>
                                    <span class="badge {{ $quote->status === 'accepted' ? 'bg-success' : 'bg-light text-dark border' }} text-capitalize">
                                        {{ $quote->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : '—' }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('quotations.show', $quote->id) }}" class="btn btn-sm btn-light"><i class="far fa-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    No quotes sent for this opportunity yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activities Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-secondary me-2"></i> Opportunity Timeline</h5>
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
                    <div class="text-center py-4 text-muted small">No activity logged for this deal yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Mark as Lost -->
<div class="modal fade" id="markLostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('deals.lost', $deal->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i> Mark Deal as Lost</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Loss <span class="text-danger">*</span></label>
                        <textarea name="lost_reason" class="form-control" rows="4" required placeholder="e.g. Budget cuts, chosen competitor (X), timeline deferred to next year..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Lost</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
