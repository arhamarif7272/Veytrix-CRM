<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;

// ── Auth routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',                [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login',               [LoginController::class, 'login']);
    Route::get('/register',             [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',            [RegisterController::class, 'register']);
    Route::get('/forgot-password',      [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password',     [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}',[ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',      [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'checkStatus'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',                   [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',                   [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',          [ProfileController::class, 'changePassword'])->name('profile.password');

    // Notifications
    Route::get('/notifications',             [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read',  [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',   [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // ── Customers ────────────────────────────────────────────────────────────
    Route::middleware('role:admin,manager,sales_executive')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('/customers/{customer}/contacts',      [CustomerController::class, 'contacts'])->name('customers.contacts');
        Route::post('/customers/{customer}/contacts',     [CustomerController::class, 'storeContact'])->name('customers.contacts.store');
        Route::delete('/customers/{customer}/contacts/{contact}', [CustomerController::class, 'destroyContact'])->name('customers.contacts.destroy');
        Route::get('/customers/{customer}/activity',      [CustomerController::class, 'activity'])->name('customers.activity');
    });

    // ── Leads ─────────────────────────────────────────────────────────────────
    Route::middleware('role:admin,manager,sales_executive')->group(function () {
        Route::resource('leads', LeadController::class);
        Route::post('/leads/{lead}/assign',               [LeadController::class, 'assign'])->name('leads.assign');
        Route::post('/leads/{lead}/convert',              [LeadController::class, 'convert'])->name('leads.convert');
        Route::post('/leads/{lead}/status',               [LeadController::class, 'updateStatus'])->name('leads.status');
    });

    // ── Deals ─────────────────────────────────────────────────────────────────
    Route::middleware('role:admin,manager,sales_executive')->group(function () {
        Route::resource('deals', DealController::class);
        Route::post('/deals/{deal}/stage',                [DealController::class, 'updateStage'])->name('deals.stage');
        Route::post('/deals/{deal}/won',                  [DealController::class, 'markWon'])->name('deals.won');
        Route::post('/deals/{deal}/lost',                 [DealController::class, 'markLost'])->name('deals.lost');
        Route::get('/pipeline',                           [DealController::class, 'pipeline'])->name('deals.pipeline');
    });

    // ── Tasks ─────────────────────────────────────────────────────────────────
    Route::resource('tasks', TaskController::class)->middleware('role:admin,manager,sales_executive,support_agent');
    Route::post('/tasks/{task}/complete',                 [TaskController::class, 'complete'])->name('tasks.complete')->middleware('auth');

    // ── Activities ────────────────────────────────────────────────────────────
    Route::get('/activities',                             [ActivityController::class, 'index'])->name('activities.index')->middleware('role:admin,manager');
    Route::post('/activities',                            [ActivityController::class, 'store'])->name('activities.store');

    // ── Quotations ────────────────────────────────────────────────────────────
    Route::middleware('role:admin,manager,sales_executive')->group(function () {
        Route::resource('quotations', QuotationController::class);
        Route::post('/quotations/{quotation}/send',       [QuotationController::class, 'send'])->name('quotations.send');
        Route::post('/quotations/{quotation}/convert',    [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');
        Route::get('/quotations/{quotation}/pdf',         [QuotationController::class, 'pdf'])->name('quotations.pdf');
    });
    // Customer: view own quotations
    Route::middleware('role:customer')->group(function () {
        Route::get('/my/quotations',                      [QuotationController::class, 'myIndex'])->name('quotations.my');
        Route::get('/my/quotations/{quotation}',          [QuotationController::class, 'myShow'])->name('quotations.my.show');
    });

    // ── Invoices ──────────────────────────────────────────────────────────────
    Route::middleware('role:admin,manager,sales_executive')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::post('/invoices/{invoice}/send',           [InvoiceController::class, 'send'])->name('invoices.send');
        Route::post('/invoices/{invoice}/payment',        [InvoiceController::class, 'recordPayment'])->name('invoices.payment');
        Route::get('/invoices/{invoice}/pdf',             [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    });
    // Customer: view own invoices
    Route::middleware('role:customer')->group(function () {
        Route::get('/my/invoices',                        [InvoiceController::class, 'myIndex'])->name('invoices.my');
        Route::get('/my/invoices/{invoice}',              [InvoiceController::class, 'myShow'])->name('invoices.my.show');
    });

    // ── Support Tickets ───────────────────────────────────────────────────────
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/assign',               [TicketController::class, 'assign'])->name('tickets.assign')->middleware('role:admin,manager,support_agent');
    Route::post('/tickets/{ticket}/status',               [TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/messages',             [TicketController::class, 'addMessage'])->name('tickets.messages.store');
    Route::get('/tickets/{ticket}/messages',              [TicketController::class, 'getMessages'])->name('tickets.messages');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::middleware('role:admin,manager')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/',              [ReportController::class, 'index'])->name('index');
        Route::get('/leads',         [ReportController::class, 'leads'])->name('leads');
        Route::get('/sales',         [ReportController::class, 'sales'])->name('sales');
        Route::get('/revenue',       [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/customers',     [ReportController::class, 'customers'])->name('customers');
        Route::get('/performance',   [ReportController::class, 'performance'])->name('performance');
        Route::get('/support',       [ReportController::class, 'support'])->name('support');
    });

    // ── Users ─────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/status',               [UserController::class, 'updateStatus'])->name('users.status');
        Route::post('/users/{user}/role',                 [UserController::class, 'updateRole'])->name('users.role');
    });

    // ── Departments ───────────────────────────────────────────────────────────
    Route::resource('departments', DepartmentController::class)->middleware('role:admin');

    // ── Products/Services ─────────────────────────────────────────────────────
    Route::resource('products', ProductController::class)->middleware('role:admin,manager');

    // ── Settings ──────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/',         [SettingController::class, 'index'])->name('index');
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('general');
        Route::post('/smtp',    [SettingController::class, 'updateSmtp'])->name('smtp');
    });

    // ── Audit Logs ────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/audit-logs',  [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{log}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });
});
