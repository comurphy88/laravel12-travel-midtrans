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
        //
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
