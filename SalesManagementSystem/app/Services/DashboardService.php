<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Activity;

class DashboardService
{
    // ── Admin Dashboard ─────────────────────────────────────────────────────

    public function getAdminStats(): array
    {
        $totalRevenue    = Invoice::paid()->sum('total');
        $pendingRevenue  = Invoice::unpaid()->sum('total');
        $monthlyRevenue  = Invoice::paid()
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('total');

        return [
            'total_customers'       => Customer::count(),
            'total_leads'           => Lead::count(),
            'open_leads'            => Lead::open()->count(),
            'open_deals'            => Deal::open()->count(),
            'total_revenue'         => $totalRevenue,
            'monthly_revenue'       => $monthlyRevenue,
            'pending_revenue'       => $pendingRevenue,
            'open_tickets'          => Ticket::open()->count(),
            'high_priority_tickets' => Ticket::open()->highPriority()->count(),
            'total_users'           => User::count(),
            'active_users'          => User::where('status', 'active')->count(),
            'won_deals'             => Deal::won()->count(),
            'lost_deals'            => Deal::lost()->count(),
            'overdue_tasks'         => Task::overdue()->count(),
            'overdue_invoices'      => Invoice::overdue()->count(),
            'recent_activities'     => Activity::orderBy('created_at', 'desc')->limit(10)->get(),
            'monthly_lead_data'     => $this->getMonthlyLeadData(),
            'monthly_revenue_data'  => $this->getMonthlyRevenueData(),
            'monthly_billed_data'   => $this->getMonthlyBilledData(),
            'pipeline_by_stage'     => $this->getPipelineByStage(),
            'ticket_priority_data'  => $this->getTicketPriorityData(),
            'ticket_status_data'    => $this->getTicketStatusData(),
        ];
    }

    // ── Manager Dashboard ───────────────────────────────────────────────────

    public function getManagerStats(): array
    {
        $salesExecs = User::where('role', User::ROLE_SALES_EXECUTIVE)->get();

        return [
            'total_leads'           => Lead::count(),
            'open_leads'            => Lead::open()->count(),
            'converted_leads'       => Lead::where('status', Lead::STATUS_CONVERTED)->count(),
            'open_deals'            => Deal::open()->count(),
            'won_deals'             => Deal::won()->count(),
            'lost_deals'            => Deal::lost()->count(),
            'pipeline_value'        => Deal::open()->sum('value'),
            'overdue_follow_ups'    => Lead::overdue()->count(),
            'team_members'          => $salesExecs->count(),
            'recent_activities'     => Activity::orderBy('created_at', 'desc')->limit(8)->get(),
            'pipeline_by_stage'     => $this->getPipelineByStage(),
            'monthly_revenue_data'  => $this->getMonthlyRevenueData(),
            'lead_status_data'      => $this->getLeadStatusData(),
            'team_performance_data' => $this->getTeamPerformanceData(),
        ];
    }

    // ── Sales Executive Dashboard ───────────────────────────────────────────

    public function getSalesStats(string $userId): array
    {
        return [
            'my_leads'             => Lead::assignedTo($userId)->count(),
            'my_open_leads'        => Lead::assignedTo($userId)->open()->count(),
            'my_overdue_leads'     => Lead::assignedTo($userId)->overdue()->count(),
            'my_deals'             => Deal::assignedTo($userId)->count(),
            'my_open_deals'        => Deal::assignedTo($userId)->open()->count(),
            'my_won_deals'         => Deal::assignedTo($userId)->won()->count(),
            'pipeline_value'       => Deal::assignedTo($userId)->open()->sum('value'),
            'my_tasks'             => Task::assignedTo($userId)->pending()->count(),
            'overdue_tasks'        => Task::assignedTo($userId)->overdue()->count(),
            'tasks_today'          => Task::assignedTo($userId)->dueToday()->get(),
            'recent_leads'         => Lead::assignedTo($userId)->orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_activities'    => Activity::where('performed_by', $userId)->orderBy('created_at', 'desc')->limit(8)->get(),
            'monthly_sales_data'   => $this->getMyMonthlySalesData($userId),
            'my_pipeline_data'     => $this->getMyPipelineData($userId),
            'my_lead_status_data'  => $this->getMyLeadStatusData($userId),
        ];
    }

    // ── Support Agent Dashboard ─────────────────────────────────────────────

    public function getSupportStats(string $userId): array
    {
        return [
            'open_tickets'          => Ticket::open()->count(),
            'assigned_to_me'        => Ticket::assignedTo($userId)->open()->count(),
            'high_priority'         => Ticket::open()->highPriority()->count(),
            'resolved_today'        => Ticket::where('resolved_at', '>=', today())->count(),
            'unassigned'            => Ticket::open()->whereNull('assigned_to')->count(),
            'recent_tickets'        => Ticket::open()->orderBy('created_at', 'desc')->limit(8)->get(),
            'my_recent_tickets'     => Ticket::assignedTo($userId)->orderBy('updated_at', 'desc')->limit(5)->get(),
            'ticket_trend_data'     => $this->getTicketTrendData(),
            'ticket_priority_data'  => $this->getTicketPriorityData(),
            'ticket_status_data'    => $this->getTicketStatusData(),
        ];
    }

