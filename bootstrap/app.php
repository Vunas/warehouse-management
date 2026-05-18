<?php

use App\Http\Middleware\CheckEmployeeActive;
use App\Http\Middleware\CheckCustomerActive;
use App\Http\Middleware\RedirectIfLoggedIn;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\URL;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'active_employee' => CheckEmployeeActive::class,
            'active_customer' => CheckCustomerActive::class,
            'redirect.login' => RedirectIfLoggedIn::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->booting(function (): void {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    })
    ->create();
