<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGuardAndReleaseTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->createOne(['role' => 'admin', 'name' => "Admin O'Brien"]);
    }

    public function test_last_admin_cannot_be_demoted(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
        ])->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_can_be_demoted_when_another_remains(): void
    {
        $first = $this->admin();
        $this->admin();
        $target = User::factory()->createOne(['role' => 'admin', 'name' => 'Second Admin']);

        $this->actingAs($first)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role' => 'user',
        ])->assertSessionHasNoErrors();

        $this->assertSame('user', $target->fresh()->role);
    }

    public function test_last_admin_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $other = $this->admin();

        // Two admins: deleting one is fine, deleting the final one is not.
        $this->actingAs($admin)->delete(route('admin.users.destroy', $other));
        $this->assertNull(User::find($other->id));

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));
        $this->assertNotNull(User::find($admin->id));
    }

    public function test_cancelling_booking_returns_promo_slot(): void
    {
        $user  = User::factory()->createOne();
        $promo = PromoCode::create([
            'code' => 'BALIK', 'discount_type' => 'fixed', 'discount_value' => 5000,
            'min_purchase' => 0, 'max_discount' => 0,
            'usage_limit' => 5, 'used_count' => 1,
            'valid_from' => now()->subDay(), 'valid_until' => now()->addDay(),
            'active' => true,
        ]);

        $bus      = Bus::create(['bus_name' => 'R', 'bus_number' => 'B1', 'capacity' => 40, 'price_per_km' => 1000, 'status' => 'active']);
        $route    = Route::create(['origin' => 'A', 'destination' => 'B', 'distance_km' => 10, 'active' => true]);
        $schedule = Schedule::create(['route_id' => $route->id, 'bus_id' => $bus->id, 'departure_time' => '08:00:00', 'active' => true]);

        $booking = Booking::create([
            'booking_code' => 'RVNTESTPROMO', 'user_id' => $user->id,
            'schedule_id' => $schedule->id, 'route_id' => $route->id,
            'promo_code_id' => $promo->id,
            'origin' => 'A', 'destination' => 'B', 'distance_km' => 10, 'price_per_km' => 1000,
            'num_passengers' => 1, 'travel_date' => now()->addDay()->toDateString(),
            'departure_time' => '08:00:00',
            'subtotal' => 100000, 'discount_amount' => 5000, 'total_price' => 95000,
            'status' => 'pending', 'payment_status' => 'unpaid',
        ]);

        $this->actingAs($user)
            ->patch(route('bookings.cancel', $booking))
            ->assertRedirect();

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame(0, $promo->fresh()->used_count, 'Promo slot must be returned.');
        $this->assertNull($booking->fresh()->promo_code_id);
    }

    public function test_release_is_idempotent(): void
    {
        $user  = User::factory()->createOne();
        $promo = PromoCode::create([
            'code' => 'DUA', 'discount_type' => 'fixed', 'discount_value' => 5000,
            'min_purchase' => 0, 'max_discount' => 0,
            'usage_limit' => 5, 'used_count' => 1,
            'valid_from' => now()->subDay(), 'valid_until' => now()->addDay(),
            'active' => true,
        ]);

        $bus      = Bus::create(['bus_name' => 'R', 'bus_number' => 'B2', 'capacity' => 40, 'price_per_km' => 1000, 'status' => 'active']);
        $route    = Route::create(['origin' => 'A', 'destination' => 'B', 'distance_km' => 10, 'active' => true]);
        $schedule = Schedule::create(['route_id' => $route->id, 'bus_id' => $bus->id, 'departure_time' => '08:00:00', 'active' => true]);

        $booking = Booking::create([
            'booking_code' => 'RVNTESTIDEM', 'user_id' => $user->id,
            'schedule_id' => $schedule->id, 'route_id' => $route->id,
            'promo_code_id' => $promo->id,
            'origin' => 'A', 'destination' => 'B', 'distance_km' => 10, 'price_per_km' => 1000,
            'num_passengers' => 1, 'travel_date' => now()->addDay()->toDateString(),
            'departure_time' => '08:00:00',
            'subtotal' => 100000, 'discount_amount' => 5000, 'total_price' => 95000,
            'status' => 'pending', 'payment_status' => 'unpaid',
        ]);

        $booking->release();
        $booking->release();

        $this->assertSame(0, $promo->fresh()->used_count, 'Double release must not decrement twice.');
    }
}
