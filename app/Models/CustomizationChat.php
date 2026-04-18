<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomizationChat extends Model
{
    protected $fillable = [
        'request_id', 'sender_type', 'sender_id', 'sender_name',
        'message', 'reply_to_id', 'local_path', 'bunny_path', 'bunny_synced',
        'file_type', 'original_filename', 'read_by_user', 'read_by_staff',
    ];

    protected function casts(): array
    {
        return [
            'bunny_synced'   => 'boolean',
            'read_by_user'   => 'boolean',
            'read_by_staff'  => 'boolean',
        ];
    }

    public function request()
    {
        return $this->belongsTo(CustomizationRequest::class, 'request_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    /**
     * SSO user who sent this message (only when sender_type = 'user').
     */
    public function ssoUser()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Portal staff who sent this message (only when sender_type = 'portal_user').
     */
    public function portalUser()
    {
        return $this->belongsTo(PortalUser::class, 'sender_id');
    }

    public function getHasFileAttribute(): bool
    {
        return $this->bunny_path || $this->local_path;
    }

    public function getIsFromUserAttribute(): bool
    {
        return $this->sender_type === 'user';
    }
}
