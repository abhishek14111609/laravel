<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\StaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'completed'])],
        ]);

        $isAssigned = StaffAssignment::where('booking_id', '=', $booking->getKey())
            ->where('staff_id', '=', Auth::id())
            ->exists();

        abort_unless($isAssigned, 403, 'You are not assigned to this booking.');

        if (! $booking->canTransitionTo($validated['status'])) {
            throw ValidationException::withMessages([
                'status' => 'Invalid status transition requested.',
            ]);
        }

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Booking status updated.');
    }
}
