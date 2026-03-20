<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\EventSlot;
use App\Models\Payment;
use App\Support\BookingQrToken;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class CheckoutController extends Controller
{
    public function show(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load('event', 'payment', 'eventSlot');

        if ($booking->payment_status === Booking::PAYMENT_PENDING && $booking->isReservationExpired()) {
            $booking->update([
                'payment_status' => Booking::PAYMENT_FAILED,
            ]);

            $booking->payment()?->update([
                'status' => Booking::PAYMENT_FAILED,
            ]);
        }

        $razorpayOrderId = null;
        $razorpayKey = config('services.razorpay.key');

        if ($booking->payment_status !== Booking::PAYMENT_PAID) {
            $razorpayOrderId = $this->prepareRazorpayOrder($booking);
        }

        return view('checkout.show', compact('booking', 'razorpayOrderId', 'razorpayKey'));
    }

    public function process(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'method' => ['required', Rule::in(['razorpay', 'cod'])],
            'razorpay_payment_id' => ['nullable', 'string', 'max:100'],
            'razorpay_order_id' => ['nullable', 'string', 'max:150'],
            'razorpay_signature' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($booking, $validated, $request) {
                $booking->refresh();

                if ($booking->payment_status === Booking::PAYMENT_PAID) {
                    throw ValidationException::withMessages([
                        'method' => 'Payment has already been completed for this booking.',
                    ]);
                }

                if ($booking->qr_token !== null) {
                    throw ValidationException::withMessages([
                        'method' => 'Checkout has already been finalized for this booking.',
                    ]);
                }

                if ($booking->isReservationExpired()) {
                    $booking->update(['payment_status' => Booking::PAYMENT_FAILED]);
                    throw ValidationException::withMessages([
                        'method' => 'Your reservation has expired. Please rebook the slot.',
                    ]);
                }

                $payment = Payment::where('booking_id', '=', $booking->getKey())
                    ->lockForUpdate()
                    ->first();

                if ($payment !== null && $payment->method !== $validated['method']) {
                    throw ValidationException::withMessages([
                        'method' => 'Payment method is locked once a payment attempt starts.',
                    ]);
                }

                if ($validated['method'] === 'cod') {
                    $this->processCashOnDelivery($booking, $payment);

                    return;
                }

                $this->processRazorpayPayment($request, $booking, $payment);
            });
        } catch (SignatureVerificationError $exception) {
            return back()->withErrors([
                'method' => 'Payment signature verification failed.',
            ]);
        }

        return redirect()->route('user.dashboard')->with('success', 'Payment processed and booking confirmed.');
    }

    private function processCashOnDelivery(Booking $booking, ?Payment $payment): void
    {
        $payment = $payment ?? new Payment(['booking_id' => $booking->id]);
        $payment->fill([
            'amount' => $booking->total_amount,
            'method' => 'cod',
            'status' => Booking::PAYMENT_PENDING,
            'meta' => ['source' => 'checkout-cod'],
        ]);
        $payment->save();

        $this->consumeSlotIfAvailable($booking);
        $this->issueTicketAndQueueMail($booking, Booking::PAYMENT_PENDING);
    }

    private function processRazorpayPayment(Request $request, Booking $booking, ?Payment $payment): void
    {
        $request->validate([
            'razorpay_payment_id' => ['required', 'string', 'max:100'],
            'razorpay_order_id' => ['required', 'string', 'max:150'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        if ($payment === null || $payment->gateway_order_id === null) {
            throw ValidationException::withMessages([
                'method' => 'Razorpay order not initialized. Please reload checkout.',
            ]);
        }

        if ($payment->gateway_order_id !== $request->string('razorpay_order_id')->toString()) {
            throw ValidationException::withMessages([
                'method' => 'Razorpay order mismatch detected.',
            ]);
        }

        if (! config('services.razorpay.key') || ! config('services.razorpay.secret')) {
            throw ValidationException::withMessages([
                'method' => 'Razorpay configuration is missing.',
            ]);
        }

        $api = new Api(
            (string) config('services.razorpay.key'),
            (string) config('services.razorpay.secret')
        );

        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $request->string('razorpay_order_id')->toString(),
            'razorpay_payment_id' => $request->string('razorpay_payment_id')->toString(),
            'razorpay_signature' => $request->string('razorpay_signature')->toString(),
        ]);

        $payment->update([
            'status' => Booking::PAYMENT_PAID,
            'transaction_id' => $request->string('razorpay_payment_id')->toString(),
            'gateway_signature' => $request->string('razorpay_signature')->toString(),
            'meta' => array_merge($payment->meta ?? [], ['source' => 'checkout-verified']),
        ]);

        $this->consumeSlotIfAvailable($booking);
        $this->issueTicketAndQueueMail($booking, Booking::PAYMENT_PAID);
    }

    private function consumeSlotIfAvailable(Booking $booking): void
    {
        $slotId = $booking->event_slot_id;
        if ($slotId === null) {
            $slotId = EventSlot::where('event_id', '=', $booking->event_id)
                ->whereDate('date', '=', $booking->date)
                ->where('slot', '=', $booking->slot)
                ->value('id');
        }

        $slot = EventSlot::whereKey($slotId)
            ->lockForUpdate()
            ->first();

        if (! $slot) {
            throw ValidationException::withMessages([
                'method' => 'Slot is missing for this booking.',
            ]);
        }

        if ($slot->booked_count >= $slot->capacity) {
            throw ValidationException::withMessages([
                'method' => 'Slot capacity is full. Please contact support.',
            ]);
        }

        $slot->increment('booked_count');
    }

    private function issueTicketAndQueueMail(Booking $booking, string $paymentStatus): void
    {
        $token = BookingQrToken::encode($booking->id);
        $ticketUrl = route('tickets.verify', ['token' => $token]);

        $booking->update([
            'payment_status' => $paymentStatus,
            'expires_at' => null,
            'qr_token' => $token,
            'qr_code' => $ticketUrl,
        ]);

        DB::afterCommit(function () use ($booking): void {
            $booking->load('event', 'user');
            Mail::to($booking->user->email)->queue(new BookingConfirmationMail($booking));
        });
    }

    private function prepareRazorpayOrder(Booking $booking): ?string
    {
        if ($booking->isReservationExpired()) {
            return null;
        }

        if (! config('services.razorpay.key') || ! config('services.razorpay.secret')) {
            return null;
        }

        $payment = Payment::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_amount,
                'method' => 'razorpay',
                'status' => Booking::PAYMENT_PENDING,
                'meta' => ['source' => 'checkout-initialize'],
            ]
        );

        if ($payment->method !== 'razorpay') {
            return null;
        }

        if ($payment->gateway_order_id) {
            return $payment->gateway_order_id;
        }

        $api = new Api(
            (string) config('services.razorpay.key'),
            (string) config('services.razorpay.secret')
        );

        $order = $api->order->create([
            'receipt' => 'booking_' . $booking->id,
            'amount' => (int) round(((float) $booking->total_amount) * 100),
            'currency' => config('services.razorpay.currency', 'INR'),
            'payment_capture' => 1,
        ]);

        $payment->update([
            'gateway_order_id' => $order['id'],
        ]);

        return $order['id'];
    }
}
