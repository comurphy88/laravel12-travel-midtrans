<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support CREATE OR REPLACE VIEW
            DB::statement("DROP VIEW IF EXISTS v_booking_summary");
            DB::statement("
                CREATE VIEW v_booking_summary AS
                SELECT
                    DATE(created_at) as date,
                    COUNT(*) as total_bookings,
                    SUM(total_price) as total_revenue,
                    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings
                FROM bookings
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");

            DB::statement("DROP VIEW IF EXISTS v_revenue_summary");
            DB::statement("
                CREATE VIEW v_revenue_summary AS
                SELECT
                    DATE(p.created_at) as date,
                    SUM(p.amount) as total_revenue,
                    COUNT(p.id) as total_payments,
                    p.payment_method,
                    COUNT(CASE WHEN p.status = 'success' THEN 1 END) as successful_payments
                FROM payments p
                GROUP BY DATE(p.created_at), p.payment_method
                ORDER BY date DESC, total_revenue DESC
            ");
        } else {
            // MySQL and other databases support CREATE OR REPLACE VIEW
            DB::statement("
                CREATE OR REPLACE VIEW v_booking_summary AS
                SELECT
                    DATE(created_at) as date,
                    COUNT(*) as total_bookings,
                    SUM(total_price) as total_revenue,
                    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings
                FROM bookings
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");

            DB::statement("
                CREATE OR REPLACE VIEW v_revenue_summary AS
                SELECT
                    DATE(p.created_at) as date,
                    SUM(p.amount) as total_revenue,
                    COUNT(p.id) as total_payments,
                    p.payment_method,
                    COUNT(CASE WHEN p.status = 'success' THEN 1 END) as successful_payments
                FROM payments p
                GROUP BY DATE(p.created_at), p.payment_method
                ORDER BY date DESC, total_revenue DESC
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_revenue_summary');
        DB::statement('DROP VIEW IF EXISTS v_booking_summary');
    }
};