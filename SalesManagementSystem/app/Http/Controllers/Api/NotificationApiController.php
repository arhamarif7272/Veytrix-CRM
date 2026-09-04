<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::forUser((string) auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($notifications);
    }

    public function markRead(string $id)
    {
        $notification = Notification::where('_id', $id)
            ->where('user_id', (string) auth()->id())
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        NotificationService::markAllRead((string) auth()->id());
        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = NotificationService::unreadCount((string) auth()->id());
        return response()->json(['unread_count' => $count]);
    }
}
