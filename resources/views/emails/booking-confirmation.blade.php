<h2>Booking Confirmed</h2>
<p>Hello {{ $booking->user->name }},</p>
<p>Your booking has been placed successfully.</p>
<ul>
    <li>Booking ID: #{{ $booking->id }}</li>
    <li>Event: {{ $booking->event->title }}</li>
    <li>Date: {{ $booking->date->format('d M, Y') }}</li>
    <li>Slot: {{ $booking->slot }}</li>
    <li>Amount: ${{ number_format($booking->total_amount, 2) }}</li>
    <li>Status: {{ ucfirst($booking->status) }}</li>
</ul>
<p>Keep this booking email for future reference.</p>
