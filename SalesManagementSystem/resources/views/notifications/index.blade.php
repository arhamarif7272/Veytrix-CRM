@extends('layouts.app')

@section('title', 'Notification Center')
@section('page-title', 'Notifications')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Notification Center</h4>
                <p class="text-muted mb-0">Assignments, deal wins, task reminders, and support updates</p>
            </div>
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-check-double me-1"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($notifications as $n)
                    <li class="list-group-item p-3 d-flex justify-content-between align-items-center {{ $n->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark mb-1">{{ $n->title }}</div>
                                <p class="small text-muted mb-1">{{ $n->message }}</p>
                                <small class="text-muted">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($n->action_url)
                                <a href="{{ $n->action_url }}" class="btn btn-sm btn-outline-primary">View</a>
                            @endif
                            @if(!$n->read_at)
                                <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light" title="Mark as read"><i class="fas fa-check"></i></button>
                                </form>
                            @endif
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-5 text-muted border-0">
                        <i class="fas fa-bell-slash fa-3x mb-3 text-secondary opacity-50"></i>
                        <h5>No notifications</h5>
                        <p class="small mb-0">You're all caught up with your latest updates</p>
                    </li>
                    @endforelse
                </ul>
            </div>
            @if($notifications->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
