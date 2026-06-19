<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\Menu;
use App\Models\MenuPermissionAuditLog;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use App\Services\MenuAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleMenuPermissionController extends Controller
{
    public function __construct(
        private readonly MenuAccessService $menuAccess
    ) {}

    public function edit(Request $request): View
    {
        $actor = $request->user();
        if ($actor === null || ! $actor->isAdministrator()) {
            abort(403, 'Apenas administradores do sistema podem gerir permissões de menu.');
        }

        $profiles = Role::query()->orderBy('label')->get();
        $profileId = (int) $request->query('profile_id', (string) ($profiles->first()?->id ?? 0));
        $profile = Role::find($profileId);

        $companyRaw = $request->query('academy_company_id');
        $companyId = $companyRaw !== null && $companyRaw !== '' ? (int) $companyRaw : null;

        $companies = AcademyCompany::query()->orderBy('name')->get();

        $portal = in_array($request->query('portal'), ['app', 'admin'], true)
            ? $request->query('portal')
            : 'app';

        $menus = Menu::query()
            ->where('portal', $portal)
            ->where('is_container', false)
            ->orderBy('order')
            ->get();

        $permissionsKeyed = collect();
        if ($profile) {
            $q = RoleMenuPermission::query()->where('role_id', $profile->id);
            if ($companyId !== null) {
                $q->where('academy_company_id', $companyId);
            } else {
                $q->whereNull('academy_company_id');
            }
            $permissionsKeyed = $q->get()->keyBy('menu_id');
        }

        return view('admin.role-menu-permissions.edit', [
            'profiles' => $profiles,
            'profile' => $profile,
            'profileId' => $profileId,
            'menus' => $menus,
            'permissionsKeyed' => $permissionsKeyed,
            'companies' => $companies,
            'companyId' => $companyId,
            'portal' => $portal,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $actor = $request->user();
        if ($actor === null || ! $actor->isAdministrator()) {
            abort(403, 'Apenas administradores do sistema podem gerir permissões de menu.');
        }

        $validated = $request->validate([
            'profile_id' => 'required|exists:roles,id',
            'portal' => 'required|in:app,admin',
            'academy_company_id' => 'nullable|exists:academy_companies,id',
        ]);

        $profileId = (int) $validated['profile_id'];
        $portal = $validated['portal'];
        $companyId = isset($validated['academy_company_id']) ? (int) $validated['academy_company_id'] : null;

        $menus = Menu::query()
            ->where('portal', $portal)
            ->where('is_container', false)
            ->pluck('id');

        $permRoot = $request->input('permissions', []);

        foreach ($menus as $menuId) {
            $flags = $permRoot[(string) $menuId] ?? $permRoot[$menuId] ?? [];
            RoleMenuPermission::updateOrCreate(
                [
                    'role_id' => $profileId,
                    'menu_id' => (int) $menuId,
                    'academy_company_id' => $companyId,
                ],
                [
                    'pode_visualizar' => ! empty($flags['pode_visualizar']),
                    'pode_criar' => ! empty($flags['pode_criar']),
                    'pode_editar' => ! empty($flags['pode_editar']),
                    'pode_excluir' => ! empty($flags['pode_excluir']),
                    'pode_exportar' => ! empty($flags['pode_exportar']),
                    'pode_imprimir' => ! empty($flags['pode_imprimir']),
                ]
            );
        }

        MenuPermissionAuditLog::create([
            'user_id' => $actor->id,
            'role_id' => $profileId,
            'academy_company_id' => $companyId,
            'action' => 'role_menu_permissions.update',
            'payload' => [
                'portal' => $portal,
                'menus_updated' => $menus->count(),
            ],
            'ip_address' => $request->ip(),
        ]);

        $this->menuAccess->bumpPermissionCacheVersion();

        return redirect()
            ->route('admin.settings.permissions.menus', array_filter([
                'profile_id' => $profileId,
                'academy_company_id' => $companyId,
                'portal' => $portal,
            ], fn ($v) => $v !== null && $v !== ''))
            ->with('success', 'Permissões de menu guardadas com sucesso.');
    }
}
