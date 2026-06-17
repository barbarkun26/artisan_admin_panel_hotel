<?php

use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\Reservation;
use App\Models\User;
use App\Models\FnbMenu;
use App\Models\Invoice;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\FnbSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('full hotel operation lifecycle flow works successfully', function () {
    // 1. Seed standard roles, permissions, rooms, and menus
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(RoomSeeder::class);
    $this->seed(FnbSeeder::class);

    // Retrieve staff accounts
    $foStaff = User::where('email', 'fo@artisan.com')->first();
    $hkStaff = User::where('email', 'hk@artisan.com')->first();
    
    // Verify room 101 is pre-seeded and is VCI (Vacant Clean Inspected)
    $room = Room::where('room_number', '101')->first();
    expect($room->status->code)->toBe('VCI');

    // 2. Front Office registers a Guest
    $guestData = [
        'name' => 'John Doe',
        'identity_type' => 'KTP',
        'identity_number' => '3201234567890001',
        'phone' => '08991234567',
        'email' => 'john.doe@gmail.com',
        'address' => '123 Baker Street',
    ];

    $this->actingAs($foStaff)
        ->post(route('guests.store'), $guestData)
        ->assertRedirect(route('guests.index'));

    $guest = Guest::where('identity_number', '3201234567890001')->first();
    expect($guest)->not->toBeNull();
    expect($guest->guest_code)->toBe('G-00001');

    // 3. Front Office books Room 101 for the Guest
    $bookingData = [
        'guest_id' => $guest->id,
        'checkin_date' => now()->format('Y-m-d'),
        'checkout_date' => now()->addDays(2)->format('Y-m-d'), // 2 nights
        'room_id' => $room->id,
        'total_guest' => 2,
        'extra_bed_qty' => 1, // Add 1 extra bed
    ];

    $this->actingAs($foStaff)
        ->post(route('reservations.store'), $bookingData)
        ->assertRedirect(route('reservations.index'));

    $reservation = Reservation::where('guest_id', $guest->id)->first();
    expect($reservation)->not->toBeNull();
    expect($reservation->status)->toBe('pending');
    expect($reservation->nights_count)->toBe(2);
    expect($reservation->booking_number)->toStartWith('AH-');

    // Verify room is allocated at the proper rate (350k + 150k extra bed = 500k/night * 2 = 1M total room charge)
    expect($reservation->room_charges_total)->toEqual(1000000.00);

    // 4. Front Office checks in the Guest
    $this->actingAs($foStaff)
        ->get(route('reservations.checkin', $reservation->id))
        ->assertRedirect();

    $reservation->refresh();
    expect($reservation->status)->toBe('checkin');

    // Verify room 101 status is now Occupied (O)
    $room->refresh();
    expect($room->status->code)->toBe('O');

    // 5. Front Office places an F&B Room Service Order
    $menuItem = FnbMenu::first(); // Nasi Goreng Artisan (Rp 45.000)
    $fnbOrderData = [
        'reservation_id' => $reservation->id,
        'items' => [
            $menuItem->id => [
                'menu_id' => $menuItem->id,
                'qty' => 2, // 2 portions = Rp 90.000
            ],
        ],
    ];

    $this->actingAs($foStaff)
        ->post(route('fnb.store'), $fnbOrderData)
        ->assertRedirect(route('fnb.index'));

    $reservation->refresh();
    expect($reservation->fnb_charges_total)->toEqual(9000000.00 / 100); // 90,000

    // 6. Guest requests Checkout -> Housekeeping inspects room
    // Housekeeping submits a room inspection with Rp 50.000 damages penalty
    $inspectionData = [
        'room_condition' => 'Damaged / Items Broken',
        'damages' => 'Stained bedsheets',
        'missing_items' => 'None',
        'additional_charge' => 50000.00,
        'notes' => 'Need deep laundering for bedsheets.',
    ];

    $this->actingAs($hkStaff)
        ->post(route('inspections.store', ['reservation' => $reservation->id, 'room' => $room->id]), $inspectionData)
        ->assertRedirect();

    $reservation->refresh();
    // Verify inspection was recorded
    expect($reservation->inspections)->toHaveCount(1);
    expect($reservation->additional_charges_total)->toEqual(50000.00);

    // Verify folio calculations:
    // Room = 1,000,000
    // F&B = 90,000
    // Inspection Additional Charges = 50,000
    // Grand Total = 1,140,000
    expect($reservation->grand_total)->toEqual(1140000.00);

    // 7. Front Office completes checkout payment settlement
    // FO records a cash payment for the entire balance due
    $paymentData = [
        'payment_method' => 'Cash',
        'amount_paid' => 1140000.00,
        'reference_number' => 'FO-CASH-SETTLE',
    ];

    $this->actingAs($foStaff)
        ->post(route('reservations.checkout.process', $reservation->id), $paymentData)
        ->assertRedirect();

    $reservation->refresh();
    expect($reservation->status)->toBe('checkout');
    expect($reservation->balance_due)->toEqual(0.00);

    // Verify invoice generated
    $invoice = Invoice::where('reservation_id', $reservation->id)->first();
    expect($invoice)->not->toBeNull();
    expect($invoice->subtotal)->toEqual(1140000.00);
    expect($invoice->tax)->toEqual(114000.00); // 10% tax = 114,000
    expect($invoice->grand_total)->toEqual(1254000.00); // 1,140,000 + 114,000 = 1,254,000

    // Verify Room 101 status is set to Vacant Dirty (VD)
    $room->refresh();
    expect($room->status->code)->toBe('VD');
});
