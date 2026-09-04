@extends('layouts.app')

@section('title', $customer->name . ' — 360° Profile')
@section('page-title', 'Customer 360°')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
    <li class="breadcrumb-item active">{{ $customer->name }}</li>
@endsection

@section('content')
<!-- Header Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center">
                <div class="avatar-circle me-3 bg-primary text-white fw-bold fs-3" style="width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="fw-bold mb-1 text-dark">{{ $customer->name }}</h3>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @if($customer->company)
                            <span class="text-muted"><i class="far fa-building me-1"></i>{{ $customer->company }}</span>
                            <span class="text-muted">•</span>
                        @endif
                        @php
                            $statusClasses = [
                                'active'   => 'bg-success text-white',
                                'prospect' => 'bg-info text-dark',
                                'inactive' => 'bg-secondary text-white',
                                'churned'  => 'bg-danger text-white',
                            ];
                        @endphp
                        <span class="badge {{ $statusClasses[$customer->status] ?? 'bg-light text-dark' }} px-2 py-1 text-capitalize">
                            {{ $customer->status }}
                        </span>
                        @if($customer->industry)
                            <span class="badge bg-light text-secondary border px-2 py-1">{{ $customer->industry }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#logActivityModal">
                    <i class="fas fa-history me-1"></i> Log Activity
                </button>
                <a href="{{ route('deals.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-success">
                    <i class="fas fa-handshake me-1"></i> New Deal
                </a>
                <a href="{{ route('quotations.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-info">
                    <i class="fas fa-file-invoice me-1"></i> New Quote
                </a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Profile
                </a>
            </div>
        </div>

        <!-- 360 Mini Metrics Strip -->
        <div class="row g-3 mt-4 pt-3 border-top">
            <div class="col-6 col-md-3">
                <div class="text-muted small">Total Deals Pipeline</div>
                <div class="fw-bold fs-5 text-dark">${{ number_format($deals->sum('value'), 2) }}</div>
                <small class="text-muted">{{ $deals->count() }} total deals</small>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">Revenue Billed</div>
                <div class="fw-bold fs-5 text-success">${{ number_format($invoices->sum('amount_paid'), 2) }}</div>
                <small class="text-muted">{{ $invoices->where('status', 'paid')->count() }} paid invoices</small>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">Outstanding Balance</div>
                <div class="fw-bold fs-5 text-danger">${{ number_format($invoices->sum('amount_due'), 2) }}</div>
                <small class="text-muted">{{ $invoices->where('amount_due', '>', 0)->count() }} unpaid invoices</small>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-muted small">Open Support Cases</div>
                <div class="fw-bold fs-5 text-warning">{{ $tickets->where('status', 'open')->count() }}</div>
                <small class="text-muted">{{ $tickets->count() }} total tickets</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Details & Contacts -->
    <div class="col-lg-4">
        <!-- Account Overview Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Account Details</h5>
            </div>
            <div class="card-body pt-0">
                <ul class="list-unstyled mb-0">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Email</span>
                        <span class="fw-semibold text-dark">{{ $customer->email ?? '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Phone</span>
                        <span class="fw-semibold text-dark">{{ $customer->phone ?? '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Website</span>
                        @if($customer->website)
                            <a href="{{ $customer->website }}" target="_blank" class="fw-semibold text-primary text-truncate" style="max-width: 180px;">{{ $customer->website }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Location</span>
                        <span class="fw-semibold text-dark text-end">{{ $customer->city ? $customer->city . ', ' : '' }}{{ $customer->country ?? '—' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Account Manager</span>
                        <span class="badge bg-primary-subtle text-primary">{{ $assignedUser?->name ?? 'Unassigned' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Annual Revenue</span>
                        <span class="fw-semibold text-dark">{{ $customer->annual_revenue ? '$' . number_format($customer->annual_revenue) : '—' }}</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Customer Since</span>
                        <span class="fw-semibold text-dark">{{ $customer->created_at ? $customer->created_at->format('M d, Y') : '—' }}</span>
                    </li>
                </ul>

                @if($customer->notes)
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Internal Notes</div>
                    <p class="small mb-0 text-dark">{{ $customer->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Contacts Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-address-book text-primary me-2"></i> Key Contacts</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
            <div class="card-body pt-0">
                @forelse($contacts as $contact)
                <div class="p-3 mb-2 bg-light rounded d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold text-dark">
                            {{ $contact->name }}
                            @if($contact->is_primary)
                                <span class="badge bg-primary text-white ms-1" style="font-size: 10px;">Primary</span>
                            @endif
                        </div>
                        <div class="small text-muted">{{ $contact->position ?? 'Contact Person' }}{{ $contact->department ? ' • ' . $contact->department : '' }}</div>
                        @if($contact->email)<div class="small text-muted"><i class="far fa-envelope me-1"></i>{{ $contact->email }}</div>@endif
                        @if($contact->phone)<div class="small text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $contact->phone }}</div>@endif
                    </div>
                    <form action="{{ route('customers.contacts.destroy', [$customer->id, $contact->id]) }}" method="POST" onsubmit="return confirm('Remove contact?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm text-danger p-0"><i class="far fa-trash-alt"></i></button>
                    </form>
                </div>
                @empty
                <div class="text-center py-3 text-muted small">
                    No contacts listed. Click "Add" to record individuals.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: 360 Tabs -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <ul class="nav nav-tabs border-bottom-0" id="customerTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#dealsTab">
                            <i class="fas fa-handshake me-1 text-primary"></i> Deals ({{ $deals->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#quotationsTab">
                            <i class="fas fa-file-invoice me-1 text-info"></i> Quotations ({{ $quotations->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#invoicesTab">
                            <i class="fas fa-receipt me-1 text-success"></i> Invoices ({{ $invoices->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#ticketsTab">
                            <i class="fas fa-headset me-1 text-warning"></i> Tickets ({{ $tickets->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#activitiesTab">
                            <i class="fas fa-stream me-1 text-secondary"></i> Timeline
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="customerTabContent">
                    <!-- DEALS TAB -->
                    <div class="tab-pane fade show active" id="dealsTab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Deal Title</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th>Expected Close</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deals as $deal)
                                    <tr>
                                        <td>
                                            <a href="{{ route('deals.show', $deal->id) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $deal->title }}
                                            </a>
                                        </td>
                                        <td class="fw-bold text-dark">${{ number_format($deal->value, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $deal->status === 'won' ? 'bg-success' : ($deal->status === 'lost' ? 'bg-danger' : 'bg-primary') }} text-capitalize">
                                                {{ $deal->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $deal->expected_close_date ? $deal->expected_close_date->format('M d, Y') : '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('deals.show', $deal->id) }}" class="btn btn-sm btn-light"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No deals recorded for this customer.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- QUOTATIONS TAB -->
                    <div class="tab-pane fade" id="quotationsTab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Quote #</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Valid Until</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quotations as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('quotations.show', $quote->id) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $quote->number }}
                                            </a>
                                        </td>
                                        <td class="fw-bold text-dark">${{ number_format($quote->total, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $quote->status === 'accepted' ? 'bg-success' : ($quote->status === 'draft' ? 'bg-secondary' : 'bg-info') }} text-capitalize">
                                                {{ $quote->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('quotations.show', $quote->id) }}" class="btn btn-sm btn-light"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No quotations generated yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- INVOICES TAB -->
                    <div class="tab-pane fade" id="invoicesTab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Total</th>
                                        <th>Paid / Due</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $inv)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoices.show', $inv->id) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $inv->number }}
                                            </a>
                                        </td>
                                        <td class="fw-bold">${{ number_format($inv->total, 2) }}</td>
                                        <td class="small">
                                            <span class="text-success">${{ number_format($inv->amount_paid, 2) }}</span> /
                                            <span class="text-danger">${{ number_format($inv->amount_due, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $inv->status === 'paid' ? 'bg-success' : ($inv->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }} text-capitalize">
                                                {{ $inv->status }}
                                            </span>
                                        </td>
                                        <td class="small text-muted">{{ $inv->due_date ? $inv->due_date->format('M d, Y') : '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-light"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No invoices found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TICKETS TAB -->
                    <div class="tab-pane fade" id="ticketsTab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $t)
                                    <tr>
                                        <td class="fw-semibold">{{ $t->ticket_number }}</td>
                                        <td>
                                            <a href="{{ route('tickets.show', $t->id) }}" class="text-dark text-decoration-none fw-medium">
                                                {{ $t->title }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge {{ $t->priority === 'critical' || $t->priority === 'high' ? 'bg-danger' : 'bg-secondary' }} text-capitalize">
                                                {{ $t->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-capitalize">{{ $t->status }}</span>
                                        </td>
                                        <td class="small text-muted">{{ $t->created_at ? $t->created_at->format('M d, Y') : '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('tickets.show', $t->id) }}" class="btn btn-sm btn-light"><i class="far fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No support tickets recorded.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TIMELINE TAB -->
                    <div class="tab-pane fade" id="activitiesTab">
                        <div class="timeline p-2">
                            @forelse($activities as $act)
                            <div class="d-flex mb-3">
                                <div class="timeline-icon me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
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
                            <div class="text-center py-4 text-muted">No activity history logged yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Contact -->
<div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customers.contacts.store', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Contact Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Jane Smith">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="jane@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+1 555 1234">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Position / Role</label>
                            <input type="text" name="position" class="form-control" placeholder="VP Operations">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" name="department" class="form-control" placeholder="Procurement">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="isPrimaryCheck">
                        <label class="form-check-label fw-medium" for="isPrimaryCheck">
                            Set as primary account contact
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Log Activity -->
<div class="modal fade" id="logActivityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customers.activity', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Log Customer Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Activity Type</label>
                        <select name="type" class="form-select" required>
                            <option value="call">Phone Call</option>
                            <option value="meeting">In-person / Virtual Meeting</option>
                            <option value="email">Email Sent / Received</option>
                            <option value="note">Internal Note</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject / Title <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" required placeholder="e.g. Q3 Roadmap Review Call">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Meeting Notes <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Key takeaways, action items, next steps..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
