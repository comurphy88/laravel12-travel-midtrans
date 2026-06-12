<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\PromoCode;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        // Single query for all booking status counts — avoids N separate COUNT(*) calls.
        $bookingStats = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'bookings'            => $bookingStats->sum(),
            'bookings_pending'    => $bookingStats->get('pending', 0),
            'bookings_confirmed'  => $bookingStats->get('confirmed', 0),
            'bookings_completed'  => $bookingStats->get('completed', 0),
            'bookings_cancelled'  => $bookingStats->get('cancelled', 0),
            'revenue'             => Booking::where('payment_status', 'paid')
                                        ->whereIn('status', ['confirmed', 'completed'])
                                        ->sum('total_price'),
            'users'               => User::count(),
            'destinations_active' => Destination::where('active', true)->count(),
            'destinations_all'    => Destination::count(),
            'galleries'           => Gallery::count(),
            'promo_codes'         => PromoCode::count(),
            'reviews'             => Review::count(),
        ];

        // Bus routes are deprecated — only show destination bookings.
        $recentBookings = Booking::with(['user', 'destination'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}