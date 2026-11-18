<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\PromoCode;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Money-path regression tests: webhook authentication and promo usage caps.
 */
class PaymentAndBookingTest extends TestCase
{
    use RefreshDatabase;

    private const SERVER_KEY = 'SB-Mid-server-TESTKEY';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.midtrans.server_key' => self::SERVER_KEY]);
    }

    private function makeBooking(float $total = 150000): Booking
    {
        $user     = User::factory()->createOne();
        $bus      = Bus::create([
            'bus_name' => 'RavenExecutive', 'bus_number' => 'B' . uniqid(),
            'capacity' => 40, 'price_per_km' => 1000, 'status' => 'active',
        ]);
        $route    = Route::create([
            'origin' => 'Jakarta', 'destination' => 'Bandung',
            'distance_km' => 150, 'active' => true,
        ]);
        $schedule = Schedule::create([
            'route_id' => $route->id, 'bus_id' => $bus->id,
            'departure_time' => '08:00:00', 'active' => true,
        ]);

        return Booking::create([
            'booking_code' => 'RVN20260101ABCDEF',
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'route_id' => $route->id,
            'origin' => 'Jakarta', 'destination' => 'Bandung',
            'distance_km' => 150, 'price_per_km' => 1000,
            'num_passengers' => 1,
            'travel_date' => now()->addDay()->toDateString(),
            'departure_time' => '08:00:00',
            'subtotal' => $total, 'discount_amount' => 0, 'total_price' => $total,
            'status' => 'pending', 'payment_status' => 'unpaid',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        $payload = array_merge([
            'order_id' => 'RVN20260101ABCDEF',
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'tx-1',
            'payment_type' => 'bank_transfer',
        ], $overrides);

        $payload['signature_key'] = hash('sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . self::SERVER_KEY);

        // Allow tests to force a bad signature after the valid one is computed above.
        return array_merge($payload, array_intersect_key($overrides, ['signature_key' => true]));
    }

    public function test_valid_signature_marks_booking_paid(): void
    {
        $booking = $this->makeBooking();

        $this->postJson(route('payment.notification'), $this->payload())
            ->assertOk();

        $this->assertSame('paid', $booking->fresh()->payment_status);
    }

    public function test_forged_signature_is_ignored(): void
    {
        $booking = $this->makeBooking();
        $payload = $this->payload(['signature_key' => str_repeat('a', 128)]);

        $this->postJson(route('payment.notification'), $payload)->assertOk();

        $this->assertSame('unpaid', $booking->fresh()->payment_status);
    }

    public function test_underpaid_gross_amount_is_rejected(): void
    {
        $booking = $this->makeBooking(150000);

        // Valid signature, but Midtrans reports a different amount than we billed.
        $this->postJson(route('payment.notification'), $this->payload(['gross_amount' => '1000.00']))
            ->assertOk();

        $this->assertSame('unpaid', $booking->fresh()->payment_status);
    }

    public function test_empty_server_key_refuses_to_process(): void
    {
        config(['services.midtrans.server_key' => '']);
        $booking = $this->makeBooking();

        $this->postJson(route('payment.notification'), $this->payload())
            ->assertStatus(500);

        $this->assertSame('unpaid', $booking->fresh()->payment_status);
    }

    public function test_promo_code_stops_at_usage_limit(): void
    {
        $user = User::factory()->createOne();

        PromoCode::create([
            'code' => 'SATU', 'discount_type' => 'fixed', 'discount_value' => 10000,
            'min_purchase' => 0, 'max_discount' => 0,
            'usage_limit' => 1, 'used_count' => 1,
            'valid_from' => now()->subDay(), 'valid_until' => now()->addDay(),
            'active' => true,
        ]);

        $bus      = Bus::create([
            'bus_name' => 'Raven', 'bus_number' => 'B' . uniqid(),
            'capacity' => 40, 'price_per_km' => 1000, 'status' => 'active',
        ]);
        $route    = Route::create(['origin' => 'A', 'destination' => 'B', 'distance_km' => 10, 'active' => true]);
        $schedule = Schedule::create(['route_id' => $route->id, 'bus_id' => $bus->id, 'departure_time' => '09:00:00', 'active' => true]);

        $this->actingAs($user)->post(route('bookings.store'), [
            'route_id' => $route->id,
            'schedule_id' => $schedule->id,
            'travel_date' => now()->addDays(2)->toDateString(),
            'num_passengers' => 1,
            'promo_code' => 'SATU',
            'passengers' => [['name' => 'Budi']],
        ])->assertRedirect();

        $booking = Booking::where('user_id', $user->id)->firstOrFail();

        $this->assertNull($booking->promo_code_id, 'Exhausted promo must not be applied.');
        $this->assertEquals(0, (float) $booking->discount_amount);
        $this->assertSame(1, PromoCode::where('code', 'SATU')->value('used_count'));
    }
}
