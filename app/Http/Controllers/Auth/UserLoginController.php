<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PortalUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Direct end-user login. Authenticates against the dashboard_db (gowolov2)
 * users table by email + password and stores the user info in session
 * under 'auth_user' — same shape used by the SSO flow, so existing
 * sso.auth-protected routes work transparently.
 */
class UserLoginController extends Controller
{
    public function showLogin()
    {
        if (session()->has('auth_user')) {
            return redirect()->route('user.dashboard');
        }
        return view('auth.user_login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // dashboard_db users table — credentials are checked here
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
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
            ->log('user_direct_login');

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
    }

    public function logout(Request $request)
    {
        session()->forget('auth_user');
        $request->session()->regenerateToken();
        return redirect()->route('user.login')->with('success', 'Logged out successfully.');
    }
}
