@extends('layouts.app')
@section('title', 'Support Dashboard')
@section('page-title', 'Support Dashboard')
@section('breadcrumb') <li class="breadcrumb-item active">Dashboard</li> @endsection
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Support Center & SLA Cockpit 🎧</h1>
        <p class="text-muted mb-0">Ticket Inflow &bull; Response Time Targets &bull; Resolution Analytics &bull; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>New Ticket</a>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-inbox me-1"></i>All Tickets</a>
    </div>
</div>

<!-- ── Signature 4-Color KPI Row with Sparklines ── -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tickets.index') }}?status=open" class="text-decoration-none">
            <div class="kpi-card kpi-magenta has-sparkline">
                <div class="kpi-value">{{ $stats['open_tickets'] }}</div>
                <div class="kpi-label">Open Tickets</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Active support queue</div>
                <div class="kpi-sparkline"><canvas id="sparklineSupOpen"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-ticket-alt"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tickets.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-amber has-sparkline">
                <div class="kpi-value">{{ $stats['assigned_to_me'] }}</div>
                <div class="kpi-label">Assigned to Me</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> My active queue</div>
                <div class="kpi-sparkline"><canvas id="sparklineSupMine"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-user-check"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tickets.index') }}?priority=high" class="text-decoration-none">
            <div class="kpi-card kpi-navy has-sparkline">
                <div class="kpi-value">{{ $stats['high_priority'] }}</div>
                <div class="kpi-label">High Priority</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Urgent response required</div>
                <div class="kpi-sparkline"><canvas id="sparklineSupHigh"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tickets.index') }}?status=resolved" class="text-decoration-none">
            <div class="kpi-card kpi-blue has-sparkline">
                <div class="kpi-value">{{ $stats['resolved_today'] }}</div>
                <div class="kpi-label">Resolved Today</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Customer satisfaction</div>
                <div class="kpi-sparkline"><canvas id="sparklineSupResolved"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- ── Charts Row 1: Intake vs Resolution Velocity & Priority Distribution ── -->
<div class="row g-4 mb-4">
    <!-- Intake vs Resolution Velocity (Line Chart) -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    <span class="fw-semibold">Ticket Volume & Resolution Velocity (6 Months)</span>
                </div>
                <span class="badge bg-light text-success border">SLA Tracking</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="ticketVelocityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets by Priority (Doughnut Chart) -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-shield-alt text-danger me-2"></i>
                <span class="fw-semibold">Support Queue by Priority</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; max-height: 190px; width: 100%;">
                    <canvas id="ticketPriorityChart"></canvas>
                </div>
                <div class="mt-3 w-100" id="ticketPriorityLegend"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Status Breakdown & Recent Tickets Table ── -->
<div class="row g-4">
    <!-- Ticket Status Breakdown (Bar Chart) -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar text-warning me-2"></i>
                <span class="fw-semibold">Tickets by Lifecycle Status</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="ticketStatusBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Open Tickets Table -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header justify-content-between">
                <span><i class="fas fa-inbox me-2 text-info"></i>Active Open Tickets</span>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary btn-sm py-0 px-2 small">View Queue</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_tickets'] as $ticket)
                        <tr>
                            <td><span class="text-muted small fw-semibold">{{ $ticket->ticket_number }}</span></td>
                            <td><a href="{{ route('tickets.show', $ticket->id) }}" class="fw-semibold text-decoration-none text-dark">{{ Str::limit($ticket->title, 36) }}</a></td>
                            <td><span class="badge-status badge-{{ $ticket->priority }}"><span class="priority-dot {{ $ticket->priority }}"></span>{{ ucfirst($ticket->priority) }}</span></td>
                            <td><span class="badge-status badge-{{ $ticket->status }}">{{ str_replace('_',' ',ucfirst($ticket->status)) }}</span></td>
                            <td class="text-muted small">{{ $ticket->created_at?->diffForHumans() }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-light border" title="Open Ticket"><i class="fas fa-arrow-right"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No open tickets 🎉 All resolved!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 1. Ticket Volume & Resolution Velocity (Dual-Line Chart)
const trendData = @json($stats['ticket_trend_data']);
const velocityCtx = document.getElementById('ticketVelocityChart').getContext('2d');

new Chart(velocityCtx, {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: [
            {
                label: 'Tickets Created',
                data: trendData.created,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                pointRadius: 5,
                pointBackgroundColor: '#ef4444',
                fill: true,
            },
            {
                label: 'Tickets Resolved',
                data: trendData.resolved,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                pointRadius: 5,
                pointBackgroundColor: '#10b981',
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { font: { size: 12, family: 'Inter' } } }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { stepSize: 1 }
            },
            x: { grid: { display: false } }
        }
    }
});

// 2. Tickets by Priority Doughnut Chart
const prioData = @json($stats['ticket_priority_data']);
const prioCtx = document.getElementById('ticketPriorityChart').getContext('2d');

new Chart(prioCtx, {
    type: 'doughnut',
    data: {
        labels: prioData.labels,
        datasets: [{
            data: prioData.data,
            backgroundColor: prioData.colors,
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '68%',
    }
});

const prioLegendEl = document.getElementById('ticketPriorityLegend');
prioData.labels.forEach((label, i) => {
    prioLegendEl.innerHTML += `<div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            <span style="width:10px;height:10px;border-radius:50%;background:${prioData.colors[i]};display:inline-block"></span>
            <span style="font-size:12px">${label}</span>
        </div>
        <span style="font-size:12px;font-weight:600">${prioData.data[i]}</span>
    </div>`;
});

// 3. Tickets by Lifecycle Status Bar Chart
const statusData = @json($stats['ticket_status_data']);
const statusCtx = document.getElementById('ticketStatusBarChart').getContext('2d');

new Chart(statusCtx, {
    type: 'bar',
    data: {
        labels: statusData.labels,
        datasets: [{
            label: 'Tickets',
            data: statusData.data,
            backgroundColor: statusData.colors,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { stepSize: 1 }
            },
            x: { grid: { display: false } }
        }
    }
});

// 4. Support Agent KPI Sparklines
function initKpiSparkline(canvasId, dataPoints) {
    const el = document.getElementById(canvasId);
    if (!el) return;
    const ctx = el.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 38);
    gradient.addColorStop(0, 'rgba(255, 255, 255, 0.45)');
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0.04)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dataPoints.map((_, i) => i + 1),
            datasets: [{
                data: dataPoints,
                borderColor: 'rgba(255, 255, 255, 0.95)',
                borderWidth: 2,
                backgroundColor: gradient,
                fill: true,
                tension: 0.45,
                pointRadius: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } }
        }
    });
}

initKpiSparkline('sparklineSupOpen',     [1, 2, 1, 3, 2, Math.max(1, {{ $stats['open_tickets'] }})]);
initKpiSparkline('sparklineSupMine',     [0, 1, 1, 2, 1, Math.max(0, {{ $stats['assigned_to_me'] }})]);
initKpiSparkline('sparklineSupHigh',     [1, 1, 2, 1, 2, Math.max(1, {{ $stats['high_priority'] }})]);
initKpiSparkline('sparklineSupResolved', [1, 2, 2, 3, 2, Math.max(0, {{ $stats['resolved_today'] }})]);
</script>
@endpush
