<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.session' => \App\Http\Middleware\AdminSessionMiddleware::class,
        ]);

        // Exclude API routes from CSRF verification (uses token-based auth)
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Log all API requests
        $middleware->append(\Illuminate\Logging\LogHttpRequests::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
