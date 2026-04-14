<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    protected $fillable = [
        'type', 'title', 'body', 'icon', 'action_url', 'action_label',
        'sender_name', 'sender_avatar', 'ref_number',
        'notifiable_id', 'notifiable_type', 'is_read', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Create a "new request" notification for all portal staff.
     */
    public static function notifyNewRequest($request): void
    {
        self::create([
            'type'            => 'new_request',
            'title'           => 'New Customization Request',
            'body'            => "A new Customization Request has been received from {$request->first_name} {$request->last_name} and is currently waiting for your review.",
            'icon'            => 'fas fa-bell',
            'action_url'      => route('admin.requests.show', $request->cuid),
            'action_label'    => 'Review Now',
            'sender_name'     => "{$request->first_name} {$request->last_name}",
            'ref_number'      => $request->ref_number,
            'notifiable_type' => 'staff',
            'notifiable_id'   => null,
        ]);
    }

    /**
     * Create a "new chat message" notification.
     */
    public static function notifyNewChat($request, string $senderName, string $recipientType = 'staff', ?int $recipientId = null): void
    {
        self::create([
            'type'            => 'new_chat',
            'title'           => 'New Customization Request Message',
            'body'            => "You have received a new message regarding customization request {$request->ref_number}.",
            'icon'            => 'fas fa-comment',
            'action_url'      => $recipientType === 'staff'
                                    ? route('admin.requests.chat', $request->cuid)
                                    : route('user.chat.show', $request->cuid),
            'action_label'    => 'View Messages',
            'sender_name'     => $senderName,
            'ref_number'      => $request->ref_number,
            'notifiable_type' => $recipientType,
            'notifiable_id'   => $recipientId,
        ]);
    }
}
