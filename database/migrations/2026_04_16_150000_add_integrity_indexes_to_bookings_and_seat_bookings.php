<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seat_bookings', function (Blueprint $table) {
            $table->unique([
                'bus_route_id',
                'travel_date',
                'seat_number',
            ], 'seat_bookings_route_date_seat_unique');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status', 'bookings_status_index');
            $table->index('payment_status', 'bookings_payment_status_index');
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->index('active', 'promo_codes_active_index');
            $table->index(['valid_from', 'valid_until'], 'promo_codes_valid_range_index');
        });
    }

    public function down(): void
    {
        Schema::table('seat_bookings', function (Blueprint $table) {
            $table->dropUnique('seat_bookings_route_date_seat_unique');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_index');
            $table->dropIndex('bookings_payment_status_index');
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropIndex('promo_codes_active_index');
            $table->dropIndex('promo_codes_valid_range_index');
        });
    }
};
