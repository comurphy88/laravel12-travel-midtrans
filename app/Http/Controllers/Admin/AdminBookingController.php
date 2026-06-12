<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    /**
     * Allowed status transitions.
     *
     * @var array<string, array<string>>
     */
    private const STATUS_TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'busRoute', 'destination']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $bookings = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['user', 'busRoute.bus', 'destination', 'passengers', 'payments']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $newStatus     = $validated['status'];
        $currentStatus = $booking->status;

        $allowed = self::STATUS_TRANSITIONS[$currentStatus] ?? [];

        if (! in_array($newStatus, $allowed, strict: true)) {
            return back()->with('error', "Tidak dapat mengubah status dari {$currentStatus} ke {$newStatus}.");
        }

        if ($newStatus === 'confirmed' && $booking->payment_status === 'unpaid') {
            return back()->with('error', 'Booking tidak dapat dikonfirmasi karena pembayaran belum diterima.');
        }

        $booking->update($validated);

        ActivityLog::log(
            'update',
            "Mengubah status booking {$booking->booking_code}: {$currentStatus} → {$newStatus}",
            'bookings',
            $booking->id
        );

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}