<?php

use App\Models\User;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\FnbMenu;
use App\Models\FnbOrder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\FnbSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->seed(RoomSeeder::class);
    $this->seed(FnbSeeder::class);

    $this->foStaff = User::where('email', 'fo@artisan.com')->first();
    $this->fnbStaff = User::where('email', 'fnb@artisan.com')->first();
    $this->adminStaff = User::where('email', 'admin@artisan.com')->first();

    // Create a dummy reservation for checkin state
    $room = Room::first();
    $guest = \App\Models\Guest::create([
        'guest_code' => 'G-00001',
        'name' => 'Jane Doe',
        'identity_type' => 'KTP',
        'identity_number' => '1234567890123456',
        'phone' => '08123456789',
        'email' => 'jane@doe.com',
        'address' => 'Test Address',
    ]);

    $this->reservation = Reservation::create([
        'booking_number' => 'AH-TEST123',
        'guest_id' => $guest->id,
        'reservation_date' => now(),
        'checkin_date' => now(),
        'checkout_date' => now()->addDay(),
        'total_guest' => 1,
        'status' => 'checkin',
        'created_by' => $this->foStaff->id,
    ]);

    $this->reservation->reservationRooms()->create([
        'room_id' => $room->id,
        'room_rate' => 300000,
    ]);
});

test('f&b order ignores items with zero quantity', function () {
    $menus = FnbMenu::take(2)->get();
    
    // We submit one item with qty=2 and one with qty=0 (simulating browser submission)
    $orderData = [
        'reservation_id' => $this->reservation->id,
        'items' => [
            $menus[0]->id => [
                'menu_id' => $menus[0]->id,
                'qty' => 2,
            ],
            $menus[1]->id => [
                'menu_id' => $menus[1]->id,
                'qty' => 0,
            ],
        ],
    ];

    $response = $this->actingAs($this->foStaff)
        ->post(route('fnb.store'), $orderData);

    $response->assertRedirect(route('fnb.index'));

    $order = FnbOrder::latest()->first();
    expect($order)->not->toBeNull();
    // Only one detail should be created (for the one with qty=2)
    expect($order->details)->toHaveCount(1);
    expect($order->details->first()->menu_id)->toBe($menus[0]->id);
    expect($order->details->first()->qty)->toBe(2);
});

test('f&b order status can transition through pending, process, waiting, and delivered', function () {
    $menu = FnbMenu::first();
    
    $order = FnbOrder::create([
        'reservation_id' => $this->reservation->id,
        'room_id' => $this->reservation->reservationRooms->first()->room_id,
        'order_date' => now(),
        'status' => 'pending',
        'total_amount' => $menu->price,
    ]);

    $order->details()->create([
        'menu_id' => $menu->id,
        'qty' => 1,
        'price' => $menu->price,
        'subtotal' => $menu->price,
    ]);

    // Update to 'process'
    $this->actingAs($this->fnbStaff)
        ->post(route('fnb.status', $order->id), ['status' => 'process'])
        ->assertRedirect();
    expect($order->fresh()->status)->toBe('process');

    // Update to 'waiting'
    $this->actingAs($this->fnbStaff)
        ->post(route('fnb.status', $order->id), ['status' => 'waiting'])
        ->assertRedirect();
    expect($order->fresh()->status)->toBe('waiting');

    // Update to 'delivered'
    $this->actingAs($this->fnbStaff)
        ->post(route('fnb.status', $order->id), ['status' => 'delivered'])
        ->assertRedirect();
    expect($order->fresh()->status)->toBe('delivered');
});

test('f&b reports page is accessible to f&b staff and admins', function () {
    // F&B Staff access
    $this->actingAs($this->fnbStaff)
        ->get(route('fnb.reports'))
        ->assertOk()
        ->assertViewIs('fnb.reports')
        ->assertSee('Total F&B Orders', false)
        ->assertSee('Delivered Revenue');

    // Admin access
    $this->actingAs($this->adminStaff)
        ->get(route('fnb.reports'))
        ->assertOk();
});

