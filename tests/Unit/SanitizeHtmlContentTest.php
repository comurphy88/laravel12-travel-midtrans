<?php

namespace Tests\Unit;

use App\Http\Middleware\SanitizeHtmlContent;
use PHPUnit\Framework\TestCase;

class SanitizeHtmlContentTest extends TestCase
{
    private function clean(string $html): string
    {
        return SanitizeHtmlContent::sanitize($html);
    }

    public function test_keeps_allowed_markup(): void
    {
        $out = $this->clean('<h3>Judul</h3><p>Isi <strong>tebal</strong></p>');

        $this->assertStringContainsString('<h3>Judul</h3>', $out);
        $this->assertStringContainsString('<strong>tebal</strong>', $out);
    }

    public function test_strips_script_tags(): void
    {
        $out = $this->clean('<p>hi</p><script>alert(1)</script>');

        $this->assertStringNotContainsString('<script', $out);
        $this->assertStringNotContainsString('alert(1)', $out);
    }

    public function test_strips_event_handlers(): void
    {
        $out = $this->clean('<img src="https://ok.jpg" onerror="alert(1)">');

        $this->assertStringNotContainsString('onerror', $out);
    }

    public function test_strips_disallowed_attributes(): void
    {
        $out = $this->clean('<p style="background:url(javascript:alert(1))">x</p>');

        $this->assertStringNotContainsString('style=', $out);
    }

    public function test_blocks_javascript_scheme(): void
    {
        $out = $this->clean('<a href="javascript:alert(1)">klik</a>');

        $this->assertStringNotContainsString('javascript:', $out);
        $this->assertStringContainsString('klik', $out);
    }

    public function test_blocks_entity_obfuscated_scheme(): void
    {
        // jav&#x09;ascript: must not survive — the DOM parser decodes the entity first.
        $out = $this->clean('<a href="jav&#x09;ascript:alert(1)">x</a>');

        $this->assertStringNotContainsString('ascript:', $out);
    }

    public function test_keeps_safe_links(): void
    {
        $out = $this->clean('<a href="https://example.com" title="ok">link</a>');

        $this->assertStringContainsString('href="https://example.com"', $out);
    }
}
