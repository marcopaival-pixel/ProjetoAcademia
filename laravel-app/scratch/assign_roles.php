<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;

$role = Role::where('name', 'aluno')->first();
if ($role) {
    $count = User::whereNull('role_id')->update(['role_id' => $role->id]);
    echo "Atualizados $count usuários para o perfil de aluno.\n";
} else {
    echo "Erro: Perfil 'aluno' não encontrado. Execute o seeder primeiro.\n";
}