    // ── Customer Dashboard ──────────────────────────────────────────────────

    public function getCustomerStats(string $userId): array
    {
        $user = auth()->user();
        $customer = Customer::where('email', $user?->email)->first();
        $customerIds = array_values(array_unique(array_filter([(string) $userId, $customer ? (string) $customer->id : null])));

        return [
            'my_tickets'          => Ticket::whereIn('customer_id', $customerIds)->count(),
            'open_tickets'        => Ticket::whereIn('customer_id', $customerIds)->open()->count(),
            'my_quotations'       => \App\Models\Quotation::whereIn('customer_id', $customerIds)->count(),
            'my_invoices'         => Invoice::whereIn('customer_id', $customerIds)->count(),
            'unpaid_invoices'     => Invoice::whereIn('customer_id', $customerIds)->unpaid()->count(),
            'total_due'           => Invoice::whereIn('customer_id', $customerIds)->unpaid()->sum('amount_due'),
            'recent_tickets'      => Ticket::whereIn('customer_id', $customerIds)->orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_invoices'     => Invoice::whereIn('customer_id', $customerIds)->orderBy('created_at', 'desc')->limit(5)->get(),
            'monthly_spend_data'  => $this->getCustomerSpendData($customerIds),
            'invoice_status_data' => $this->getCustomerInvoiceStatusData($customerIds),
            'ticket_status_data'  => $this->getCustomerTicketStatusData($customerIds),
        ];
    }

    // ── Chart Data Helpers ──────────────────────────────────────────────────

