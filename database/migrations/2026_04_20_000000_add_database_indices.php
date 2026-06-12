<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bookings table indices - wrap in try-catch to handle existing indices
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('user_id');
                $table->index('bus_route_id');
                $table->index('destination_id');
                $table->index(['status', 'created_at']);
                $table->index(['payment_status', 'user_id']);
                $table->index('booking_code');
            });
        } catch (\Exception $e) {
            // Some indices may already exist
        }

        // Bus Routes table indices
        try {
            Schema::table('bus_routes', function (Blueprint $table) {
                $table->index('bus_id');
                $table->index('active');
                $table->index(['active', 'price']);
            });
        } catch (\Exception $e) {}

        // Destinations table indices
        try {
            Schema::table('destinations', function (Blueprint $table) {
                $table->index('active');
                $table->index(['active', 'rating']);
            });
        } catch (\Exception $e) {}

        // Passengers table indices
        if (Schema::hasTable('passengers')) {
            try {
                Schema::table('passengers', function (Blueprint $table) {
                    $table->index('booking_id');
                });
            } catch (\Exception $e) {}
        }

        // Seat Bookings table indices
        if (Schema::hasTable('seat_bookings')) {
            try {
                Schema::table('seat_bookings', function (Blueprint $table) {
                    $table->index('booking_id');
                    $table->index('bus_route_id');
                    $table->index(['bus_route_id', 'seat_number']);
                });
            } catch (\Exception $e) {}
        }

        // Users table indices
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role');
                $table->index('email');
            });
        } catch (\Exception $e) {}

        // Activity Logs table indices
        if (Schema::hasTable('activity_logs')) {
            try {
                Schema::table('activity_logs', function (Blueprint $table) {
                    $table->index('user_id');
                    $table->index(['action', 'created_at']);
                });
            } catch (\Exception $e) {}
        }

        // Reviews table indices
        if (Schema::hasTable('reviews')) {
            try {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->index('destination_id');
                    $table->index('user_id');
                    $table->index('rating');
                });
            } catch (\Exception $e) {}
        }

        // Email Logs table indices
        if (Schema::hasTable('email_logs')) {
            try {
                Schema::table('email_logs', function (Blueprint $table) {
                    $table->index('user_id');
                    $table->index(['status', 'created_at']);
                });
            } catch (\Exception $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['bus_route_id']);
            $table->dropIndex(['destination_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['payment_status', 'user_id']);
            $table->dropIndex(['booking_code']);
        });

        Schema::table('bus_routes', function (Blueprint $table) {
            $table->dropIndex(['bus_id']);
            $table->dropIndex(['active']);
            $table->dropIndex(['active', 'price']);
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['active', 'rating']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['email']);
        });
    }
};
