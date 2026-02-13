<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing admin if exists
        \App\Models\User::where('email', 'superadmin@poultry.com')->delete();
        
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@poultry.com',
            'password' => bcrypt('SuperAdmin@2026'),
            'role' => 'superadmin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
