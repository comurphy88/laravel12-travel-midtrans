<?php

namespace App\Exceptions;

use App\Services\SecurityLogger;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the request.
     * Note: "dontFlash" is Laravel's official naming convention.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'token',
        'secret',
        'api_key',
        'authorization',
    ];

    /**
     * SQL injection detection patterns.
     *
     * @var array<string>
     */
    private const SQL_PATTERNS = [
        '/\b(union|select|insert|update|delete|drop|truncate|exec|execute|cast|convert|declare)\b/i',
        '/(\-\-|\/\*|\*\/|;--|;\s*drop|;\s*select)/i',
        '/\bchar\s*\(\d+\)/i',
        '/0x[0-9a-fA-F]+/i',
    ];

    /**
     * XSS detection patterns.
     *
     * @var array<string>
     */
    private const XSS_PATTERNS = [
        '/<\s*script/i',
        '/javascript\s*:/i',
        '/on\w+\s*=/i',
        '/data\s*:\s*text\s*\/\s*html/i',
        '/<\s*iframe/i',
        '/eval\s*\(/i',
    ];

    /**
     * Auth field candidates, checked in order.
     *
     * @var array<string>
     */
    private const AUTH_IDENTIFIER_FIELDS = ['email', 'username', 'login', 'phone'];

    /**
     * How long (minutes) to suppress duplicate auth-failure log entries per IP.
     */
    private const AUTH_RATE_LIMIT_TTL = 1;

    public function __construct(
        private readonly SecurityLogger $securityLogger,
    ) {
        parent::__construct(app());
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            if ($this->shouldSkip()) {
                return;
            }

            match (true) {
                $e instanceof AuthenticationException => $this->logFailedAuthentication(),
                $e instanceof AuthorizationException   => $this->logUnauthorizedAccess(),
                $e instanceof ValidationException      => $this->logValidationFailure($e),
                default                                => null,
            };
        });
    }

    // -------------------------------------------------------------------------
    // Private logging methods
    // -------------------------------------------------------------------------

    /**
     * Log failed authentication attempts, rate-limited per IP.
     */
    private function logFailedAuthentication(): void
    {
        $request   = request();
        $ip        = (string) $request->ip();
        $cacheKey  = "sec.auth_failure.{$ip}";

        if (Cache::has($cacheKey)) {
            return;
        }

        $identifier = $this->resolveAuthIdentifier($request);

        $this->safeLog(
            fn () => $this->securityLogger->logFailedLogin($identifier),
            'Failed to log authentication failure'
        );

        Cache::put($cacheKey, true, now()->addMinutes(self::AUTH_RATE_LIMIT_TTL));
    }

    /**
     * Log unauthorized access attempts.
     */
    private function logUnauthorizedAccess(): void
    {
        $request = request();

        $this->safeLog(
            fn () => $this->securityLogger->logUnauthorizedAccess(
                $request->path(),
                (string) ($request->user()?->id ?? 'guest'),
            ),
            'Failed to log unauthorized access'
        );
    }

    /**
     * Log validation failures that contain suspicious input patterns.
     */
    private function logValidationFailure(ValidationException $e): void
    {
        $input           = request()->all();
        $suspiciousFields = $this->detectMaliciousFields($input);

        if (empty($suspiciousFields)) {
            return;
        }

        $this->safeLog(
            fn () => $this->securityLogger->logSuspiciousActivity(
                'Validation failure — potential injection detected',
                [
                    'triggered_fields'  => $suspiciousFields,
                    'validation_errors' => array_keys($e->errors()),
                ],
            ),
            'Failed to log suspicious activity'
        );
    }

    // -------------------------------------------------------------------------
    // Detection helpers
    // -------------------------------------------------------------------------

    /**
     * Return the field names whose values matched a malicious pattern.
     * Checks each field independently to avoid cross-field false positives.
     *
     * @param  array<mixed> $input
     * @return array<string>
     */
    private function detectMaliciousFields(array $input): array
    {
        $patterns = array_merge(self::SQL_PATTERNS, self::XSS_PATTERNS);
        $hits     = [];

        $this->walkFields($input, function (string $key, string $value) use ($patterns, &$hits): void {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $hits[] = $key;
                    return; // one hit per field is enough
                }
            }
        });

        return array_unique($hits);
    }

    /**
     * Recursively walk all scalar leaves, calling $callback($dotKey, $stringValue).
     *
     * @param  array<mixed>                        $array
     * @param  callable(string, string): void      $callback
     * @param  string                              $prefix
     */
    private function walkFields(array $array, callable $callback, string $prefix = ''): void
    {
        foreach ($array as $key => $value) {
            $dotKey = $prefix !== '' ? "{$prefix}.{$key}" : (string) $key;

            if (is_array($value)) {
                $this->walkFields($value, $callback, $dotKey);
            } elseif (is_scalar($value)) {
                $callback($dotKey, (string) $value);
            }
        }
    }

    /**
     * Find the first recognised auth identifier present in the request.
     */
    private function resolveAuthIdentifier(Request $request): string
    {
        foreach (self::AUTH_IDENTIFIER_FIELDS as $field) {
            $value = $request->input($field);
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return 'unknown';
    }

    // -------------------------------------------------------------------------
    // Utility
    // -------------------------------------------------------------------------

    /**
     * Execute $callback, swallowing any exception and writing a fallback log entry.
     */
    private function safeLog(callable $callback, string $fallbackMessage): void
    {
        try {
            $callback();
        } catch (Throwable $e) {
            Log::error($fallbackMessage, ['exception' => $e]);
        }
    }

    /**
     * Return true when logging should be skipped entirely.
     */
    private function shouldSkip(): bool
    {
        return app()->runningInConsole();
    }
}