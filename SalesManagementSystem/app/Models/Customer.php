<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'customers';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PROSPECT = 'prospect';
    const STATUS_CHURNED  = 'churned';

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'website', 'industry',
        'address', 'city', 'country',
        'assigned_to',   // user _id
        'created_by',    // user _id
        'status',
        'source',
        'notes',
        'tags',          // array
        'annual_revenue',
        'employee_count',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    // Scopes
    public function scopeActive($query)      { return $query->where('status', self::STATUS_ACTIVE); }
    public function scopeAssignedTo($query, $userId) { return $query->where('assigned_to', $userId); }
}
