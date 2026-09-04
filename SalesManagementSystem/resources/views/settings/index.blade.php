@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'Global Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark">System Configuration</h4>
                <p class="text-muted mb-0">Company corporate profile, currency standards, and SMTP email parameters</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <ul class="nav nav-tabs border-bottom-0" id="settingTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#generalTab">
                            <i class="fas fa-sliders-h me-1 text-primary"></i> General & Company
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#smtpTab">
                            <i class="fas fa-envelope-open-text me-1 text-info"></i> SMTP & Mail Delivery
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="settingTabContent">
                    <!-- GENERAL TAB -->
                    <div class="tab-pane fade show active" id="generalTab">
                        <form action="{{ route('settings.general') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Company / Brand Name</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name']->value ?? 'Veytrix' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Support Email</label>
                                    <input type="email" name="company_email" class="form-control" value="{{ $settings['company_email']->value ?? 'support@veytrix.com' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Official Phone Number</label>
                                    <input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone']->value ?? '+1 (800) 555-0199' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Default Currency</label>
                                    <select name="currency" class="form-select">
                                        <option value="USD" {{ ($settings['currency']->value ?? 'USD') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ ($settings['currency']->value ?? '') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP" {{ ($settings['currency']->value ?? '') === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="CAD" {{ ($settings['currency']->value ?? '') === 'CAD' ? 'selected' : '' }}>CAD ($)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Corporate Headquarters Address</label>
                                    <input type="text" name="company_address" class="form-control" value="{{ $settings['company_address']->value ?? '100 Silicon Way, Tech City, CA' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">System Timezone</label>
                                    <select name="timezone" class="form-select">
                                        <option value="UTC" {{ ($settings['timezone']->value ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="America/New_York" {{ ($settings['timezone']->value ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                        <option value="Europe/London" {{ ($settings['timezone']->value ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                        <option value="Asia/Kolkata" {{ ($settings['timezone']->value ?? '') === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save General Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- SMTP TAB -->
                    <div class="tab-pane fade" id="smtpTab">
                        <form action="{{ route('settings.smtp') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mail Host Server</label>
                                    <input type="text" name="mail_host" class="form-control" value="{{ $settings['mail_host']->value ?? 'smtp.mailtrap.io' }}" placeholder="smtp.gmail.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mail Port</label>
                                    <input type="number" name="mail_port" class="form-control" value="{{ $settings['mail_port']->value ?? '587' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username']->value ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password</label>
                                    <input type="password" name="mail_password" class="form-control" value="{{ $settings['mail_password']->value ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Encryption</label>
                                    <select name="mail_encryption" class="form-select">
                                        <option value="tls" {{ ($settings['mail_encryption']->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ ($settings['mail_encryption']->value ?? '') === 'none' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">From Email Address</label>
                                    <input type="email" name="mail_from_address" class="form-control" value="{{ $settings['mail_from_address']->value ?? 'notifications@crm360.com' }}">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save SMTP Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
