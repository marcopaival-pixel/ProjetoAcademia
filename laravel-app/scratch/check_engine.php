<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$status = DB::select("SHOW TABLE STATUS WHERE Name = 'active_rest_routines'");
if ($status) {
    echo "Engine: " . $status[0]->Engine . PHP_EOL;
}
