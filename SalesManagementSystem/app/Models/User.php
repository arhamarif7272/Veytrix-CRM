<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, Notifiable, CanResetPassword;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    // Roles constants
    const ROLE_ADMIN           = 'admin';
    const ROLE_MANAGER         = 'manager';
    const ROLE_SALES_EXECUTIVE = 'sales_executive';
    const ROLE_SUPPORT_AGENT   = 'support_agent';
    const ROLE_CUSTOMER        = 'customer';

    // Status constants
    const STATUS_ACTIVE    = 'active';
    const STATUS_INACTIVE  = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'status',
        'phone',
        'avatar',
        'address',
        'last_login_at',
        'email_verified_at',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'settings'          => 'array',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isSalesExecutive(): bool
    {
        return $this->role === self::ROLE_SALES_EXECUTIVE;
    }

    public function isSupportAgent(): bool
    {
        return $this->role === self::ROLE_SUPPORT_AGENT;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role, $roles);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_SALES_EXECUTIVE,
            self::ROLE_SUPPORT_AGENT,
        ]);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN           => 'Administrator',
            self::ROLE_MANAGER         => 'Manager',
            self::ROLE_SALES_EXECUTIVE => 'Sales Executive',
            self::ROLE_SUPPORT_AGENT   => 'Support Agent',
            self::ROLE_CUSTOMER        => 'Customer',
            default                    => ucfirst($this->role ?? 'Unknown'),
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $name    = urlencode($this->name ?? 'U');
        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=fff&size=128&bold=true";
    }
}
