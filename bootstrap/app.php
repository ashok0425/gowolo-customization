<?php

use App\Http\Middleware\PortalAuthenticate;
use App\Http\Middleware\SSOAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'portal.auth' => PortalAuthenticate::class,
            'sso.auth'    => SSOAuthenticate::class,
            'permission'  => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);

        // Exclude SSO and API polling from CSRF
        $middleware->validateCsrfTokens(except: [
            '/auth/sso',
            '/api/chat/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
