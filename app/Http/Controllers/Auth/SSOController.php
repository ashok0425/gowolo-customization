<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\SSOTokenService;
use Illuminate\Http\Request;

class SSOController extends Controller
{
    public function __construct(
        private SSOTokenService $sso,
        private ActivityLogService $logger
    ) {}

    /**
     * GET /auth/sso?token=XXX
     * Decode token → fetch user from dashboard_db → store in session → redirect.
     */
    public function handle(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('portal.login')
                ->with('error', 'Invalid access. Please return to your dashboard.');
        }

        try {
            $user = $this->sso->decodeAndLogin($token);

            // Store user in session — this is our "logged in" state for SSO users
            session([
                'auth_user' => [
                    'id'        => $user->id,
                    'name'      => $user->name . ' ' . ($user->last_name ?? ''),
                    'email'     => $user->email,
                    'phone'     => $user->phone ?? null,
                    'logged_in_at' => now()->toISOString(),
                ],
            ]);

            $this->logger->log('user_login_sso', null, [], [], 'SSO login', $request);

            return redirect()->route('user.dashboard');

        } catch (\InvalidArgumentException) {
            return redirect()->route('portal.login')
                ->with('error', 'Access link expired or invalid. Please try again from your dashboard.');
        }
    }

    public function logout()
    {
        session()->forget('auth_user');
        return redirect()->route('portal.login')->with('success', 'Logged out.');
    }
}
