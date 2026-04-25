<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
app(App\Services\MenuService::class)->clearCache(21);
echo "Cleared cache for user 21" . PHP_EOL;
