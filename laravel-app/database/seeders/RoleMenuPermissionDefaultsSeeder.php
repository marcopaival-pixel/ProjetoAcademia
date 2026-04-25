<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Database\Seeder;

class RoleMenuPermissionDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MenuSeeder::class);
        $this->call(AdminPortalMenusSeeder::class);

        $appMenusByRole = [
            'admin' => ['dashboard', 'users', 'settings', 'report', 'finance_admin', 'profile'],
            'professional' => [
                'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                'report', 'export', 'messages', 'settings', 'profile',
            ],
            'aluno' => [
                'profile', 'progression.plans', 'diary', 'assessments', 'calendar', 'plano',
                'export', 'messages', 'presence', 'dashboard', 'nutrition', 'weight',
                'hydration', 'chat', 'leaderboard', 'active-rest',
            ],
            'receptionist' => ['dashboard', 'user_registration', 'presence', 'plano', 'profile'],
            'finance' => ['dashboard', 'billing', 'financial_reports', 'plano', 'profile'],
            'manager' => [
                'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                'report', 'export', 'messages', 'settings', 'profile',
            ],
            'instructor' => [
                'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                'report', 'export', 'messages', 'settings', 'profile',
            ],
            'supervisor' => [
                'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                'report', 'export', 'messages', 'settings', 'profile',
            ],
        ];

        $roles = Role::query()->get()->keyBy('name');

        foreach ($roles as $roleName => $role) {
            $allowedApp = $appMenusByRole[$roleName] ?? ['dashboard', 'profile'];

            foreach (Menu::query()->where('portal', 'app')->get() as $menu) {
                $can = in_array($menu->name, $allowedApp, true);
                $this->upsertGlobal($role->id, $menu->id, $can);
            }

            $hasAdminAccess = $role->permissions->contains(fn ($p) => $p->name === 'admin.access')
                || $roleName === 'admin';

            foreach (Menu::query()->where('portal', 'admin')->get() as $menu) {
                $can = (bool) $hasAdminAccess;
                $this->upsertGlobal($role->id, $menu->id, $can);
            }
        }
    }

    private function upsertGlobal(int $roleId, int $menuId, bool $can): void
    {
        RoleMenuPermission::updateOrCreate(
            [
                'role_id' => $roleId,
                'menu_id' => $menuId,
                'academy_company_id' => null,
            ],
            [
                'pode_visualizar' => $can,
                'pode_criar' => $can,
                'pode_editar' => $can,
                'pode_excluir' => $can,
                'pode_exportar' => $can,
                'pode_imprimir' => $can,
            ]
        );
    }
}
