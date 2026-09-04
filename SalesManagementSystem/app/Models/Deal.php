<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Deal extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'deals';

    const STATUS_OPEN = 'open';
    const STATUS_WON  = 'won';
    const STATUS_LOST = 'lost';

    protected $fillable = [
        'title',
        'customer_id',
        'lead_id',
        'assigned_to',      // user _id
        'created_by',       // user _id
        'stage_id',
        'value',
        'currency',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'status',
        'lost_reason',
        'notes',
        'quotation_id',
    ];

    protected function casts(): array
    {
        return [
            'value'               => 'float',
            'probability'         => 'integer',
            'expected_close_date' => 'datetime',
            'actual_close_date'   => 'datetime',
        ];
    }

    public function isWon(): bool  { return $this->status === self::STATUS_WON; }
    public function isLost(): bool { return $this->status === self::STATUS_LOST; }
    public function isOpen(): bool { return $this->status === self::STATUS_OPEN; }

    public function scopeAssignedTo($query, $userId) { return $query->where('assigned_to', $userId); }
    public function scopeOpen($query)   { return $query->where('status', self::STATUS_OPEN); }
    public function scopeWon($query)    { return $query->where('status', self::STATUS_WON); }
    public function scopeLost($query)   { return $query->where('status', self::STATUS_LOST); }
    public function scopeForCustomer($query, $customerId) { return $query->where('customer_id', $customerId); }
}
