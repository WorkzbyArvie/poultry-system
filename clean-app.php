<?php
$host = 'aws-1-ap-southeast-2.pooler.supabase.com';
$port = '6543';
$db = 'postgres';
$user = 'postgres.fcociwpbmvcadlomdrkm';
$pass = 'Lawrencetabutol_31';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Disable FK constraints
    $pdo->exec("SET session_replication_role = replica");
    
    // Get all tables
    $stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename DESC");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        if (!in_array($table, ['migrations', 'pg_stat_statements', 'spatial_ref_sys'])) {
            echo "Dropping $table...\n";
            $pdo->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
        }
    }
    
    // Re-enable FK constraints
    $pdo->exec("SET session_replication_role = default");
    
    echo "All tables dropped. Migrations table preserved.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
