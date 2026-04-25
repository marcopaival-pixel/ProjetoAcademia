<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$columns = DB::select('SHOW COLUMNS FROM users');
foreach($columns as $col) {
    if (strtolower($col->Field) == 'id') {
        echo "{$col->Field}: {$col->Type}" . PHP_EOL;
    }
}
