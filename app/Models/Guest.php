<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_code',
        'name',
        'identity_type',
        'identity_number',
        'phone',
        'email',
        'address',
        'profession',
        'company',
        'nationality',
        'birth_date',
        'member_card_no',
    ];

    /**
     * Get guest reservations.
     *
     * @return HasMany<Reservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
