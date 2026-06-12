<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusRoute;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        // DEPRECATED: Bus-route booking hidden in UI since 2026-05-26.
        // Data still loaded for legacy deep-link support.
        $routes       = BusRoute::with('bus')->where('active', true)->orderBy('route_name')->get();
        $destinations = Destination::where('active', true)->orderBy('name')->get();

        $selectedRoute       = $request->filled('route')
            ? BusRoute::find($request->integer('route'))
            : null;

        $selectedDestination = $request->filled('destination')
            ? Destination::find($request->integer('destination'))
            : null;

        return view('bookings.create', compact('routes', 'destinations', 'selectedRoute', 'selectedDestination'));
    }

    public function store(Request $request): RedirectResponse
    {
        // 'route' booking_type is deprecated (2026-05-26) but kept for
        // backward-compatible API / legacy deep-links.
        $validated = $request->validate([
            'booking_type'   => 'required|in:route,destination',
            'bus_route_id'   => 'required_if:booking_type,route|nullable|exists:bus_routes,id',
            'destination_id' => 'required_if:booking_type,destination|nullable|exists:destinations,id',
            'travel_date'    => 'required|date|after:today|before:+1 year',
            'num_passengers' => 'required|integer|min:1|max:45',
            'phone'          => ['required', 'regex:#^(\+62|0)[0-9]{9,12}$#'],
            'email'          => 'required|email:rfc|max:255',
            'notes'          => 'nullable|string|max:500',
        ], [
            'travel_date.after'      => 'Tanggal perjalanan harus setelah hari ini',
            'travel_date.before'     => 'Tanggal perjalanan tidak boleh lebih dari 1 tahun ke depan',
            'num_passengers.max'     => 'Maksimal 45 penumpang per booking',
            'phone.regex'            => 'Format nomor telepon tidak valid (contoh: +6281234567890 atau 081234567890)',
            'email.email'            => 'Format email tidak valid',
        ]);

        $validated['notes'] = isset($validated['notes']) ? strip_tags($validated['notes']) : null;

        $price = $this->resolvePrice($validated);

        $totalPrice = $price * $validated['num_passengers'];

        $booking = Booking::create([
            'booking_code'   => 'RVN' . date('Ymd') . strtoupper(Str::random(6)),
            'user_id'        => Auth::id(),
            'bus_route_id'   => $validated['bus_route_id']   ?? null,
            'destination_id' => $validated['destination_id'] ?? null,
            'num_passengers' => $validated['num_passengers'],
            'travel_date'    => $validated['travel_date'],
            'subtotal'       => $totalPrice,
            'total_price'    => $totalPrice,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'notes'          => $validated['notes'],
        ]);

        // Sync phone when the user has updated it during checkout.
        // Email updates require a separate verification flow.
        /** @var User $user */
        $user = Auth::user();
        if ($user->phone !== $validated['phone']) {
            $user->update(['phone' => $validated['phone']]);
        }

        return redirect()
            ->route('payment.show', $booking)
            ->with('success', 'Booking berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    public function index(): View
    {
        $bookings = Booking::with(['busRoute', 'destination'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->trashed(), 404);

        $booking->load(['busRoute.bus', 'destination', 'passengers', 'payments']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(! in_array($booking->status, ['pending', 'confirmed'], strict: true), 403);
        abort_if(
            $booking->payment_status === 'paid',
            403,
            'Booking sudah dibayar dan tidak dapat dibatalkan. Hubungi admin.'
        );

        $booking->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    // -------------------------------------------------------------------------

    /**
     * Resolve the per-passenger price from the validated booking data.
     *
     * @param  array<string, mixed> $validated
     */
    private function resolvePrice(array $validated): int|float
    {
        if ($validated['booking_type'] === 'route' && ! empty($validated['bus_route_id'])) {
            return BusRoute::findOrFail($validated['bus_route_id'])->price;
        }

        if ($validated['booking_type'] === 'destination' && ! empty($validated['destination_id'])) {
            return Destination::findOrFail($validated['destination_id'])->price;
        }

        return 0;
    }
}