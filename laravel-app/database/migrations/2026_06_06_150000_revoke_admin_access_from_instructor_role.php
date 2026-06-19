<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::where('name', 'admin.access')->first();
        if ($permission === null) {
            return;
        }

        $roles = Role::whereIn('name', ['instructor', 'aluno', 'paciente'])->get();
        foreach ($roles as $role) {
            $role->permissions()->detach($permission->id);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'admin.access')->first();
        if ($permission === null) {
            return;
        }

        Role::where('name', 'instructor')->first()?->permissions()->syncWithoutDetaching([$permission->id]);
    }
};
