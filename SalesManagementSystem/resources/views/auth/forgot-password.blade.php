@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('content')
<div class="auth-form-section">
    <h2 class="auth-form-title">Reset Password</h2>
    <p class="auth-form-subtitle">Enter your email to receive a reset link</p>

    @if (session('status'))
    <div class="alert alert-success mb-3"><i class="fas fa-check-circle me-2"></i>{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-auth w-100">
            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
        </button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="auth-link"><i class="fas fa-arrow-left me-1"></i>Back to login</a>
    </div>
</div>
@endsection
