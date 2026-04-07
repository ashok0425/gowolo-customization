<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomizationRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ref_number', 'origin_cust_req_id', 'origin_question_id',
        'user_id', 'user_email', 'user_name',
        'first_name', 'last_name', 'email', 'phone', 'sec_phone',
        'company_name', 'company_phone', 'company_address',
        'community_name', 'community_handle_name', 'community_domain_name',
        'req_logo', 'req_icon', 'req_app_background', 'req_landing_page', 'req_others',
        'req_primary_color', 'req_sec_color', 'req_donation',
        'request_description', 'request_donate_description', 'additional_features',
        'status', 'pay_type', 'pay_amount', 'pay_status', 'pay_id', 'paid_at',
        'assigned_tech_id1', 'assigned_tech_name1',
        'assigned_tech_id2', 'assigned_tech_name2',
        'supervisor_id', 'supervisor_name',
        'tech_receive_date', 'tech_process_date', 'date_complete', 'num_of_days',
        'technician_comments', 'last_updated_by', 'user_alert',
        'login_email', 'login_password',
    ];

    protected function casts(): array
    {
        return [
            'req_logo' => 'boolean',
            'req_icon' => 'boolean',
            'req_app_background' => 'boolean',
            'req_landing_page' => 'boolean',
            'req_others' => 'boolean',
            'req_donation' => 'boolean',
            'additional_features' => 'boolean',
            'user_alert' => 'boolean',
            'paid_at' => 'datetime',
            'tech_receive_date' => 'datetime',
            'tech_process_date' => 'datetime',
            'date_complete' => 'datetime',
        ];
    }

    // Status constants
    const STATUS_PENDING         = 0;
    const STATUS_ASSIGNED        = 1;
    const STATUS_IN_REVIEW       = 2;
    const STATUS_SENT_FOR_REVIEW = 3;
    const STATUS_APPROVED        = 4;
    const STATUS_COMPLETED       = 5;

    // Backward-compatible alias
    const STATUS_NEW         = 0;
    const STATUS_IN_PROGRESS = 1;

    const PAY_TYPE_FREE = 1;
    const PAY_TYPE_PAID = 2;

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING         => 'Pending',
            self::STATUS_ASSIGNED        => 'Assigned',
            self::STATUS_IN_REVIEW       => 'In Review',
            self::STATUS_SENT_FOR_REVIEW => 'Sent for Review',
            self::STATUS_APPROVED        => 'Approved',
            self::STATUS_COMPLETED       => 'Completed',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? 'Unknown';
    }

    /** Badge class for each status */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING         => 'badge-warning',
            self::STATUS_ASSIGNED        => 'badge-info',
            self::STATUS_IN_REVIEW       => 'badge-primary',
            self::STATUS_SENT_FOR_REVIEW => 'badge-secondary',
            self::STATUS_APPROVED        => 'badge-success',
            self::STATUS_COMPLETED       => 'badge-dark',
            default                      => 'badge-light',
        };
    }

    public function answers()
    {
        return $this->hasMany(CustomizationAnswer::class, 'request_id');
    }

    public function files()
    {
        return $this->hasMany(CustomizationFile::class, 'request_id');
    }

    public function chats()
    {
        return $this->hasMany(CustomizationChat::class, 'request_id')->orderBy('id');
    }

    public function activityLogs()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject')->latest();
    }

    public function primaryTechnician()
    {
        return $this->belongsTo(PortalUser::class, 'assigned_tech_id1');
    }

    public function secondaryTechnician()
    {
        return $this->belongsTo(PortalUser::class, 'assigned_tech_id2');
    }

    public function supervisor()
    {
        return $this->belongsTo(PortalUser::class, 'supervisor_id');
    }
}
