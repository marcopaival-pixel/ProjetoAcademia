<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;
$plans = DB::table('user_plans')->where('user_id', 21)->get();
foreach($plans as $p) {
    echo "Plan #{$p->id}: plan_id={$p->plan_id}, status={$p->status}, start={$p->start_date}, end={$p->end_date}" . PHP_EOL;
}
