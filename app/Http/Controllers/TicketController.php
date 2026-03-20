<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\BookingQrToken;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function verify(string $token): View
    {
        try {
            $bookingId = BookingQrToken::decode($token);
        } catch (\Throwable $exception) {
            abort(404, 'Invalid ticket token.');
        }

        $booking = Booking::with('event', 'user')->findOrFail($bookingId);

        abort_unless($booking->qr_token === $token, 404, 'Ticket token mismatch.');

        return view('user.ticket-verify', compact('booking'));
    }
}
