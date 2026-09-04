<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function stats(Request $request)
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

        return response()->json([
            'success' => true,
            'role'    => $user->role,
            'data'    => $stats,
        ]);
    }
}
