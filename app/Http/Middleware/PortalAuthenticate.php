<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('portal')->check()) {
            return redirect()->route('portal.login')
                ->with('error', 'Please login to continue.');
        }

        $user = Auth::guard('portal')->user();
        if (!$user->is_active) {
            Auth::guard('portal')->logout();
            return redirect()->route('portal.login')
                ->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
