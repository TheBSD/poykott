<?php

use App\Http\Middleware\SeoMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration as SentryIntegration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            SeoMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        if (app()->isProduction()) {
            SentryIntegration::handles($exceptions);
        }
    })->create();
