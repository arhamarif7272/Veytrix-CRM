# CRM360 — Enterprise CRM System Implementation Plan

A professional, portfolio-ready Enterprise CRM built with **Laravel 12 + MongoDB + Blade.php + Bootstrap**.

## Project Overview

**Goal**: Build a full-stack enterprise CRM system (CRM360) with 5 roles, complete business workflows, dashboards, reports, API endpoints, and SMTP email — deployed on Render free tier.

**Core Workflow**: Lead → Customer → Deal → Quotation → Invoice → Payment Tracking → Support → Customer Activity History

**Location**: `d:\Laravel API's\CRM Sales Management\SalesManagementSystem\`

---

## Open Questions

> [!IMPORTANT]
> **MongoDB Provider**: The plan calls for MongoDB. We'll use `mongodb/laravel-mongodb` (the official Laravel MongoDB ODM by MongoDB Inc.). Do you have a MongoDB instance available? Options:
> - **MongoDB Atlas Free Tier** (recommended for Render deployment)
> - **Local MongoDB** (for development only)
> - **Specify your connection URI**

> [!IMPORTANT]
> **MongoDB Atlas Connection URI**: Please provide your MongoDB Atlas URI or let me know if you'd like setup guidance. Format: `mongodb+srv://user:pass@cluster.mongodb.net/crm360`

> [!IMPORTANT]
> **SMTP Credentials**: For email functionality, do you have an SMTP provider? Options: Gmail, Mailgun, Mailtrap (for testing), or others.

> [!NOTE]
> **Starter Admin Credentials**: I'll seed a default admin user: `admin@crm360.com` / `Admin@123`. Confirm or suggest different defaults.

---

## Proposed Changes

### Phase 1 — Foundation & MongoDB Setup

#### [MODIFY] `composer.json`
- Add `mongodb/laravel-mongodb` package (official ODM)
- Add `barryvdh/laravel-dompdf` for PDF generation (quotations/invoices)

#### [MODIFY] `.env`
- Reconfigure for MongoDB connection
- Set `APP_NAME=CRM360`
- Configure session driver to `file` (safe for free tier)
- Configure cache to `file`
- Queue connection to `sync` (no Redis needed)

#### [NEW] `config/mongodb.php`
- MongoDB connection config

#### [MODIFY] `config/app.php`
- Register MongoDB service provider

#### [NEW] `config/session.php` adjustments
- Use file-based sessions for Render compatibility

---

### Phase 2 — Authentication System

#### [NEW] `app/Http/Controllers/Auth/LoginController.php`
#### [NEW] `app/Http/Controllers/Auth/RegisterController.php` *(Admin only)*
#### [NEW] `app/Http/Controllers/Auth/ForgotPasswordController.php`
#### [NEW] `app/Http/Controllers/Auth/ResetPasswordController.php`
#### [NEW] `app/Http/Controllers/Auth/ProfileController.php`
#### [NEW] `resources/views/auth/login.blade.php`
#### [NEW] `resources/views/auth/forgot-password.blade.php`
#### [NEW] `resources/views/auth/reset-password.blade.php`
#### [NEW] `resources/views/auth/profile.blade.php`

---

### Phase 3 — Roles & Middleware

#### [NEW] `app/Models/User.php` (MongoDB-backed)
- Fields: name, email, password, role, department_id, status, phone, avatar, last_login, settings

#### [NEW] `app/Models/Role.php`
- Roles enum: admin, manager, sales_executive, support_agent, customer

#### [NEW] `app/Http/Middleware/RoleMiddleware.php`
- Role-aware route protection

#### [NEW] `app/Http/Middleware/CheckAccountStatus.php`
- Block inactive/suspended accounts

#### [MODIFY] `bootstrap/app.php`
- Register custom middleware aliases: `role`, `checkStatus`

---

### Phase 4 — MongoDB Models (Core Collections)

#### [NEW] `app/Models/Customer.php`
- Fields: name, email, phone, company, address, assigned_to, status, notes, source, created_by, tags

#### [NEW] `app/Models/Contact.php`
- Fields: customer_id, name, email, phone, position, is_primary

#### [NEW] `app/Models/Lead.php`
- Fields: title, source, priority, status, assigned_to, customer_id, deal_id, follow_up_date, notes, converted_at, created_by

#### [NEW] `app/Models/Deal.php`
- Fields: title, customer_id, assigned_to, stage_id, value, probability, expected_close_date, status, notes, quotation_id

#### [NEW] `app/Models/DealStage.php`
- Fields: name, order, color, is_default, is_won, is_lost

#### [NEW] `app/Models/Task.php`
- Fields: title, type (call/meeting/email/follow_up), due_date, assigned_to, related_type, related_id, status, notes, priority

