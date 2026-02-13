<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles
        Role::query()->delete();
        Role::create([
            'name' => 'superadmin',
            'display_name' => 'Super Administrator',
            'description' => 'Full system access and administration',
        ]);

        Role::create([
            'name' => 'farm_owner',
            'display_name' => 'Farm Owner',
            'description' => 'Farm business owner with subscription',
        ]);

        Role::create([
            'name' => 'consumer',
            'display_name' => 'Consumer',
            'description' => 'End user purchasing products',
        ]);

        Role::create([
            'name' => 'staff',
            'display_name' => 'Staff Member',
            'description' => 'System staff member',
        ]);

        // Seed super admin user
        $this->call(AdminUserSeeder::class);
    }
}