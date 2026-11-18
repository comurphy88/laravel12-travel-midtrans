<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Security logging + injection detection live in App\Exceptions\Handler.
        // Binding it here (before anything resolves the contract) is the only
        // place that wins: withExceptions() binds the framework default first,
        // and Collision wraps whatever is bound.
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin-access', fn ($user) => $user && $user->role === 'admin');

        // Note: Removed automatic caching from boot() to avoid database queries on every request
        // Caching is now handled in individual controllers/models as needed
        // This prevents cache stampede and database connection issues
    }
}
