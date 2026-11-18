<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        $totalTrips    = Booking::where('user_id', $user->id)->count();
        $activeTickets = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $recentBookings = Booking::with(['schedule.bus', 'schedule.route'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('totalTrips', 'activeTickets', 'recentBookings'));
    }
}
