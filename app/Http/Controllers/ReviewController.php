<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $hasCompletedBooking = Booking::where('user_id', '=', Auth::id())
            ->where('event_id', '=', $event->getKey())
            ->where('status', '=', Booking::STATUS_COMPLETED)
            ->exists();

        abort_unless($hasCompletedBooking, 403, 'Review allowed only after completed booking.');

        Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'event_id' => $event->getKey(),
            ],
            $validated
        );

        return back()->with('success', 'Review submitted.');
    }
}
