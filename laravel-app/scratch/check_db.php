<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

if (Schema::hasTable('plan_features')) {
    echo "plan_features exists" . PHP_EOL;
    $features = DB::table('plan_features')->get();
    foreach($features as $f) {
        echo "Plan #{$f->plan_id}: {$f->feature_key} = " . ($f->is_enabled ? 'ON' : 'OFF') . PHP_EOL;
    }
} else {
    echo "plan_features DOES NOT exist" . PHP_EOL;
}
