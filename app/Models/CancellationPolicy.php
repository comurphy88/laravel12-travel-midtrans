<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    protected $fillable = [
        'name', 'hours_before_travel', 'refund_percentage', 'description', 'active',
    ];

    protected $casts = [
        'refund_percentage' => 'decimal:2',
        'active' => 'boolean',
    ];
}
