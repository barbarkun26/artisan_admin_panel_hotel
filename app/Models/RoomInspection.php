<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'room_id',
        'inspected_by',
        'inspection_date',
        'room_condition',
        'missing_items',
        'damages',
        'additional_charge',
        'notes',
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'inspection_date' => 'datetime',
            'additional_charge' => 'decimal:2',
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
     * Get the inspector (staff).
     *
     * @return BelongsTo<User, $this>
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }
}
