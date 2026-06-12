<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * BusRoute Model
 * 
 * DEPRECATED: Bus routing feature disabled 2026-05-26
 * Kept in codebase for:
 * - Legacy booking support (historical data integrity)
 * - Future re-enablement if needed
 * 
 * UI is completely hidden. Bookings now use Destination model only.
 * Database and relationships maintained for backward compatibility.
 */
class BusRoute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'route_name', 'origin', 'destination', 'distance', 'duration',
        'price', 'bus_id', 'departure_time', 'arrival_time', 'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function seatBookings(): HasMany
    {
        return $this->hasMany(SeatBooking::class);
    }
}
