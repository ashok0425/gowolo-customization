<?php

use App\Models\PortalUser;

return [

    'defaults' => [
        'guard' => 'portal',
        'passwords' => 'portal_users',
    ],

    'guards' => [
        // Portal guard: admin + technician login
        'portal' => [
            'driver' => 'session',
            'provider' => 'portal_users',
        ],

        // SSO guard: users redirected from dashboardv2 (session-based, no password)
        'sso' => [
            'driver' => 'session',
            'provider' => 'portal_users', // reuse, but SSO users stored in user_sso_sessions
        ],
    ],

    'providers' => [
        'portal_users' => [
            'driver' => 'eloquent',
            'model' => PortalUser::class,
        ],
    ],

    'passwords' => [
        'portal_users' => [
            'provider' => 'portal_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
