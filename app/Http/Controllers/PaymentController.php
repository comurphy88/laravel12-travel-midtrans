<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SeatMap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Booking $booking): View
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->trashed(), 404);

        $booking->load(['schedule.bus', 'payments']);

        if (!$booking->payment_token && $booking->payment_status === 'unpaid') {
            try {
                $this->configureMidtrans();

                $params = [
                    'transaction_details' => [
                        'order_id'     => $booking->booking_code,
                        'gross_amount' => (int) $booking->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => $booking->user->name,
                        'email'      => $booking->user->email,
                        'phone'      => $booking->user->phone ?? '',
                    ],
                    'item_details' => [
                        [
                            'id'       => $booking->schedule_id,
                            'price'    => (int) ($booking->subtotal / $booking->num_passengers),
                            'quantity' => $booking->num_passengers,
                            'name'     => $booking->origin . ' → ' . $booking->destination
                                . ' ' . $booking->departure_time,
                        ],
                    ],
                    'expiry' => [
                        'start_time' => now()->format('Y-m-d H:i:s O'),
                        'unit'       => 'hour',
                        'duration'   => 24,
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $booking->update(['payment_token' => $snapToken]);

                Payment::updateOrCreate(
                    ['booking_id' => $booking->id, 'status' => 'pending'],
                    [
                        'amount'     => $booking->total_price,
                        'expired_at' => now()->addHours(24),
                        'status'     => 'pending',
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Midtrans token error: ' . $e->getMessage());
            }
        }

        return view('bookings.pay', compact('booking'));
    }

    public function finish(Booking $booking, Request $request): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->trashed(), 404);

        $booking->load(['schedule.bus', 'payments']);

        try {
            $this->configureMidtrans();
            $orderId = $booking->booking_code;
            $status  = \Midtrans\Transaction::status($orderId);
            $this->updateBookingStatus($booking, $status);
        } catch (\Exception $e) {
            Log::error('Midtrans finish error: ' . $e->getMessage());
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function notification(Request $request): Response
    {
        $serverKey = (string) config('services.midtrans.server_key');

        // Without a server key the notification cannot be authenticated at all.
        if ($serverKey === '') {
            Log::error('Midtrans webhook hit but MIDTRANS_SERVER_KEY is empty.');

            return response('Misconfigured', 500);
        }

        // SanitizeInput only merges query/body input, so JSON payloads are not
        // visible via input(). Midtrans posts application/json; read the raw body.
        $payload = json_decode($request->getContent(), true);
        if (! is_array($payload)) {
            return response('Invalid payload', 400);
        }

        $orderId       = (string) ($payload['order_id'] ?? '');
        $statusCode    = (string) ($payload['status_code'] ?? '');
        $grossAmount   = (string) ($payload['gross_amount'] ?? '');
        $signatureKey  = (string) ($payload['signature_key'] ?? '');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey === '' || ! hash_equals($expected, $signatureKey)) {
            Log::warning('Midtrans webhook signature mismatch', ['order_id' => $orderId]);

            // 200 so an unauthenticated caller learns nothing from the response.
            return response('OK', 200);
        }

        try {
            $booking = Booking::where('booking_code', $orderId)->first();

            if (! $booking) {
                Log::warning('Midtrans webhook for unknown booking', ['order_id' => $orderId]);

                return response('OK', 200);
            }

            // Never trust a notification claiming a different amount than what we billed.
            if ((int) round((float) $grossAmount) !== (int) round((float) $booking->total_price)) {
                Log::error('Midtrans webhook amount mismatch', [
                    'order_id' => $orderId,
                    'expected' => $booking->total_price,
                    'received' => $grossAmount,
                ]);

                return response('OK', 200);
            }

            $this->updateBookingStatus($booking, $payload);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());

            return response('Error', 500);
        }

        return response('OK', 200);
    }

    // ── Private ───────────────────────────────────────────────

    private function configureMidtrans(): void
    {
        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }

    private function updateBookingStatus(Booking $booking, mixed $status): void
    {
        $read = fn (string $key) => is_array($status) ? ($status[$key] ?? null) : ($status->$key ?? null);

        $transactionStatus = $read('transaction_status');
        $fraudStatus       = $read('fraud_status');
        $paymentType       = $read('payment_type');
        $transactionId     = $read('transaction_id');
        $grossAmount       = $read('gross_amount') ?? $booking->total_price;

        if (($transactionStatus === 'capture' && $fraudStatus === 'accept') || $transactionStatus === 'settlement') {
            $paymentStatus = 'paid';
            $bookingStatus = 'confirmed';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $paymentStatus = 'unpaid';
            $bookingStatus = 'cancelled';
        } elseif ($transactionStatus === 'pending') {
            $paymentStatus = 'unpaid';
            $bookingStatus = $booking->status;
        } else {
            return;
        }

        DB::transaction(function () use ($booking, $paymentStatus, $bookingStatus, $paymentType, $transactionId, $grossAmount) {
            $booking->update([
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentType,
                'status'         => $bookingStatus,
            ]);

            if ($paymentStatus === 'paid') {
                Payment::updateOrCreate(
                    ['booking_id' => $booking->id, 'transaction_id' => $transactionId],
                    [
                        'amount'         => $grossAmount,
                        'payment_method' => $paymentType,
                        'status'         => 'success',
                        'verified_at'    => now(),
                    ]
                );
            }

            if ($bookingStatus === 'cancelled') {
                $booking->release();
            }
        });
    }
}