    public function getMonthlyLeadData(): array
    {
        $months = [];
        $counts = [];
        $converted = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $counts[] = Lead::where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->count();

            $converted[] = Lead::where('status', Lead::STATUS_CONVERTED)
                ->where('converted_at', '>=', $start)
                ->where('converted_at', '<=', $end)
                ->count();
        }
        return ['labels' => $months, 'data' => $counts, 'converted' => $converted];
    }

    public function getMonthlyRevenueData(): array
    {
        $months = [];
        $totals = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $totals[] = Invoice::paid()
                ->where('paid_at', '>=', $date->copy()->startOfMonth())
                ->where('paid_at', '<=', $date->copy()->endOfMonth())
                ->sum('total');
        }
        return ['labels' => $months, 'data' => $totals];
    }

    public function getMonthlyBilledData(): array
    {
        $months = [];
        $totals = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $totals[] = Invoice::where('created_at', '>=', $date->copy()->startOfMonth())
                ->where('created_at', '<=', $date->copy()->endOfMonth())
                ->sum('total');
        }
        return ['labels' => $months, 'data' => $totals];
    }

    public function getPipelineByStage(): array
    {
        $stages = DealStage::ordered()->get();
        $labels = [];
        $counts = [];
        $values = [];

        foreach ($stages as $stage) {
            $labels[] = $stage->name;
            $counts[] = Deal::where('stage_id', (string) $stage->id)->open()->count();
            $values[] = Deal::where('stage_id', (string) $stage->id)->open()->sum('value');
        }

        return ['labels' => $labels, 'counts' => $counts, 'values' => $values];
    }

    public function getTicketPriorityData(): array
    {
        $priorities = [
            'critical' => ['label' => 'Critical', 'color' => '#ef4444'],
            'high'     => ['label' => 'High',     'color' => '#f59e0b'],
            'medium'   => ['label' => 'Medium',   'color' => '#3b82f6'],
            'low'      => ['label' => 'Low',      'color' => '#10b981'],
        ];
        $labels = [];
        $data = [];
        $colors = [];
        foreach ($priorities as $key => $meta) {
            $labels[] = $meta['label'];
            $data[]   = Ticket::where('priority', $key)->count();
            $colors[] = $meta['color'];
        }
        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    public function getTicketStatusData(): array
    {
        $statuses = [
            'open'        => ['label' => 'Open',        'color' => '#ef4444'],
            'in_progress' => ['label' => 'In Progress', 'color' => '#f59e0b'],
            'resolved'    => ['label' => 'Resolved',    'color' => '#10b981'],
            'closed'      => ['label' => 'Closed',      'color' => '#6b7280'],
        ];
        $labels = [];
        $data = [];
        $colors = [];
        foreach ($statuses as $key => $meta) {
            $labels[] = $meta['label'];
            $data[]   = Ticket::where('status', $key)->count();
            $colors[] = $meta['color'];
        }
        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    public function getLeadStatusData(): array
    {
        $statuses = [
            Lead::STATUS_NEW         => ['label' => 'New',         'color' => '#3b82f6'],
            Lead::STATUS_CONTACTED   => ['label' => 'Contacted',   'color' => '#8b5cf6'],
            Lead::STATUS_QUALIFIED   => ['label' => 'Qualified',   'color' => '#10b981'],
            Lead::STATUS_CONVERTED   => ['label' => 'Converted',   'color' => '#f59e0b'],
            Lead::STATUS_LOST        => ['label' => 'Lost',        'color' => '#ef4444'],
        ];
        $labels = [];
        $data = [];
        $colors = [];
        foreach ($statuses as $key => $meta) {
            $labels[] = $meta['label'];
            $data[]   = Lead::where('status', $key)->count();
            $colors[] = $meta['color'];
        }
        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    public function getTeamPerformanceData(): array
    {
        $reps = User::where('role', User::ROLE_SALES_EXECUTIVE)->get();
        $names = [];
        $wonCounts = [];
        $revenue = [];
        foreach ($reps as $rep) {
            $names[] = $rep->name;
            $wonCounts[] = Deal::where('assigned_to', (string) $rep->id)->won()->count();
            $rev = Deal::where('assigned_to', (string) $rep->id)->won()->sum('value');
            if ($rev == 0) {
                $rev = Invoice::paid()->where('created_by', (string) $rep->id)->sum('total');
            }
            $revenue[] = $rev;
        }
        return ['labels' => $names, 'deals' => $wonCounts, 'revenue' => $revenue];
    }

    public function getMyMonthlySalesData(string $userId): array
    {
        $months = [];
        $revenue = [];
        $deals = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $rev = Invoice::paid()
                ->where('created_by', $userId)
                ->where('paid_at', '>=', $start)
                ->where('paid_at', '<=', $end)
                ->sum('total');

            if ($rev == 0) {
                $rev = Deal::where('assigned_to', $userId)->won()
                    ->where('actual_close_date', '>=', $start)
                    ->where('actual_close_date', '<=', $end)
                    ->sum('value');
            }
            $revenue[] = $rev;
            $deals[] = Deal::where('assigned_to', $userId)->won()
                ->where('actual_close_date', '>=', $start)
                ->where('actual_close_date', '<=', $end)
                ->count();
        }
        return ['labels' => $months, 'revenue' => $revenue, 'deals' => $deals];
    }

    public function getMyPipelineData(string $userId): array
    {
        $stages = DealStage::ordered()->get();
        $labels = [];
        $counts = [];
        $values = [];
        foreach ($stages as $stage) {
            $labels[] = $stage->name;
            $counts[] = Deal::where('assigned_to', $userId)->where('stage_id', (string) $stage->id)->count();
            $values[] = Deal::where('assigned_to', $userId)->where('stage_id', (string) $stage->id)->sum('value');
        }
        return ['labels' => $labels, 'counts' => $counts, 'values' => $values];
    }

    public function getMyLeadStatusData(string $userId): array
    {
        $statuses = [
            Lead::STATUS_NEW         => 'New',
            Lead::STATUS_CONTACTED   => 'Contacted',
            Lead::STATUS_QUALIFIED   => 'Qualified',
            Lead::STATUS_CONVERTED   => 'Converted',
            Lead::STATUS_LOST        => 'Lost',
        ];
        $labels = [];
        $counts = [];
        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            $counts[] = Lead::where('assigned_to', $userId)->where('status', $key)->count();
        }
        return ['labels' => $labels, 'counts' => $counts];
    }

    public function getTicketTrendData(): array
    {
        $months = [];
        $created = [];
        $resolved = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            $created[]  = Ticket::where('created_at', '>=', $start)->where('created_at', '<=', $end)->count();
            $resolved[] = Ticket::where('resolved_at', '>=', $start)->where('resolved_at', '<=', $end)->count();
        }
        return ['labels' => $months, 'created' => $created, 'resolved' => $resolved];
    }

    public function getCustomerSpendData(array $customerIds): array
    {
        $months = [];
        $totals = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $totals[] = Invoice::whereIn('customer_id', $customerIds)
                ->where('created_at', '>=', $date->copy()->startOfMonth())
                ->where('created_at', '<=', $date->copy()->endOfMonth())
                ->sum('total');
        }
        return ['labels' => $months, 'data' => $totals];
    }

    public function getCustomerInvoiceStatusData(array $customerIds): array
    {
        $paid = Invoice::whereIn('customer_id', $customerIds)->paid()->count();
        $unpaid = Invoice::whereIn('customer_id', $customerIds)->unpaid()->count();
        $partial = Invoice::whereIn('customer_id', $customerIds)->where('status', Invoice::STATUS_PARTIAL)->count();
        return [
            'labels' => ['Settled / Paid', 'Pending Payment', 'Partially Paid'],
            'data'   => [$paid, $unpaid, $partial],
            'colors' => ['#10b981', '#ef4444', '#f59e0b']
        ];
    }

    public function getCustomerTicketStatusData(array $customerIds): array
    {
        $open = Ticket::whereIn('customer_id', $customerIds)->open()->count();
        $resolved = Ticket::whereIn('customer_id', $customerIds)->whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])->count();
        return [
            'labels' => ['Active / In-Progress', 'Resolved & Closed'],
            'data'   => [$open, $resolved],
            'colors' => ['#3b82f6', '#10b981']
        ];
    }
}
