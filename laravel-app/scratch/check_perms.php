<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$results = DB::table('role_menu_permissions')
    ->join('menus', 'menus.id', '=', 'role_menu_permissions.menu_id')
    ->join('roles', 'roles.id', '=', 'role_menu_permissions.role_id')
    ->where('roles.name', 'aluno')
    ->where('menus.name', 'like', '%Chat%')
    ->get(['role_menu_permissions.pode_visualizar', 'menus.name']);

foreach ($results as $row) {
    echo "Menu: {$row->name} | Pode Visualizar: {$row->pode_visualizar}\n";
}
