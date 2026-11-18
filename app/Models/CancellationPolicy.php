<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CancellationPolicy extends Model
{
    protected $fillable = [
        'name', 'hours_before_travel', 'refund_percentage', 'description', 'active',
    ];

    protected $casts = [
        'refund_percentage' => 'decimal:2',
        'active'            => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Hitung refund berdasarkan total harga booking
     */
    public function calculateRefund(float $totalPrice): float
    {
        return $totalPrice * ($this->refund_percentage / 100);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
