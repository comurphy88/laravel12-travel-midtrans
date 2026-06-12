<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$clientKey    = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = config('services.midtrans.is_sanitized');
        Config::$is3ds        = config('services.midtrans.is_3ds');
    }

    // -------------------------------------------------------------------------
    // Public actions
    // -------------------------------------------------------------------------

    /**
     * Show payment page and create a Midtrans Snap token.
     */
    public function show(Booking $booking): View|RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403, 'Anda tidak memiliki akses ke booking ini.');
        abort_if($booking->payment_status === 'paid',    404, 'Booking sudah dibayar.');
        abort_if($booking->status === 'cancelled',       404, 'Booking sudah dibatalkan.');
        abort_if($booking->total_price <= 0,             400, 'Harga booking tidak valid.');

        $booking->load(['busRoute.bus', 'destination', 'user']);

        $orderId     = $booking->booking_code . '-' . time();
        $grossAmount = (int) $booking->total_price;
        $itemDetails = $this->buildItemDetails($booking, $grossAmount);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email'      => $booking->user->email,
                'phone'      => $booking->user->phone ?? '',
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap error: ' . $e->getMessage());

            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
        }

        $booking->update(['payment_token' => $orderId]);

        return view('bookings.pay', [
            'booking'      => $booking,
            'snapToken'    => $snapToken,
            'clientKey'    => config('services.midtrans.client_key'),
            'isProduction' => config('services.midtrans.is_production'),
        ]);
    }

    /**
     * Handle the finish redirect from Snap (client-side callback).
     * Actual status is authoritative from the webhook — this just redirects.
     */
    public function finish(Request $request, Booking $booking): RedirectResponse
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Pembayaran sedang diproses. Status akan diperbarui otomatis.');
    }

    /**
     * Handle Midtrans webhook notifications.
     */
    public function notification(Request $request): JsonResponse
    {
        try {
            $notification = new Notification;
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());

            return response()->json(['message' => 'Notifikasi tidak valid'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $orderId           = $notification->order_id           ?? null;
        $transactionStatus = $notification->transaction_status ?? null;
        $transactionId     = $notification->transaction_id     ?? null;
        $paymentType       = $notification->payment_type       ?? 'unknown';
        $fraudStatus       = $notification->fraud_status       ?? 'accept';
        $grossAmount       = $notification->gross_amount       ?? 0;

        if (! $orderId || ! $transactionStatus || ! $transactionId) {
            Log::warning('Midtrans notification: missing required fields');

            return response()->json(['message' => 'Data tidak lengkap'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $booking = $this->resolveBooking($orderId);

        if ($booking instanceof JsonResponse) {
            return $booking; // error response already built
        }

        if ($booking->payment_token && $booking->payment_token !== $orderId) {
            Log::warning("Midtrans notification: order_id mismatch for booking {$booking->booking_code}");

            return response()->json(['message' => 'Order ID tidak cocok'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (abs((float) $grossAmount - (float) $booking->total_price) > 0.01) {
            Log::warning("Midtrans amount mismatch for {$orderId}: expected {$booking->total_price}, got {$grossAmount}");

            return response()->json(['message' => 'Jumlah pembayaran tidak cocok'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $paymentStatus = $this->resolvePaymentStatus($transactionStatus, $fraudStatus);

        Payment::updateOrCreate(
            ['booking_id' => $booking->id, 'transaction_id' => $transactionId],
            [
                'amount'         => $grossAmount,
                'payment_method' => $paymentType,
                'status'         => $paymentStatus,
            ]
        );

        $this->applyBookingStatus($booking, $paymentStatus, $paymentType, $transactionStatus, $grossAmount);

        Log::info("Midtrans notification processed: {$orderId} -> {$transactionStatus} -> {$paymentStatus}");

        return response()->json(['message' => 'OK']);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build the Midtrans item_details array, adding an adjustment line if
     * integer rounding causes the item total to differ from the gross amount.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildItemDetails(Booking $booking, int $grossAmount): array
    {
        $unitPrice = (int) round($grossAmount / $booking->num_passengers);
        $items     = [];

        if ($booking->busRoute) {
            $items[] = [
                'id'       => 'ROUTE-' . $booking->bus_route_id,
                'price'    => $unitPrice,
                'quantity' => $booking->num_passengers,
                'name'     => substr($booking->busRoute->route_name, 0, 50),
            ];
        } elseif ($booking->destination) {
            $items[] = [
                'id'       => 'DEST-' . $booking->destination_id,
                'price'    => $unitPrice,
                'quantity' => $booking->num_passengers,
                'name'     => substr($booking->destination->name, 0, 50),
            ];
        }

        // Rounding adjustment
        $itemTotal = array_sum(array_map(fn ($i) => $i['price'] * $i['quantity'], $items));
        $diff      = $grossAmount - $itemTotal;

        if ($diff !== 0 && count($items) > 0) {
            $items[] = [
                'id'       => 'ADJ',
                'price'    => $diff,
                'quantity' => 1,
                'name'     => 'Penyesuaian harga',
            ];
        }

        return $items;
    }

    /**
     * Parse the order_id to find the corresponding Booking, or return a 4xx JsonResponse.
     */
    private function resolveBooking(string $orderId): Booking|JsonResponse
    {
        $sep = strrpos($orderId, '-');

        if ($sep === false) {
            Log::warning("Midtrans notification: invalid order_id format {$orderId}");

            return response()->json(['message' => 'Order ID tidak valid'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $bookingCode = substr($orderId, 0, $sep);
        $booking     = Booking::where('booking_code', $bookingCode)->first();

        if (! $booking) {
            Log::warning("Midtrans notification: booking not found for order {$orderId}");

            return response()->json(['message' => 'Pesanan tidak ditemukan'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $booking;
    }

    /**
     * Map a Midtrans transaction status to an internal payment status string.
     */
    private function resolvePaymentStatus(string $transactionStatus, string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture'                      => $fraudStatus === 'accept' ? 'success' : 'pending',
            'settlement'                   => 'success',
            'cancel', 'deny', 'expire'     => 'failed',
            'refund', 'partial_refund'     => 'refunded',
            default                        => 'pending',
        };
    }

    /**
     * Update the booking record and write an activity log entry based on payment outcome.
     */
    private function applyBookingStatus(
        Booking $booking,
        string  $paymentStatus,
        string  $paymentType,
        string  $transactionStatus,
        mixed   $grossAmount,
    ): void {
        match ($paymentStatus) {
            'success' => (function () use ($booking, $paymentType, $grossAmount): void {
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentType,
                    'status'         => 'confirmed',
                ]);
                ActivityLog::logWebhook(
                    'payment',
                    "Pembayaran berhasil: {$booking->booking_code} via {$paymentType} (Rp " .
                        number_format($grossAmount, 0, ',', '.') . ')',
                    'bookings',
                    $booking->id
                );
            })(),

            'failed' => (function () use ($booking, $transactionStatus): void {
                $booking->update(['payment_status' => 'unpaid']);
                ActivityLog::logWebhook(
                    'payment',
                    "Pembayaran gagal: {$booking->booking_code} ({$transactionStatus})",
                    'bookings',
                    $booking->id
                );
            })(),

            'refunded' => (function () use ($booking): void {
                $booking->update([
                    'payment_status' => 'refunded',
                    'status'         => 'cancelled',
                ]);
                ActivityLog::logWebhook(
                    'payment',
                    "Pembayaran di-refund: {$booking->booking_code}",
                    'bookings',
                    $booking->id
                );
            })(),

            default => null,
        };
    }
}