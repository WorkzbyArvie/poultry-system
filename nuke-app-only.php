<?php
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Drop all existing app tables (not system tables)
$tables = DB::select("
    SELECT tablename FROM pg_tables 
    WHERE schemaname = 'public' 
    AND tablename NOT IN ('migrations', 'pg_stat_statements', 'spatial_ref_sys')
    ORDER BY tablename DESC
");

foreach ($tables as $table) {
    echo "Dropping {$table->tablename}...\n";
    DB::statement("DROP TABLE IF EXISTS \"" . $table->tablename . "\" CASCADE");
}

echo "All application tables dropped.\n";

// Verify migrations table still exists
if (!DB::select("SELECT * FROM information_schema.tables WHERE table_name = 'migrations'")) {
    echo "ERROR: Migrations table doesn't exist!\n";
} else {
    echo "Migrations table verified.\n";
}
