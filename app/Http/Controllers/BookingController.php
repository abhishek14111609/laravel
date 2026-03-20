<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'event_slot_id' => [
                'required',
                'integer',
                Rule::exists('event_slots', 'id')->where(fn($query) => $query->where('event_id', '=', $event->getKey())),
            ],
        ]);

        $booking = DB::transaction(function () use ($validated, $event) {
            $slot = EventSlot::whereKey($validated['event_slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $activeReservations = $slot->bookings()
                ->where('payment_status', '=', Booking::PAYMENT_PENDING)
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->count();

            // Keep slot consumption delayed until payment confirmation.
            if (($slot->booked_count + $activeReservations) >= $slot->capacity) {
                throw ValidationException::withMessages([
                    'event_slot_id' => 'Selected slot is no longer available.',
                ]);
            }

            return Booking::create([
                'user_id' => Auth::id(),
                'event_id' => $event->getKey(),
                'event_slot_id' => $slot->id,
                'date' => $slot->date,
                'slot' => $slot->slot,
                'status' => Booking::STATUS_PENDING,
                'payment_status' => Booking::PAYMENT_PENDING,
                'expires_at' => Carbon::now()->addMinutes(15),
                'total_amount' => $event->price,
            ]);
        });

        return redirect()->route('checkout.show', $booking)->with('success', 'Booking created. Complete payment to confirm.');
    }
}
