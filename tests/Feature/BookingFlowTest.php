<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Passenger;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\SeatMap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end booking lifecycle: create -> seats held -> paid via webhook
 * -> ticket accessible -> cancel releases seats.
 */
class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private const SERVER_KEY = 'SB-Mid-server-TESTKEY';

    private User $user;
    private Route $route;
    private Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.midtrans.server_key' => self::SERVER_KEY]);

        $this->user = User::factory()->createOne(['name' => 'Budi Santoso']);

        $bus = Bus::create([
            'bus_name' => 'Raven Executive', 'bus_number' => 'B-001',
            'capacity' => 4, 'price_per_km' => 1000, 'status' => 'active',
        ]);
        $this->route = Route::create([
            'origin' => 'Jakarta', 'destination' => 'Bandung',
            'distance_km' => 150, 'active' => true,
        ]);
        $this->schedule = Schedule::create([
            'route_id' => $this->route->id, 'bus_id' => $bus->id,
            'departure_time' => '08:00:00', 'active' => true,
        ]);
    }

    private function book(int $passengers = 2, ?string $promo = null): \Illuminate\Testing\TestResponse
    {
        $payload = [
            'route_id' => $this->route->id,
            'schedule_id' => $this->schedule->id,
            'travel_date' => now()->addDays(3)->toDateString(),
            'num_passengers' => $passengers,
            'passengers' => array_map(
                fn ($i) => ['name' => 'Penumpang ' . $i],
                range(1, $passengers),
            ),
        ];

        if ($promo) {
            $payload['promo_code'] = $promo;
        }

        return $this->actingAs($this->user)->post(route('bookings.store'), $payload);
    }

    private function settle(Booking $booking): void
    {
        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => number_format((float) $booking->total_price, 2, '.', ''),
            'transaction_status' => 'settlement',
            'transaction_id' => 'tx-' . $booking->id,
            'payment_type' => 'bank_transfer',
        ];
        $payload['signature_key'] = hash('sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . self::SERVER_KEY);

        $this->postJson(route('payment.notification'), $payload)->assertOk();
    }

    public function test_full_lifecycle_create_pay_and_view_ticket(): void
    {
        $this->book(2)->assertRedirect();

        $booking = Booking::where('user_id', $this->user->id)->sole();

        // Seats are held and passengers recorded.
        $this->assertSame('pending', $booking->status);
        $this->assertSame('unpaid', $booking->payment_status);
        $this->assertCount(2, $booking->passengers);
        $this->assertSame(
            ['S1', 'S2'],
            SeatMap::where('booking_id', $booking->id)->orderBy('seat_number')->pluck('seat_number')->all(),
        );
        $this->assertSame(300000.0, (float) $booking->subtotal);

        // Pay via webhook.
        $this->settle($booking);
        $booking->refresh();
        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame('confirmed', $booking->status);

        // Ticket and detail pages render for the owner.
        $this->actingAs($this->user)->get(route('bookings.ticket', $booking))->assertOk();
        $this->actingAs($this->user)->get(route('bookings.show', $booking))->assertOk();
    }

    public function test_another_user_cannot_view_someone_elses_booking(): void
    {
        $this->book(1);
        $booking = Booking::sole();

        $intruder = User::factory()->createOne();

        $this->actingAs($intruder)->get(route('bookings.show', $booking))->assertForbidden();
        $this->actingAs($intruder)->get(route('bookings.ticket', $booking))->assertForbidden();
    }

    public function test_second_booking_takes_remaining_seats(): void
    {
        $this->book(2);
        $this->book(2);

        $seats = SeatMap::orderBy('seat_number')->pluck('seat_number');
        $this->assertSame(['S1', 'S2', 'S3', 'S4'], $seats->all());
        $this->assertSame(4, $seats->unique()->count(), 'No seat may be double-booked.');
    }

    public function test_booking_is_rejected_when_bus_is_full(): void
    {
        // Capacity is 4.
        $this->book(4);

        $this->book(1)->assertSessionHasErrors('num_passengers');

        $this->assertSame(1, Booking::count());
        $this->assertSame(4, SeatMap::count());
    }

    public function test_cancelling_releases_seats_for_rebooking(): void
    {
        $this->book(4);
        $booking = Booking::sole();

        $this->actingAs($this->user)->patch(route('bookings.cancel', $booking))->assertRedirect();

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame(0, SeatMap::where('status', 'booked')->count());

        // Seats are reusable.
        $this->book(4)->assertSessionHasNoErrors();
        $this->assertSame(4, SeatMap::where('status', 'booked')->count());
    }

    public function test_paid_booking_cannot_be_cancelled_by_user(): void
    {
        $this->book(1);
        $booking = Booking::sole();
        $this->settle($booking);

        $this->actingAs($this->user)
            ->patch(route('bookings.cancel', $booking))
            ->assertForbidden();

        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_promo_discount_is_applied_to_total(): void
    {
        \App\Models\PromoCode::create([
            'code' => 'HEMAT', 'discount_type' => 'percentage', 'discount_value' => 10,
            'min_purchase' => 0, 'max_discount' => 0,
            'usage_limit' => 0, 'used_count' => 0,
            'valid_from' => now()->subDay(), 'valid_until' => now()->addDay(),
            'active' => true,
        ]);

        $this->book(2, 'HEMAT')->assertRedirect();

        $booking = Booking::sole();
        $this->assertSame(30000.0, (float) $booking->discount_amount);
        $this->assertSame(270000.0, (float) $booking->total_price);
    }
}
