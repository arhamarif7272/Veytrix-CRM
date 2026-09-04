<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Activity extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'activities';

    const TYPE_NOTE      = 'note';
    const TYPE_CALL      = 'call';
    const TYPE_MEETING   = 'meeting';
    const TYPE_EMAIL     = 'email';
    const TYPE_STATUS    = 'status_change';
    const TYPE_ASSIGNED  = 'assignment';
    const TYPE_CREATED   = 'created';
    const TYPE_CONVERTED = 'converted';

    protected $fillable = [
        'type', 'subject', 'description',
        'related_type',     // 'lead'|'deal'|'customer'|'ticket'
        'related_id',
        'performed_by',     // user _id
        'performed_by_name',
        'occurred_at',
        'metadata',         // array of extra data
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'metadata'    => 'array',
        ];
    }

    public function scopeForRecord($query, string $type, string $id)
    {
        return $query->where('related_type', $type)->where('related_id', $id);
    }
}
