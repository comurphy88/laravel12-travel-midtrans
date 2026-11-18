<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bus_name',
        'bus_number',
        'capacity',
        'price_per_km',
        'status',
    ];

    protected $casts = [
        'price_per_km' => 'decimal:2',
    ];

    // ── Relations ────────────────────────────────────────────

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getFormattedPricePerKmAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_km, 0, ',', '.');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function calculatePrice(int $distanceKm, int $passengers = 1): int
    {
        return (int) ($this->price_per_km * $distanceKm * $passengers);
    }
}
