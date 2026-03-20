<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\Payment;
use App\Models\StaffAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_creation_creates_reservation_without_consuming_slot_count(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role' => User::ROLE_USER, 'is_blocked' => false]);
        $category = Category::create(['name' => 'Tests']);
        $event = Event::create([
            'title' => 'Sample Event',
            'category_id' => $category->id,
            'price' => 120,
            'location' => 'Test City',
            'description' => 'Test description',
            'total_slots' => 5,
            'is_active' => true,
        ]);
        $slot = EventSlot::create([
            'event_id' => $event->id,
            'date' => now()->addDay()->toDateString(),
            'slot' => '10:00 AM - 12:00 PM',
            'capacity' => 2,
            'booked_count' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'event_slot_id' => $slot->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'event_slot_id' => $slot->id,
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $slot->refresh();
        $this->assertSame(0, $slot->booked_count);
    }

    public function test_slot_reservation_blocks_second_booking_when_capacity_is_full(): void
    {
        $category = Category::create(['name' => 'Tests 2']);
        $event = Event::create([
            'title' => 'Capacity Event',
            'category_id' => $category->id,
            'price' => 80,
            'location' => 'Capacity City',
            'description' => 'Test description',
            'total_slots' => 1,
            'is_active' => true,
        ]);
        $slot = EventSlot::create([
            'event_id' => $event->id,
            'date' => now()->addDay()->toDateString(),
            'slot' => '2:00 PM - 4:00 PM',
            'capacity' => 1,
            'booked_count' => 0,
        ]);

        /** @var User $userOne */
        $userOne = User::factory()->createOne(['role' => User::ROLE_USER]);
        /** @var User $userTwo */
        $userTwo = User::factory()->createOne(['role' => User::ROLE_USER]);

        $this->actingAs($userOne)->post(route('bookings.store', $event), [
            'event_slot_id' => $slot->id,
        ])->assertRedirect();

        $this->actingAs($userTwo)
            ->post(route('bookings.store', $event), ['event_slot_id' => $slot->id])
            ->assertSessionHasErrors('event_slot_id');
    }

    public function test_razorpay_requires_signature_verification_fields(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role' => User::ROLE_USER]);
        $category = Category::create(['name' => 'Payments']);
        $event = Event::create([
            'title' => 'Payment Event',
            'category_id' => $category->id,
            'price' => 150,
            'location' => 'Payment City',
            'description' => 'Test description',
            'total_slots' => 10,
            'is_active' => true,
        ]);
        $slot = EventSlot::create([
            'event_id' => $event->id,
            'date' => now()->addDay()->toDateString(),
            'slot' => '6:00 PM - 8:00 PM',
            'capacity' => 10,
            'booked_count' => 0,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'event_slot_id' => $slot->id,
            'date' => $slot->date,
            'slot' => $slot->slot,
            'status' => Booking::STATUS_PENDING,
            'payment_status' => Booking::PAYMENT_PENDING,
            'expires_at' => now()->addMinutes(15),
            'total_amount' => 150,
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 150,
            'method' => 'razorpay',
            'status' => Booking::PAYMENT_PENDING,
            'gateway_order_id' => 'order_test_123',
        ]);

        $this->actingAs($user)
            ->post(route('checkout.process', $booking), [
                'method' => 'razorpay',
                'razorpay_payment_id' => 'pay_test_123',
                'razorpay_order_id' => 'order_test_123',
                // signature intentionally missing
            ])
            ->assertSessionHasErrors('razorpay_signature');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);
    }

    public function test_paid_booking_cannot_be_processed_again(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role' => User::ROLE_USER]);
        $category = Category::create(['name' => 'Reprocess']);
        $event = Event::create([
            'title' => 'Paid Event',
            'category_id' => $category->id,
            'price' => 60,
            'location' => 'Secure City',
            'description' => 'Test description',
            'total_slots' => 5,
            'is_active' => true,
        ]);
        $slot = EventSlot::create([
            'event_id' => $event->id,
            'date' => now()->addDay()->toDateString(),
            'slot' => '8:00 PM - 9:00 PM',
            'capacity' => 5,
            'booked_count' => 1,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'event_slot_id' => $slot->id,
            'date' => $slot->date,
            'slot' => $slot->slot,
            'status' => Booking::STATUS_PENDING,
            'payment_status' => Booking::PAYMENT_PAID,
            'expires_at' => null,
            'total_amount' => 60,
            'qr_token' => 'seeded-token',
        ]);

        $this->actingAs($user)
            ->post(route('checkout.process', $booking), ['method' => 'cod'])
            ->assertSessionHasErrors('method');
    }

    public function test_status_transitions_are_strict_for_admin_and_staff(): void
    {
        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => User::ROLE_ADMIN]);
        /** @var User $staff */
        $staff = User::factory()->createOne(['role' => User::ROLE_STAFF]);
        /** @var User $customer */
        $customer = User::factory()->createOne(['role' => User::ROLE_USER]);

        $category = Category::create(['name' => 'Status']);
        $event = Event::create([
            'title' => 'Status Event',
            'category_id' => $category->id,
            'price' => 90,
            'location' => 'Status City',
            'description' => 'Test description',
            'total_slots' => 2,
            'is_active' => true,
        ]);
        $slot = EventSlot::create([
            'event_id' => $event->id,
            'date' => now()->addDay()->toDateString(),
            'slot' => '1:00 PM - 3:00 PM',
            'capacity' => 2,
            'booked_count' => 1,
        ]);

        $booking = Booking::create([
            'user_id' => $customer->id,
            'event_id' => $event->id,
            'event_slot_id' => $slot->id,
            'date' => $slot->date,
            'slot' => $slot->slot,
            'status' => Booking::STATUS_PENDING,
            'payment_status' => Booking::PAYMENT_PAID,
            'total_amount' => 90,
            'expires_at' => null,
        ]);

        StaffAssignment::create([
            'booking_id' => $booking->id,
            'staff_id' => $staff->id,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.bookings.update-status', $booking), ['status' => Booking::STATUS_COMPLETED])
            ->assertSessionHasErrors('status');

        $this->actingAs($admin)
            ->patch(route('admin.bookings.update-status', $booking), ['status' => Booking::STATUS_APPROVED])
            ->assertSessionHasNoErrors();

        $booking->refresh();
        $this->assertSame(Booking::STATUS_APPROVED, $booking->status);

        $this->actingAs($staff)
            ->patch(route('staff.bookings.update-status', $booking), ['status' => Booking::STATUS_COMPLETED])
            ->assertSessionHasNoErrors();

        $booking->refresh();
        $this->assertSame(Booking::STATUS_COMPLETED, $booking->status);
    }
}
