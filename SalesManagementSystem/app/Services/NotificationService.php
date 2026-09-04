<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Send an in-app notification to a user.
     */
    public static function send(
        string $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        string $relatedType = '',
        string $relatedId = '',
        string $actionUrl = ''
    ): void {
        try {
            Notification::create([
                'user_id'      => $userId,
                'type'         => $type,
                'title'        => $title,
                'message'      => $message,
                'data'         => $data,
                'related_type' => $relatedType,
                'related_id'   => $relatedId,
                'action_url'   => $actionUrl,
                'read_at'      => null,
            ]);
        } catch (\Throwable $e) {
            logger()->error('NotificationService failed: ' . $e->getMessage());
        }
    }

    /**
     * Get unread count for a user.
     */
    public static function unreadCount(string $userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllRead(string $userId): void
    {
        Notification::forUser($userId)->unread()->update(['read_at' => now()]);
    }
}
