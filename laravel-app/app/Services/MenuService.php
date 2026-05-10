<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    public function __construct(
        private readonly MenuAccessService $menuAccess
    ) {}

    /**
     * Get the menus for the specified user.
     *
     * @param User|null $user
     * @return Collection
     */
    public function getMenusForUser(?User $user, bool $ignorePreferences = false): Collection
    {
        if (!$user) {
            return collect();
        }

        $permVer = $this->menuAccess->permissionCacheVersion();
        $cacheKey = "user_menus_v6_{$user->id}_pv{$permVer}_ip" . ($ignorePreferences ? '1' : '0');

        return Cache::remember($cacheKey, 3600, function () use ($user, $ignorePreferences) {
            $allMenus = Menu::query()->where('portal', 'app')->orderBy('order')->get();

            if ($user->isAdministrator()) {
                // Admin can see all menus in the app portal (general dashboard + athlete features)
                return $allMenus;
            }

            $role = $user->userRole?->name;
            $roleId = $user->userRole?->id;

            // Mapping menus to Roles as per User Request
            $roleMenus = [
                'admin' => ['dashboard', 'users', 'pdf_companies', 'settings', 'report', 'finance_admin', 'profile'],
                'professional' => [
                    'dashboard', 'patients', 'exercise', 'diary', 'assessments', 'calendar', 'report',
                    'messages', 'presence', 'plano', 'profile', 'nutrition', 'weight', 'hydration',
                    'chat', 'leaderboard', 'active-rest', 'evolution', 'trophies', 'body-analysis', 
                    'patient.professionals.search', 'community'
                ],
                'aluno' => [
                    'profile', 'progression.plans', 'diary', 'assessments', 'calendar', 'plano',
                    'export', 'messages', 'presence', 'nutrition', 'weight',
                    'hydration', 'chat', 'leaderboard', 'active-rest', 'exercise', 'evolution', 
                    'trophies', 'body-analysis', 'patient.professionals.search', 'report', 'community'
                ],
                'paciente' => [
                    'patient.unified.dashboard', 'patient.portal', 'profile', 'plano', 'report'
                ],
                'receptionist' => ['dashboard', 'user_registration', 'presence', 'plano', 'profile', 'clinic_settings', 'clinic_billing'],
                'finance' => ['dashboard', 'billing', 'financial_reports', 'plano', 'profile'],
                'manager' => [
                    'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                    'report', 'export', 'messages', 'settings', 'profile', 'clinic_settings', 'clinic_billing'
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

            $allowedMenus = $roleMenus[$role] ?? ['dashboard', 'profile'];

            $useDbPermissions = $this->menuAccess->roleHasConfiguredGlobalMenuPermissions($roleId);

            return $allMenus->filter(function ($menu) use ($user, $role, $allowedMenus, $useDbPermissions, $ignorePreferences) {
                if ($useDbPermissions) {
                    if (! $this->menuAccess->canVisualizarMenu($user, $menu)) {
                        return false;
                    }
                } else {
                    if (! in_array($menu->name, $allowedMenus)) {
                        return false;
                    }
                }

                // Personal Preference Check
                if (! $ignorePreferences && ! $menu->is_required) {
                    $preference = \App\Models\UserMenuPreference::where('user_id', $user->id)
                        ->where('menu_id', $menu->id)
                        ->first();

                    if ($preference && ! $preference->visible) {
                        return false;
                    }
                }

                // Additional plan-based filtering for students
                if ($role === 'aluno') {
                    $featureMapping = [
                        'assessments' => 'register_measurements',
                        'chat' => ['ai_training', 'ai_nutrition'],
                        'report' => 'advanced_reports',
                    ];

                    $requiredFeature = $featureMapping[$menu->name] ?? null;
                    if ($requiredFeature) {
                        if (is_array($requiredFeature)) {
                            foreach ($requiredFeature as $feat) {
                                if ($user->hasPlanFeature($feat)) {
                                    return true;
                                }
                            }

                            return false;
                        }

                        return $user->hasPlanFeature($requiredFeature);
                    }
                }

                return true;
            });
        });
    }

    /**
     * Clear the menu cache for a user.
     *
     * @param int $userId
     * @return void
     */
    public function clearCache(int $userId): void
    {
        $v = $this->menuAccess->permissionCacheVersion();
        Cache::forget("user_menus_{$userId}");
        Cache::forget("user_menus_v3_{$userId}");
        Cache::forget("user_menus_v4_{$userId}");
        Cache::forget("user_menus_v5_{$userId}");
        Cache::forget("user_menus_v5_{$userId}_pv{$v}");
        Cache::forget("user_accordion_menus_{$userId}");
        Cache::forget("user_accordion_menus_v6_{$userId}_pv{$v}");
    }

    /**
     * Get menus structured into accordion groups based on user profile.
     * Active state is computed per request (not cached) so the highlight follows the current route.
     */
    public function getAccordionMenus(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $isPremium = $user->hasPremiumAccess();
        $groups = $this->buildAccordionMenuGroups($user, $isPremium);

        return $this->ensureSingleActiveMenuItem($groups);
    }

    /**
     * Monta os grupos do painel lateral (estrutura por perfil).
     */
    private function buildAccordionMenuGroups(User $user, bool $isPremium): array
    {
        $groups = [];
        $activeRole = session('active_role');
        $isAdmin = $user->isAdministrator();

        // 1. Painel Administrativo / Governança (Prioritário para Admin ou se active_role == admin)
        if ($isAdmin && (!$activeRole || $activeRole === 'admin' || $activeRole === 'gestor')) {
            $groups[] = [
                'id' => 'administration',
                'label' => 'Menu de Administração',
                'icon' => 'fas fa-user-shield',
                'items' => $this->prepareItems($user, [
                    ['name' => 'admin_dashboard', 'label' => 'Painel Admin', 'route' => 'admin.dashboard', 'icon' => 'fas fa-shield-alt'],
                    ['name' => 'users_manage', 'label' => 'Gestão de Usuários', 'route' => 'admin.users', 'icon' => 'fas fa-users-cog'],
                    ['name' => 'pdf_companies', 'label' => 'Empresas & Unidades', 'route' => 'admin.pdf-companies.index', 'icon' => 'fas fa-building'],
                    ['name' => 'settings', 'label' => 'Configurações', 'route' => 'admin.settings', 'icon' => 'fas fa-cogs'],
                    ['name' => 'finance_sys', 'label' => 'Financeiro SaaS', 'route' => 'admin.financial.dashboard', 'icon' => 'fas fa-university'],
                    ['name' => 'billing_credits', 'label' => 'Cobrança / Créditos', 'route' => 'admin.billing.credits', 'icon' => 'fas fa-credit-card'],
                    ['name' => 'finance_mgmt', 'label' => 'Gestão de Cobrança', 'route' => 'admin.financial.management', 'icon' => 'fas fa-file-invoice-dollar'],
                    ['name' => 'report_sys', 'label' => 'Relatórios Globais', 'route' => 'admin.financial.reports', 'icon' => 'fas fa-chart-pie'],
                    ['name' => 'marketing_banners', 'label' => 'Marketing: Banners', 'route' => 'admin.marketing.banners.index', 'icon' => 'fas fa-ad'],
                ], $isPremium),
            ];
        }

        // 2. Menu da Recepção / Clínica (Para recepção, gestor ou admin explorando)
        if (($user->hasRole(['receptionist', 'manager']) && (!$activeRole || in_array($activeRole, ['receptionist', 'manager', 'gestor']))) || ($isAdmin && $activeRole === 'admin')) {
             // Admin vê recepção mesmo em 'admin' mode por ser parte da gestão clínica
            $groups[] = [
                'id' => 'reception',
                'label' => 'Menu da Recepção',
                'icon' => 'fas fa-concierge-bell',
                'items' => $this->prepareItems($user, [
                    ['name' => 'recep_dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fas fa-home'],
                    ['name' => 'user_registration', 'label' => 'Cadastro de Usuário', 'route' => 'admin.registrations.index', 'icon' => 'fas fa-user-plus'],
                    ['name' => 'presence', 'label' => 'Controle de Presença', 'route' => 'admin.users', 'icon' => 'fas fa-id-card'],
                    ['name' => 'calendar', 'label' => 'Agenda Geral', 'route' => 'agenda.index', 'icon' => 'fas fa-calendar-day'],
                    ['name' => 'clinic_settings', 'label' => 'Gestão da Clínica', 'route' => 'admin.clinic.settings', 'icon' => 'fas fa-building'],
                    ['name' => 'clinic_protocols', 'label' => 'Protocolos Padrão', 'route' => 'admin.clinic.protocols.index', 'icon' => 'fas fa-notes-medical'],
                    ['name' => 'clinic_billing', 'label' => 'Faturamento/Assinatura', 'route' => 'admin.clinic.billing', 'icon' => 'fas fa-credit-card'],
                ], $isPremium),
            ];
        }

        // 3. Portal do Profissional (Profissional, instrutor ou admin explorando)
        if (($user->hasRole(['professional', 'instructor']) && (!$activeRole || $activeRole === 'professional')) || ($isAdmin && $activeRole === 'professional')) {
            $groups[] = [
                'id' => 'professional',
                'label' => 'Portal do Profissional',
                'icon' => 'fas fa-user-md',
                'items' => $this->prepareItems($user, [
                    ['name' => 'prof_dashboard', 'label' => 'Painel de Controle', 'route' => 'professional.dashboard', 'icon' => 'fas fa-tachometer-alt'],
                    ['name' => 'prof_agenda', 'label' => 'Minha Agenda', 'route' => 'agenda.index', 'icon' => 'fas fa-calendar-alt'],
                    ['name' => 'prof_patients', 'label' => 'Meus Pacientes', 'route' => 'professional.patients.index', 'icon' => 'fas fa-user-friends'],
                    ['name' => 'prof_library', 'label' => 'Biblioteca de Exercícios', 'route' => 'exercise.catalog', 'icon' => 'fas fa-book-medical'],
                    ['name' => 'prof_protocols', 'label' => 'Protocolos de Treino', 'route' => 'progression.plans.index', 'icon' => 'fas fa-dumbbell'],
                    ['name' => 'prof_assessments', 'label' => 'Avaliações Físicas', 'route' => 'assessments.index', 'icon' => 'fas fa-file-medical-alt'],
                ], $isPremium),
            ];
        }

        // 4. Painel do Aluno / Atleta (Aluno ou admin explorando)
        if (($user->hasRole('aluno') && (!$activeRole || $activeRole === 'aluno')) || ($isAdmin && $activeRole === 'aluno')) {
            $groups[] = [
                'id' => 'athlete',
                'label' => 'Painel do Aluno',
                'icon' => 'fas fa-dumbbell',
                'items' => $this->prepareItems($user, [
                    ['name' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-th-large'],
                    ['name' => 'training', 'label' => 'Meus Treinos', 'route' => 'progression.plans.index', 'icon' => 'fas fa-running'],
                    ['name' => 'diet', 'label' => 'Dieta & Nutrição', 'route' => 'diary', 'icon' => 'fas fa-utensils'],
                    ['name' => 'evolution', 'label' => 'Minha Evolução', 'route' => 'evolution.index', 'icon' => 'fas fa-chart-line'],
                    ['name' => 'community', 'label' => 'Comunidade NexShape', 'route' => 'community.index', 'icon' => 'fas fa-users'],
                    ['name' => 'assessments', 'label' => 'Avaliações', 'route' => 'body-analysis.index', 'icon' => 'fas fa-heartbeat'],
                    ['name' => 'profile', 'label' => 'Meu Perfil', 'route' => 'profile', 'icon' => 'fas fa-user-circle'],
                ], $isPremium),
            ];
        }

        // 5. Painel do Paciente (Paciente ou admin explorando)
        if (($user->hasRole('paciente') && (!$activeRole || $activeRole === 'paciente')) || ($isAdmin && $activeRole === 'paciente')) {
            $groups[] = [
                'id' => 'patient',
                'label' => 'Painel do Paciente',
                'icon' => 'fas fa-user-injured',
                'items' => $this->prepareItems($user, [
                    ['name' => 'patient_dashboard', 'label' => 'Visão Geral', 'route' => 'patient.unified.dashboard', 'icon' => 'fas fa-hospital-user'],
                    ['name' => 'patient_records', 'label' => 'Prontuário', 'route' => 'patient.medical-records.index', 'icon' => 'fas fa-file-medical'],
                    ['name' => 'patient_exams', 'label' => 'Exames & Docs', 'route' => 'patient.documents', 'icon' => 'fas fa-file-invoice'],
                    ['name' => 'patient_appointments', 'label' => 'Consultas', 'route' => 'patient.agenda', 'icon' => 'fas fa-calendar-check'],
                ], $isPremium),
            ];
        }

        // 6. Portal do Representante
        if ($user->hasRole('representative') && (!$activeRole || $activeRole === 'representative')) {
            $groups[] = [
                'id' => 'representative',
                'label' => 'Portal do Representante',
                'icon' => 'fas fa-handshake',
                'items' => $this->prepareItems($user, [
                    ['name' => 'rep_dashboard', 'label' => 'Minhas Vendas', 'route' => 'representative.dashboard', 'icon' => 'fas fa-chart-bar'],
                    ['name' => 'rep_clients', 'label' => 'Meus Clientes', 'route' => 'representative.referrals', 'icon' => 'fas fa-users'],
                ], $isPremium),
            ];
        }

        // 7. Atendimento & Ajuda
        $groups[] = [
            'id' => 'support',
            'label' => 'Suporte e Ajuda',
            'icon' => 'fas fa-question-circle',
            'items' => $this->prepareItems($user, [
                ['name' => 'support_tech', 'label' => 'Suporte', 'route' => 'support.tickets.index', 'icon' => 'fas fa-life-ring'],
                ['name' => 'kb_index', 'label' => 'Ajuda', 'route' => 'kb.index', 'icon' => 'fas fa-book'],
                ['name' => 'legal_terms', 'label' => 'Privacidade', 'route' => 'legal.terms', 'icon' => 'fas fa-shield-alt'],
            ], $isPremium),
        ];

        return $groups;
    }

    /**
     * Garante no máximo um item ativo; em empate (mesma rota em vários links), mantém o primeiro na ordem do menu.
     *
     * @param  array<int, array{id: string, label: string, icon: string, items: array<int, array<string, mixed>>}>  $groups
     * @return array<int, array{id: string, label: string, icon: string, items: array<int, array<string, mixed>>}>
     */
    private function ensureSingleActiveMenuItem(array $groups): array
    {
        $candidates = [];
        $order = 0;
        foreach ($groups as $gi => $group) {
            foreach ($group['items'] as $ii => $item) {
                if (! empty($item['is_active'])) {
                    $candidates[] = [
                        'gi' => $gi,
                        'ii' => $ii,
                        'order' => $order++,
                        'score' => $this->activeMenuMatchScore($item['route'] ?? ''),
                    ];
                }
            }
        }

        if (count($candidates) <= 1) {
            return $groups;
        }

        usort($candidates, function ($a, $b) {
            if ($a['score'] !== $b['score']) {
                return $b['score'] <=> $a['score'];
            }

            return $a['order'] <=> $b['order'];
        });

        array_shift($candidates);
        foreach ($candidates as $c) {
            $groups[$c['gi']]['items'][$c['ii']]['is_active'] = false;
        }

        return $groups;
    }

    /**
     * Prioriza correspondência exata ao nome da rota atual; depois especificidade (comprimento do nome da rota do item).
     */
    private function activeMenuMatchScore(string $itemRoute): int
    {
        $currentRoute = request()->route()?->getName() ?? '';
        if ($currentRoute === $itemRoute) {
            return 100_000 + strlen($itemRoute);
        }

        return strlen($itemRoute);
    }

    /**
     * Prepare menu items with status, active state and badges.
     */
    private function prepareItems(User $user, array $items, bool $isPremium): array
    {
        $prepared = [];
        $isClinic = $user->hasRole(['professional', 'instructor', 'manager', 'receptionist', 'supervisor', 'paciente']);

        foreach ($items as $item) {
            $isLocked = ($item['premium'] ?? false) && !$isPremium;
            $currentRoute = request()->route()?->getName() ?? '';
            $itemRoute = $item['route'] ?? '';
            $isActive = $this->isRouteActive($currentRoute, $itemRoute);

            $label = $item['label'];
            
            // Medical Language Adaptation for Clinic Experience
            if ($isClinic) {
                $medicalMap = [
                    'Pacientes' => 'Pacientes',
                    'Membros' => 'Pacientes',
                    'Alunos' => 'Pacientes',
                    'Meus Alunos' => 'Meus Pacientes',
                    'Agenda' => 'Agenda de Consultas',
                    'Treinos' => 'Prescrições / Treinos',
                    'Meus Treinos' => 'Conduta Clínica',
                    'Dashboard' => 'Painel Clínico',
                    'Meu Perfil' => 'Prontuário Profissional',
                    'Evolução' => 'Evolução Clínica',
                ];
                $label = $medicalMap[$label] ?? $label;
            }

            $prepared[] = [
                'name' => $item['name'],
                'label' => $label,
                'route' => $item['route'],
                'icon' => $item['icon'],
                'is_locked' => $isLocked,
                'is_premium' => $item['premium'] ?? false,
                'is_active' => $isActive,
                'badge' => $this->getItemBadge($user, $item['name']),
            ];
        }
        return $prepared;
    }

    /**
     * Verifica se a rota atual corresponde ao item de menu, considerando sub-rotas.
     */
    private function isRouteActive(string $currentRoute, string $itemRoute): bool
    {
        // 1. Match direto
        if ($currentRoute === $itemRoute) return true;

        // 2. Match por prefixo (ex: paciente.relatorios -> paciente.relatorios.show)
        // Remove .index, .show, .create, .edit para pegar a base
        $itemBase = preg_replace('/\.(index|show|create|edit|store|update|destroy)$/', '', $itemRoute);
        $currentBase = preg_replace('/\.(index|show|create|edit|store|update|destroy)$/', '', $currentRoute);
        
        if ($itemBase !== '' && $itemBase === $currentBase) return true;

        // 3. Fallback para str_starts_with (casos onde a base é maior)
        if ($itemBase !== '' && str_starts_with($currentRoute, $itemBase . '.')) return true;

        // 4. Fallback por URL (request path)
        $path = trim(str_replace('.', '/', $itemRoute), '/');
        if ($path !== '' && request()->is($path . '*')) return true;

        return false;
    }

    /**
     * Get notification badge count for specific items.
     */
    private function getItemBadge(User $user, string $name): mixed
    {
        if ($name === 'messages') {
            return Message::whereHas('conversation', function($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })->where('sender_id', '!=', $user->id)->where('is_read', false)->count();
        }

        if ($name === 'support_tech') {
            return \App\Models\SupportTicket::where('user_id', $user->id)
                ->whereIn('status', ['Open', 'Pending', 'In Progress'])
                ->count();
        }

        if ($name === 'manual') {
            return 'Novo';
        }

        return null;
    }
}
