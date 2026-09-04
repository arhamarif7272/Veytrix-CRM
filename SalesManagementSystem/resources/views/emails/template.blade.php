<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Veytrix Notification' }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; }
        .email-container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .email-header { background: #ffffff; padding: 25px 30px; text-align: center; border-bottom: 1px solid #e2e8f0; }
        .email-logo { height: 48px; max-width: 200px; object-fit: contain; }
        .email-body { padding: 30px; font-size: 15px; line-height: 1.6; color: #334155; }
        .email-footer { background: #f8fafc; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .btn-action { display: inline-block; padding: 12px 24px; background: #f57c00; color: #ffffff !important; text-decoration: none; font-weight: 600; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header" style="padding: 25px 30px; text-align: center; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
            <a href="{{ config('app.url') }}" target="_blank" style="display: inline-flex; align-items: center; text-decoration: none; justify-content: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Veytrix" style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #10b981; vertical-align: middle;">
                <span style="font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; vertical-align: middle; margin-left: 10px; font-family: Arial, sans-serif;">Veytrix</span>
            </a>
        </div>
        <div class="email-body">
            @yield('content')
        </div>
        <div class="email-footer">
            <p style="margin: 0 0 5px 0;"><strong>Veytrix</strong> &bull; Enterprise Customer Relationship &amp; Workflow Management System</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} Veytrix. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
