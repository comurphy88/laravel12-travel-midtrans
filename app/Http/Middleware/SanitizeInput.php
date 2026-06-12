<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitize scalar request input on mutating HTTP methods.
 *
 * Strips null bytes and non-printable control characters from every
 * string value in the request bag. Does NOT escape HTML — leave that
 * to output encoding / SanitizeHtmlContent where appropriate.
 */
class SanitizeInput
{
    /**
     * HTTP methods whose input is sanitized.
     *
     * DELETE is excluded: it typically carries no body.
     *
     * @var array<string>
     */
    private const SANITIZED_METHODS = ['GET', 'POST', 'PUT', 'PATCH'];

    /**
     * Control-character pattern:
     *   \x00–\x08  : NUL … BS
     *   \x0B–\x0C  : VT, FF          (preserve \x09 TAB, \x0A LF)
     *   \x0E–\x1F  : SO … US
     *   \x7F       : DEL
     */
    private const CONTROL_CHAR_PATTERN = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u';

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), self::SANITIZED_METHODS, strict: true)) {
            $request->merge($this->sanitizeArray($request->all()));
        }

        return $next($request);
    }

    // -------------------------------------------------------------------------

    /**
     * Recursively sanitize every scalar value in an array.
     *
     * @param  array<mixed> $input
     * @return array<mixed>
     */
    private function sanitizeArray(array $input): array
    {
        foreach ($input as $key => $value) {
            $input[$key] = match (true) {
                is_array($value)  => $this->sanitizeArray($value),
                is_string($value) => $this->sanitizeString($value),
                default           => $value,
            };
        }

        return $input;
    }

    /**
     * Clean a single string value.
     */
    private function sanitizeString(string $value): string
    {
        // Strip null bytes explicitly (belt-and-suspenders before the regex).
        $value = str_replace("\0", '', $value);

        // Strip remaining non-printable control characters.
        $value = preg_replace(self::CONTROL_CHAR_PATTERN, '', $value) ?? $value;

        return trim($value);
    }
}