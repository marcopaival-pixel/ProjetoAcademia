<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$user = App\Models\User::find(21);
if ($user) {
    echo 'profile_id: ' . $user->profile_id . PHP_EOL;
    echo 'role_name: ' . ($user->userRole ? $user->userRole->name : 'NONE') . PHP_EOL;
}
