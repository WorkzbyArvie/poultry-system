<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Delete old admin if exists
User::where('email', 'superadmin@poultry.com')->delete();

// Create new superadmin
$user = User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@poultry.com',
    'password' => Hash::make('SuperAdmin@2026'),
    'role' => 'superadmin',
    'status' => 'active',
    'email_verified_at' => now(),
]);

echo "✓ Admin created successfully!\n";
echo "Email: " . $user->email . "\n";
echo "ID: " . $user->id . "\n";
