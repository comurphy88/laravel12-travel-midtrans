<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Gallery;
use App\Models\PromoCode;
use App\Models\Review;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $bookingStats = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'bookings'           => $bookingStats->sum(),
            'bookings_pending'   => $bookingStats->get('pending', 0),
            'bookings_confirmed' => $bookingStats->get('confirmed', 0),
            'bookings_completed' => $bookingStats->get('completed', 0),
            'bookings_cancelled' => $bookingStats->get('cancelled', 0),
            'revenue'            => Booking::where('payment_status', 'paid')
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
            'users'              => User::count(),
            'buses_active'       => Bus::where('status', 'active')->count(),
            'buses_all'          => Bus::count(),
            'routes_active'      => Route::where('active', true)->count(),
            'routes_all'         => Route::count(),
            'schedules_active'   => Schedule::where('active', true)->count(),
            'galleries'          => Gallery::count(),
            'promo_codes'        => PromoCode::count(),
            'reviews_pending'    => Review::where('status', 'pending')->count(),
            'reviews'            => Review::count(),
        ];

        $recentBookings = Booking::with(['user', 'schedule.bus', 'schedule.route'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}
