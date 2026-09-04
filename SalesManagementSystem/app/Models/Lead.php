<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Lead extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'leads';

    const STATUS_NEW         = 'new';
    const STATUS_CONTACTED   = 'contacted';
    const STATUS_QUALIFIED   = 'qualified';
    const STATUS_UNQUALIFIED = 'unqualified';
    const STATUS_CONVERTED   = 'converted';
    const STATUS_LOST        = 'lost';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';

    const SOURCE_WEBSITE    = 'website';
    const SOURCE_REFERRAL   = 'referral';
    const SOURCE_SOCIAL     = 'social_media';
    const SOURCE_EMAIL      = 'email_campaign';
    const SOURCE_COLD_CALL  = 'cold_call';
    const SOURCE_EVENT      = 'event';
    const SOURCE_OTHER      = 'other';

    protected $fillable = [
        'title', 'first_name', 'last_name', 'email', 'phone', 'company',
        'source', 'priority', 'status',
        'assigned_to',      // user _id
        'created_by',       // user _id
        'customer_id',      // set on conversion
        'deal_id',          // set on conversion
        'follow_up_date',
        'notes',
        'value_estimate',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'datetime',
            'converted_at'   => 'datetime',
        ];
    }

    public function isConverted(): bool { return $this->status === self::STATUS_CONVERTED; }
    public function isOverdue(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isPast()
            && ! in_array($this->status, [self::STATUS_CONVERTED, self::STATUS_LOST]);
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function scopeAssignedTo($query, $userId) { return $query->where('assigned_to', $userId); }
    public function scopeOpen($query) { return $query->whereNotIn('status', [self::STATUS_CONVERTED, self::STATUS_LOST]); }
    public function scopeOverdue($query) { return $query->where('follow_up_date', '<', now())->open(); }
}
