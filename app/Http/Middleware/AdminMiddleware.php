<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict access to admin-only routes.
 *
 * Requires:
 *  - An authenticated user (401 if not).
 *  - The "admin-access" gate to pass (403 if not).
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            abort(401, 'Unauthenticated.');
        }

        if (Gate::denies('admin-access')) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}