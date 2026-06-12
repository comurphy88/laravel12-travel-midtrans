<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SanitizeInput;
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
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/notification',
        ]);

        // HTTPS enforcement in production
        if (env('APP_ENV') === 'production') {
            $middleware->redirectTo(function ($request) {
                if (! $request->secure()) {
                    return redirect()->secure($request->getRequestUri());
                }
            });
        }

        // Input sanitization - remove control characters and null bytes
        $middleware->append(SanitizeInput::class);
        
        // Security headers
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function ($schedule) {
        // Cleanup activity logs setiap hari pada jam 2 pagi
        $schedule->command('activity-logs:cleanup')->dailyAt('02:00');
    })
    ->create();
