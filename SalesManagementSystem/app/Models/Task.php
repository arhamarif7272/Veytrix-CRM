<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    const TYPE_CALL       = 'call';
    const TYPE_MEETING    = 'meeting';
    const TYPE_EMAIL      = 'email';
    const TYPE_FOLLOW_UP  = 'follow_up';
    const TYPE_DEMO       = 'demo';
    const TYPE_OTHER      = 'other';

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';

    protected $fillable = [
        'title', 'description', 'type', 'priority', 'status',
        'assigned_to',      // user _id
        'created_by',
        'related_type',     // 'lead'|'deal'|'customer'|'ticket'
        'related_id',
        'due_date',
        'completed_at',
        'reminder_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date'     => 'datetime',
            'completed_at' => 'datetime',
            'reminder_at'  => 'datetime',
        ];
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->due_date && $this->due_date->isPast();
    }

    public function scopeAssignedTo($query, $userId) { return $query->where('assigned_to', $userId); }
    public function scopePending($query)  { return $query->where('status', self::STATUS_PENDING); }
    public function scopeDueToday($query) { return $query->pending()->whereDate('due_date', today()); }
    public function scopeOverdue($query)  { return $query->pending()->where('due_date', '<', now()); }
}
