<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain');

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    if (Schema::hasTable('system_errors')) {
        echo "Table 'system_errors' exists.\n";
        $count = \Illuminate\Support\Facades\DB::table('system_errors')->count();
        echo "Total records: " . $count . "\n";
    } else {
        echo "Table 'system_errors' DOES NOT EXIST. Please run 'php artisan migrate'.\n";
    }
} catch (\Exception $e) {
    echo "Error checking DB: " . $e->getMessage() . "\n";
}
