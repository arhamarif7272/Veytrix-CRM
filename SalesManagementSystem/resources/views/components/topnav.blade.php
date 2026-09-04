@php
    $user = auth()->user();
    $unreadCount = \App\Services\NotificationService::unreadCount($user->id);
    $recentNotifications = \App\Models\Notification::forUser($user->id)
        ->orderBy('created_at','desc')->limit(5)->get();
@endphp

<header class="crm-topnav" id="crm-topnav">
    <div class="topnav-left">
        <!-- Sidebar Toggle (Mobile & Desktop) -->
        <button class="topnav-menu-toggle d-lg-none" id="mobileSidebarToggle" title="Toggle Menu">
            <i class="fas fa-bars"></i>
        </button>
        <button class="topnav-menu-toggle d-none d-lg-flex" id="sidebarToggleTop" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Search Pill (As shown in screenshot) -->
        <div class="topnav-search-box d-none d-sm-flex">
            <div class="search-input-group">
                <input type="text" class="form-control rounded-pill topnav-search-input" placeholder="Search..." id="globalCrmSearch" autocomplete="off">
                <button class="search-action-btn" type="button" title="Search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Quick Tool Icons (Chat, @, Briefcase) -->
        <div class="topnav-quick-tools d-none d-md-flex align-items-center ms-2">
            <a href="{{ route('tickets.index') }}" class="topnav-tool-btn" title="Customer Discussions & Tickets">
                <i class="far fa-comment-dots"></i>
            </a>
            <a href="mailto:support@crm360.com" class="topnav-tool-btn" title="Direct Email Communication">
                <i class="fas fa-at"></i>
            </a>
            <a href="{{ $user->isStaff() ? route('tasks.index') : route('quotations.my') }}" class="topnav-tool-btn" title="Workflows & Briefcase">
                <i class="fas fa-briefcase"></i>
            </a>
        </div>
    </div>

    <div class="topnav-right">
        <!-- Quick Action Add Button -->
        @if($user->isStaff())
        <div class="dropdown d-none d-sm-block">
            <button class="btn btn-light btn-sm fw-bold px-3 quick-add-btn" data-bs-toggle="dropdown" title="Quick Add">
                <i class="fas fa-plus text-warning me-1"></i>
                <span>New</span>
                <i class="fas fa-chevron-down ms-1 small text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                @if(in_array($user->role, ['admin','manager','sales_executive']))
                <li><a class="dropdown-item" href="{{ route('leads.create') }}"><i class="fas fa-funnel-dollar me-2 text-primary"></i>New Lead</a></li>
                <li><a class="dropdown-item" href="{{ route('customers.create') }}"><i class="fas fa-building me-2 text-success"></i>New Customer</a></li>
                <li><a class="dropdown-item" href="{{ route('deals.create') }}"><i class="fas fa-handshake me-2 text-warning"></i>New Deal</a></li>
                @endif
                <li><a class="dropdown-item" href="{{ route('tickets.create') }}"><i class="fas fa-ticket-alt me-2 text-info"></i>New Ticket</a></li>
                <li><a class="dropdown-item" href="{{ route('tasks.create') }}"><i class="fas fa-tasks me-2 text-secondary"></i>New Task</a></li>
            </ul>
        </div>
        @endif

        <!-- Fullscreen Toggle -->
        <button class="topnav-tool-btn d-none d-md-flex" id="fullscreenToggle" title="Toggle Fullscreen">
            <i class="fas fa-expand"></i>
        </button>

        <!-- Notifications -->
        <div class="dropdown">
            <button class="topnav-tool-btn position-relative" data-bs-toggle="dropdown" id="notificationBtn" title="Notifications">
                <i class="far fa-bell"></i>
                @if($unreadCount > 0)
                <span class="topnav-notif-pill">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end notif-dropdown shadow-lg">
                <div class="notif-header">
                    <span class="fw-bold">Notifications</span>
                    @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm p-0 text-white opacity-75">Mark all read</button>
                    </form>
                    @endif
                </div>
                <div class="notif-list">
                    @forelse($recentNotifications as $notif)
                    <a href="{{ $notif->action_url ?: route('notifications.index') }}"
                       class="notif-item {{ $notif->isUnread() ? 'unread' : '' }}">
                        <div class="notif-dot"></div>
                        <div class="notif-content">
                            <p class="notif-title">{{ $notif->title }}</p>
                            <p class="notif-msg">{{ Str::limit($notif->message, 60) }}</p>
                            <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="notif-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>
                    @endforelse
                </div>
                <div class="notif-footer">
                    <a href="{{ route('notifications.index') }}">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- User Menu Capsule -->
        <div class="dropdown">
            <button class="topnav-user-capsule" data-bs-toggle="dropdown" id="userMenuBtn">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="topnav-avatar-round">
                <span class="topnav-user-name-text d-none d-md-inline">{{ $user->name }}</span>
                <i class="fas fa-chevron-down ms-1 small opacity-75"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end user-menu shadow-lg">
                <li class="user-menu-header">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-menu-avatar">
                    <div>
                        <p class="mb-0 fw-bold">{{ $user->name }}</p>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2 text-muted"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i class="fas fa-bell me-2 text-muted"></i>Notifications @if($unreadCount > 0)<span class="badge bg-danger ms-1">{{ $unreadCount }}</span>@endif</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Settings Cog Icon -->
        <a href="{{ $user->isAdmin() ? route('settings.index') : route('profile.show') }}" class="topnav-tool-btn d-none d-sm-flex" title="Settings">
            <i class="fas fa-cog"></i>
        </a>
    </div>
</header>
