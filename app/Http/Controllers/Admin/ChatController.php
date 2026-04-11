<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationChat;
use App\Models\CustomizationRequest;
use App\Models\PortalNotification;
use App\Services\BunnyStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct(private BunnyStorageService $bunny) {}

    public function show(CustomizationRequest $customizationRequest)
    {
        $user   = Auth::guard('portal')->user();
        $seeAll = $user->hasPermissionTo('view_all_requests');

        if (!$seeAll) {
            abort_unless(
                $customizationRequest->assigned_tech_id1 == $user->id ||
                $customizationRequest->assigned_tech_id2 == $user->id,
                403
            );
        }

        $chats  = $customizationRequest->chats()->with('replyTo')->get();
        $lastId = $chats->last()?->id ?? 0;

        CustomizationChat::where('request_id', $customizationRequest->id)
            ->where('sender_type', 'user')
            ->where('read_by_staff', false)
            ->update(['read_by_staff' => true]);

        return view('admin.chat.show', compact('customizationRequest', 'chats', 'lastId'));
    }

    public function store(Request $request, CustomizationRequest $customizationRequest)
    {
        $user   = Auth::guard('portal')->user();
        $seeAll = $user->hasPermissionTo('view_all_requests');

        if (!$seeAll) {
            abort_unless(
                $customizationRequest->assigned_tech_id1 == $user->id ||
                $customizationRequest->assigned_tech_id2 == $user->id,
                403
            );
        }

        abort_unless($user->hasPermissionTo('send_chat'), 403);

        $request->validate([
            'message'     => 'required_without:file|nullable|string',
            'file'        => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,mp4|max:20480',
            'reply_to_id' => 'nullable|integer|exists:customization_chats,id',
        ]);

        $chat = new CustomizationChat([
            'request_id'    => $customizationRequest->id,
            'sender_type'   => 'portal_user',
            'sender_id'     => $user->id,
            'sender_name'   => $user->full_name,
            'message'       => $request->message,
            'reply_to_id'   => $request->reply_to_id,
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
        $customizationRequest->update(['user_alert' => false]);

        activity('customization')
            ->causedBy($user)
            ->performedOn($customizationRequest)
            ->log('chat_sent');

        // Notify the user (customer) about the new message from staff
        PortalNotification::notifyNewChat(
            $customizationRequest,
            $user->full_name,
            'user',
            $customizationRequest->user_id
        );

        return response()->json(['success' => true, 'chat' => $this->formatChat($chat)]);
    }

    private function getFileType(string $ext): string
    {
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'image';
        if ($ext === 'pdf') return 'pdf';
        if (in_array($ext, ['mp4', 'webm'])) return 'video';
        return 'document';
    }

    private function formatChat(CustomizationChat $chat): array
    {
        $chat->loadMissing('replyTo');
        return [
            'id'                => $chat->id,
            'sender_type'       => $chat->sender_type,
            'sender_name'       => $chat->sender_name,
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
}
