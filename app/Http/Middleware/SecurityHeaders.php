<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attach security-related HTTP response headers.
 *
 * CSP NOTE — 'unsafe-inline' on script-src
 * -----------------------------------------
 * This project still uses inline event handlers and inline <script> blocks
 * for Midtrans payment integration. Until those are refactored to use
 * a nonce-based or hash-based approach, 'unsafe-inline' is required.
 *
 * Tracking issue: replace 'unsafe-inline' with nonce-based CSP.
 */
class SecurityHeaders
{
    /**
     * Trusted external origins that may serve scripts.
     *
     * @var array<string>
     */
    private const SCRIPT_ORIGINS = [
        'https://app.midtrans.com',
        'https://app.sandbox.midtrans.com',
        'https://cdn.jsdelivr.net',
        'https://unpkg.com',
    ];

    /**
     * Trusted external origins that may serve stylesheets.
     *
     * @var array<string>
     */
    private const STYLE_ORIGINS = [
        'https://fonts.googleapis.com',
        'https://cdn.jsdelivr.net',
        'https://unpkg.com',
    ];

    /**
     * Trusted external origins that may serve fonts.
     *
     * @var array<string>
     */
    private const FONT_ORIGINS = [
        'https://fonts.gstatic.com',
        'https://cdn.jsdelivr.net',
    ];

    /**
     * Trusted external origins for XHR / fetch (connect-src).
     *
     * @var array<string>
     */
    private const CONNECT_ORIGINS = [
        'https://api.midtrans.com',
        'https://fonts.googleapis.com',
        'https://cdn.jsdelivr.net',
    ];

    /**
     * Origins allowed to load inside <frame> / <iframe>.
     *
     * @var array<string>
     */
    private const FRAME_ORIGINS = [
        'https://app.midtrans.com',
        'https://app.sandbox.midtrans.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Content-Security-Policy',       $this->buildCsp());
        $response->headers->set('Strict-Transport-Security',     'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options',               'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options',        'nosniff');
        $response->headers->set('Referrer-Policy',               'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy',            'geolocation=(), microphone=(), camera=()');

        return $response;
    }

    // -------------------------------------------------------------------------

    /**
     * Build the Content-Security-Policy header value from the origin constants.
     */
    private function buildCsp(): string
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' "  . $this->origins(self::SCRIPT_ORIGINS),
            "style-src 'self' 'unsafe-inline' "   . $this->origins(self::STYLE_ORIGINS),
            "img-src 'self' data: https:",
            "font-src 'self' data: "               . $this->origins(self::FONT_ORIGINS),
            "connect-src 'self' "                  . $this->origins(self::CONNECT_ORIGINS),
            "frame-src "                           . $this->origins(self::FRAME_ORIGINS),
        ];

        return implode('; ', $directives);
    }

    /**
     * Join an array of origins into a space-separated string for a CSP directive.
     *
     * @param  array<string> $origins
     */
    private function origins(array $origins): string
    {
        return implode(' ', $origins);
    }
}