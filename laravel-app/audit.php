<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- DATABASE AUDIT ---\n";

try {
    DB::connection()->getPdo();
    echo "DB Connection: OK\n";
} catch (\Exception $e) {
    echo "DB Connection: FAILED - " . $e->getMessage() . "\n";
    exit;
}

$tables = DB::select('SHOW TABLES');
$tableKey = 'Tables_in_' . env('DB_DATABASE');

$emptyTables = [];
$totalRows = 0;

foreach ($tables as $tableInfo) {
    $table = $tableInfo->$tableKey;
    try {
        $count = DB::table($table)->count();
        if ($count == 0) {
            $emptyTables[] = $table;
        }
        $totalRows += $count;
    } catch (\Exception $e) {
        echo "Error querying table $table: " . $e->getMessage() . "\n";
    }
}

echo "Total Tables: " . count($tables) . "\n";
echo "Total Rows Across All Tables: " . $totalRows . "\n";
echo "Empty Tables: " . count($emptyTables) . "\n";
if (count($emptyTables) > 0) {
    echo "Empty Tables List: " . implode(', ', array_slice($emptyTables, 0, 10)) . (count($emptyTables) > 10 ? '...' : '') . "\n";
}

// Orphans check (example: users and profiles)
if (Schema::hasTable('users') && Schema::hasTable('user_profiles')) {
    $orphanProfiles = DB::table('user_profiles')
        ->leftJoin('users', 'user_profiles.user_id', '=', 'users.id')
        ->whereNull('users.id')
        ->count();
    echo "Orphan Profiles: " . $orphanProfiles . "\n";
}

echo "\n--- BACKEND AUDIT ---\n";
echo "Exceptions in laravel.log (Last 1000 lines):\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -1000);
    $exceptionCount = 0;
    foreach ($lastLines as $line) {
        if (strpos($line, 'local.ERROR') !== false || strpos($line, 'Exception') !== false) {
            $exceptionCount++;
        }
    }
    echo "Recent Errors/Exceptions Found: $exceptionCount\n";
} else {
    echo "laravel.log not found.\n";
}

echo "\nAudit Script Completed.\n";
