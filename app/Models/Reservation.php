<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'guest_id',
        'reservation_date',
        'checkin_date',
        'checkout_date',
        'total_guest',
        'status', // pending, checkin, checkout, cancelled, skipper
        'created_by',
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'checkin_date' => 'date',
            'checkout_date' => 'date',
        ];
    }

    /**
     * Get the guest.
     *
     * @return BelongsTo<Guest, $this>
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the creator (staff).
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get rooms reserved in this booking.
     *
     * @return HasMany<ReservationRoom, $this>
     */
    public function reservationRooms(): HasMany
    {
        return $this->hasMany(ReservationRoom::class);
    }

    /**
     * Get check-in record.
     *
     * @return HasOne<Checkin, $this>
     */
    public function checkin(): HasOne
    {
        return $this->hasOne(Checkin::class);
    }

    /**
     * Get check-out record.
     *
     * @return HasOne<Checkout, $this>
     */
    public function checkout(): HasOne
    {
        return $this->hasOne(Checkout::class);
    }

    /**
     * Get payments.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get invoice.
     *
     * @return HasOne<Invoice, $this>
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get F&B orders.
     *
     * @return HasMany<FnbOrder, $this>
     */
    public function fnbOrders(): HasMany
    {
        return $this->hasMany(FnbOrder::class);
    }

    /**
     * Get laundry requests.
     *
     * @return HasMany<LaundryRequest, $this>
     */
    public function laundryRequests(): HasMany
    {
        return $this->hasMany(LaundryRequest::class);
    }

    /**
     * Get additional charges (e.g. damages, extra bed).
     *
     * @return HasMany<AdditionalCharge, $this>
     */
    public function additionalCharges(): HasMany
    {
        return $this->hasMany(AdditionalCharge::class);
    }

    /**
     * Get inspections.
     *
     * @return HasMany<RoomInspection, $this>
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(RoomInspection::class);
    }

    /**
     * Calculate nights stayed.
     *
     * @return int
     */
    public function getNightsCountAttribute(): int
    {
        $start = Carbon::parse($this->checkin_date);
        $end = Carbon::parse($this->checkout_date);
        $nights = $start->diffInDays($end);
        return $nights > 0 ? $nights : 1;
    }

    /**
     * Calculate total room charges (base rate + extra beds).
     *
     * @return float
     */
    public function getRoomChargesTotalAttribute(): float
    {
        $nights = $this->nights_count;
        $total = 0;
        foreach ($this->reservationRooms as $resRoom) {
            $total += ($resRoom->room_rate * $nights) + ($resRoom->extra_bed_qty * $resRoom->extra_bed_price * $nights);
        }
        return (float) $total;
    }

    /**
     * Calculate total F&B orders.
     *
     * @return float
     */
    public function getFnbChargesTotalAttribute(): float
    {
        return (float) $this->fnbOrders()->sum('total_amount');
    }

    /**
     * Calculate total laundry charges.
     *
     * @return float
     */
    public function getLaundryChargesTotalAttribute(): float
    {
        return (float) $this->laundryRequests()->sum('total_amount');
    }

    /**
     * Calculate total additional charges.
     *
     * @return float
     */
    public function getAdditionalChargesTotalAttribute(): float
    {
        return (float) $this->additionalCharges()->sum('amount');
    }

    /**
     * Calculate grand total of all charges.
     *
     * @return float
     */
    public function getGrandTotalAttribute(): float
    {
        return $this->room_charges_total + $this->fnb_charges_total + $this->laundry_charges_total + $this->additional_charges_total;
    }

    /**
     * Calculate total payments received.
     *
     * @return float
     */
    public function getPaymentsTotalAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * Calculate balance due.
     *
     * @return float
     */
    public function getBalanceDueAttribute(): float
    {
        return $this->grand_total - $this->payments_total;
    }
}
