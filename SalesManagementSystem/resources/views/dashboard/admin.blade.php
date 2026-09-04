@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Welcome back, {{ auth()->user()->name }} 👋</h1>
        <p class="text-muted mb-0">Here's what's happening with your CRM today — {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Lead</a>
        <a href="{{ route('customers.create') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-building me-1"></i>Add Customer</a>
    </div>
</div>

<!-- ── KPI Row 1 (Industrial 4-Palette Signature with Drill-Down & Sparklines) ── -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('customers.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-magenta has-sparkline">
                <div class="kpi-value">{{ number_format($stats['total_customers']) }}</div>
                <div class="kpi-label">Total Customers</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> View all registered</div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminCustomers"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-building"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('leads.index') }}?status=new" class="text-decoration-none">
            <div class="kpi-card kpi-amber has-sparkline">
                <div class="kpi-value">{{ number_format($stats['open_leads']) }}</div>
                <div class="kpi-label">Open Leads</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> {{ $stats['total_leads'] }} total in pipeline</div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminLeads"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-funnel-dollar"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('deals.pipeline') }}" class="text-decoration-none">
            <div class="kpi-card kpi-navy has-sparkline">
                <div class="kpi-value">{{ number_format($stats['open_deals']) }}</div>
                <div class="kpi-label">Active Deals</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Open sales pipeline</div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminDeals"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-handshake"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('invoices.index') }}?status=paid" class="text-decoration-none">
            <div class="kpi-card kpi-blue has-sparkline">
                <div class="kpi-value">${{ number_format($stats['monthly_revenue'], 0) }}</div>
                <div class="kpi-label">Monthly Revenue</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Settled cashflow</div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminRevenue"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- ── KPI Row 2 (Operations & Service Metrics with Drill-Down & Sparklines) ── -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tickets.index') }}?status=open" class="text-decoration-none">
            <div class="kpi-card kpi-purple has-sparkline">
                <div class="kpi-value">{{ number_format($stats['open_tickets']) }}</div>
                <div class="kpi-label">Open Support Tickets</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> {{ $stats['high_priority_tickets'] }} high priority
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminTickets"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-ticket-alt"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('invoices.index') }}?status=sent" class="text-decoration-none">
            <div class="kpi-card kpi-teal has-sparkline">
                <div class="kpi-value">${{ number_format($stats['pending_revenue'], 0) }}</div>
                <div class="kpi-label">Pending Invoices</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> {{ $stats['overdue_invoices'] }} overdue
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminPending"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('users.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-indigo has-sparkline">
                <div class="kpi-value">{{ number_format($stats['active_users']) }}</div>
                <div class="kpi-label">Active System Users</div>
                <div class="kpi-trend"><i class="fas fa-arrow-right"></i> Manage team access</div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminUsers"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-users"></i></div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tasks.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-orange has-sparkline">
                <div class="kpi-value">{{ number_format($stats['overdue_tasks']) }}</div>
                <div class="kpi-label">Overdue Tasks</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> View pending actions
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineAdminTasks"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-tasks"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- ── Charts Row 1: Revenue Momentum & Deal Pipeline ── -->
<div class="row g-4 mb-4">
    <!-- Monthly Revenue Chart with Interactive Line / Bar / Combo Mode Switcher -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-area text-primary me-2"></i>
                    <span class="fw-semibold">Financial Revenue Performance (Last 6 Months)</span>
                </div>
                <div class="btn-group btn-group-sm" role="group" id="revenueChartTypeGroup">
                    <button type="button" class="btn btn-sm btn-outline-primary active" id="btnChartLine"><i class="fas fa-chart-line me-1"></i>Line Trend</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnChartBar"><i class="fas fa-chart-bar me-1"></i>Bar Compare</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnChartCombo"><i class="fas fa-layer-group me-1"></i>Dual-Axis</button>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Pipeline by Stage -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie text-warning me-2"></i>
                <span class="fw-semibold">Pipeline Value by Stage</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; max-height: 200px; width: 100%;">
                    <canvas id="pipelineChart"></canvas>
                </div>
                <div class="mt-3 w-100" id="pipelineLegend"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Charts Row 2: Lead Acquisition Velocity & Support SLA Breakdown ── -->
