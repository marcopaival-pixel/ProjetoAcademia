<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$user = App\Models\User::find(21);
if ($user) {
    echo 'hasPremiumAccess: ' . ($user->hasPremiumAccess() ? 'TRUE' : 'FALSE') . PHP_EOL;
    echo 'isPremiumActive: ' . ($user->isPremiumActive() ? 'TRUE' : 'FALSE') . PHP_EOL;
    echo 'is_premium: ' . ($user->is_premium ? 'YES' : 'NO') . PHP_EOL;
    echo 'plan: ' . ($user->activePlan ? $user->activePlan->plan->name : 'NONE') . PHP_EOL;
}
