<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-master-data',
            'manage-guests',
            'manage-reservations',
            'view-rooms',
            'manage-laundry-requests',
            'manage-fnb-orders',
            'manage-payments',
            'manage-inspections',
            'update-room-statuses',
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Create roles and assign existing permissions
        $adminRole = Role::create(['name' => 'Administrator']);
        $adminRole->givePermissionTo(Permission::all());

        $foRole = Role::create(['name' => 'Front Office']);
        $foRole->givePermissionTo([
            'manage-guests',
            'manage-reservations',
            'view-rooms',
            'manage-laundry-requests',
            'manage-fnb-orders',
            'manage-payments',
        ]);

        $hkRole = Role::create(['name' => 'Housekeeping']);
        $hkRole->givePermissionTo([
            'manage-inspections',
            'update-room-statuses',
            'manage-laundry-requests',
            'view-rooms',
        ]);

        $fnbRole = Role::create(['name' => 'Food & Beverage']);
        $fnbRole->givePermissionTo([
            'manage-fnb-orders',
            'view-rooms',
        ]);

        // Create default users
        $admin = User::create([
            'name' => 'Artisan Admin',
            'email' => 'admin@artisan.com',
            'password' => bcrypt('password'),
            'phone' => '081234567890',
            'status' => 'active',
        ]);
        $admin->assignRole($adminRole);

        $fo = User::create([
            'name' => 'Front Office Staff',
            'email' => 'fo@artisan.com',
            'password' => bcrypt('password'),
            'phone' => '081234567891',
            'status' => 'active',
        ]);
        $fo->assignRole($foRole);

        $hk = User::create([
            'name' => 'Housekeeping Staff',
            'email' => 'hk@artisan.com',
            'password' => bcrypt('password'),
            'phone' => '081234567892',
            'status' => 'active',
        ]);
        $hk->assignRole($hkRole);

        $fnb = User::create([
            'name' => 'F&B Staff',
            'email' => 'fnb@artisan.com',
            'password' => bcrypt('password'),
            'phone' => '081234567893',
            'status' => 'active',
        ]);
        $fnb->assignRole($fnbRole);
    }
}
