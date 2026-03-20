<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('bookings:expire-reservations')]
#[Description('Expire stale booking reservations and mark payment attempts as failed')]
class ExpireReservationsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expired = DB::transaction(function () {
            $bookings = Booking::where('payment_status', '=', Booking::PAYMENT_PENDING)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->get();

            foreach ($bookings as $booking) {
                $booking->update([
                    'payment_status' => Booking::PAYMENT_FAILED,
                ]);

                Payment::where('booking_id', '=', $booking->getKey())
                    ->where('status', '=', Booking::PAYMENT_PENDING)
                    ->update(['status' => Booking::PAYMENT_FAILED]);
            }

            return $bookings->count();
        });

        $this->info("Expired {$expired} reservation(s).");

        return self::SUCCESS;
    }
}
