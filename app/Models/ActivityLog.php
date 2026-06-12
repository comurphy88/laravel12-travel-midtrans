<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'activity_type', 'description',
        'related_table', 'related_id', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $type, string $description, ?string $table = null, ?int $relatedId = null): static
    {
        return static::create([
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'description' => $description,
            'related_table' => $table,
            'related_id' => $relatedId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log webhook/system events tanpa user authentication
     * Digunakan untuk mencatat event dari Midtrans, cron jobs, atau system operations
     */
    public static function logWebhook(string $type, string $description, ?string $table = null, ?int $relatedId = null): static
    {
        return static::create([
            'user_id' => null, // Webhook tidak terautentikasi
            'activity_type' => $type,
            'description' => $description,
            'related_table' => $table,
            'related_id' => $relatedId,
            'ip_address' => Request::ip(),
            'user_agent' => 'Webhook/System',
        ]);
    }
}
