<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
if(!$user) { echo "No user"; exit; }

$token = $user->createToken('test')->plainTextToken;
echo "TOKEN: $token\n";
