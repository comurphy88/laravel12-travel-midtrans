<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitize HTML content to prevent XSS attacks
 * while preserving a known-safe subset of tags and attributes.
 */
class SanitizeHtmlContent
{
    /**
     * Tags that may appear in sanitized output.
     *
     * @var array<string>
     */
    private const ALLOWED_TAGS = [
        'p', 'br',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'strong', 'b', 'em', 'i', 'u',
        'a', 'img',
        'blockquote',
    ];

    /**
     * Attributes that are unconditionally stripped, regardless of tag.
     * Covers inline event handlers (onclick, onload, onerror, …).
     *
     * @var array<string>
     */
    private const DANGEROUS_ATTR_PATTERN = '/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i';

    /**
     * Schemes that are never allowed in href / src attributes.
     *
     * @var array<string>
     */
    private const BLOCKED_SCHEMES = ['javascript', 'vbscript', 'data'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only touch HTML responses — leave JSON, binary, etc. untouched.
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }

        $original = $response->getContent();
        if ($original !== false && $original !== '') {
            $response->setContent(self::sanitize($original));
        }

        return $response;
    }

    /**
     * Sanitize an HTML string:
     *  1. Strip disallowed tags.
     *  2. Remove all inline event-handler attributes.
     *  3. Neutralise dangerous URL schemes in href / src.
     */
    public static function sanitize(string $html): string
    {
        // 1. Strip tags not in the allowlist.
        $allowString = implode('', array_map(fn (string $t) => "<{$t}>", self::ALLOWED_TAGS));
        $cleaned = strip_tags($html, $allowString);

        // 2. Remove every on* attribute (onerror=, onclick=, …).
        $cleaned = preg_replace(self::DANGEROUS_ATTR_PATTERN, '', $cleaned) ?? $cleaned;

        // 3. Neutralise blocked URL schemes in href / src attributes.
        $schemePattern = '/(\b(?:href|src)\s*=\s*["\']?)\s*(?:'
            . implode('|', self::BLOCKED_SCHEMES)
            . ')\s*:/i';

        $cleaned = preg_replace($schemePattern, '$1#', $cleaned) ?? $cleaned;

        return $cleaned;
    }

    // -------------------------------------------------------------------------

    private function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }
}