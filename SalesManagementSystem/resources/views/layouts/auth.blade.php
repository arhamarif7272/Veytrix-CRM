@extends('layouts.master')

@section('body-class', 'auth-body')

@section('layout-content')
<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Logo / Brand -->
        <div class="auth-brand text-center mb-4">
            <div class="auth-logo-circle mb-3" style="width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; overflow: hidden; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.25), 0 0 0 3px rgba(245, 124, 0, 0.3);">
                <img src="/images/logo.png" alt="Veytrix" width="60" height="60" style="width: 60px; height: 60px; max-width: 60px; max-height: 60px; object-fit: contain; border-radius: 50%; display: block;">
            </div>
            <h1 class="auth-title" style="font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">Veytrix</h1>
            <p class="auth-subtitle">Enterprise Customer Relationship &amp; Workflow Management System</p>
        </div>

        @yield('content')
    </div>

    <div class="auth-footer text-center mt-3 pb-3">
        <p class="auth-footer-text mb-0">
            &copy; {{ date('Y') }} Veytrix &bull; Enterprise Customer Relationship &amp; Workflow Management System
        </p>
    </div>
</div>
@endsection
