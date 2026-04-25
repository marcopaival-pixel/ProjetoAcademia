<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use App\Services\MenuService;

$user = User::where('role_id', '!=', 1)->first();
if (!$user) {
    echo "No non-admin user found, testing with first user\n";
    $user = User::first();
}

if (!$user) {
    echo "No user in database\n";
    exit;
}

echo "Testing for user: " . $user->name . " (Role: " . ($user->userRole->name ?? 'None') . ")\n";
$menus = app(MenuService::class)->getMenusForUser($user);
print_r($menus->pluck('name')->toArray());
