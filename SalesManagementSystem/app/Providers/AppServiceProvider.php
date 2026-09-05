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
        // Enforce HTTPS scheme in production or when behind SSL proxy (e.g. Render)
        if (
            $this->app->environment('production') ||
            request()->header('x-forwarded-proto') === 'https' ||
            str_starts_with((string) config('app.url'), 'https://')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
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
