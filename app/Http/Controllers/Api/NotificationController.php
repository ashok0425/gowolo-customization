<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications — fetch unread notifications for the current user.
     */
    public function index(Request $request)
    {
        $query = PortalNotification::where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(20);

        if (Auth::guard('portal')->check()) {
            // Staff: see notifications addressed to them OR broadcast to all staff
            $userId = Auth::guard('portal')->id();
            $query->where('notifiable_type', 'staff')
                  ->where(fn ($q) => $q->whereNull('notifiable_id')->orWhere('notifiable_id', $userId));
        } elseif (session()->has('auth_user')) {
            // User: see only their own notifications
            $userId = session('auth_user.user_id');
            $query->where('notifiable_type', 'user')
                  ->where('notifiable_id', $userId);
        } else {
            return response()->json(['notifications' => [], 'count' => 0]);
        }

        $notifications = $query->get();

        return response()->json([
            'notifications' => $notifications->map(fn ($n) => [
                'id'           => $n->id,
                'type'         => $n->type,
                'title'        => $n->title,
                'body'         => $n->body,
                'icon'         => $n->icon,
                'action_url'   => $n->action_url,
                'action_label' => $n->action_label,
                'sender_name'  => $n->sender_name,
                'ref_number'   => $n->ref_number,
                'time_ago'     => $n->created_at->diffForHumans(),
            ]),
            'count' => $notifications->count(),
        ]);
    }

    /**
     * POST /api/notifications/{id}/dismiss — mark one as read.
     */
    public function dismiss(PortalNotification $notification)
    {
        $notification->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }

    /**
     * POST /api/notifications/clear — mark all as read for current user.
     */
    public function clearAll(Request $request)
    {
        $query = PortalNotification::where('is_read', false);

        if (Auth::guard('portal')->check()) {
            $userId = Auth::guard('portal')->id();
            $query->where('notifiable_type', 'staff')
                  ->where(fn ($q) => $q->whereNull('notifiable_id')->orWhere('notifiable_id', $userId));
        } elseif (session()->has('auth_user')) {
            $userId = session('auth_user.user_id');
            $query->where('notifiable_type', 'user')
                  ->where('notifiable_id', $userId);
        }

        $query->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
