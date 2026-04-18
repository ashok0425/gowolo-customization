<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'message',
        'steps_to_reproduce',
        'screenshot_path',
        'is_read',
        'status',
        'remark',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read'     => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    // Status constants
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_DUPLICATED = 'duplicated';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_APPROVED   = 'approved';

    public static function statuses(): array
    {
        return [
            self::STATUS_IN_REVIEW  => 'In Review',
            self::STATUS_DUPLICATED => 'Duplicated',
            self::STATUS_REJECTED   => 'Rejected',
            self::STATUS_APPROVED   => 'Approved',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status ?? self::STATUS_IN_REVIEW] ?? 'In Review';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED   => 'badge-success',
            self::STATUS_REJECTED   => 'badge-danger',
            self::STATUS_DUPLICATED => 'badge-secondary',
            default                 => 'badge-warning',   // in_review + null
        };
    }
}
