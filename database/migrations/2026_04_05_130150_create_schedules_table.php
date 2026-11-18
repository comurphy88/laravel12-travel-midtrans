<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->time('departure_time');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['route_id', 'bus_id', 'departure_time']);
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
