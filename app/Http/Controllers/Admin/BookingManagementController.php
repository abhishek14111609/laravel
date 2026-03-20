<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\StaffAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingManagementController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with(['user', 'event', 'staffAssignment.staff', 'payment'])
            ->latest('created_at')
            ->paginate(15);
        $staffUsers = User::where('role', '=', User::ROLE_STAFF)
            ->where('is_blocked', '=', false)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.bookings.index', compact('bookings', 'staffUsers'));
    }

    public function assignStaff(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => ['required', Rule::exists('users', 'id')->where(fn($query) => $query->where('role', '=', User::ROLE_STAFF))],
        ]);

        StaffAssignment::updateOrCreate(
            ['booking_id' => $booking->getKey()],
            ['staff_id' => $validated['staff_id']]
        );

        return back()->with('success', 'Staff assigned successfully.');
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([Booking::STATUS_APPROVED, Booking::STATUS_REJECTED])],
        ]);

        if (! $booking->canTransitionTo($validated['status'])) {
            throw ValidationException::withMessages([
                'status' => 'Invalid status transition requested.',
            ]);
        }

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Booking status updated.');
    }
}
