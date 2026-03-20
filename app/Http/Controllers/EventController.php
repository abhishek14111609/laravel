<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::with(['category'])
            ->withAvg('reviews', 'rating')
            ->active();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->string('search')->trim() . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', '=', (int) $request->integer('category'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->string('location')->trim() . '%');
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        if ($request->filled('date')) {
            $selectedDate = (string) $request->input('date');
            $query->whereHas('slots', function ($slotQuery) use ($selectedDate) {
                $slotQuery->whereDate('date', '=', $selectedDate)
                    ->whereColumn('booked_count', '<', 'capacity');
            });
        }

        $events = $query->latest('created_at')->paginate(9)->withQueryString();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('events.index', compact('events', 'categories'));
    }

    public function show(Event $event): View
    {
        $event->load([
            'category',
            'slots' => fn($query) => $query
                ->withCount([
                    'bookings as active_reservations_count' => fn($bookingQuery) => $bookingQuery
                        ->where('payment_status', '=', Booking::PAYMENT_PENDING)
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '>', now()),
                ])
                ->orderBy('date', 'asc')
                ->orderBy('slot', 'asc'),
            'reviews.user',
        ])->loadAvg('reviews', 'rating');

        return view('events.show', compact('event'));
    }
}
