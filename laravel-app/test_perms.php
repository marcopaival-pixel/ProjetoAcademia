<?php
$u = App\Models\User::where('email', 'aluno@academia.com')->first();
echo 'Roles: ' . $u->roles->pluck('name') . "\n";
echo 'Permissions: ' . $u->getAllPermissions()->pluck('name') . "\n";
