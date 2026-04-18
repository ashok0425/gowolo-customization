<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PortalUser;
use App\Services\SSOTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

            // Clear any active portal (admin/tech) session to prevent dual menus
            if (Auth::guard('portal')->check()) {
                Auth::guard('portal')->logout();
            }

            session([
                'auth_user' => [
                    'user_id'      => $user->id,
                    'name'         => trim($user->name . ' ' . ($user->last_name ?? '')),
                    'email'        => $user->email,
                    'phone'        => $user->phone ?? null,
                    'profile_pic'  => $user->profile_pic ?? null,
                    'logged_in_at' => now()->toISOString(),
                ],
            ]);

            activity('customization')
                ->withProperties(['user_id' => $user->id, 'ip' => $request->ip()])
                ->log('user_sso_login');

            // support@gowologlobal.com → auto-login as super_admin on the portal guard
            if (strtolower($user->email) === 'support@gowologlobal.com') {
                $portalUser = PortalUser::firstOrCreate(
                    ['email' => 'support@gowologlobal.com'],
                    [
                        'name'      => $user->name ?? 'Support',
                        'last_name' => $user->last_name ?? 'Admin',
                        'password'  => Hash::make(Str::random(32)),
                        'is_active' => true,
                    ]
                );

                if (!$portalUser->hasRole('super_admin')) {
                    $portalUser->assignRole('super_admin');
                }

                Auth::guard('portal')->login($portalUser);

                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('user.dashboard');

        } catch (\InvalidArgumentException $e) {
            return redirect()->route('portal.login')
                ->with('error', 'Access link expired or invalid. Please try again from your dashboard.');
        }
    }

    /**
     * Generate a token and redirect the SSO user back to the dashboard.
     * GET /auth/dashboard-redirect
     */
    public function dashboardRedirect()
    {
        $ssoUser = session('auth_user');

        if (!$ssoUser) {
            return redirect()->route('user.login');
        }

        $token       = $this->sso->generateToken($ssoUser['user_id']);
        $dashboardUrl = rtrim(env('DASHBOARD_URL', 'https://dashboard.gowologlobal.com'), '/');

        return redirect($dashboardUrl . '/auth/sso-return?token=' . $token);
    }

    public function logout()
    {
        session()->forget('auth_user');
        return redirect()->route('user.login')->with('success', 'Logged out.');
    }
}
