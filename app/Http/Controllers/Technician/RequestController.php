<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\CustomizationRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct(private ActivityLogService $logger) {}

    public function show(CustomizationRequest $customizationRequest)
    {
        $this->authorizeRequest($customizationRequest);

        $customizationRequest->load(['answers', 'files', 'chats', 'activityLogs']);
        $chats = $customizationRequest->chats()->get();
        $lastId = $chats->last()?->id ?? 0;

        // Mark messages as read
        $customizationRequest->chats()
            ->where('sender_type', 'user')
            ->where('read_by_staff', false)
            ->update(['read_by_staff' => true]);

        return view('technician.requests.show', compact('customizationRequest', 'chats', 'lastId'));
    }

    public function updateStatus(Request $request, CustomizationRequest $customizationRequest)
    {
        $this->authorizeRequest($customizationRequest);

        $request->validate(['status' => 'required|in:1,2']);

        $oldStatus = $customizationRequest->status;

        $data = [
            'status'          => $request->status,
            'last_updated_by' => Auth::guard('portal')->id(),
        ];

        if ($request->status == CustomizationRequest::STATUS_COMPLETED) {
            $data['date_complete'] = now();
        }

        if ($request->filled('technician_comments')) {
            $data['technician_comments'] = $request->technician_comments;
        }

        $customizationRequest->update($data);

        $this->logger->log(
            'status_changed',
            $customizationRequest->id,
            ['status' => $oldStatus],
            ['status' => $request->status],
            'Technician updated status',
            $request
        );

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function sendChat(Request $request, CustomizationRequest $customizationRequest)
    {
        $this->authorizeRequest($customizationRequest);

        $request->validate([
            'message' => 'required_without:file|nullable|string',
            'file'    => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:20480',
        ]);

        $user = Auth::guard('portal')->user();

        $chat = $customizationRequest->chats()->create([
            'sender_type'   => 'portal_user',
            'sender_id'     => $user->id,
            'sender_name'   => $user->full_name,
            'message'       => $request->message,
            'read_by_staff' => true,
        ]);

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename  = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path('uploads/chat'), $filename);
            $chat->update([
                'local_path'        => '/uploads/chat/' . $filename,
                'file_type'         => $this->getFileType($extension),
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

        $this->logger->log('chat_sent', $customizationRequest->id, [], [], 'Technician sent message', $request);

        return response()->json(['success' => true]);
    }

    private function authorizeRequest(CustomizationRequest $request): void
    {
        $techId = Auth::guard('portal')->id();
        if ($request->assigned_tech_id1 !== $techId && $request->assigned_tech_id2 !== $techId) {
            abort(403, 'You are not assigned to this request.');
        }
    }

    private function getFileType(string $ext): string
    {
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) return 'image';
        if ($ext === 'pdf') return 'pdf';
        return 'document';
    }
}
