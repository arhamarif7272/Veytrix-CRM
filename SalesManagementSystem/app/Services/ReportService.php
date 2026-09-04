<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Ticket;
use App\Models\User;

class ReportService
{
    public function getLeadMetrics(?string $from = null, ?string $to = null): array
    {
        $query = Lead::query();
        if ($from) $query->where('created_at', '>=', $from);
        if ($to)   $query->where('created_at', '<=', $to);

        $totalLeads = (clone $query)->count();
        $converted = (clone $query)->where('status', Lead::STATUS_CONVERTED)->count();
        $conversionRate = $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 1) : 0;

        $byStatus = [
            'new'         => (clone $query)->where('status', Lead::STATUS_NEW)->count(),
            'contacted'   => (clone $query)->where('status', Lead::STATUS_CONTACTED)->count(),
            'qualified'   => (clone $query)->where('status', Lead::STATUS_QUALIFIED)->count(),
            'converted'   => $converted,
            'unqualified' => (clone $query)->where('status', Lead::STATUS_UNQUALIFIED)->count(),
            'lost'        => (clone $query)->where('status', Lead::STATUS_LOST)->count(),
        ];

        $bySource = [];
        $sources = [
            Lead::SOURCE_WEBSITE, Lead::SOURCE_REFERRAL, Lead::SOURCE_SOCIAL,
            Lead::SOURCE_EMAIL, Lead::SOURCE_COLD_CALL, Lead::SOURCE_EVENT, Lead::SOURCE_OTHER
        ];
        foreach ($sources as $source) {
            $bySource[$source] = (clone $query)->where('source', $source)->count();
        }

        return [
            'total'           => $totalLeads,
            'converted'       => $converted,
            'conversion_rate' => $conversionRate,
            'by_status'       => $byStatus,
            'by_source'       => $bySource,
        ];
    }

    public function getSalesMetrics(?string $from = null, ?string $to = null): array
    {
        $query = Deal::query();
        if ($from) $query->where('created_at', '>=', $from);
        if ($to)   $query->where('created_at', '<=', $to);

        $totalDeals = (clone $query)->count();
        $wonDeals = (clone $query)->where('status', Deal::STATUS_WON)->get();
        $lostDeals = (clone $query)->where('status', Deal::STATUS_LOST)->count();
        $openDeals = (clone $query)->where('status', Deal::STATUS_OPEN)->get();

        $wonValue = $wonDeals->sum('value');
        $pipelineValue = $openDeals->sum('value');
        $winRate = $totalDeals > 0 ? round(($wonDeals->count() / $totalDeals) * 100, 1) : 0;
        $avgDealSize = $wonDeals->count() > 0 ? round($wonValue / $wonDeals->count(), 2) : 0;

        return [
            'total_deals'    => $totalDeals,
            'won_count'      => $wonDeals->count(),
            'lost_count'     => $lostDeals,
            'open_count'     => $openDeals->count(),
            'won_value'      => $wonValue,
            'pipeline_value' => $pipelineValue,
            'win_rate'       => $winRate,
            'avg_deal_size'  => $avgDealSize,
        ];
    }

    public function getRevenueMetrics(): array
    {
        $paidInvoices = Invoice::where('status', Invoice::STATUS_PAID)->get();
        $totalRevenue = $paidInvoices->sum('amount_paid');

        $pendingInvoices = Invoice::whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])->get();
        $pendingRevenue = $pendingInvoices->sum('amount_due');

        $overdueInvoices = Invoice::where('status', Invoice::STATUS_OVERDUE)->get();
        $overdueRevenue = $overdueInvoices->sum('amount_due');

        return [
            'total_collected' => $totalRevenue,
            'pending'         => $pendingRevenue,
            'overdue'         => $overdueRevenue,
            'invoices_count'  => Invoice::count(),
            'paid_count'      => $paidInvoices->count(),
        ];
    }

    public function getSupportMetrics(): array
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::open()->count();
        $resolvedTickets = Ticket::whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])->count();
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0;

        return [
            'total'           => $totalTickets,
            'open'            => $openTickets,
            'resolved'        => $resolvedTickets,
            'resolution_rate' => $resolutionRate,
        ];
    }

    public function getEmployeePerformance(): array
    {
        $reps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER])->get();
        $performance = [];

        foreach ($reps as $rep) {
            $leadsCount = Lead::where('assigned_to', (string) $rep->id)->count();
            $dealsWon = Deal::where('assigned_to', (string) $rep->id)->where('status', Deal::STATUS_WON)->get();
            $revenue = $dealsWon->sum('value');

            $performance[] = [
                'user'       => $rep,
                'leads'      => $leadsCount,
                'deals_won'  => $dealsWon->count(),
                'revenue'    => $revenue,
            ];
        }

        return $performance;
    }
}
