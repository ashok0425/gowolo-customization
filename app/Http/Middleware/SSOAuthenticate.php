<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SSOAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth_user')) {
            return redirect()->route('portal.login')
                ->with('error', 'Session expired. Please re-access from your dashboard.');
        }

        return $next($request);
    }
}
