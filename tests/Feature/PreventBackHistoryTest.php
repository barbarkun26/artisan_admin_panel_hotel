<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('prevent back history middleware is applied to authenticated routes for all roles', function (string $email, string $dashboardUrl) {
    // Seed standard roles and permissions
    $this->seed(RoleAndPermissionSeeder::class);

    // Retrieve staff account
    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();

    // 1. Assert response when authenticated has no-cache headers
    $response = $this->actingAs($user)->get($dashboardUrl);
    
    $response->assertSuccessful();
    $response->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store, private');
    $response->assertHeader('Pragma', 'no-cache');
    $response->assertHeader('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

    // 2. Perform logout
    $this->post(route('logout'));

    // 3. Try to access the dashboard again (as a guest) and verify redirect to login
    $responseAfterLogout = $this->get($dashboardUrl);
    $responseAfterLogout->assertRedirect(route('login'));
})->with([
    ['admin@artisan.com', '/admin/dashboard'],
    ['fo@artisan.com', '/fo/dashboard'],
    ['hk@artisan.com', '/hk/dashboard'],
    ['fnb@artisan.com', '/fnb/dashboard'],
]);
