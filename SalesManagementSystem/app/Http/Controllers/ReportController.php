<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index()
    {
        $leads = $this->reportService->getLeadMetrics();
        $sales = $this->reportService->getSalesMetrics();
        $revenue = $this->reportService->getRevenueMetrics();
        $support = $this->reportService->getSupportMetrics();

        return view('reports.index', compact('leads', 'sales', 'revenue', 'support'));
    }

    public function leads(Request $request)
    {
        $metrics = $this->reportService->getLeadMetrics($request->from, $request->to);
        return view('reports.leads', compact('metrics'));
    }

    public function sales(Request $request)
    {
        $metrics = $this->reportService->getSalesMetrics($request->from, $request->to);
        return view('reports.sales', compact('metrics'));
    }

    public function revenue(Request $request)
    {
        $metrics = $this->reportService->getRevenueMetrics();
        return view('reports.revenue', compact('metrics'));
    }

    public function performance()
    {
        $performance = $this->reportService->getEmployeePerformance();
        return view('reports.performance', compact('performance'));
    }

    public function support()
    {
        $metrics = $this->reportService->getSupportMetrics();
        return view('reports.support', compact('metrics'));
    }

    public function customers()
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', Customer::STATUS_ACTIVE)->count();
        $prospects = Customer::where('status', Customer::STATUS_PROSPECT)->count();
        $churned = Customer::where('status', Customer::STATUS_CHURNED)->count();

        return view('reports.customers', compact('totalCustomers', 'activeCustomers', 'prospects', 'churned'));
    }
}