#### [NEW] `app/Models/Activity.php`
- Fields: type, subject, description, related_type, related_id, performed_by, occurred_at

#### [NEW] `app/Models/Quotation.php`
- Fields: customer_id, deal_id, number, status, items[], subtotal, tax, discount, total, notes, valid_until, sent_at

#### [NEW] `app/Models/Invoice.php`
- Fields: customer_id, quotation_id, number, status (draft/sent/paid/overdue/cancelled), items[], subtotal, tax, discount, total, due_date, paid_at, payment_method, notes

#### [NEW] `app/Models/Ticket.php`
- Fields: customer_id, assigned_to, title, description, priority, status, category, resolved_at, created_by

#### [NEW] `app/Models/TicketMessage.php`
- Fields: ticket_id, sender_id, sender_role, message, attachments[], created_at

#### [NEW] `app/Models/Notification.php`
- Fields: user_id, type, title, message, data, read_at, related_type, related_id

#### [NEW] `app/Models/AuditLog.php`
- Fields: actor_id, actor_name, action, module, entity_type, entity_id, old_values, new_values, ip_address, user_agent, created_at

#### [NEW] `app/Models/Department.php`
- Fields: name, description, manager_id, created_at

#### [NEW] `app/Models/Product.php`
- Fields: name, description, unit_price, unit, category, is_active, sku

#### [NEW] `app/Models/Setting.php`
- Fields: group, key, value, type, label, description

---

### Phase 5 — Services Layer

#### [NEW] `app/Services/LeadService.php`
- createLead, updateLead, assignLead, convertLead (Lead → Customer + Deal), getLeadStats

#### [NEW] `app/Services/DealService.php`
- createDeal, moveDealStage, closeDeal (won/lost), getDealStats, pipelineData

#### [NEW] `app/Services/QuotationService.php`
- createQuotation, calculateTotals, sendQuotation (email), convertToInvoice

#### [NEW] `app/Services/InvoiceService.php`
- createInvoice, markPaid, recordPayment, generatePdfData, getPaymentStats

#### [NEW] `app/Services/TicketService.php`
- createTicket, assignTicket, addMessage, updateStatus, escalate, getTicketStats

#### [NEW] `app/Services/DashboardService.php`
- getAdminStats, getManagerStats, getSalesStats, getSupportStats, getCustomerStats

#### [NEW] `app/Services/ReportService.php`
- leadConversionReport, salesReport, revenueReport, employeePerformanceReport, supportMetricsReport

#### [NEW] `app/Services/AuditService.php`
- log(actor, action, module, entityType, entityId, oldValues, newValues)

#### [NEW] `app/Services/NotificationService.php`
- send(userId, type, title, message, data), markRead, getUnread

---

### Phase 6 — Controllers

#### [NEW] `app/Http/Controllers/DashboardController.php`
#### [NEW] `app/Http/Controllers/CustomerController.php`
#### [NEW] `app/Http/Controllers/LeadController.php`
#### [NEW] `app/Http/Controllers/DealController.php`
#### [NEW] `app/Http/Controllers/TaskController.php`
#### [NEW] `app/Http/Controllers/ActivityController.php`
#### [NEW] `app/Http/Controllers/QuotationController.php`
#### [NEW] `app/Http/Controllers/InvoiceController.php`
#### [NEW] `app/Http/Controllers/TicketController.php`
#### [NEW] `app/Http/Controllers/ReportController.php`
#### [NEW] `app/Http/Controllers/UserController.php`
#### [NEW] `app/Http/Controllers/SettingController.php`
#### [NEW] `app/Http/Controllers/DepartmentController.php`
#### [NEW] `app/Http/Controllers/ProductController.php`
#### [NEW] `app/Http/Controllers/NotificationController.php`
#### [NEW] `app/Http/Controllers/AuditLogController.php`
#### [NEW] `app/Http/Controllers/Api/DashboardApiController.php`
#### [NEW] `app/Http/Controllers/Api/LeadApiController.php`
#### [NEW] `app/Http/Controllers/Api/CustomerApiController.php`
#### [NEW] `app/Http/Controllers/Api/DealApiController.php`
#### [NEW] `app/Http/Controllers/Api/TaskApiController.php`
#### [NEW] `app/Http/Controllers/Api/TicketApiController.php`
#### [NEW] `app/Http/Controllers/Api/NotificationApiController.php`

---

### Phase 7 — Mail Classes

#### [NEW] `app/Mail/WelcomeMail.php`
#### [NEW] `app/Mail/PasswordResetMail.php`
#### [NEW] `app/Mail/LeadAssignedMail.php`
#### [NEW] `app/Mail/QuotationMail.php`
#### [NEW] `app/Mail/InvoiceMail.php`
#### [NEW] `app/Mail/TicketUpdateMail.php`
#### [NEW] `app/Mail/DealStatusMail.php`

