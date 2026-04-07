<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CustomizationChat;
use App\Models\CustomizationRequest;
use App\Services\BunnyStorageService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private BunnyStorageService $bunny) {}

    public function show(CustomizationRequest $customizationRequest)
    {
        $this->authorizeSso($customizationRequest);

        $chats  = $customizationRequest->chats()->get();
        $lastId = $chats->last()?->id ?? 0;

        CustomizationChat::where('request_id', $customizationRequest->id)
            ->where('sender_type', 'portal_user')
            ->where('read_by_user', false)
            ->update(['read_by_user' => true]);

        $customizationRequest->update(['user_alert' => true]);

        return view('user.chat.show', compact('customizationRequest', 'chats', 'lastId'));
    }

    public function store(Request $request, CustomizationRequest $customizationRequest)
    {
        $this->authorizeSso($customizationRequest);

        $request->validate([
            'message' => 'required_without:file|nullable|string',
            'file'    => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
        ]);

        $ssoUser = session('auth_user');

        $chat = new CustomizationChat([
            'request_id'   => $customizationRequest->id,
            'sender_type'  => 'user',
            'sender_id'    => $ssoUser['user_id'],
            'sender_name'  => $ssoUser['name'] ?? $ssoUser['email'],
            'message'      => $request->message,
            'read_by_user' => true,
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

        return response()->json(['success' => true]);
    }

    private function getFileType(string $ext): string
    {
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) return 'image';
        if ($ext === 'pdf') return 'pdf';
        return 'document';
    }

    private function authorizeSso(CustomizationRequest $request): void
    {
        if ($request->user_id != session('auth_user.user_id')) {
            abort(403);
        }
    }
}
