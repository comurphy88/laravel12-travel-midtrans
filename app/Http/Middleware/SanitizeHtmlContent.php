<?php

namespace App\Http\Middleware;

use Closure;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitize HTML content to prevent XSS attacks, preserving a safe subset.
 *
 * Deliberately NOT registered as global middleware: it would re-parse every
 * Blade-rendered page and strip layout markup. Use it explicitly at output
 * points that render stored/trusted HTML fragments (see PageController).
 *
 * Implemented with DOM parsing rather than regex. Regex cannot be made safe
 * against HTML: entities (`jav&#x09;ascript:`), unquoted attributes, and
 * attribute-less vectors all slip through pattern matching. Walking the node
 * tree and allowlisting tags/attributes is the only approach that holds.
 *
 * ponytail: no href scheme allowlist beyond a blocklist of javascript/data/
 * vbscript. If user-submitted rich text becomes a feature, swap in
 * mews/purifier (HTMLPurifier) instead of extending this.
 */
class SanitizeHtmlContent
{
    /** @var array<string> */
    private const ALLOWED_TAGS = [
        'p', 'br',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'strong', 'b', 'em', 'i', 'u',
        'a', 'img',
        'blockquote',
    ];

    /** @var array<string, array<string>> Attributes permitted per tag. */
    private const ALLOWED_ATTRIBUTES = [
        'a'   => ['href', 'title'],
        'img' => ['src', 'alt', 'width', 'height', 'title'],
    ];

    /** @var array<string> */
    private const BLOCKED_SCHEMES = ['javascript', 'vbscript', 'data'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isHtmlResponse($response)) {
            return $response;
        }

        $original = $response->getContent();
        if ($original !== false && $original !== '') {
            $response->setContent(self::sanitize($original));
        }

        return $response;
    }

    /**
     * Sanitize an HTML fragment: drop disallowed tags and attributes,
     * neutralise dangerous URL schemes.
     */
    public static function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="utf-8"?><div id="__sanitize_root__">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementsByTagName('div')->item(0);
        if (! $root) {
            // Parsing failed — refuse to emit anything rather than risk raw HTML.
            return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        self::cleanChildren($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }

    private static function cleanChildren(DOMNode $node): void
    {
        // Iterate a static copy: we mutate the tree while walking it.
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->nodeName);

                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    // Keep the text content of stripped elements (e.g. <span>x</span>).
                    $child->parentNode?->removeChild($child);

                    continue;
                }

                self::cleanAttributes($child, $tag);
                self::cleanChildren($child);

                continue;
            }

            // Non-element nodes (text, comments). Comments can hide conditional
            // comments in old IE, but modern browsers treat them as inert.
            if ($child->nodeType === XML_COMMENT_NODE) {
                $child->parentNode?->removeChild($child);
            }
        }
    }

    private static function cleanAttributes(DOMElement $element, string $tag): void
    {
        $allowed = self::ALLOWED_ATTRIBUTES[$tag] ?? [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->nodeName);

            if (! in_array($name, $allowed, true)) {
                $element->removeAttribute($attribute->nodeName);

                continue;
            }

            if (in_array($name, ['href', 'src'], true) && self::hasBlockedScheme($attribute->nodeValue)) {
                $element->removeAttribute($attribute->nodeName);
            }
        }
    }

    private static function hasBlockedScheme(?string $url): bool
    {
        // HTML entities are already decoded by the DOM parser, so numeric
        // obfuscation like jav&#x09;ascript: is resolved before this check.
        $normalised = strtolower(preg_replace('/[\x00-\x20\s]+/', '', (string) $url) ?? '');

        foreach (self::BLOCKED_SCHEMES as $scheme) {
            if (str_starts_with($normalised, $scheme . ':')) {
                return true;
            }
        }

        return false;
    }

    private function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }
}
