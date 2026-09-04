@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="auth-form-section">
    <h2 class="auth-form-title">Welcome back</h2>
    <p class="auth-form-subtitle">Sign in to your account to continue</p>

    @if ($errors->any())
    <div class="alert alert-danger alert-sm mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ $errors->first() }}
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-sm mb-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="admin@crm360.com"
                       required
                       autofocus
                       autocomplete="email">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required
                       autocomplete="current-password">
                <button type="button" class="input-group-text btn-eye" id="togglePassword">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remember + Forgot -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary btn-auth w-100" id="loginBtn">
            <span class="btn-text"><i class="fas fa-sign-in-alt me-2"></i>Sign In</span>
            <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Signing in...</span>
        </button>

        <!-- Sign Up Link -->
        <div class="text-center mt-3 pt-2">
            <p class="text-muted small mb-0">
                Don't have an account? 
                <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">
                    <i class="fas fa-user-plus me-1"></i>Create Customer Account
                </a>
            </p>
        </div>
    </form>

    <!-- Demo credentials hint -->
    <div class="demo-credentials mt-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted small fw-bold text-uppercase"><i class="fas fa-key text-warning me-1"></i> Quick Demo Login</span>
            <small class="text-muted">Click to instant sign-in</small>
        </div>
        <div class="demo-grid demo-grid-5">
            <button type="button" class="demo-btn" onclick="fillCredentials('admin@crm360.com','Admin@123')">
                <i class="fas fa-shield-alt text-primary"></i> Admin
            </button>
            <button type="button" class="demo-btn" onclick="fillCredentials('manager@crm360.com','Manager@123')">
                <i class="fas fa-users-cog text-info"></i> Manager
            </button>
            <button type="button" class="demo-btn" onclick="fillCredentials('sales@crm360.com','Sales@123')">
                <i class="fas fa-chart-line text-success"></i> Sales
            </button>
            <button type="button" class="demo-btn" onclick="fillCredentials('support@crm360.com','Support@123')">
                <i class="fas fa-headset text-warning"></i> Support
            </button>
            <button type="button" class="demo-btn demo-btn-customer" onclick="fillCredentials('customer@crm360.com','Customer@123')">
                <i class="fas fa-user-tie text-danger"></i> Customer Portal (Alex Mercer)
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Form loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');
        btn.disabled = true;
    });

    // Fill demo credentials
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        document.getElementById('loginForm').submit();
    }
</script>
@endpush
