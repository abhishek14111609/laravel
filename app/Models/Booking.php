<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int|null $event_slot_id
 * @property Carbon|null $date
 * @property string $slot
 * @property string $status
 * @property string $payment_status
 * @property Carbon|null $expires_at
 * @property float|string $total_amount
 * @property string|null $qr_code
 * @property string|null $qr_token
 */
class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'event_id',
        'event_slot_id',
        'date',
        'slot',
        'status',
        'payment_status',
        'expires_at',
        'total_amount',
        'qr_code',
        'qr_token',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'expires_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function eventSlot(): BelongsTo
    {
        return $this->belongsTo(EventSlot::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function staffAssignment(): HasOne
    {
        return $this->hasOne(StaffAssignment::class);
    }

    public function isReservationExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function shouldHoldSlot(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID || $this->expires_at === null;
    }

    public function canTransitionTo(string $nextStatus): bool
    {
        return in_array($nextStatus, self::allowedTransitions()[$this->status] ?? [], true);
    }

    public static function allowedTransitions(): array
    {
        return [
            self::STATUS_PENDING => [self::STATUS_APPROVED, self::STATUS_REJECTED],
            self::STATUS_APPROVED => [self::STATUS_COMPLETED],
            self::STATUS_REJECTED => [],
            self::STATUS_COMPLETED => [],
        ];
    }

    public function getQrImageUrlAttribute(): ?string
    {
        if ($this->qr_code === null) {
            return null;
        }

        if (Str::contains($this->qr_code, 'api.qrserver.com/v1/create-qr-code')) {
            return $this->qr_code;
        }

        return 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($this->qr_code);
    }
}
