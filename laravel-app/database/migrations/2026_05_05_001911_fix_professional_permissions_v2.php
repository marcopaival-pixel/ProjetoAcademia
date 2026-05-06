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
        if (!$role) return;

        $routes = ['weight', 'hydration.index', 'professional.billing.index'];
        $menus = \App\Models\Menu::whereIn('route', $routes)->get();

        foreach ($menus as $menu) {
            \App\Models\RoleMenuPermission::updateOrCreate(
                ['role_id' => $role->id, 'menu_id' => $menu->id],
                ['pode_visualizar' => true]
            );
        }

        try {
            app(\App\Services\MenuAccessService::class)->bumpPermissionCacheVersion();
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = \App\Models\Role::where('name', 'professional')->first();
        if (!$role) return;

        $routes = ['weight', 'hydration.index', 'professional.billing.index'];
        $menus = \App\Models\Menu::whereIn('route', $routes)->get();

        foreach ($menus as $menu) {
            \App\Models\RoleMenuPermission::updateOrCreate(
                ['role_id' => $role->id, 'menu_id' => $menu->id],
                ['pode_visualizar' => false]
            );
        }

        try {
            app(\App\Services\MenuAccessService::class)->bumpPermissionCacheVersion();
        } catch (\Throwable $e) {}
    }
};
