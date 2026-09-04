@extends('layouts.auth')
@section('title', 'Reset Password')
@section('content')
<div class="auth-form-section">
    <h2 class="auth-form-title">Set New Password</h2>
    <p class="auth-form-subtitle">Choose a strong new password</p>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $email) }}" required readonly>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Min. 8 characters" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-auth w-100">
            <i class="fas fa-key me-2"></i>Reset Password
        </button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="auth-link"><i class="fas fa-arrow-left me-1"></i>Back to login</a>
    </div>
</div>
@endsection
