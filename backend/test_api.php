<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
if(!$user) { echo "No user"; exit; }
$request = Illuminate\Http\Request::create('/api/v1/nutrition/meal-templates', 'GET');
$request->setUserResolver(fn() => $user);

$response = app()->handle($request);
echo "--- templates ---\n";
echo $response->getContent();

$request2 = Illuminate\Http\Request::create('/api/v1/nutrition/diary', 'GET');
$request2->setUserResolver(fn() => $user);
$response2 = app()->handle($request2);
echo "\n--- diary ---\n";
echo $response2->getContent();

$request3 = Illuminate\Http\Request::create('/api/v1/hydration/status', 'GET');
$request3->setUserResolver(fn() => $user);
$response3 = app()->handle($request3);
echo "\n--- hydration ---\n";
echo $response3->getContent();
