<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\CancellationPolicy;
use App\Models\Passenger;
use App\Models\PromoCode;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\SeatMap;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $routes = Route::active()->orderBy('origin')->get();

        $selectedRouteId = $request->input('route_id');
        $selectedDate    = $request->input('travel_date');
        $schedules       = collect();

        if ($selectedRouteId) {
            $schedules = Schedule::with('bus')
                ->where('route_id', $selectedRouteId)
                ->active()
                ->orderBy('departure_time')
                ->get();
        }

        return view('bookings.create', compact(
            'routes',
            'schedules',
            'selectedRouteId',
            'selectedDate',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'route_id'       => 'required|exists:routes,id',
            'schedule_id'    => 'required|exists:schedules,id',
            'travel_date'    => 'required|date|after:today|before:+1 year',
            'num_passengers' => 'required|integer|min:1|max:45',
            'promo_code'     => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
            'passengers'     => 'required|array|min:1',
            'passengers.*.name'      => 'required|string|max:255',
            'passengers.*.id_number' => 'nullable|string|max:50',
            'passengers.*.phone'     => ['nullable', 'regex:#^(\+62|0)[0-9]{9,12}$#'],
            'passengers.*.email'     => 'nullable|email|max:255',
        ], [
            'travel_date.after'  => 'Tanggal perjalanan harus setelah hari ini.',
            'travel_date.before' => 'Tanggal perjalanan tidak boleh lebih dari 1 tahun ke depan.',
            'num_passengers.max' => 'Maksimal 45 penumpang per booking.',
        ]);

        $route    = Route::findOrFail($validated['route_id']);
        $schedule = Schedule::with('bus')->findOrFail($validated['schedule_id']);

        // Pastikan schedule milik route yang dipilih
        abort_if($schedule->route_id !== $route->id, 422, 'Jadwal tidak sesuai dengan rute.');

        // Cek kursi tersedia
        $available = $schedule->getAvailableSeats($validated['travel_date']);
        if ($available < $validated['num_passengers']) {
            return back()->withInput()->withErrors([
                'num_passengers' => "Kursi tersedia hanya $available untuk tanggal tersebut.",
            ]);
        }

        // Ambil cancellation policy yang paling applicable
        $policy = CancellationPolicy::where('active', true)
            ->orderByDesc('hours_before_travel')
            ->first();

        // Hitung harga
        $pricePerKm = $schedule->bus->price_per_km;
        $subtotal   = $schedule->bus->calculatePrice($route->distance_km, $validated['num_passengers']);

        // Promo code — usage_limit must be enforced atomically, otherwise a single
        // code can be redeemed far past its cap under concurrent requests.
        $promoCodeId    = null;
        $discountAmount = 0;
        if (!empty($validated['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper($validated['promo_code']))
                ->where('active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_until', '>=', now())
                ->where(function ($q) {
                    $q->where('usage_limit', 0)
                      ->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->first();

            if ($promo && $subtotal >= $promo->min_purchase) {
                $promoCodeId    = $promo->id;
                $maxDiscount    = (float) $promo->max_discount;
                $discountAmount = $promo->discount_type === 'percentage'
                    // max_discount = 0 means "no cap". The decimal cast returns
                    // the string "0.00", which is truthy, so a plain `?:` would
                    // cap every discount at zero.
                    ? min(
                        $subtotal * ((float) $promo->discount_value / 100),
                        $maxDiscount > 0 ? $maxDiscount : PHP_INT_MAX,
                    )
                    : (float) $promo->discount_value;

                // Conditional increment: returns 0 if another request won the last slot.
                $claimed = PromoCode::whereKey($promo->id)
                    ->where(function ($q) {
                        $q->where('usage_limit', 0)
                          ->orWhereColumn('used_count', '<', 'usage_limit');
                    })
                    ->increment('used_count');

                if (! $claimed) {
                    $promoCodeId    = null;
                    $discountAmount = 0;
                }
            }
        }

        $totalPrice = $subtotal - $discountAmount;

        // Seat allocation relies on the unique (schedule_id, travel_date, seat_number)
        // index to win races: a concurrent booking that grabs the same seat makes the
        // insert fail, and we retry with the next free seat.
        $attempts = 0;
        while (true) {
            try {
                $booking = $this->createBooking(
                    $validated, $route, $schedule, $pricePerKm,
                    $subtotal, $discountAmount, $totalPrice, $promoCodeId, $policy
                );
                break;
            } catch (UniqueConstraintViolationException $e) {
                if (++$attempts >= 3) {
                    throw $e;
                }
            }
        }

        return redirect()
            ->route('payment.show', $booking)
            ->with('success', 'Booking berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    /**
     * @param  array<string, mixed> $validated
     */
    private function createBooking(
        array $validated,
        Route $route,
        Schedule $schedule,
        mixed $pricePerKm,
        mixed $subtotal,
        mixed $discountAmount,
        mixed $totalPrice,
        ?int $promoCodeId,
        ?CancellationPolicy $policy,
    ): Booking {
        return DB::transaction(function () use (
            $validated, $route, $schedule, $pricePerKm,
            $subtotal, $discountAmount, $totalPrice, $promoCodeId, $policy
        ) {
            $booking = Booking::create([
                'booking_code'           => 'RVN' . date('Ymd') . strtoupper(Str::random(6)),
                'user_id'                => Auth::id(),
                'schedule_id'            => $schedule->id,
                'route_id'               => $route->id,
                'promo_code_id'          => $promoCodeId,
                'cancellation_policy_id' => $policy?->id,
                'origin'                 => $route->origin,
                'destination'            => $route->destination,
                'distance_km'            => $route->distance_km,
                'price_per_km'           => $pricePerKm,
                'num_passengers'         => $validated['num_passengers'],
                'travel_date'            => $validated['travel_date'],
                'departure_time'         => $schedule->departure_time,
                'subtotal'               => $subtotal,
                'discount_amount'        => $discountAmount,
                'total_price'            => $totalPrice,
                'status'                 => 'pending',
                'payment_status'         => 'unpaid',
                'notes'                  => !empty($validated['notes']) ? strip_tags($validated['notes']) : null,
            ]);

            // Simpan passengers & auto-assign kursi
            $seatNumber = 1;
            foreach ($validated['passengers'] as $passengerData) {
                // Cari seat_number berikutnya yang available
                while (
                    SeatMap::where('schedule_id', $schedule->id)
                        ->where('travel_date', $validated['travel_date'])
                        ->where('seat_number', 'S' . $seatNumber)
                        ->exists()
                ) {
                    $seatNumber++;
                }

                $seat = 'S' . $seatNumber;

                $passenger = Passenger::create([
                    'booking_id'  => $booking->id,
                    'name'        => strip_tags($passengerData['name']),
                    'id_number'   => $passengerData['id_number'] ?? null,
                    'phone'       => $passengerData['phone'] ?? null,
                    'email'       => $passengerData['email'] ?? null,
                    'seat_number' => $seat,
                ]);

                SeatMap::create([
                    'schedule_id'  => $schedule->id,
                    'booking_id'   => $booking->id,
                    'passenger_id' => $passenger->id,
                    'travel_date'  => $validated['travel_date'],
                    'seat_number'  => $seat,
                    'status'       => 'booked',
                ]);

                $seatNumber++;
            }

            return $booking;
        });
    }

    public function index(): View
    {
        $bookings = Booking::with(['schedule.bus', 'schedule.route'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->trashed(), 404);

        $booking->load(['schedule.bus', 'schedule.route', 'passengers', 'payments', 'cancellationPolicy']);

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

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $booking->release();
        });

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }


    public function ticket(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->trashed(), 404);

        $booking->load(['schedule.bus', 'passengers', 'payments']);

        return view('bookings.ticket', compact('booking'));
    }
    public function calculatePrice(Request $request): JsonResponse
    {
        $request->validate([
            'route_id'       => 'required|exists:routes,id',
            'schedule_id'    => 'required|exists:schedules,id',
            'num_passengers' => 'required|integer|min:1',
        ]);

        $route    = Route::findOrFail($request->route_id);
        $schedule = Schedule::with('bus')->findOrFail($request->schedule_id);

        $pricePerPax = $schedule->bus->calculatePrice($route->distance_km);
        $total       = $pricePerPax * $request->num_passengers;

        return response()->json([
            'distance_km'   => $route->distance_km,
            'price_per_pax' => $pricePerPax,
            'total'         => $total,
            'formatted'     => 'Rp ' . number_format($total, 0, ',', '.'),
        ]);
    }

    public function checkSeats(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'travel_date' => 'required|date',
        ]);

        $schedule  = Schedule::with('bus')->findOrFail($request->schedule_id);
        $available = $schedule->getAvailableSeats($request->travel_date);

        return response()->json([
            'available' => $available,
            'capacity'  => $schedule->bus->capacity,
        ]);
    }
}
