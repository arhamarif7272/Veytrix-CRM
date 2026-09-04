<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tickets';

    const STATUS_OPEN        = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING     = 'waiting_customer';
    const STATUS_RESOLVED    = 'resolved';
    const STATUS_CLOSED      = 'closed';

    const PRIORITY_LOW      = 'low';
    const PRIORITY_MEDIUM   = 'medium';
    const PRIORITY_HIGH     = 'high';
    const PRIORITY_CRITICAL = 'critical';

    const CATEGORY_BILLING  = 'billing';
    const CATEGORY_TECH     = 'technical';
    const CATEGORY_GENERAL  = 'general';
    const CATEGORY_FEATURE  = 'feature_request';
    const CATEGORY_OTHER    = 'other';

    protected $fillable = [
        'ticket_number',
        'title', 'description',
        'customer_id',
        'created_by',
        'assigned_to',      // support agent _id
        'priority', 'status', 'category',
        'resolved_at',
        'closed_at',
        'first_response_at',
        'due_date',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at'       => 'datetime',
            'closed_at'         => 'datetime',
            'first_response_at' => 'datetime',
            'due_date'          => 'datetime',
            'tags'              => 'array',
        ];
    }

    public function isOpen(): bool     { return $this->status === self::STATUS_OPEN; }
    public function isResolved(): bool { return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]); }

    public function scopeOpen($query)         { return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_WAITING]); }
    public function scopeAssignedTo($query, $userId) { return $query->where('assigned_to', $userId); }
    public function scopeForCustomer($query, $customerId) { return $query->where('customer_id', $customerId); }
    public function scopeHighPriority($query) { return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]); }
}