<div class="row g-4 mb-4">
    <!-- Lead Inflow vs Conversion (Line & Bar Combo) -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-funnel-dollar text-success me-2"></i>
                <span class="fw-semibold">Lead Acquisition & Conversion Velocity (Monthly)</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 230px; width: 100%;">
                    <canvas id="leadConversionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Tickets by Priority (Doughnut Chart) -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-shield-alt text-danger me-2"></i>
                <span class="fw-semibold">Support Operations: Severity Breakdown</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; max-height: 180px; width: 100%;">
                    <canvas id="supportPriorityChart"></canvas>
                </div>
                <div class="mt-2 w-100" id="supportPriorityLegend"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Recent Activities + Quick Stats ── -->
<div class="row g-4">
    <!-- Recent Activities -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history text-info me-2"></i>
                Recent Activities
                <a href="{{ route('activities.index') }}" class="ms-auto btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($stats['recent_activities'] as $activity)
                <div class="activity-item px-4">
                    <div class="activity-icon" style="background: {{ match($activity->type) {
                        'created'       => '#4f46e5',
                        'updated'       => '#0ea5e9',
                        'call'          => '#10b981',
                        'meeting'       => '#f59e0b',
                        'status_change' => '#8b5cf6',
                        'assignment'    => '#06b6d4',
                        default         => '#64748b',
                    } }}">
                        <i class="fas fa-{{ match($activity->type) {
                            'created'   => 'plus',
                            'updated'   => 'pen',
                            'call'      => 'phone',
                            'meeting'   => 'calendar',
                            'assignment'=> 'user-plus',
                            default     => 'circle-dot',
                        } }}"></i>
                    </div>
                    <div class="activity-body">
                        <p class="activity-text">
                            <strong>{{ $activity->performed_by_name ?? 'System' }}</strong>
                            {{ $activity->subject }}
                        </p>
                        <span class="activity-meta">{{ $activity->created_at?->diffForHumans() }} &middot; {{ ucfirst($activity->related_type ?? '') }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state py-5">
                    <i class="fas fa-history"></i>
                    <h5>No activities yet</h5>
                    <p>Activities will appear here as your team uses the CRM.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-12 col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar text-success me-2"></i> Deal Summary
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Open Deals</span>
                    <span class="fw-bold">{{ $stats['open_deals'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Won This Month</span>
                    <span class="fw-bold text-success">{{ $stats['won_deals'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Lost This Month</span>
                    <span class="fw-bold text-danger">{{ $stats['lost_deals'] }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Total Revenue</span>
                    <span class="fw-bold text-primary">${{ number_format($stats['total_revenue'], 0) }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-link text-primary me-2"></i> Quick Actions
            </div>
            <div class="card-body p-2">
                <a href="{{ route('leads.create') }}"     class="btn btn-light w-100 text-start mb-2"><i class="fas fa-funnel-dollar me-2 text-primary"></i>Add New Lead</a>
                <a href="{{ route('customers.create') }}" class="btn btn-light w-100 text-start mb-2"><i class="fas fa-building me-2 text-success"></i>Add Customer</a>
                <a href="{{ route('deals.create') }}"     class="btn btn-light w-100 text-start mb-2"><i class="fas fa-handshake me-2 text-warning"></i>Create Deal</a>
                <a href="{{ route('tickets.create') }}"   class="btn btn-light w-100 text-start mb-2"><i class="fas fa-ticket-alt me-2 text-info"></i>New Ticket</a>
                <a href="{{ route('users.create') }}"     class="btn btn-light w-100 text-start"><i class="fas fa-user-plus me-2 text-secondary"></i>Add User</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 1. Interactive Revenue Chart (Line / Bar / Combo)
const revLabels = @json($stats['monthly_revenue_data']['labels']);
const revPaidData = @json($stats['monthly_revenue_data']['data']);
const revBilledData = @json($stats['monthly_billed_data']['data']);
const revLeadsData = @json($stats['monthly_lead_data']['data']);

const revenueCanvas = document.getElementById('revenueChart');
const revenueCtx = revenueCanvas.getContext('2d');

// Helper to create gradient for smooth area line chart
const createGradient = (ctx, startColor, endColor) => {
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, startColor);
    gradient.addColorStop(1, endColor);
    return gradient;
};

const chartConfigs = {
    line: {
        type: 'line',
        data: {
            labels: revLabels,
            datasets: [
                {
                    label: 'Paid Revenue ($)',
                    data: revPaidData,
                    borderColor: '#4f46e5',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return 'rgba(79, 70, 229, 0.15)';
                        return createGradient(ctx, 'rgba(79, 70, 229, 0.35)', 'rgba(79, 70, 229, 0.02)');
                    },
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
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
                        label: function(context) {
                            return ' ' + context.dataset.label + ': $' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { callback: value => '$' + Number(value).toLocaleString() }
                },
                x: { grid: { display: false } }
            }
        }
    },
    bar: {
        type: 'bar',
        data: {
            labels: revLabels,
            datasets: [
                {
                    label: 'Collected / Paid ($)',
                    data: revPaidData,
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                },
                {
                    label: 'Invoiced / Billed ($)',
                    data: revBilledData,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 6,
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
                        label: function(context) {
                            return ' ' + context.dataset.label + ': $' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { callback: value => '$' + Number(value).toLocaleString() }
                },
                x: { grid: { display: false } }
            }
        }
    },
    combo: {
        type: 'bar',
        data: {
            labels: revLabels,
            datasets: [
                {
                    label: 'Invoiced ($)',
                    data: revBilledData,
                    backgroundColor: 'rgba(14, 165, 233, 0.65)',
                    borderRadius: 6,
                    yAxisID: 'y',
                },
                {
                    label: 'Collected Cash ($)',
                    data: revPaidData,
                    type: 'line',
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.35,
                    pointRadius: 5,
                    yAxisID: 'y',
                },
                {
                    label: 'Leads Acquired',
                    data: revLeadsData,
                    type: 'line',
                    borderColor: '#f59e0b',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 4,
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
                        label: function(context) {
                            if (context.dataset.yAxisID === 'y1') {
                                return ' ' + context.dataset.label + ': ' + context.raw;
                            }
                            return ' ' + context.dataset.label + ': $' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: '#f1f5f9' },
                    ticks: { callback: value => '$' + Number(value).toLocaleString() }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Leads' }
                },
                x: { grid: { display: false } }
            }
        }
    }
};

let currentRevenueChart = new Chart(revenueCtx, chartConfigs.line);

// Handle interactive mode switching
const switchRevenueChart = (mode, activeBtn) => {
    document.querySelectorAll('#revenueChartTypeGroup button').forEach(b => b.classList.remove('active'));
    activeBtn.classList.add('active');
    currentRevenueChart.destroy();
    currentRevenueChart = new Chart(revenueCtx, chartConfigs[mode]);
};

document.getElementById('btnChartLine')?.addEventListener('click', function() { switchRevenueChart('line', this); });
document.getElementById('btnChartBar')?.addEventListener('click', function() { switchRevenueChart('bar', this); });
document.getElementById('btnChartCombo')?.addEventListener('click', function() { switchRevenueChart('combo', this); });


// 2. Pipeline Doughnut Chart
const pipelineData = @json($stats['pipeline_by_stage']);
const pipelineColors = ['#6366f1','#8b5cf6','#0ea5e9','#f59e0b','#10b981','#ef4444'];
const pipelineCtx = document.getElementById('pipelineChart').getContext('2d');
new Chart(pipelineCtx, {
    type: 'doughnut',
    data: {
        labels: pipelineData.labels,
        datasets: [{
            data: pipelineData.counts,
            backgroundColor: pipelineColors,
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const idx = context.dataIndex;
                        const count = pipelineData.counts[idx];
                        const val = pipelineData.values[idx] || 0;
                        return ` ${context.label}: ${count} deals ($${Number(val).toLocaleString()})`;
                    }
                }
            }
        },
        cutout: '68%',
    }
});

const legendEl = document.getElementById('pipelineLegend');
pipelineData.labels.forEach((label, i) => {
    legendEl.innerHTML += `<div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            <span style="width:10px;height:10px;border-radius:50%;background:${pipelineColors[i]};display:inline-block"></span>
            <span style="font-size:12px">${label}</span>
        </div>
        <span style="font-size:12px;font-weight:600">${pipelineData.counts[i]}</span>
    </div>`;
});


// 3. Lead Acquisition & Conversion Funnel Chart
const leadConvCtx = document.getElementById('leadConversionChart').getContext('2d');
const leadMonths = @json($stats['monthly_lead_data']['labels']);
const leadAcquired = @json($stats['monthly_lead_data']['data']);
const leadConverted = @json($stats['monthly_lead_data']['converted']);

new Chart(leadConvCtx, {
    type: 'bar',
    data: {
        labels: leadMonths,
        datasets: [
            {
                label: 'New Leads Inflow',
                data: leadAcquired,
                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                borderRadius: 5,
            },
            {
                label: 'Converted Customers',
                data: leadConverted,
                type: 'line',
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
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


// 4. Support Tickets by Priority Doughnut Chart
const priorityData = @json($stats['ticket_priority_data']);
const supportPriorityCtx = document.getElementById('supportPriorityChart').getContext('2d');

new Chart(supportPriorityCtx, {
    type: 'doughnut',
    data: {
        labels: priorityData.labels,
        datasets: [{
            data: priorityData.data,
            backgroundColor: priorityData.colors,
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
        },
        cutout: '65%',
    }
});

const supLegendEl = document.getElementById('supportPriorityLegend');
priorityData.labels.forEach((label, i) => {
});

// ── 5. Sparkline Mini-Charts for All 8 Upper KPI Cards ──
function initKpiSparkline(canvasId, dataPoints, type = 'line') {
    const el = document.getElementById(canvasId);
    if (!el) return;
    const ctx = el.getContext('2d');
    
    if (type === 'line') {
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
                    pointHoverRadius: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    } else {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dataPoints.map((_, i) => i + 1),
                datasets: [{
                    data: dataPoints,
                    backgroundColor: 'rgba(255, 255, 255, 0.75)',
                    borderRadius: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    }
}

// Instantiate sparklines for all 8 cards
initKpiSparkline('sparklineAdminCustomers', [1, 1, 1, 2, 2, Math.max(1, {{ $stats['total_customers'] }})]);
initKpiSparkline('sparklineAdminLeads',     [1, 2, 3, 2, 4, Math.max(1, {{ $stats['open_leads'] }})]);
initKpiSparkline('sparklineAdminDeals',     [1, 2, 2, 3, 4, Math.max(1, {{ $stats['open_deals'] }})]);
initKpiSparkline('sparklineAdminRevenue',   @json(array_slice($stats['monthly_revenue_data']['data'], -6)));
initKpiSparkline('sparklineAdminTickets',   [1, 2, 1, 3, 2, Math.max(1, {{ $stats['open_tickets'] }})]);
initKpiSparkline('sparklineAdminPending',   [2200, 3100, 4400, 3800, 5200, Math.max(1000, {{ $stats['pending_revenue'] }})]);
initKpiSparkline('sparklineAdminUsers',     [3, 4, 4, 5, 5, Math.max(1, {{ $stats['active_users'] }})]);
initKpiSparkline('sparklineAdminTasks',     [4, 3, 2, 2, 1, Math.max(0, {{ $stats['overdue_tasks'] }})]);
</script>
@endpush
