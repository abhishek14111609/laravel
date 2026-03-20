<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlistItems = Wishlist::with('event.category')
            ->where('user_id', '=', Auth::id())
            ->latest('created_at')
            ->paginate(10);

        return view('user.wishlist', compact('wishlistItems'));
    }

    public function store(Event $event): RedirectResponse
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'event_id' => $event->getKey(),
        ]);

        return back()->with('success', 'Added to wishlist.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        Wishlist::where('user_id', '=', Auth::id())
            ->where('event_id', '=', $event->getKey())
            ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}
