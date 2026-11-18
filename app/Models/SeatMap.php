<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Always persist travel_date as a plain Y-m-d string.
     *
     * Without this, Eloquent serialises the 'date' cast as "Y-m-d H:i:s",
     * which does not match the date column and breaks the unique
     * (schedule_id, travel_date, seat_number) index as well as every
     * where('travel_date', ...) lookup.
     */
    protected function travelDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value instanceof \DateTimeInterface
                ? $value->format('Y-m-d')
                : (is_string($value) ? substr($value, 0, 10) : $value),
        );
    }

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
