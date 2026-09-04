@extends('layouts.app')

@section('title', $ticket->ticket_number . ' — ' . $ticket->title)
@section('page-title', 'Support Case')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
    <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Main Left: Conversation Thread & Reply -->
    <div class="col-lg-8">
        <!-- Ticket Header Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                    <div>
                        <span class="badge bg-light text-muted border text-uppercase me-2">{{ $ticket->ticket_number }}</span>
                        <span class="badge bg-primary text-capitalize">{{ str_replace('_', ' ', $ticket->category) }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('tickets.edit', $ticket->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit Ticket</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Delete ticket?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="far fa-trash-alt me-2"></i> Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ $ticket->title }}</h4>
                <p class="text-muted small mb-0">
                    Opened by <strong class="text-dark">{{ $creator?->name ?? 'User' }}</strong> &bull;
                    {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '' }}
                </p>
            </div>
        </div>

        <!-- Thread Messages Stream -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-comments text-primary me-2"></i> Discussion History ({{ $messages->count() }})</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    @forelse($messages as $msg)
                    @php
                        $sender = $senders->get((string) $msg->sender_id);
                        $isAgent = in_array($msg->sender_role, ['support_agent', 'admin', 'manager']);
                    @endphp
                    <div class="d-flex {{ $isAgent ? 'flex-row' : 'flex-row' }} gap-3">
                        <div class="avatar-circle rounded-circle text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0 {{ $isAgent ? 'bg-primary' : 'bg-secondary' }}" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($sender?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 p-3 rounded {{ $isAgent ? 'bg-light border-start border-4 border-primary' : 'bg-white border' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="text-dark">{{ $sender?->name ?? 'User' }}</strong>
                                    <span class="badge {{ $isAgent ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1" style="font-size: 11px;">
                                        {{ $isAgent ? 'Support Team' : 'Client' }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $msg->created_at ? $msg->created_at->format('M d, h:i A') : '' }}</small>
                            </div>
                            <div class="text-dark" style="white-space: pre-wrap; font-size: 14px;">{{ $msg->message }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No messages in this case yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Post Reply Box -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-reply text-primary me-2"></i> Post Reply</h6>
                <form action="{{ route('tickets.messages.store', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="4" placeholder="Write your response here..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Customer and assignee will receive immediate notification.
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fas fa-paper-plane me-1"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Ticket Metadata & Assignment -->
    <div class="col-lg-4">
        <!-- Status & Actions Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-sliders-h text-primary me-2"></i> Case Controls</h6>
            </div>
            <div class="card-body pt-0">
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Case Status</label>
                    <form action="{{ route('tickets.status', $ticket->id) }}" method="POST">
                        @csrf
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </form>
                </div>

                @if(!auth()->user()->isCustomer())
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Assigned Agent</label>
                    <form action="{{ route('tickets.assign', $ticket->id) }}" method="POST">
                        @csrf
                        <select name="assigned_to" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Unassigned</option>
                            @foreach($agents as $ag)
                                <option value="{{ $ag->id }}" {{ $ticket->assigned_to == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif

                <ul class="list-unstyled mb-0 pt-2 border-top">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">Priority:</span>
                        @php
                            $priorityColors = [
                                'critical' => 'bg-danger text-white',
                                'high'     => 'bg-danger-subtle text-danger',
                                'medium'   => 'bg-warning-subtle text-warning-emphasis',
                                'low'      => 'bg-info-subtle text-info',
                            ];
                        @endphp
                        <span class="badge {{ $priorityColors[$ticket->priority] ?? 'bg-light text-dark' }} text-capitalize">
                            {{ $ticket->priority }}
                        </span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">Category:</span>
                        <span class="text-capitalize small fw-semibold text-dark">{{ str_replace('_', ' ', $ticket->category) }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted small">First Response:</span>
                        <span class="small text-dark">{{ $ticket->first_response_at ? $ticket->first_response_at->diffForHumans() : 'Pending' }}</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span class="text-muted small">Resolution SLA:</span>
                        <span class="small text-dark">{{ $ticket->due_date ? $ticket->due_date->format('M d, Y') : 'Standard SLA' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Customer Summary -->
        @if($customer)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-building text-primary me-2"></i> Client Details</h6>
                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-light">View 360°</a>
            </div>
            <div class="card-body pt-0">
                <div class="fw-bold text-dark">{{ $customer->name }}</div>
                @if($customer->company)<div class="small text-muted">{{ $customer->company }}</div>@endif
                <div class="small text-muted mt-2"><i class="far fa-envelope me-1"></i>{{ $customer->email ?? '—' }}</div>
                <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $customer->phone ?? '—' }}</div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
