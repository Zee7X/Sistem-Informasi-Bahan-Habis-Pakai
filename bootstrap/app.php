<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'role'  => RoleMiddleware::class,
            'admin' => IsAdmin::class,
        ]);
        // Trust proxy headers (X-Forwarded-*) via env TRUSTED_PROXIES,
        // dibutuhkan di belakang reverse proxy (Render) agar scheme https
        // terdeteksi dan asset URL tidak jatuh ke http (mixed content).
        // Pakai API bawaan agar mengset instance TrustProxies framework
        // yang memang ada di global stack (bukan instance duplikat).
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '*'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
