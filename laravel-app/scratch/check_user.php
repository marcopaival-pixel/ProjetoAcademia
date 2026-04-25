<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$user = App\Models\User::find(21);
if ($user) {
    echo 'is_premium: ' . ($user->is_premium ? 'YES' : 'NO') . PHP_EOL;
    echo 'plan_id: ' . $user->plan_id . PHP_EOL;
    echo 'activePlan: ' . ($user->activePlan ? $user->activePlan->plan->name : 'NONE') . PHP_EOL;
} else {
    echo 'User 21 not found' . PHP_EOL;
}
