<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_code',
        'user_id',
        'schedule_id',
        'route_id',
        'promo_code_id',
        'cancellation_policy_id',
        'origin',
        'destination',
        'distance_km',
        'price_per_km',
        'num_passengers',
        'travel_date',
        'departure_time',
        'subtotal',
        'discount_amount',
        'total_price',
        'status',
        'payment_method',
        'payment_token',
        'payment_status',
        'cancellation_reason',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'travel_date'      => 'date',
        'price_per_km'     => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'total_price'      => 'decimal:2',
        'cancelled_at'     => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function cancellationPolicy(): BelongsTo
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function seatMaps(): HasMany
    {
        return $this->hasMany(SeatMap::class);
    }

    /**
     * Free the seats held by this booking and give back any promo slot used.
     * Safe to call more than once.
     */
    public function release(): void
    {
        SeatMap::where('booking_id', $this->id)->update([
            'status'       => 'available',
            'booking_id'   => null,
            'passenger_id' => null,
        ]);

        if ($this->promo_code_id) {
            PromoCode::whereKey($this->promo_code_id)
                ->where('used_count', '>', 0)
                ->decrement('used_count');

            $this->promo_code_id = null;
            $this->save();
        }
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getFormattedTravelDateAttribute(): string
    {
        return $this->travel_date->translatedFormat('d F Y');
    }

    public function getRouteNameAttribute(): string
    {
        return ($this->origin ?? '-') . ' → ' . ($this->destination ?? '-');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
