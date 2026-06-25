<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->admin = User::where('email', 'admin@artisan.com')->first();
    $this->foUser = User::where('email', 'fo@artisan.com')->first();
});

test('administrator can view user listing', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
    $response->assertSuccessful();
    $response->assertSee($this->foUser->name);
});

test('non-administrator cannot view user listing', function () {
    $response = $this->actingAs($this->foUser)->get(route('admin.users.index'));
    $response->assertForbidden();
});

test('administrator can create a user and assign a role', function () {
    $userData = [
        'name' => 'New Test Staff',
        'email' => 'newstaff@artisan.com',
        'password' => 'secretpassword',
        'phone' => '081234567',
        'status' => 'active',
        'role' => 'Front Office',
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);
    
    $response->assertRedirect(route('admin.users.index'));
    
    $user = User::where('email', 'newstaff@artisan.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New Test Staff');
    expect($user->hasRole('Front Office'))->toBeTrue();
});

test('administrator can edit and update a user details and role', function () {
    $userData = [
        'name' => 'Updated Staff Name',
        'email' => 'fo@artisan.com',
        'phone' => '089999999',
        'status' => 'inactive',
        'role' => 'Housekeeping',
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->foUser), $userData);
    
    $response->assertRedirect(route('admin.users.index'));
    
    $this->foUser->refresh();
    expect($this->foUser->name)->toBe('Updated Staff Name');
    expect($this->foUser->status)->toBe('inactive');
    expect($this->foUser->hasRole('Housekeeping'))->toBeTrue();
    expect($this->foUser->hasRole('Front Office'))->toBeFalse();
});

test('administrator can delete a user', function () {
    $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->foUser));
    
    $response->assertRedirect(route('admin.users.index'));
    
    $user = User::find($this->foUser->id);
    expect($user)->toBeNull();
});

test('administrator cannot delete themselves', function () {
    $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
    
    $response->assertRedirect(route('admin.users.index'));
    
    $user = User::find($this->admin->id);
    expect($user)->not->toBeNull();
});
