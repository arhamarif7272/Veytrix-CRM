@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();

    $navGroups = [
        [
            'label' => 'Main',
            'items' => [
                ['route' => 'dashboard', 'icon' => 'fas fa-th-large', 'label' => 'Dashboard', 'roles' => null],
            ]
        ],
        [
            'label' => 'CRM',
            'items' => [
                ['route' => 'customers.index', 'icon' => 'fas fa-building', 'label' => 'Customers', 'roles' => ['admin','manager','sales_executive']],
                ['route' => 'leads.index',     'icon' => 'fas fa-funnel-dollar', 'label' => 'Leads', 'roles' => ['admin','manager','sales_executive']],
                ['route' => 'deals.pipeline',  'icon' => 'fas fa-layer-group', 'label' => 'Pipeline', 'roles' => ['admin','manager','sales_executive']],
                ['route' => 'deals.index',     'icon' => 'fas fa-handshake', 'label' => 'Deals', 'roles' => ['admin','manager','sales_executive']],
                ['route' => 'tasks.index',     'icon' => 'fas fa-tasks', 'label' => 'Tasks', 'roles' => ['admin','manager','sales_executive','support_agent']],
            ]
        ],
        [
            'label' => 'Finance',
            'items' => [
                ['route' => 'quotations.index', 'icon' => 'fas fa-file-invoice', 'label' => 'Quotations', 'roles' => ['admin','manager','sales_executive']],
                ['route' => 'invoices.index',   'icon' => 'fas fa-receipt', 'label' => 'Invoices', 'roles' => ['admin','manager','sales_executive']],
            ]
        ],
        [
            'label' => 'Support',
            'items' => [
                ['route' => 'tickets.index', 'icon' => 'fas fa-ticket-alt', 'label' => 'Tickets', 'roles' => null],
            ]
        ],
        [
            'label' => 'Customer Portal',
            'items' => [
                ['route' => 'quotations.my', 'icon' => 'fas fa-file-invoice', 'label' => 'My Quotations', 'roles' => ['customer']],
                ['route' => 'invoices.my',   'icon' => 'fas fa-receipt', 'label' => 'My Invoices', 'roles' => ['customer']],
            ]
        ],
        [
            'label' => 'Analytics',
            'items' => [
                ['route' => 'reports.index', 'icon' => 'fas fa-chart-bar', 'label' => 'Reports', 'roles' => ['admin','manager']],
                ['route' => 'activities.index', 'icon' => 'fas fa-history', 'label' => 'Activities', 'roles' => ['admin','manager']],
            ]
        ],
        [
            'label' => 'Administration',
            'items' => [
                ['route' => 'users.index',       'icon' => 'fas fa-users', 'label' => 'Users', 'roles' => ['admin']],
                ['route' => 'departments.index', 'icon' => 'fas fa-sitemap', 'label' => 'Departments', 'roles' => ['admin']],
                ['route' => 'products.index',    'icon' => 'fas fa-box', 'label' => 'Products', 'roles' => ['admin','manager']],
                ['route' => 'audit-logs.index',  'icon' => 'fas fa-shield-alt', 'label' => 'Audit Logs', 'roles' => ['admin']],
                ['route' => 'settings.index',    'icon' => 'fas fa-cog', 'label' => 'Settings', 'roles' => ['admin']],
            ]
        ],
    ];
@endphp

<aside class="crm-sidebar" id="crm-sidebar">
    <!-- Brand Header -->
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none" title="Veytrix — Enterprise Customer Relationship & Workflow Management System">
            <div class="sidebar-logo-circle" style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; background: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <img src="{{ asset('images/logo.png') }}" alt="Veytrix" class="sidebar-logo-img" width="34" height="34" style="width: 34px; height: 34px; max-width: 34px; max-height: 34px; object-fit: contain; border-radius: 50%; display: block;">
            </div>
            <div class="sidebar-brand-text">
                <span class="brand-name">Veytrix</span>
                <span class="brand-tagline">CRM &amp; Workflow</span>
            </div>
        </a>
        <button class="sidebar-toggle-btn d-none d-lg-flex ms-auto" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        @foreach($navGroups as $group)
            @php
                $visibleItems = collect($group['items'])->filter(function($item) use ($user) {
                    return $item['roles'] === null || in_array($user->role, $item['roles']);
                });
            @endphp

            @if($visibleItems->isNotEmpty())
            <div class="nav-group">
                <span class="nav-group-label">{{ $group['label'] }}</span>
                @foreach($visibleItems as $item)
                    @php
                        try {
                            $isActive = request()->routeIs(rtrim($item['route'], '.index') . '*');
                        } catch(\Exception $e) {
                            $isActive = false;
                        }
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-item {{ $isActive ? 'active' : '' }}"
                       title="{{ $item['label'] }}">
                        <i class="{{ $item['icon'] }} nav-icon"></i>
                        <span class="nav-label">{{ $item['label'] }}</span>
                        @if($item['route'] === 'tickets.index')
                            @php $openTickets = \App\Models\Ticket::open()->count() @endphp
                            @if($openTickets > 0)
                            <span class="nav-badge">{{ $openTickets }}</span>
                            @endif
                        @endif
                        <i class="fas fa-chevron-right nav-arrow ms-auto"></i>
                    </a>
                @endforeach
            </div>
            @endif
        @endforeach
    </nav>

    <!-- Sidebar Footer & Copyright -->
    <div class="sidebar-footer">
        <div class="sidebar-watermark text-center mb-2">
            <strong>Veytrix</strong><br>
            <span class="small text-muted">&copy; {{ date('Y') }} All Rights Reserved</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item nav-logout w-100 justify-content-center">
                <i class="fas fa-sign-out-alt nav-icon"></i>
                <span class="nav-label">Logout</span>
            </button>
        </form>
    </div>
</aside>
