<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditService
{
    public static function log(
        string $action,
        string $module,
        string $entityType = '',
        string $entityId = '',
        string $entityLabel = '',
        array $oldValues = [],
        array $newValues = [],
        string $description = '',
        ?string $actorId = null,
        ?string $actorName = null,
        ?string $actorRole = null,
    ): void {
        try {
            $user = auth()->user();

            AuditLog::create([
                'actor_id'    => $actorId   ?? ($user?->id ?? 'system'),
                'actor_name'  => $actorName ?? ($user?->name ?? 'System'),
                'actor_role'  => $actorRole ?? ($user?->role ?? 'system'),
                'action'      => $action,
                'module'      => $module,
                'entity_type' => $entityType,
                'entity_id'   => (string) $entityId,
                'entity_label'=> $entityLabel,
                'old_values'  => $oldValues,
                'new_values'  => $newValues,
                'description' => $description,
                'ip_address'  => request()?->ip(),
                'user_agent'  => request()?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Never let audit log failures break the main workflow
            logger()->error('AuditService failed: ' . $e->getMessage());
        }
    }
}
