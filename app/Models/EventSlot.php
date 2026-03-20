<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $slot
 * @property int $capacity
 * @property int $booked_count
 * @property int|null $active_reservations_count
 */
class EventSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'date',
        'slot',
        'capacity',
        'booked_count',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availableSpots(): int
    {
        $reservedCount = $this->active_reservations_count ?? $this->activeReservationCount();

        return max(0, $this->capacity - $this->booked_count - $reservedCount);
    }

    public function activeReservationCount(): int
    {
        return $this->bookings()
            ->where('payment_status', Booking::PAYMENT_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->count();
    }
}
