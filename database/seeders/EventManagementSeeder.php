<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Event;
use App\Models\Payment;
use App\Models\StaffAssignment;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EventManagementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@eventapp.test'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_blocked' => false,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@eventapp.test'],
            [
                'name' => 'Operations Staff',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
                'is_blocked' => false,
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'user@eventapp.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'is_blocked' => false,
            ]
        );

        $music = Category::firstOrCreate(['name' => 'Music']);
        $business = Category::firstOrCreate(['name' => 'Business']);

        $eventOne = Event::updateOrCreate(
            ['title' => 'City Jazz Night'],
            [
                'category_id' => $music->id,
                'price' => 49.99,
                'location' => 'Downtown Arena',
                'description' => 'An evening of live jazz performances and curated dining.',
                'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4',
                'total_slots' => 120,
                'is_active' => true,
            ]
        );

        $eventTwo = Event::updateOrCreate(
            ['title' => 'Startup Growth Summit'],
            [
                'category_id' => $business->id,
                'price' => 79.00,
                'location' => 'Innovation Hub',
                'description' => 'Scaling strategies from founders, investors, and growth leaders.',
                'image' => 'https://images.unsplash.com/photo-1511578314322-379afb476865',
                'total_slots' => 80,
                'is_active' => true,
            ]
        );

        $eventOne->slots()->delete();
        $eventTwo->slots()->delete();

        $eventOneSlots = $eventOne->slots()->createMany([
            ['date' => now()->addDays(5)->toDateString(), 'slot' => '10:00 AM - 12:00 PM', 'capacity' => 60, 'booked_count' => 0],
            ['date' => now()->addDays(6)->toDateString(), 'slot' => '3:00 PM - 5:00 PM', 'capacity' => 60, 'booked_count' => 0],
        ]);

        $eventTwo->slots()->createMany([
            ['date' => now()->addDays(10)->toDateString(), 'slot' => '11:00 AM - 2:00 PM', 'capacity' => 40, 'booked_count' => 0],
            ['date' => now()->addDays(11)->toDateString(), 'slot' => '2:00 PM - 5:00 PM', 'capacity' => 40, 'booked_count' => 0],
        ]);

        $seedSlot = $eventOneSlots->first();

        $booking = Booking::updateOrCreate(
            ['user_id' => $user->id, 'event_id' => $eventOne->id, 'slot' => '10:00 AM - 12:00 PM'],
            [
                'event_slot_id' => $seedSlot?->id,
                'date' => now()->addDays(5)->toDateString(),
                'status' => Booking::STATUS_PENDING,
                'payment_status' => Booking::PAYMENT_PAID,
                'expires_at' => null,
                'total_amount' => $eventOne->price,
                'qr_code' => 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=BOOKING-DEMO',
            ]
        );

        Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_amount,
                'method' => 'razorpay',
                'status' => 'paid',
                'transaction_id' => 'pay_demo_txn_001',
                'meta' => ['seeded' => true],
            ]
        );

        StaffAssignment::updateOrCreate(
            ['booking_id' => $booking->id],
            ['staff_id' => $staff->id]
        );

        Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'event_id' => $eventTwo->id,
        ]);
    }
}
