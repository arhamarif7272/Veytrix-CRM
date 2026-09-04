<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'name', 'description', 'sku',
        'unit_price', 'unit',
        'category',
        'tax_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'float',
            'tax_rate'   => 'float',
            'is_active'  => 'boolean',
        ];
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
