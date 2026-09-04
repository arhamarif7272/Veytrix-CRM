@extends('layouts.auth')

@section('title', 'Customer Registration')

@section('content')
<div class="auth-form-section">
    <div class="text-center mb-3">
        <h2 class="auth-form-title">Create an Account</h2>
        <p class="auth-form-subtitle">Register for instant access to the Veytrix Customer Portal</p>
    </div>

    <!-- Security & Role Policy Banner -->
    <div class="p-2 px-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" 
         style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25);">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fas fa-user-tag me-1"></i> Customer</span>
            <small class="text-secondary fw-semibold" style="font-size: 11.5px;">Default Assigned Role</small>
        </div>
        <small class="text-muted text-end" style="font-size: 11px;" title="Staff roles (Sales, Manager, Support) require Administrator assignment.">
            <i class="fas fa-shield-alt text-primary me-1"></i>Admin Verified
        </small>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-sm mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Full Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="Alex Mercer"
                       required
                       autofocus
                       autocomplete="name">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="alex@enterprise.com"
                       required
                       autocomplete="email">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Company & Phone Row -->
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label for="company" class="form-label">Company / Org <span class="text-muted small">(Optional)</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input type="text"
                           id="company"
                           name="company"
                           class="form-control @error('company') is-invalid @enderror"
                           value="{{ old('company') }}"
                           placeholder="Acme Corp">
                </div>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number <span class="text-muted small">(Optional)</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text"
                           id="phone"
                           name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}"
                           placeholder="+1 (555) 019-283">
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="At least 8 characters"
                       required
                       autocomplete="new-password">
                <button type="button" class="input-group-text btn-eye" id="togglePassword">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Re-enter password"
                       required
                       autocomplete="new-password">
                <button type="button" class="input-group-text btn-eye" id="toggleConfirmPassword">
                    <i class="fas fa-eye" id="eyeConfirmIcon"></i>
                </button>
            </div>
        </div>

        <!-- Terms / Info Note -->
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="terms" required checked>
            <label class="form-check-label text-muted small" for="terms">
                I agree to the Customer Terms of Service and acknowledge role access is set to Customer.
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-auth w-100" id="registerBtn">
            <span class="btn-text"><i class="fas fa-user-check me-2"></i>Create Customer Account</span>
            <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Creating Account...</span>
        </button>
    </form>

    <!-- Switch to Sign In -->
    <div class="text-center mt-4 pt-3 border-top">
        <p class="text-muted small mb-0">
            Already have an account? 
            <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Sign In here</a>
        </p>
    </div>

    <!-- Admin Provisioning Note -->
    <div class="mt-3 p-2 bg-light rounded text-center">
        <small class="text-muted" style="font-size: 11.5px;">
            <i class="fas fa-info-circle text-info me-1"></i>
            Need Staff or Manager access? Register your account first, then request role promotion from your CRM Administrator.
        </small>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    const setupEyeToggle = (btnId, inputId, iconId) => {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        btn.addEventListener('click', function() {
            const pwd = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    };

    setupEyeToggle('togglePassword', 'password', 'eyeIcon');
    setupEyeToggle('toggleConfirmPassword', 'password_confirmation', 'eyeConfirmIcon');

    // Form loading state
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('registerBtn');
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
        btn.disabled = true;
    });
</script>
@endpush
