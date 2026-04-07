<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationChat;
use App\Models\CustomizationRequest;
use App\Services\ActivityLogService;
use App\Services\BunnyStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(
        private BunnyStorageService $bunny,
        private ActivityLogService $logger
    ) {}

    public function show(CustomizationRequest $customizationRequest)
    {
        $chats = $customizationRequest->chats()->get();
        $lastId = $chats->last()?->id ?? 0;

        // Mark unread messages as read by staff
        CustomizationChat::where('request_id', $customizationRequest->id)
            ->where('sender_type', 'user')
            ->where('read_by_staff', false)
            ->update(['read_by_staff' => true]);

        return view('admin.chat.show', compact('customizationRequest', 'chats', 'lastId'));
    }

    public function store(Request $request, CustomizationRequest $customizationRequest)
    {
        $request->validate([
            'message' => 'required_without:file|nullable|string',
            'file'    => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,mp4|max:20480',
        ]);

        $user = Auth::guard('portal')->user();

        $chat = new CustomizationChat([
            'request_id'  => $customizationRequest->id,
            'sender_type' => 'portal_user',
            'sender_id'   => $user->id,
            'sender_name' => $user->full_name,
            'message'     => $request->message,
            'read_by_staff' => true,
        ]);

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $fileType  = $this->getFileType($extension);

            if ($this->bunny->isConfigured()) {
                $bunnyPath = $this->bunny->upload($file, 'chat/' . $fileType . 's');
                $chat->bunny_path        = $bunnyPath;
                $chat->bunny_synced      = true;
                $chat->file_type         = $fileType;
                $chat->original_filename = $file->getClientOriginalName();
            } else {
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $file->move(public_path('uploads/chat'), $filename);
                $chat->local_path        = '/uploads/chat/' . $filename;
                $chat->file_type         = $fileType;
                $chat->original_filename = $file->getClientOriginalName();
            }
        }

        $chat->save();

        // Mark user-side as unread so they get notified
        $customizationRequest->update(['user_alert' => false]);

        $this->logger->log('chat_sent', $customizationRequest->id, [], [], 'Staff sent message', $request);

        return response()->json(['success' => true, 'chat' => $this->formatChat($chat)]);
    }

    private function getFileType(string $ext): string
    {
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'image';
        if ($ext === 'pdf') return 'pdf';
        if (in_array($ext, ['mp4', 'webm'])) return 'video';
        if (in_array($ext, ['doc', 'docx'])) return 'document';
        return 'unknown';
    }

    private function formatChat(CustomizationChat $chat): array
    {
        return [
            'id'          => $chat->id,
            'sender_type' => $chat->sender_type,
            'sender_name' => $chat->sender_name,
            'message'     => $chat->message,
            'file_type'   => $chat->file_type,
            'file_url'    => $chat->bunny_path
                ? app(BunnyStorageService::class)->signedUrl($chat->bunny_path)
                : ($chat->local_path ? asset($chat->local_path) : null),
            'original_filename' => $chat->original_filename,
            'created_at'  => $chat->created_at->format('M d, Y H:i'),
        ];
    }
}
