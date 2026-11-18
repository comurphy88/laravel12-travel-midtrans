<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_proof',
        'expired_at',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'expired_at'  => 'datetime',
        'verified_at' => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
                     ->where('expired_at', '<', now());
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast() && $this->status === 'pending';
    }
}
