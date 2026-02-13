<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename");
echo "Tables in public schema:\n";
foreach ($tables as $table) {
    echo "  - " . $table->tablename . "\n";
}

// Check migrations table
echo "\nMigrations batch table:\n";
$migrations = DB::table('migrations')->select('migration', 'batch')->orderBy('batch')->get();
foreach ($migrations as $m) {
    echo "  - {$m->migration} (batch {$m->batch})\n";
}
