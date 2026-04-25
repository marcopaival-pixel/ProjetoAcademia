<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$plans = App\Models\Plan::with('features')->get();
foreach($plans as $p) {
    echo "Plan #{$p->id} ({$p->name}):" . PHP_EOL;
    foreach($p->features as $f) {
        echo "  - {$f->feature_key}: " . ($f->is_enabled ? 'ON' : 'OFF') . PHP_EOL;
    }
}
