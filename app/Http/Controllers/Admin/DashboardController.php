<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count('*');
        $totalBookings = Booking::count('*');
        $totalRevenue = Payment::where('status', '=', 'paid')->sum('amount');
        $recentBookings = Booking::with('user', 'event')->latest('created_at')->take(8)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalBookings', 'totalRevenue', 'recentBookings'));
    }
}
