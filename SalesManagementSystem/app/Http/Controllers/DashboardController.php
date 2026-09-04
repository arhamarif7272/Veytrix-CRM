<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index()
    {
        $user = auth()->user();

        $stats = match ($user->role) {
            User::ROLE_ADMIN           => $this->dashboardService->getAdminStats(),
            User::ROLE_MANAGER         => $this->dashboardService->getManagerStats(),
            User::ROLE_SALES_EXECUTIVE => $this->dashboardService->getSalesStats($user->id),
            User::ROLE_SUPPORT_AGENT   => $this->dashboardService->getSupportStats($user->id),
            User::ROLE_CUSTOMER        => $this->dashboardService->getCustomerStats($user->id),
            default                    => [],
        };

        $view = match ($user->role) {
            User::ROLE_ADMIN           => 'dashboard.admin',
            User::ROLE_MANAGER         => 'dashboard.manager',
            User::ROLE_SALES_EXECUTIVE => 'dashboard.sales',
            User::ROLE_SUPPORT_AGENT   => 'dashboard.support',
            User::ROLE_CUSTOMER        => 'dashboard.customer',
            default                    => 'dashboard.admin',
        };

        return view($view, compact('stats', 'user'));
    }
}
