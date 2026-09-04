<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — Veytrix</title>
    <meta name="description" content="Veytrix — Enterprise Customer Relationship & Workflow Management System">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link href="{{ asset('css/crm360.css') }}" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Logo / Brand -->
            <div class="auth-brand text-center mb-4">
                <div class="auth-logo-circle mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Veytrix">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
