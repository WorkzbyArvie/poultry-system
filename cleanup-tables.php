<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = [
    'notifications',
    'documents',
    'staff',
    'order_items',
    'orders',
    'products',
    'subscriptions',
    'farm_owners',
    'roles'
];

echo "Dropping tables in order...\n";
foreach ($tables as $table) {
    try {
        DB::statement("DROP TABLE IF EXISTS \"$table\" CASCADE");
        echo "✓ Dropped: $table\n";
    } catch (\Exception $e) {
        echo "✗ Failed to drop $table: " . $e->getMessage() . "\n";
    }
}
echo "Done!\n";
