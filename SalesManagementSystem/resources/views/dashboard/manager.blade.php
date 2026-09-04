@extends('layouts.app')
@section('title', 'Manager Dashboard')
@section('page-title', 'Manager Dashboard')
@section('breadcrumb') <li class="breadcrumb-item active">Dashboard</li> @endsection
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Sales Leadership Cockpit — {{ auth()->user()->name }} 📊</h1>
        <p class="text-muted mb-0">Pipeline Velocity &bull; Team Quota Realization &bull; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('deals.pipeline') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-layer-group me-1"></i>Deals Pipeline</a>
        <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-sm"><i class="fas fa-chart-line me-1"></i>Sales Reports</a>
    </div>
</div>

<!-- ── Signature 4-Color KPI Row with Sparklines ── -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('leads.index') }}?status=new" class="text-decoration-none">
            <div class="kpi-card kpi-magenta has-sparkline">
                <div class="kpi-value">{{ $stats['open_leads'] }}</div>
                <div class="kpi-label">Open Leads</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['total_leads'] }} total in pipeline</div>
                <div class="kpi-sparkline"><canvas id="sparklineMgrLeads"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-funnel-dollar"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('deals.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-amber has-sparkline">
                <div class="kpi-value">{{ $stats['won_deals'] }}</div>
                <div class="kpi-label">Won Deals</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Closed won deals</div>
                <div class="kpi-sparkline"><canvas id="sparklineMgrWon"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('deals.pipeline') }}" class="text-decoration-none">
            <div class="kpi-card kpi-navy has-sparkline">
                <div class="kpi-value">${{ number_format($stats['pipeline_value'], 0) }}</div>
                <div class="kpi-label">Pipeline Value</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['open_deals'] }} active deals</div>
                <div class="kpi-sparkline"><canvas id="sparklineMgrPipeline"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tasks.index') }}?status=overdue" class="text-decoration-none">
            <div class="kpi-card kpi-blue has-sparkline">
                <div class="kpi-value">{{ $stats['overdue_follow_ups'] }}</div>
                <div class="kpi-label">Overdue Follow-ups</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Review overdue items</div>
                <div class="kpi-sparkline"><canvas id="sparklineMgrOverdue"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- ── Charts Row 1: Revenue Velocity & Lead Health ── -->
<div class="row g-4 mb-4">
    <!-- Revenue Trend Line Chart -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    <span class="fw-semibold">Monthly Revenue Velocity (6 Months)</span>
                </div>
                <span class="badge bg-light text-primary border">Paid Collections</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Status Doughnut Chart -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-funnel-dollar text-success me-2"></i>
                <span class="fw-semibold">Lead Pipeline Health</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; max-height: 200px; width: 100%;">
                    <canvas id="leadStatusChart"></canvas>
                </div>
                <div class="mt-3 w-100" id="leadStatusLegend"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Pipeline Value by Stage & Sales Rep Leaderboard ── -->
<div class="row g-4 mb-4">
    <!-- Pipeline Value by Stage (Horizontal Bar) -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar text-warning me-2"></i>
                <span class="fw-semibold">Pipeline Volume & Value by Stage</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="stageBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Rep Performance Leaderboard -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-trophy text-info me-2"></i>
                <span class="fw-semibold">Sales Executive Performance Leaderboard</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="teamPerfChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Quick Actions Row ── -->
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt text-primary me-2"></i>
                <span class="fw-semibold">Manager Quick Actions & Shortcuts</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('leads.index') }}" class="btn btn-light w-100 text-start py-2 border">
                            <i class="fas fa-funnel-dollar me-2 text-primary"></i>Lead Routing & Queue
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('deals.pipeline') }}" class="btn btn-light w-100 text-start py-2 border">
                            <i class="fas fa-layer-group me-2 text-warning"></i>Kanban Supervision
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('reports.sales') }}" class="btn btn-light w-100 text-start py-2 border">
                            <i class="fas fa-chart-bar me-2 text-success"></i>Revenue Analytics
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('reports.performance') }}" class="btn btn-light w-100 text-start py-2 border">
                            <i class="fas fa-users me-2 text-info"></i>Quota Realization
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 1. Revenue Velocity Line Chart
const revCtx = document.getElementById('revenueTrendChart').getContext('2d');
const revGradient = revCtx.createLinearGradient(0, 0, 0, 260);
revGradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
revGradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

