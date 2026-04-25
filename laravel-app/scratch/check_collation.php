<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$tables = ['users', 'active_rest_routines'];
foreach($tables as $table) {
    $status = DB::select("SHOW TABLE STATUS WHERE Name = '$table'");
    if ($status) {
        echo "Table: $table, Collation: " . $status[0]->Collation . PHP_EOL;
    }
}
