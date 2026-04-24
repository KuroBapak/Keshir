<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = ['owner', 'manager', 'cashier', 'kitchen_staff'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Owner account
        $ownerRole = Role::where('name', 'owner')->first();
        User::firstOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner',
                'password' => bcrypt('password'),
                'role_id' => $ownerRole->id,
            ]
        );

        // Sample staff accounts for development
        $managerRole = Role::where('name', 'manager')->first();
        User::firstOrCreate(
            ['username' => 'manager'],  
            [
                'name' => 'Manager',
                'password' => bcrypt('password'),
                'role_id' => $managerRole->id,
            ]
        );

        $cashierRole = Role::where('name', 'cashier')->first();
        User::firstOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir',
                'password' => bcrypt('password'),
                'role_id' => $cashierRole->id,
            ]
        );

        $kitchenRole = Role::where('name', 'kitchen_staff')->first();
        User::firstOrCreate(
            ['username' => 'dapur'],
            [
                'name' => 'Staff Dapur',
                'password' => bcrypt('password'),
                'role_id' => $kitchenRole->id,
            ]
        );

        // System Settings (Tax & Service Charge defaults)
        $settings = [
            ['key' => 'tax_enabled', 'value' => '1'],
            ['key' => 'tax_rate', 'value' => '11'], // PPN 11%
            ['key' => 'service_charge_enabled', 'value' => '1'],
            ['key' => 'service_charge_rate', 'value' => '5'], // 5%
        ];
        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        // Sample Tables
        for ($i = 1; $i <= 10; $i++) {
            Table::firstOrCreate(
                ['table_number' => 'T' . $i],
                ['capacity' => rand(2, 6), 'status' => 'available']
            );
        }

        // Generate Dummy Demo Data (Menus, Ingredients, Transactions)
        $this->call(DemoDataSeeder::class);
    }
}