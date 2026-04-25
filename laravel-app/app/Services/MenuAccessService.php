<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\RoleMenuPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuAccessService
{
    public const DENIED_MESSAGE = 'Acesso negado. Você não possui permissão para acessar este módulo.';

    public function bumpPermissionCacheVersion(): void
    {
        $v = (int) Cache::get('role_menu_permissions_ver', 1);
        Cache::forever('role_menu_permissions_ver', $v + 1);
        Cache::forget('menu_route_resolver_exact_menus');
        Cache::forget('menu_route_resolver_pattern_menus');
    }

    public function permissionCacheVersion(): int
    {
        return (int) Cache::get('role_menu_permissions_ver', 1);
    }

    /**
     * Indica se existem permissões globais (sem empresa) configuradas para o perfil.
     */
    public function roleHasConfiguredGlobalMenuPermissions(?int $profileId): bool
    {
        if ($profileId === null) {
            return false;
        }

        $ver = $this->permissionCacheVersion();

        return Cache::remember(
            "menu_rmp_global_exists.profile_{$profileId}.v{$ver}",
            3600,
            fn (): bool => RoleMenuPermission::query()
                ->where('role_id', $profileId)
                ->whereNull('academy_company_id')
                ->exists()
        );
    }

    /**
     * Permissão efetiva: override por empresa, senão global.
     * Cacheada para evitar N+1 na construção de menus e validação de rotas.
     */
    public function getEffectivePermission(User $user, Menu $menu): ?RoleMenuPermission
    {
        $activeRoleName = session('active_role');
        $role = $activeRoleName ? \App\Models\Role::where('name', $activeRoleName)->first() : $user->roles()->first();
        $roleId = $role?->id ?? $user->profile_id;

        if ($roleId === null) {
            return null;
        }

        $ver = $this->permissionCacheVersion();
        $companyId = $user->academy_company_id;
        $cacheKey = "menu_perm_eff.p{$roleId}.m{$menu->id}.c" . ($companyId ?? 0) . ".v{$ver}";

        return Cache::remember($cacheKey, 1800, function() use ($roleId, $menu, $companyId) {
            if ($companyId) {
                $specific = RoleMenuPermission::query()
                    ->where('role_id', $roleId)
                    ->where('menu_id', $menu->id)
                    ->where('academy_company_id', $companyId)
                    ->first();
                if ($specific !== null) {
                    return $specific;
                }
            }

            return RoleMenuPermission::query()
                ->where('role_id', $roleId)
                ->where('menu_id', $menu->id)
                ->whereNull('academy_company_id')
                ->first();
        });
    }

    public function canVisualizarMenu(User $user, Menu $menu): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }

        if ($user->hasAdminPanelAccess() && $menu->portal === 'admin') {
            return true;
        }

        if ($menu->is_container) {
            return true;
        }

        $activeRoleName = session('active_role');
        $role = $activeRoleName ? \App\Models\Role::where('name', $activeRoleName)->first() : $user->roles()->first();
        $roleId = $role?->id ?? $user->profile_id;

        if (! $this->roleHasConfiguredGlobalMenuPermissions($roleId)) {
            return true;
        }

        $perm = $this->getEffectivePermission($user, $menu);

        return $perm ? (bool) $perm->pode_visualizar : true;
    }

    /**
     * Mapa name => pode_visualizar para itens do painel admin (portal=admin), para o sidebar.
     *
     * @return array<string, bool>
     */
    public function getAdminNavVisibilityMap(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        $activeRoleName = session('active_role');
        $role = $activeRoleName ? \App\Models\Role::where('name', $activeRoleName)->first() : $user->roles()->first();
        $roleId = $role?->id ?? $user->profile_id;

        $ver = $this->permissionCacheVersion();
        $companyId = $user->academy_company_id;
        $cacheKey = "nav_visibility_map.p{$roleId}.c" . ($companyId ?? 0) . ".v{$ver}";

        return Cache::remember($cacheKey, 1800, function() use ($user) {
            if ($user->hasAdminPanelAccess()) {
                return Menu::query()
                    ->where('portal', 'admin')
                    ->where('is_container', false)
                    ->pluck('name')
                    ->mapWithKeys(fn (string $name) => [$name => true])
                    ->all();
            }

            $menus = Menu::query()->where('portal', 'admin')->orderBy('order')->get();
            $hasGlobalMatrix = $this->roleHasConfiguredGlobalMenuPermissions($roleId);
            $out = [];
            foreach ($menus as $menu) {
                if ($menu->is_container) {
                    continue;
                }
                if ($hasGlobalMatrix) {
                    $perm = $this->getEffectivePermission($user, $menu);
                    $out[$menu->name] = $perm ? (bool) $perm->pode_visualizar : true;
                } else {
                    $out[$menu->name] = true;
                }
            }

            return $out;
        });
    }

    /**
     * Encontra o menu aplicável à rota nomeada (folhas; ignora containers).
     */
    public function findMenuForRouteName(?string $routeName, Request $request): ?Menu
    {
        if ($routeName === null) {
            return null;
        }

        $exactMenus = Cache::remember('menu_route_resolver_exact_menus', 300, fn () => Menu::query()
            ->where('is_container', false)
            ->where('match_mode', 'exact')
            ->get());

        foreach ($exactMenus as $menu) {
            $names = array_map('trim', explode('|', (string) $menu->route));
            if (in_array($routeName, $names, true)) {
                return $menu;
            }
        }

        $patterns = Cache::remember('menu_route_resolver_pattern_menus', 300, fn () => Menu::query()
            ->where('is_container', false)
            ->where('match_mode', 'pattern')
            ->orderByDesc('route')
            ->get());

        foreach ($patterns as $menu) {
            if ($request->routeIs($menu->route)) {
                return $menu;
            }
        }

        return null;
    }

    /**
     * Bloqueio por URL quando existe permissão explícita e pode_visualizar=false.
     */
    public function canAccessRoute(User $user, ?string $routeName, Request $request): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }

        if ($routeName !== null && str_starts_with($routeName, 'admin.') && $user->hasAdminPanelAccess()) {
            return true;
        }

        $menu = $this->findMenuForRouteName($routeName, $request);
        if ($menu === null) {
            return true;
        }

        $activeRoleName = session('active_role');
        $role = $activeRoleName ? \App\Models\Role::where('name', $activeRoleName)->first() : $user->roles()->first();
        $roleId = $role?->id ?? $user->profile_id;

        if (! $this->roleHasConfiguredGlobalMenuPermissions($roleId)) {
            return true;
        }

        $perm = $this->getEffectivePermission($user, $menu);
        if ($perm === null) {
            return true;
        }

        $canVisualizar = (bool) $perm->pode_visualizar;

        // Se o menu exigir uma permissão de plano (via convenção de nome ou se adicionarmos futuramente)
        // Por agora, vamos assumir que se o usuário tiver a permissão via Perfil, ele também deve ter via Plano se for Premium
        // Mas a regra diz: Plano define o nível de acesso.
        
        return $canVisualizar;
    }

    public function shouldBypassMiddleware(?string $routeName): bool
    {
        if ($routeName === null) {
            return true;
        }

        $exact = [
            'home',
            'theme',
            'report',
        ];

        if (in_array($routeName, $exact, true)) {
            return true;
        }

        $prefixes = [
            'login',
            'logout',
            'register',
            'password.',
            'verification.',
            'legal.',
            'onboarding.',
            'documents.validate',
            'omni.webhook',
            'sanctum.',
            'registration.',
        ];

        foreach ($prefixes as $p) {
            if (str_starts_with($routeName, $p)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Árvore de menus para um portal (ex.: admin) com nós para a UI de configuração.
     *
     * @return Collection<int, Menu>
     */
    public function menuTreeForPortal(string $portal): Collection
    {
        return Menu::query()
            ->where('portal', $portal)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
    }
}
