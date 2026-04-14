<?php

namespace App\Http\Controllers;

use App\Models\PortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Inbox-style pages for Messages and Notifications (all items, read + unread,
 * paginated). Used by both admin and user sides via two separate routes
 * that share the same view.
 */
class NotificationPageController extends Controller
{
    public function messages(Request $request)
    {
        $query = $this->baseQuery()->where('type', 'new_chat');
        $notifications = $query->paginate(20)->withQueryString();

        return view('inbox.index', [
            'notifications' => $notifications,
            'pageType'      => 'messages',
            'pageTitle'     => 'Messages',
            'emptyIcon'     => 'fa-comment-slash',
            'emptyText'     => 'No messages yet',
        ]);
    }

    public function notifications(Request $request)
    {
        $query = $this->baseQuery()->where('type', '!=', 'new_chat');
        $notifications = $query->paginate(20)->withQueryString();

        return view('inbox.index', [
            'notifications' => $notifications,
            'pageType'      => 'notifications',
            'pageTitle'     => 'Notifications',
            'emptyIcon'     => 'fa-bell-slash',
            'emptyText'     => 'No notifications yet',
        ]);
    }

    /**
     * Scope to the current viewer (staff or SSO user).
     */
    private function baseQuery()
    {
        $query = PortalNotification::orderByDesc('created_at');

        if (Auth::guard('portal')->check()) {
            $userId = Auth::guard('portal')->id();
            $query->where('notifiable_type', 'staff')
                  ->where(function ($q) use ($userId) {
                      $q->whereNull('notifiable_id')->orWhere('notifiable_id', $userId);
                  });
        } elseif (session()->has('auth_user')) {
            $userId = session('auth_user.user_id');
            $query->where('notifiable_type', 'user')
                  ->where('notifiable_id', $userId);
        } else {
            abort(403);
        }

        return $query;
    }

    /**
     * Mark a single notification as read when clicked from the inbox.
     */
    public function markRead(PortalNotification $notification)
    {
        $notification->update(['is_read' => true, 'read_at' => now()]);
        return $notification->action_url
            ? redirect($notification->action_url)
            : back();
    }
}
