<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'current_status_id',
    ];

    /**
     * Get room type.
     *
     * @return BelongsTo<RoomType, $this>
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get status of the room.
     *
     * @return BelongsTo<RoomStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(RoomStatus::class, 'current_status_id');
    }

    /**
     * Get reservation allocations for this room.
     *
     * @return HasMany<ReservationRoom, $this>
     */
    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }

    /**
     * Get inspections for this room.
     *
     * @return HasMany<RoomInspection, $this>
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(RoomInspection::class);
    }
}
