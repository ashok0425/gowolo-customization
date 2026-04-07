<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PortalUser;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PortalLoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('portal')->check()) {
            return $this->redirectByRole(Auth::guard('portal')->user());
        }
        return view('auth.login');
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

        $user = PortalUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account is deactivated.'])->withInput();
        }

        Auth::guard('portal')->login($user, $request->boolean('remember'));

        app(ActivityLogService::class)->log('portal_login', null, [], [], 'Login', $request);

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::guard('portal')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login')->with('success', 'Logged out successfully.');
    }

    private function redirectByRole(PortalUser $user)
    {
        if ($user->hasRole('technician')) {
            return redirect()->route('tech.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
}
