<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('passenger_id')->nullable()->constrained('passengers')->nullOnDelete();
            $table->date('travel_date');
            $table->string('seat_number', 10);
            $table->enum('status', ['available', 'booked'])->default('available');
            $table->timestamps();

            $table->unique(['schedule_id', 'travel_date', 'seat_number']);
            $table->index('travel_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_maps');
    }
};
