<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = \App\Models\Role::where('name', 'professional')->first();
        $menu = \App\Models\Menu::where('route', 'nutrition.index')->first();

        if ($role && $menu) {
            \App\Models\RoleMenuPermission::updateOrCreate(
                ['role_id' => $role->id, 'menu_id' => $menu->id],
                ['pode_visualizar' => true]
            );
            
            // Limpa o cache de permissões do sistema
            try {
                app(\App\Services\MenuAccessService::class)->bumpPermissionCacheVersion();
            } catch (\Throwable $e) {
                // Silently fail if service not available during migration
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = \App\Models\Role::where('name', 'professional')->first();
        $menu = \App\Models\Menu::where('route', 'nutrition.index')->first();

        if ($role && $menu) {
            \App\Models\RoleMenuPermission::updateOrCreate(
                ['role_id' => $role->id, 'menu_id' => $menu->id],
                ['pode_visualizar' => false]
            );
            
            try {
                app(\App\Services\MenuAccessService::class)->bumpPermissionCacheVersion();
            } catch (\Throwable $e) {}
        }
    }
};
