<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\DealApiController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\NotificationApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Veytrix
|--------------------------------------------------------------------------
| These routes are prefixed with /api and use the 'auth' middleware (web
| session guard). No Sanctum tokens are used — the Blade frontend makes
| AJAX calls with the CSRF cookie set.
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard stats (AJAX)
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats'])->name('api.dashboard.stats');

    // Leads
    Route::get('/leads',                  [LeadApiController::class, 'index'])->name('api.leads.index');
    Route::get('/leads/{id}',             [LeadApiController::class, 'show'])->name('api.leads.show');
    Route::post('/leads/{id}/assign',     [LeadApiController::class, 'assign'])->name('api.leads.assign');
    Route::post('/leads/{id}/status',     [LeadApiController::class, 'updateStatus'])->name('api.leads.status');

    // Customers (search/autocomplete)
    Route::get('/customers',              [CustomerApiController::class, 'index'])->name('api.customers.index');
    Route::get('/customers/{id}',         [CustomerApiController::class, 'show'])->name('api.customers.show');
    Route::get('/customers/search',       [CustomerApiController::class, 'search'])->name('api.customers.search');

    // Deals
    Route::get('/deals',                  [DealApiController::class, 'index'])->name('api.deals.index');
    Route::get('/deals/pipeline',         [DealApiController::class, 'pipeline'])->name('api.deals.pipeline');
    Route::post('/deals/{id}/stage',      [DealApiController::class, 'updateStage'])->name('api.deals.stage');

    // Tasks
    Route::get('/tasks',                  [TaskApiController::class, 'index'])->name('api.tasks.index');
    Route::post('/tasks/{id}/complete',   [TaskApiController::class, 'complete'])->name('api.tasks.complete');

    // Tickets
    Route::get('/tickets',                [TicketApiController::class, 'index'])->name('api.tickets.index');
    Route::get('/tickets/{id}/messages',  [TicketApiController::class, 'messages'])->name('api.tickets.messages');

    // Notifications
    Route::get('/notifications',          [NotificationApiController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{id}/read', [NotificationApiController::class, 'markRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [NotificationApiController::class, 'markAllRead'])->name('api.notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationApiController::class, 'unreadCount'])->name('api.notifications.count');
});
