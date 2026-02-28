<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'pakasir/callback',
            'telegram/webhook',
        ]);
        $middleware->web(prepend: [
            \App\Http\Middleware\OriginAccessMiddleware::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'customer_app' => \App\Http\Middleware\CustomerApp\AuthenticateCustomerApp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()
                ->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Sesi login Anda telah kedaluwarsa karena lama tidak dimuat ulang. Halaman telah di-refresh, silakan coba login kembali.');
        });
    })->create();
