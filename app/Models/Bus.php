<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bus_name', 'bus_number', 'capacity', 'bus_type', 'facilities', 'status',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(BusRoute::class);
    }
}
