@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'My Sales Dashboard')
@section('breadcrumb') <li class="breadcrumb-item active">Dashboard</li> @endsection
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>My Sales Cockpit — {{ auth()->user()->name }} 🎯</h1>
        <p class="text-muted mb-0">Personal Deal Pipeline &bull; Daily Conversion Velocity &bull; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>New Lead</a>
        <a href="{{ route('deals.create') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-handshake me-1"></i>New Deal</a>
    </div>
</div>

<!-- ── Signature 4-Color KPI Row with Sparklines ── -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('leads.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-magenta has-sparkline">
                <div class="kpi-value">{{ $stats['my_leads'] }}</div>
                <div class="kpi-label">My Assigned Leads</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['my_open_leads'] }} open pipeline</div>
                <div class="kpi-sparkline"><canvas id="sparklineExecLeads"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-funnel-dollar"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('deals.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-amber has-sparkline">
                <div class="kpi-value">{{ $stats['my_won_deals'] }}</div>
                <div class="kpi-label">Deals Closed Won</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['my_deals'] }} total deals</div>
                <div class="kpi-sparkline"><canvas id="sparklineExecWon"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('deals.pipeline') }}" class="text-decoration-none">
            <div class="kpi-card kpi-navy has-sparkline">
                <div class="kpi-value">${{ number_format($stats['pipeline_value'], 0) }}</div>
                <div class="kpi-label">My Pipeline Value</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['my_open_deals'] }} active deals</div>
                <div class="kpi-sparkline"><canvas id="sparklineExecPipeline"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tasks.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-blue has-sparkline">
                <div class="kpi-value">{{ $stats['my_tasks'] }}</div>
                <div class="kpi-label">Tasks Pending</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['overdue_tasks'] }} overdue</div>
                <div class="kpi-sparkline"><canvas id="sparklineExecTasks"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-tasks"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- ── Charts Row 1: Personal Sales Trend & Pipeline Stages ── -->
<div class="row g-4 mb-4">
    <!-- My Monthly Closed Sales Velocity (Line / Area Chart) -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    <span class="fw-semibold">My Monthly Closed Revenue Velocity (6 Months)</span>
                </div>
                <span class="badge bg-light text-primary border">Closed Won</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="mySalesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- My Deals by Stage (Doughnut Chart) -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-layer-group text-warning me-2"></i>
                <span class="fw-semibold">My Deals by Stage</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; max-height: 200px; width: 100%;">
                    <canvas id="myPipelineChart"></canvas>
                </div>
                <div class="mt-3 w-100" id="myPipelineLegend"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Lead Status Portfolio & Tasks Due Today ── -->
<div class="row g-4 mb-4">
    <!-- My Lead Status Distribution (Bar Chart) -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-funnel-dollar text-success me-2"></i>
                <span class="fw-semibold">My Lead Pipeline Status Breakdown</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="myLeadStatusBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Due Today -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header justify-content-between">
                <span><i class="fas fa-calendar-check me-2 text-warning"></i>Tasks Due Today ({{ count($stats['tasks_today']) }})</span>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm py-0 px-2 small">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($stats['tasks_today'] as $task)
                <div class="activity-item px-4">
                    <div class="activity-icon" style="background: {{ match($task->type) { 'call'=>'#10b981','meeting'=>'#4f46e5','email'=>'#0ea5e9',default=>'#f59e0b' } }}">
                        <i class="fas fa-{{ match($task->type) { 'call'=>'phone','meeting'=>'calendar','email'=>'envelope',default=>'tasks' } }}"></i>
                    </div>
                    <div class="activity-body">
                        <p class="activity-text fw-semibold mb-0">{{ $task->title }}</p>
                        <span class="activity-meta">{{ ucfirst($task->type) }} &middot; Due {{ $task->due_date?->format('H:i') }}</span>
                    </div>
                    <form method="POST" action="{{ route('tasks.complete', $task->id) }}" class="ms-auto">
                        @csrf <button type="submit" class="btn btn-sm btn-outline-success" title="Mark Complete"><i class="fas fa-check"></i></button>
                    </form>
                </div>
                @empty
                <div class="empty-state py-4 text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2 opacity-50"></i>
                    <p class="text-muted mb-0">No pending tasks due today. All caught up!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ── Quick Actions Row ── -->
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-link text-primary me-2"></i>
                <span class="fw-semibold">Executive Shortcuts</span>
            </div>
            <div class="card-body p-2">
                <div class="row g-2">
                    <div class="col-6 col-md-3"><a href="{{ route('leads.create') }}" class="btn btn-light w-100 text-start border"><i class="fas fa-plus me-2 text-primary"></i>New Lead</a></div>
                    <div class="col-6 col-md-3"><a href="{{ route('deals.pipeline') }}" class="btn btn-light w-100 text-start border"><i class="fas fa-layer-group me-2 text-warning"></i>My Kanban</a></div>
                    <div class="col-6 col-md-3"><a href="{{ route('tasks.create') }}" class="btn btn-light w-100 text-start border"><i class="fas fa-tasks me-2 text-info"></i>Add Task</a></div>
                    <div class="col-6 col-md-3"><a href="{{ route('quotations.create') }}" class="btn btn-light w-100 text-start border"><i class="fas fa-file-invoice me-2 text-success"></i>New Quotation</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 1. My Monthly Sales Velocity Line Chart
