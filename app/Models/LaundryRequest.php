<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'room_id',
        'request_date',
        'status', // pending, processing, completed
        'total_amount',
        'payment_type',
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the reservation.
     *
     * @return BelongsTo<Reservation, $this>
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the room.
     *
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get items in this laundry request.
     *
     * @return HasMany<LaundryItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(LaundryItem::class);
    }
}
