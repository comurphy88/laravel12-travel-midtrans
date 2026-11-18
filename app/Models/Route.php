<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'distance_km',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getRouteNameAttribute(): string
    {
        return $this->origin . ' → ' . $this->destination;
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