---

### Phase 8 — Blade Views

#### Layouts
- `resources/views/layouts/app.blade.php` — Main authenticated layout (sidebar + topnav)
- `resources/views/layouts/auth.blade.php` — Auth layout (centered card)
- `resources/views/layouts/print.blade.php` — Print-friendly layout for quotations/invoices
- `resources/views/components/sidebar.blade.php`
- `resources/views/components/topnav.blade.php`
- `resources/views/components/breadcrumb.blade.php`
- `resources/views/components/kpi-card.blade.php`
- `resources/views/components/alert.blade.php`
- `resources/views/components/pagination.blade.php`
- `resources/views/components/modal.blade.php`

#### Dashboards
- `resources/views/dashboard/admin.blade.php`
- `resources/views/dashboard/manager.blade.php`
- `resources/views/dashboard/sales.blade.php`
- `resources/views/dashboard/support.blade.php`
- `resources/views/dashboard/customer.blade.php`

#### Customers, Leads, Deals, Tasks, Quotations, Invoices, Tickets, Reports, Users, Settings

Each module will have: `index`, `create`, `edit`, `show` views.

---

### Phase 9 — Routes

#### [MODIFY] `routes/web.php`
- Auth routes (login, logout, password reset, profile)
- Dashboard routes (role-dispatched)
- Resource routes for all modules with role middleware

#### [NEW] `routes/api.php`
- API routes for dashboard, leads, customers, deals, tasks, tickets, notifications

---

### Phase 10 — Database Seeders

#### [NEW] `database/seeders/DatabaseSeeder.php`
- Seed roles, deal stages, default admin user, sample settings

#### [NEW] `database/seeders/DealStageSeeder.php`
- Default stages: Prospecting, Qualification, Proposal, Negotiation, Closed Won, Closed Lost

#### [NEW] `database/seeders/SettingSeeder.php`
- Company name, currency, timezone defaults

---

### Phase 11 — Frontend Assets

#### [NEW] `resources/css/app.css` — Custom Bootstrap overrides, CRM theme
#### [NEW] `resources/js/app.js` — Bootstrap init, AJAX helpers, chart setup
#### CDN Assets (via Blade layouts):
- Bootstrap 5.3
- Chart.js (dashboard charts)
- SortableJS (Kanban drag-and-drop)
- Select2 (enhanced dropdowns)
- DataTables (searchable/sortable tables)
- Font Awesome 6 (icons)
- Google Fonts (Inter)
- Flatpickr (date pickers)

---

## Verification Plan

### After Phase 1–3 (Foundation):
```bash
composer install
php artisan key:generate
php artisan serve
```
- Verify MongoDB connection, login page renders, auth flow works

### After Phase 4–6 (Core CRM):
- Test CRUD for customers, leads, deals, tickets
- Verify role middleware blocks unauthorized access

### After Phase 8 (Dashboards):
- Test all 5 dashboard variants render correctly
- Verify charts load with real data

### After Phase 10 (Seeding):
```bash
php artisan db:seed
```
- Login as admin@crm360.com / Admin@123
- Verify all modules accessible

### Final:
- Test all API endpoints return JSON
- Test PDF generation for quotations/invoices
- Test SMTP email delivery
- Test Kanban drag-and-drop for deals

---

## Implementation Sequence

I'll implement the phases in order:

| Phase | Description | Estimated Files |
|-------|-------------|----------------|
| 1 | Foundation: MongoDB, .env, config | ~8 files |
| 2 | Authentication: login, reset, profile | ~12 files |
| 3 | Roles & Middleware | ~6 files |
| 4 | MongoDB Models (16 models) | ~16 files |
| 5 | Services Layer (9 services) | ~9 files |
| 6 | Controllers (20+ controllers) | ~22 files |
| 7 | Mail Classes | ~7 files |
| 8 | Blade Views (50+ views) | ~55 files |
| 9 | Routes (web + api) | ~2 files |
| 10 | Seeders | ~4 files |
| 11 | Frontend Assets | ~5 files |

**Total**: ~146 files across all phases

> [!WARNING]
> This is a **very large project** that will take significant time to build completely. I recommend we proceed phase by phase. After each phase I'll confirm before moving to the next.

> [!NOTE]
> **No Sanctum** — API routes will use session auth via `auth` middleware (web guard works for AJAX calls from Blade).
> **No Tailwind** — Pure Bootstrap 5 + custom CSS.
> **No DB::transaction** — Validate-first, write-second pattern.
> **No Redis** — File-based sessions/cache for Render free tier.
