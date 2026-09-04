<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',             // array
        'related_type',
        'related_id',
        'read_at',
        'action_url',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function isRead(): bool    { return $this->read_at !== null; }
    public function isUnread(): bool  { return $this->read_at === null; }

    public function markRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function scopeUnread($query)         { return $query->whereNull('read_at'); }
    public function scopeForUser($query, $userId) { return $query->where('user_id', $userId); }
}
