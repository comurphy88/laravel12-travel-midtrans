<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class SecurityLogger
{
    public static function logFailedLogin(string $email): void
    {
        Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }

    public static function logSuccessfulLogin(string $email): void
    {
        Log::warning('Successful login', [
            'email' => $email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }

    public static function logSuspiciousActivity(string $activity, array $details = []): void
    {
        Log::warning('Suspicious activity detected', array_merge([
            'activity' => $activity,
            'ip' => request()->ip(),
            'user_id' => request()->user()?->id ?? 'Guest',
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ], $details));
    }

    public static function logUnauthorizedAccess(string $resource, string $userId): void
    {
        Log::warning('Unauthorized access attempt', [
            'resource' => $resource,
            'user_id' => $userId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }

    public static function logValidationFailure(string $field, string $value): void
    {
        Log::notice('Validation failure', [
            'field' => $field,
            'ip' => request()->ip(),
            'user_id' => request()->user()?->id ?? 'Guest',
            'timestamp' => now(),
        ]);
    }

    public static function logSensitiveDataAccess(string $dataType, ?string $userId = null): void
    {
        Log::info('Sensitive data accessed', [
            'data_type' => $dataType,
            'user_id' => $userId ?? request()->user()?->id ?? 'Guest',
            'ip' => request()->ip(),
            'timestamp' => now(),
        ]);
    }
}
