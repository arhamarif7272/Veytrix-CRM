@extends('layouts.app')
@section('title', 'Customer Portal')
@section('page-title', 'Customer Portal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Customer Portal</li>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Welcome, {{ auth()->user()->name }} 👋</h1>
        <p class="text-muted mb-0">Industrial Enterprise Account Summary &bull; {{ now()->format('l, d M Y') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('quotations.my') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-invoice me-1"></i>My Quotations</a>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>New Support Ticket</a>
    </div>
</div>

<!-- ── Signature 4-Color KPI Row with Drill-Down & Sparklines ── -->
<div class="row g-4 mb-4">
    <!-- Card 1: Ruby Magenta (My Tickets) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('tickets.index') }}" class="text-decoration-none">
            <div class="kpi-card kpi-magenta has-sparkline">
                <div class="kpi-value">{{ $stats['my_tickets'] }}</div>
                <div class="kpi-label">My Support Tickets</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> {{ $stats['open_tickets'] }} active cases
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineCustTickets"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-ticket-alt"></i></div>
            </div>
        </a>
    </div>

    <!-- Card 2: Golden Amber (Quotations) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('quotations.my') }}" class="text-decoration-none">
            <div class="kpi-card kpi-amber has-sparkline">
                <div class="kpi-value">{{ $stats['my_quotations'] }}</div>
                <div class="kpi-label">Commercial Quotations</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> Review proposals
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineCustQuotes"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </a>
    </div>

    <!-- Card 3: Deep Royal Navy (Outstanding Balance) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('invoices.my') }}?status=unpaid" class="text-decoration-none">
            <div class="kpi-card kpi-navy has-sparkline">
                <div class="kpi-value">${{ number_format($stats['total_due'], 0) }}</div>
                <div class="kpi-label">Outstanding Balance</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> {{ $stats['unpaid_invoices'] }} unpaid invoice(s)
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineCustBalance"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
            </div>
        </a>
    </div>

    <!-- Card 4: Ocean Sapphire Blue (Total Invoices) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('invoices.my') }}" class="text-decoration-none">
            <div class="kpi-card kpi-blue has-sparkline">
                <div class="kpi-value">{{ $stats['my_invoices'] }}</div>
                <div class="kpi-label">Billing Records</div>
                <div class="kpi-trend">
                    <i class="fas fa-arrow-right"></i> Lifetime history
                </div>
                <div class="kpi-sparkline"><canvas id="sparklineCustInvoices"></canvas></div>
                <div class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </a>
    </div>
</div>

<!-- ── Account Financial Status Callout (Inspired by reference Item Sales block) ── -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-5 border-end-md">
                        <span class="text-muted text-uppercase fw-bold small">Total Account Invoiced</span>
                        <h2 class="stat-highlight-val my-1">
                            ${{ number_format($stats['recent_invoices']->sum('total'), 2) }}
                        </h2>
                        <p class="text-muted small mb-0">Total volume of approved enterprise agreements & services</p>
                    </div>
                    <div class="col-12 col-md-7 mt-3 mt-md-0 ps-md-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <h4 class="mb-0 fw-bold text-success">{{ $stats['my_invoices'] - $stats['unpaid_invoices'] }}</h4>
                                    <small class="text-muted fw-semibold">Settled</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <h4 class="mb-0 fw-bold text-warning">{{ $stats['unpaid_invoices'] }}</h4>
                                    <small class="text-muted fw-semibold">Pending</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <h4 class="mb-0 fw-bold text-info">{{ $stats['open_tickets'] }}</h4>
                                    <small class="text-muted fw-semibold">Tickets</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Charts Row: Account Expenditure Trajectory & Settlement Distribution ── -->
<div class="row g-4 mb-4">
    <!-- Monthly Expenditure Trend (Line Chart) -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    <span class="fw-semibold">Account Billing & Expenditure Trajectory (6 Months)</span>
                </div>
                <span class="badge bg-light text-primary border">Commercial Services</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="customerSpendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Settlement Status Distribution (Doughnut Chart) -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-receipt text-warning me-2"></i>
                <span class="fw-semibold">Invoicing Settlement Status</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; max-height: 200px; width: 100%;">
                    <canvas id="customerInvoiceChart"></canvas>
                </div>
                <div class="mt-3 w-100" id="customerInvoiceLegend"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Lists: Recent Tickets & Invoices ── -->
