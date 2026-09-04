<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Veytrix</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #334155;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 540px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                    <!-- Header with Logo -->
                    <tr>
                        <td align="center" style="padding: 35px 30px 20px; background-color: #ffffff; text-align: center;">
                            @php
                                $logoPath = public_path('images/logo.png');
                                if (isset($message) && method_exists($message, 'embed') && file_exists($logoPath)) {
                                    $logoSrc = $message->embed($logoPath);
                                } elseif (file_exists($logoPath)) {
                                    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                                } else {
                                    $logoSrc = asset('images/logo.png');
                                }
                            @endphp
                            <div style="margin: 0 auto 12px; text-align: center;">
                                <img src="{{ $logoSrc }}" alt="Veytrix Logo" width="68" height="68" style="width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 2.5px solid #10b981; display: inline-block; vertical-align: middle;">
                            </div>
                            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">Veytrix</h1>
                            <p style="margin: 4px 0 0; font-size: 11.5px; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Enterprise Customer Relationship &amp; Workflow Management</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 10px 35px 30px; font-size: 15px; line-height: 1.6; color: #334155;">
                            <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 15px;">Hello {{ $user->name ?? 'there' }},</h2>
                            <p style="margin-bottom: 20px;">You are receiving this email because we received a password reset request for your Veytrix account.</p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #10b981; color: #ffffff !important; text-decoration: none; font-weight: 600; font-size: 15px; padding: 13px 32px; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.35);">
                                    Reset Password
                                </a>
                            </div>

                            <p style="font-size: 13.5px; color: #64748b; margin-bottom: 15px;">
                                <strong style="color: #ef4444;"><i style="margin-right: 4px;">&#9201;</i> Security Notice:</strong> This password reset link will expire in <strong>{{ $expire ?? 3 }} minutes</strong> (3 min).
                            </p>
                            <p style="font-size: 13.5px; color: #64748b; margin-bottom: 25px;">If you did not request a password reset, no further action is required.</p>
                            
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">
                            
                            <p style="font-size: 12px; color: #94a3b8; word-break: break-all; margin: 0;">
                                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
                                <a href="{{ $resetUrl }}" style="color: #10b981; text-decoration: underline;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 4px;">&copy; {{ date('Y') }} Veytrix. All rights reserved.</p>
                            <p style="margin: 0; font-size: 11px; color: #cbd5e1;">Enterprise Customer Relationship &amp; Workflow Management System</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
