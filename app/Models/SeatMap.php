<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatMap extends Model
{
    protected $fillable = [
        'schedule_id',
        'booking_id',
        'passenger_id',
        'travel_date',
        'seat_number',
        'status',
    ];

    protected $casts = [
        'travel_date' => 'date',
    ];

    // ── Relations ────────────────────────────────────────────

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Passenger::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('travel_date', $date);
    }
}
