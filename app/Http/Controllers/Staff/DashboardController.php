<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $assignedBookings = StaffAssignment::with(['booking.event', 'booking.user'])
            ->where('staff_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('staff.dashboard', compact('assignedBookings'));
    }
}