new Chart(revCtx, {
    type: 'line',
    data: {
        labels: @json($stats['monthly_revenue_data']['labels']),
        datasets: [{
            label: 'Collected Revenue ($)',
            data: @json($stats['monthly_revenue_data']['data']),
            borderColor: '#4f46e5',
            backgroundColor: revGradient,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4f46e5',
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
                    label: context => ` Collected Revenue: $${Number(context.raw).toLocaleString()}`
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

// 2. Lead Status Doughnut Chart
const leadData = @json($stats['lead_status_data']);
const leadCtx = document.getElementById('leadStatusChart').getContext('2d');

new Chart(leadCtx, {
    type: 'doughnut',
    data: {
        labels: leadData.labels,
        datasets: [{
            data: leadData.data,
            backgroundColor: leadData.colors,
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

const leadLegendEl = document.getElementById('leadStatusLegend');
leadData.labels.forEach((label, i) => {
    leadLegendEl.innerHTML += `<div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            <span style="width:10px;height:10px;border-radius:50%;background:${leadData.colors[i]};display:inline-block"></span>
            <span style="font-size:12px">${label}</span>
        </div>
        <span style="font-size:12px;font-weight:600">${leadData.data[i]}</span>
    </div>`;
});

// 3. Pipeline Value by Stage (Horizontal Bar)
const stageData = @json($stats['pipeline_by_stage']);
const stageCtx = document.getElementById('stageBarChart').getContext('2d');
const stageColors = ['#6366f1','#8b5cf6','#0ea5e9','#f59e0b','#10b981','#ef4444'];

new Chart(stageCtx, {
    type: 'bar',
    data: {
        labels: stageData.labels,
        datasets: [{
            label: 'Stage Value ($)',
            data: stageData.values,
            backgroundColor: stageColors,
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: context => ` Pipeline Value: $${Number(context.raw).toLocaleString()} (${stageData.counts[context.dataIndex]} deals)`
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { callback: v => '$' + Number(v).toLocaleString() }
            },
            y: { grid: { display: false } }
        }
    }
});

// 4. Sales Executive Performance Leaderboard
const teamData = @json($stats['team_performance_data']);
const teamCtx = document.getElementById('teamPerfChart').getContext('2d');

new Chart(teamCtx, {
    type: 'bar',
    data: {
        labels: teamData.labels.length ? teamData.labels : ['No Sales Reps'],
        datasets: [
            {
                label: 'Deals Won',
                data: teamData.deals.length ? teamData.deals : [0],
                backgroundColor: '#10b981',
                borderRadius: 5,
                yAxisID: 'y',
            },
            {
                label: 'Closed Value ($)',
                data: teamData.revenue.length ? teamData.revenue : [0],
                backgroundColor: '#3b82f6',
                borderRadius: 5,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { font: { size: 12, family: 'Inter' } } },
            tooltip: {
                callbacks: {
                    label: context => {
                        if (context.dataset.yAxisID === 'y1') {
                            return ` Closed Value: $${Number(context.raw).toLocaleString()}`;
                        }
                        return ` Deals Won: ${context.raw}`;
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: { stepSize: 1 },
                title: { display: true, text: 'Deals' }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                ticks: { callback: v => '$' + Number(v).toLocaleString() },
                title: { display: true, text: 'Value ($)' }
            },
        }
    }
});

// 5. Manager KPI Sparklines
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

initKpiSparkline('sparklineMgrLeads',    [1, 2, 3, 2, 4, Math.max(1, {{ $stats['open_leads'] }})]);
initKpiSparkline('sparklineMgrWon',      [0, 1, 1, 2, 1, Math.max(1, {{ $stats['won_deals'] }})]);
initKpiSparkline('sparklineMgrPipeline', [18000, 32000, 48000, 62000, Math.max(10000, {{ $stats['pipeline_value'] }})]);
initKpiSparkline('sparklineMgrOverdue',  [4, 3, 2, 2, 1, Math.max(0, {{ $stats['overdue_follow_ups'] }})]);
</script>
@endpush
