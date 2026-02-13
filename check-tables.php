<?php
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Schema;

$pdo = DB::connection()->getPdo();

// Drop subscriptions if it exists (it shouldn't, but something went wrong)
try {
    if (Schema::hasTable('subscriptions')) {
        Schema::dropIfExists('subscriptions');
        echo "Dropped subscriptions table.\n";
    }
} catch (\Exception $e) {
    echo "Error checking/dropping subscriptions: " . $e->getMessage() . "\n";
}

// List all tables
$tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename")->fetchAll();
echo "\nCurrent tables in public schema:\n";
foreach ($tables as $t) {
    echo "  - " . $t['tablename'] . "\n";
}
