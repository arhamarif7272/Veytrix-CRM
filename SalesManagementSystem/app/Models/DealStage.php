<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DealStage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'deal_stages';

    protected $fillable = [
        'name', 'order', 'color',
        'is_won', 'is_lost', 'is_default',
        'win_probability',
    ];

    protected function casts(): array
    {
        return [
            'is_won'          => 'boolean',
            'is_lost'         => 'boolean',
            'is_default'      => 'boolean',
            'win_probability' => 'integer',
            'order'           => 'integer',
        ];
    }

    public function scopeOrdered($query) { return $query->orderBy('order', 'asc'); }
}
