<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Points to dashboardv2's users table via dashboard_db connection.
 * Used for SSO login — no local user table needed.
 */
class User extends Authenticatable
{
    protected $connection = 'dashboard_db';
    protected $table      = 'users';

    protected $hidden = ['password', 'remember_token'];
}
