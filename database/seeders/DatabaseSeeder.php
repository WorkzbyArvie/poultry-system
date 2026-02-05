<?php

namespace Database\Seeders;

use App\Models\User;
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
        // This creates your Admin account with all required fields
        User::create([
            'full_name' => 'Admin User',
            'username'  => 'admin',
            'email'     => 'admin@poultry.com',
            'password'  => bcrypt('admin123'),
        ]);
    }
}