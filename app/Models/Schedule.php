<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'route_id',
        'bus_id',
        'departure_time',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function seatMaps(): HasMany
    {
        return $this->hasMany(SeatMap::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getFormattedDepartureTimeAttribute(): string
    {
        return substr($this->departure_time, 0, 5); // HH:MM
    }

    public function getRouteNameAttribute(): string
    {
        return $this->route?->route_name ?? '-';
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function getAvailableSeats(string $date): int
    {
        $booked = $this->seatMaps()
            ->where('travel_date', $date)
            ->where('status', 'booked')
            ->count();

        return $this->bus->capacity - $booked;
    }
}
