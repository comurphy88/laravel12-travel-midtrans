<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\SeatMap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'schedule.bus', 'schedule.route']);

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($term) {
                $q->where('booking_code', 'like', "%{$term}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%"))
                    ->orWhere('origin', 'like', "%{$term}%")
                    ->orWhere('destination', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['user', 'schedule.bus', 'schedule.route', 'passengers', 'payments', 'cancellationPolicy']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $old = $booking->status;

        DB::transaction(function () use ($booking, $validated, $old) {
            $booking->update(['status' => $validated['status']]);

            // Jika dibatalkan admin, bebaskan kursi dan kembalikan kuota promo
            if ($validated['status'] === 'cancelled' && $old !== 'cancelled') {
                $booking->release();
            }

            ActivityLog::log(
                'update',
                "Admin mengubah status booking #{$booking->booking_code}: {$old} → {$validated['status']}",
                'bookings',
                $booking->id
            );
        });

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
