<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomizationChat;
use App\Models\CustomizationRequest;
use App\Services\BunnyStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatPollController extends Controller
{
    public function __construct(private BunnyStorageService $bunny) {}

    /**
     * Poll for new messages since last_id.
     * Called every 5 seconds by the chat page.
     * GET /api/chat/{requestId}/poll?last_id=X&viewer=user|staff
     */
    public function poll(Request $request, int $requestId)
    {
        $custRequest = CustomizationRequest::findOrFail($requestId);
        $viewer      = $request->query('viewer', 'user'); // 'user' or 'staff'
        $lastId      = (int) $request->query('last_id', 0);

        // Validate access
        if ($viewer === 'user') {
            $ssoUser = session('auth_user');
            if (!$ssoUser || $custRequest->user_id != $ssoUser['user_id']) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($viewer === 'staff') {
            if (!Auth::guard('portal')->check()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            // Technicians can only poll their assigned requests
            $portalUser = Auth::guard('portal')->user();
            if (!$portalUser->hasPermissionTo('view_all_requests')) {
                if ($custRequest->assigned_tech_id1 !== $portalUser->id && $custRequest->assigned_tech_id2 !== $portalUser->id) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }
        }

        // Fetch new messages
        $messages = CustomizationChat::where('request_id', $requestId)
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->get();

        // Mark as read
        if ($viewer === 'user' && $messages->isNotEmpty()) {
            CustomizationChat::where('request_id', $requestId)
                ->where('id', '>', $lastId)
                ->where('sender_type', 'portal_user')
                ->update(['read_by_user' => true]);
        } elseif ($viewer === 'staff' && $messages->isNotEmpty()) {
            CustomizationChat::where('request_id', $requestId)
                ->where('id', '>', $lastId)
                ->where('sender_type', 'user')
                ->update(['read_by_staff' => true]);
        }

        $formatted = $messages->map(fn($msg) => $this->format($msg, $viewer));

        return response()->json([
            'messages'      => $formatted,
            'last_id'       => $messages->last()?->id ?? $lastId,
            'unread_count'  => CustomizationChat::where('request_id', $requestId)
                ->when($viewer === 'user', fn($q) => $q->where('read_by_user', false)->where('sender_type', 'portal_user'))
                ->when($viewer === 'staff', fn($q) => $q->where('read_by_staff', false)->where('sender_type', 'user'))
                ->count(),
        ]);
    }

    private function format(CustomizationChat $msg, string $viewer): array
    {
        $msg->loadMissing('replyTo');

        $fileUrl = null;
        if ($msg->bunny_path && $this->bunny->isConfigured()) {
            $fileUrl = $this->bunny->signedUrl($msg->bunny_path);
        } elseif ($msg->local_path) {
            // Full URLs (legacy files hosted on dashboardv2) are used as-is;
            // relative paths go through asset() to prefix the current domain.
            $fileUrl = preg_match('#^https?://#i', $msg->local_path)
                ? $msg->local_path
                : asset($msg->local_path);
        }

        return [
            'id'                => $msg->id,
            'sender_type'       => $msg->sender_type,
            'sender_name'       => $msg->sender_name,
            'is_mine'           => ($viewer === 'user' && $msg->sender_type === 'user')
                                || ($viewer === 'staff' && $msg->sender_type === 'portal_user'),
            'message'           => $msg->message,
            'reply_to_id'       => $msg->reply_to_id,
            'reply_sender'      => $msg->replyTo?->sender_name,
            'reply_text'        => $msg->replyTo ? \Str::limit(strip_tags($msg->replyTo->message), 40) : null,
            'file_url'          => $fileUrl,
            'file_type'         => $msg->file_type,
            'original_filename' => $msg->original_filename,
            'created_at'        => $msg->created_at->format('m/d/Y h:i A'),
            'time_ago'          => $msg->created_at->diffForHumans(),
        ];
    }
}
