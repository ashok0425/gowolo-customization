<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class PortalUser extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $table = 'portal_users';

    protected $fillable = [
        'name', 'last_name', 'email', 'password',
        'phone', 'profile_photo', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Super-admin bypass: admin@gowolo* and support@gowologlobal.com
     * get every permission without needing it explicitly assigned.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if (str_starts_with($this->email, 'admin@gowolo') || $this->email === 'support@gowologlobal.com') {
            return true;
        }
        return parent::hasPermissionTo($permission, $guardName);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(CustomizationRequest::class, 'assigned_tech_id1');
    }

    public function assignedRequestsSecondary(): HasMany
    {
        return $this->hasMany(CustomizationRequest::class, 'assigned_tech_id2');
    }

    public function supervisedRequests(): HasMany
    {
        return $this->hasMany(CustomizationRequest::class, 'supervisor_id');
    }
}
