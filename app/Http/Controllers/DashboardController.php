<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Soft-delete filtering is handled automatically by the global scope
        // on the Booking model — no need for manual whereNull('deleted_at').
        $totalTrips = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $activeTickets = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $recentBookings = Booking::with('destination')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('totalTrips', 'activeTickets', 'recentBookings'));
    }
}