const mySalesCtx = document.getElementById('mySalesTrendChart').getContext('2d');
const mySalesGradient = mySalesCtx.createLinearGradient(0, 0, 0, 260);
mySalesGradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
mySalesGradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

new Chart(mySalesCtx, {
    type: 'line',
    data: {
        labels: @json($stats['monthly_sales_data']['labels']),
        datasets: [{
            label: 'Closed Value ($)',
            data: @json($stats['monthly_sales_data']['revenue']),
            borderColor: '#10b981',
            backgroundColor: mySalesGradient,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: context => ` Closed Value: $${Number(context.raw).toLocaleString()}`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { callback: v => '$' + Number(v).toLocaleString() }
            },
            x: { grid: { display: false } }
        }
    }
});

// 2. My Deals by Stage Doughnut Chart
const myPipeData = @json($stats['my_pipeline_data']);
const myPipeColors = ['#6366f1','#8b5cf6','#0ea5e9','#f59e0b','#10b981','#ef4444'];
const myPipeCtx = document.getElementById('myPipelineChart').getContext('2d');

new Chart(myPipeCtx, {
    type: 'doughnut',
    data: {
        labels: myPipeData.labels,
        datasets: [{
            data: myPipeData.counts,
            backgroundColor: myPipeColors,
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

const myPipeLegend = document.getElementById('myPipelineLegend');
myPipeData.labels.forEach((label, i) => {
    myPipeLegend.innerHTML += `<div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            <span style="width:10px;height:10px;border-radius:50%;background:${myPipeColors[i]};display:inline-block"></span>
            <span style="font-size:12px">${label}</span>
        </div>
        <span style="font-size:12px;font-weight:600">${myPipeData.counts[i]}</span>
    </div>`;
});

// 3. My Lead Pipeline Status Breakdown (Bar Chart)
const myLeadData = @json($stats['my_lead_status_data']);
const myLeadCtx = document.getElementById('myLeadStatusBarChart').getContext('2d');
const myLeadColors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444'];

new Chart(myLeadCtx, {
    type: 'bar',
    data: {
        labels: myLeadData.labels,
        datasets: [{
            label: 'Leads',
            data: myLeadData.counts,
            backgroundColor: myLeadColors,
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

// 4. Sales Executive KPI Sparklines
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

initKpiSparkline('sparklineExecLeads',    [1, 2, 2, 3, 2, Math.max(1, {{ $stats['my_leads'] }})]);
initKpiSparkline('sparklineExecWon',      [0, 1, 1, 2, 1, Math.max(1, {{ $stats['my_won_deals'] }})]);
initKpiSparkline('sparklineExecPipeline', [12000, 22000, 35000, 48000, Math.max(10000, {{ $stats['pipeline_value'] }})]);
initKpiSparkline('sparklineExecTasks',    [3, 2, 2, 1, 1, Math.max(0, {{ $stats['my_tasks'] }})]);
</script>
@endpush
