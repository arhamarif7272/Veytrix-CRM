<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'audit_logs';

    // Timestamps handled by MongoDB, but we also have created_at via Model
    public $timestamps = true;

    protected $fillable = [
        'actor_id',
        'actor_name',
        'actor_role',
        'action',           // 'created'|'updated'|'deleted'|'login'|'assigned'|'status_changed'|etc.
        'module',           // 'users'|'leads'|'deals'|'customers'|etc.
        'entity_type',
        'entity_id',
        'entity_label',
        'old_values',       // array
        'new_values',       // array
        'description',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function scopeForModule($query, string $module) { return $query->where('module', $module); }
    public function scopeForActor($query, string $actorId) { return $query->where('actor_id', $actorId); }
    public function scopeRecent($query, int $limit = 50)   { return $query->orderBy('created_at', 'desc')->limit($limit); }
}
