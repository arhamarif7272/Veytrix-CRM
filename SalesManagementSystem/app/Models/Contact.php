<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Contact extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contacts';

    protected $fillable = [
        'customer_id',
        'name', 'email', 'phone',
        'position', 'department',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
