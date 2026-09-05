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
        // Enforce HTTPS for ALL URL helpers (url, route, asset) behind Render's SSL proxy
        $appUrl = (string) config('app.url');
        if (
            $this->app->environment('production') ||
            request()->header('x-forwarded-proto') === 'https' ||
            str_starts_with($appUrl, 'https://')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            // forceRootUrl is required to fix asset() — forceScheme alone does NOT affect asset()
            \Illuminate\Support\Facades\URL::forceRootUrl($appUrl ?: 'https://veytrix-crm.onrender.com');
        }

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
