<?php
require __DIR__ . '/../laravel-app/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;

$user = User::where('email', 'demo@nexshape.com.br')->first();

if ($user) {
    echo "Email: " . $user->email . "\n";
    echo "Admin: " . ($user->is_admin ? "Sim" : "Não") . "\n";
    echo "Roles: " . implode(', ', $user->getRoleNames()) . "\n";
    echo "Permissions: " . implode(', ', $user->permissions->pluck('name')->toArray()) . "\n";
} else {
    echo "Usuário demo não encontrado.\n";
}
