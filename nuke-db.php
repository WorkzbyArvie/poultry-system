<?php
$host = 'aws-1-ap-southeast-2.pooler.supabase.com';
$port = '6543';
$db = 'postgres';
$user = 'postgres.fcociwpbmvcadlomdrkm';
$pass = 'Lawrencetabutol_31';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Disable foreign key checks
$pdo->exec("SET session_replication_role = replica");

$tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public'")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    if ($table !== 'spatial_ref_sys') {
        echo "Dropping $table...\n";
        $pdo->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
    }
}

// Re-enable foreign key checks
$pdo->exec("SET session_replication_role = default");

echo "All tables dropped.\n";
