<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Throttle is handled by Laravel's built-in RateLimiter if needed
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Check account status
        if (! $user->isActive()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive or suspended. Please contact an administrator.',
            ]);
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Audit log
        AuditService::log('login', 'auth', 'user', $user->id, $user->name);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditService::log('logout', 'auth', 'user', $user->id, $user->name);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
