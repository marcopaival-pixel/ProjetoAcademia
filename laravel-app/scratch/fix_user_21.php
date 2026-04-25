<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$user = App\Models\User::find(21);
if ($user) {
    $user->is_premium = true;
    $user->save();
    
    $user->userPlans()->create([
        'plan_id' => 2, // PRO
        'start_date' => now(),
        'status' => 'active',
    ]);
    
    echo "Fixed user 21" . PHP_EOL;
}
