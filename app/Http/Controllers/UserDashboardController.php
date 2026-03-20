<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with(['event.category', 'payment', 'staffAssignment.staff'])
            ->where('user_id', '=', Auth::id())
            ->latest('created_at')
            ->paginate(10);

        $wishlistCount = Wishlist::where('user_id', '=', Auth::id())->count('*');

        return view('user.dashboard', compact('bookings', 'wishlistCount'));
    }
}
