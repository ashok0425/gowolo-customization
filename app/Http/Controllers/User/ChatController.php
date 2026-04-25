<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CustomizationChat;
use App\Models\CustomizationRequest;
use App\Models\PortalNotification;
use App\Services\BunnyStorageService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private BunnyStorageService $bunny) {}

    public function show(string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSso($customizationRequest);

        $chats  = $customizationRequest->chats()->with(['replyTo', 'ssoUser', 'portalUser'])->get();
        $lastId = $chats->last()?->id ?? 0;

        CustomizationChat::where('request_id', $customizationRequest->id)
            ->where('sender_type', 'portal_user')
            ->where('read_by_user', false)
            ->update(['read_by_user' => true]);

        $customizationRequest->update(['user_alert' => true]);

        return view('user.chat.show', compact('customizationRequest', 'chats', 'lastId'));
    }

    public function store(Request $request, string $cuid)
    {
        $customizationRequest = CustomizationRequest::where('cuid', $cuid)->firstOrFail();
        $this->authorizeSso($customizationRequest);

        $request->validate([
            'message'     => 'required_without:files|nullable|string',
            'files'       => 'nullable|array|max:10',
            'files.*'     => 'file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:10240',
            'reply_to_id' => 'nullable|integer|exists:customization_chats,id',
        ], [
            'files.*.mimes' => 'Only image (JPEG, PNG, GIF, WEBP), PDF, and DOC/DOCX files are allowed.',
            'files.*.max'   => 'Each file must be less than 10 MB.',
            'files.max'     => 'You can upload a maximum of 10 files at once.',
        ]);

        $ssoUser = session('auth_user');
        $files   = $request->file('files', []);
        $chats   = [];

        // First chat gets the message text + first file (if any)
        $chat = new CustomizationChat([
            'request_id'   => $customizationRequest->id,
            'sender_type'  => 'user',
            'sender_id'    => $ssoUser['user_id'],
            'sender_name'  => $ssoUser['name'] ?? $ssoUser['email'],
            'message'      => $request->message,
            'reply_to_id'  => $request->reply_to_id,
            'read_by_user' => true,
        ]);

        if (!empty($files)) {
            $this->attachFile($chat, $files[0]);
        }

        $chat->save();
        $chats[] = $chat;

        // Additional files get their own chat rows (no message text)
        for ($i = 1; $i < count($files); $i++) {
            $extraChat = new CustomizationChat([
                'request_id'   => $customizationRequest->id,
                'sender_type'  => 'user',
                'sender_id'    => $ssoUser['user_id'],
                'sender_name'  => $ssoUser['name'] ?? $ssoUser['email'],
                'read_by_user' => true,
            ]);
            $this->attachFile($extraChat, $files[$i]);
            $extraChat->save();
            $chats[] = $extraChat;
        }

        // Notify staff about the new message from user
        PortalNotification::notifyNewChat(
            $customizationRequest,
            $ssoUser['name'] ?? $ssoUser['email'],
            'staff',
            null  // broadcast to all staff
        );

        return response()->json([
            'success' => true,
            'chats'   => array_map(fn ($c) => $this->formatChat($c), $chats),
        ]);
    }

    private function attachFile(CustomizationChat $chat, \Illuminate\Http\UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileType  = $this->getFileType($extension);

        if ($this->bunny->isConfigured()) {
            $chat->bunny_path        = $this->bunny->upload($file, 'chat/' . $fileType . 's');
            $chat->bunny_synced      = true;
        } else {
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path('uploads/chat'), $filename);
            $chat->local_path        = '/uploads/chat/' . $filename;
        }
        $chat->file_type         = $fileType;
        $chat->original_filename = $file->getClientOriginalName();
    }

    private function getFileType(string $ext): string
    {
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'image';
        if ($ext === 'pdf') return 'pdf';
        return 'document';
    }

    private function formatChat(CustomizationChat $chat): array
    {
        $chat->loadMissing(['replyTo', 'ssoUser', 'portalUser']);

        $avatarUrl = null;
        $dashboardBaseUrl = rtrim(config('services.dashboardv2.base_url', 'https://dashboard.gowologlobal.com'), '/');
        if ($chat->sender_type === 'user' && $chat->ssoUser && $chat->ssoUser->profile_pic) {
            $pic = $chat->ssoUser->profile_pic;
            $avatarUrl = preg_match('#^https?://#i', $pic) ? $pic : $dashboardBaseUrl . '/' . ltrim($pic, '/');
        } elseif ($chat->sender_type === 'portal_user' && $chat->portalUser && $chat->portalUser->profile_photo) {
            $avatarUrl = asset($chat->portalUser->profile_photo);
        }

        return [
            'id'                => $chat->id,
            'sender_type'       => $chat->sender_type,
            'sender_name'       => $chat->sender_name,
            'avatar_url'        => $avatarUrl,
            'message'           => $chat->message,
            'reply_to_id'       => $chat->reply_to_id,
            'reply_sender'      => $chat->replyTo?->sender_name,
            'reply_text'        => $chat->replyTo ? \Str::limit(strip_tags($chat->replyTo->message), 40) : null,
            'file_type'         => $chat->file_type,
            'file_url'          => $chat->bunny_path
                ? app(BunnyStorageService::class)->signedUrl($chat->bunny_path)
                : ($chat->local_path ? asset($chat->local_path) : null),
            'original_filename' => $chat->original_filename,
            'created_at'        => $chat->created_at->format('M d, Y H:i'),
        ];
    }

    private function authorizeSso(CustomizationRequest $request): void
    {
        if ($request->user_id != session('auth_user.user_id')) {
            abort(403);
        }
    }
}
