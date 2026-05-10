<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SystemSetting;

$key = 'app_banner_enabled';
$val = SystemSetting::get($key);
$isTrue = SystemSetting::isTrue($key);

echo "Key: $key\n";
echo "Value in DB: " . var_export($val, true) . "\n";
echo "isTrue result: " . var_export($isTrue, true) . "\n";