<div class="row g-4">
    <!-- Recent Tickets -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header justify-content-between">
                <span><i class="fas fa-ticket-alt me-2 text-primary"></i>My Support Tickets</span>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary btn-sm py-0 px-2 small">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($stats['recent_tickets'] as $ticket)
                <div class="activity-item px-4">
                    <div class="activity-icon" style="background: {{ $ticket->priority === 'high' ? '#ef4444' : ($ticket->priority === 'medium' ? '#f59e0b' : '#10b981') }}">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="activity-body">
                        <p class="activity-text fw-semibold mb-1">{{ Str::limit($ticket->title, 40) }}</p>
                        <span class="activity-meta">
                            <span class="badge-status badge-{{ $ticket->status }}">{{ str_replace('_',' ', ucfirst($ticket->status)) }}</span>
                            &bull; {{ $ticket->created_at?->diffForHumans() }}
                        </span>
                    </div>
                    <a href="{{ route('tickets.show', $ticket->id) }}" class="table-action-btn ms-auto" title="View Ticket"><i class="fas fa-arrow-right"></i></a>
                </div>
                @empty
                <div class="empty-state py-4 text-center">
                    <i class="fas fa-ticket-alt fa-2x text-muted mb-2 opacity-50"></i>
                    <p class="text-muted">No tickets yet.<br><a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm mt-2">Create your first ticket</a></p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header justify-content-between">
                <span><i class="fas fa-receipt me-2 text-warning"></i>Recent Invoices</span>
                <a href="{{ route('invoices.my') }}" class="btn btn-outline-primary btn-sm py-0 px-2 small">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($stats['recent_invoices'] as $invoice)
                <div class="activity-item px-4">
                    <div class="activity-icon" style="background: {{ $invoice->status === 'paid' ? '#10b981' : ($invoice->status === 'overdue' ? '#ef4444' : '#f59e0b') }}">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="activity-body">
                        <p class="activity-text fw-semibold mb-1">{{ $invoice->number }}</p>
                        <span class="activity-meta">
                            <span class="badge-status badge-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                            &bull; ${{ number_format($invoice->total, 2) }}
                        </span>
                    </div>
                    <a href="{{ route('invoices.my.show', $invoice->id) }}" class="table-action-btn ms-auto" title="View Invoice"><i class="fas fa-arrow-right"></i></a>
                </div>
                @empty
                <div class="empty-state py-4 text-center">
                    <i class="fas fa-receipt fa-2x text-muted mb-2 opacity-50"></i>
                    <p class="text-muted">No invoices recorded yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 1. Monthly Expenditure Trend (Line Chart)
const spendCtx = document.getElementById('customerSpendChart').getContext('2d');
const spendGradient = spendCtx.createLinearGradient(0, 0, 0, 260);
spendGradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
spendGradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

new Chart(spendCtx, {
    type: 'line',
    data: {
        labels: @json($stats['monthly_spend_data']['labels']),
        datasets: [{
            label: 'Invoiced Amount ($)',
            data: @json($stats['monthly_spend_data']['data']),
            borderColor: '#4f46e5',
            backgroundColor: spendGradient,
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
                    label: context => ` Invoiced Volume: $${Number(context.raw).toLocaleString()}`
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

// 2. Settlement Status Distribution (Doughnut Chart)
const invStatusData = @json($stats['invoice_status_data']);
const invStatusCtx = document.getElementById('customerInvoiceChart').getContext('2d');

new Chart(invStatusCtx, {
    type: 'doughnut',
    data: {
        labels: invStatusData.labels,
        datasets: [{
            data: invStatusData.data,
            backgroundColor: invStatusData.colors,
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

const invLegendEl = document.getElementById('customerInvoiceLegend');
invStatusData.labels.forEach((label, i) => {
    invLegendEl.innerHTML += `<div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            <span style="width:10px;height:10px;border-radius:50%;background:${invStatusData.colors[i]};display:inline-block"></span>
            <span style="font-size:12px">${label}</span>
        </div>
        <span style="font-size:12px;font-weight:600">${invStatusData.data[i]}</span>
    </div>`;
});

// 3. Customer Portal KPI Sparklines
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

initKpiSparkline('sparklineCustTickets',  [1, 2, 1, 2, Math.max(1, {{ $stats['my_tickets'] }})]);
initKpiSparkline('sparklineCustQuotes',   [1, 1, 2, 2, Math.max(1, {{ $stats['my_quotations'] }})]);
initKpiSparkline('sparklineCustBalance',  [6000, 5200, 4800, Math.max(1000, {{ $stats['total_due'] }})]);
initKpiSparkline('sparklineCustInvoices', [1, 2, 2, 3, Math.max(1, {{ $stats['my_invoices'] }})]);
</script>
@endpush

