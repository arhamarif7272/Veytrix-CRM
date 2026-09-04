<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Brevo HTTPS API Mail Transport (bypasses Render Free SMTP port 587 block over HTTPS port 443)
        \Illuminate\Support\Facades\Mail::extend('brevo', function (array $config = []) {
            return new \App\Services\BrevoTransport();
        });

        // Custom Password Reset Email with embedded Veytrix circular logo
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Reset Password Notification — Veytrix')
                ->view('emails.reset-password', [
                    'user'     => $notifiable,
                    'resetUrl' => $resetUrl,
                    'expire'   => config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 3),
                ]);
        });
    }
}
