<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SSOTokenService;
use Illuminate\Http\Request;

class SSOController extends Controller
{
    public function __construct(private SSOTokenService $sso) {}

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

            session([
                'auth_user' => [
                    'user_id'      => $user->id,
                    'name'         => trim($user->name . ' ' . ($user->last_name ?? '')),
                    'email'        => $user->email,
                    'phone'        => $user->phone ?? null,
                    'logged_in_at' => now()->toISOString(),
                ],
            ]);

            activity('customization')
                ->withProperties(['user_id' => $user->id, 'ip' => $request->ip()])
                ->log('user_sso_login');

            return redirect()->route('user.dashboard');

        } catch (\InvalidArgumentException $e) {
            return redirect()->route('portal.login')
                ->with('error', 'Access link expired or invalid. Please try again from your dashboard.');
        }
    }

    public function logout()
    {
        session()->forget('auth_user');
        return redirect()->route('user.login')->with('success', 'Logged out.');
    }
}
