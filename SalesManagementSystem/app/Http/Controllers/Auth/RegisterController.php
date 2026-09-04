<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the public registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle public registration request.
     * Newly registered users are STRICTLY assigned the 'customer' role.
     * Any other roles (admin, manager, sales_executive, support_agent) can ONLY
     * be assigned later by an Administrator.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'company'  => ['nullable', 'string', 'max:150'],
            'phone'    => ['nullable', 'string', 'max:30'],
        ]);

        // Strict default: Public registration can NEVER self-assign internal staff/admin roles.
        $user = User::create([
            'name'              => $validated['name'],
            'email'             => strtolower(trim($validated['email'])),
            'password'          => Hash::make($validated['password']),
            'role'              => User::ROLE_CUSTOMER,
            'status'            => User::STATUS_ACTIVE,
            'phone'             => $validated['phone'] ?? null,
            'email_verified_at' => now(),
            'last_login_at'     => now(),
        ]);

        // Auto-provision Customer profile so the customer portal immediately functions
        $companyName = !empty($validated['company']) ? $validated['company'] : ($validated['name'] . ' Co.');
        Customer::create([
            'name'        => $validated['name'],
            'email'       => strtolower(trim($validated['email'])),
            'phone'       => $validated['phone'] ?? null,
            'company'     => $companyName,
            'status'      => Customer::STATUS_ACTIVE,
            'source'      => 'Online Registration',
            'created_by'  => (string) $user->id,
            'notes'       => 'Self-registered customer account created via customer signup portal.',
        ]);

        // Audit Trail
        AuditService::log(
            action: 'registered',
            module: 'auth',
            entityType: 'User',
            entityId: (string) $user->id,
            entityLabel: $user->name,
            description: "Customer '{$user->name}' ({$user->email}) registered with default 'customer' role."
        );

        // Auto-authenticate customer session
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', "Welcome to Veytrix, {$user->name}! Your customer account has been created.");
    }
}
