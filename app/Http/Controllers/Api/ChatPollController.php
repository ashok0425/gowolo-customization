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
        $msg->loadMissing(['replyTo', 'ssoUser', 'portalUser']);

        // Resolve sender avatar
        $dashboardBaseUrl = rtrim(config('services.dashboardv2.base_url', 'https://dashboard.gowologlobal.com'), '/');
        $avatarUrl = null;
        if ($msg->sender_type === 'user' && $msg->ssoUser && $msg->ssoUser->profile_pic) {
            $pic = $msg->ssoUser->profile_pic;
            $avatarUrl = preg_match('#^https?://#i', $pic) ? $pic : $dashboardBaseUrl . '/' . ltrim($pic, '/');
        } elseif ($msg->sender_type === 'portal_user' && $msg->portalUser && $msg->portalUser->profile_photo) {
            $avatarUrl = asset($msg->portalUser->profile_photo);
        }

        $fileUrl = null;
        if ($msg->bunny_path && $this->bunny->isConfigured()) {
            $fileUrl = $this->bunny->signedUrl($msg->bunny_path);
        } elseif ($msg->local_path) {
            $fileUrl = preg_match('#^https?://#i', $msg->local_path)
                ? $msg->local_path
                : asset($msg->local_path);
        }

        // Normalize legacy file_type — old DB may store different values or null
        $fileType = $msg->file_type;
        if ($fileUrl && !$fileType && $msg->original_filename) {
            $ext = strtolower(pathinfo($msg->original_filename, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $fileType = 'image';
            } elseif ($ext === 'pdf') {
                $fileType = 'pdf';
            } elseif (in_array($ext, ['mp4', 'webm'])) {
                $fileType = 'video';
            } else {
                $fileType = 'document';
            }
        }

        return [
            'id'                => $msg->id,
            'sender_type'       => $msg->sender_type,
            'sender_name'       => $msg->sender_name,
            'avatar_url'        => $avatarUrl,
            'is_mine'           => ($viewer === 'user' && $msg->sender_type === 'user')
                                || ($viewer === 'staff' && $msg->sender_type === 'portal_user'),
            'message'           => $msg->message,
            'reply_to_id'       => $msg->reply_to_id,
            'reply_sender'      => $msg->replyTo?->sender_name,
            'reply_text'        => $msg->replyTo ? \Str::limit(strip_tags($msg->replyTo->message), 40) : null,
            'file_url'          => $fileUrl,
            'file_type'         => $fileType,
            'original_filename' => $msg->original_filename,
            'created_at'        => $msg->created_at->format('m/d/Y h:i A'),
            'time_ago'          => $msg->created_at->diffForHumans(),
        ];
    }
}
