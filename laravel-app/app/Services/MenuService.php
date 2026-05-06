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
                    'patient.professionals.search'
                ],
                'aluno' => [
                    'profile', 'progression.plans', 'diary', 'assessments', 'calendar', 'plano',
                    'export', 'messages', 'presence', 'nutrition', 'weight',
                    'hydration', 'chat', 'leaderboard', 'active-rest', 'exercise', 'evolution', 
                    'trophies', 'body-analysis', 'patient.professionals.search', 'report'
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

        // 1. Portal do Profissional (Prioridade para Profissionais)
        if ($user->isAdministrator() || $user->hasRole(['professional', 'instructor', 'supervisor'])) {
            $groups[] = [
                'id' => 'professional_portal',
                'label' => 'Portal do Profissional',
                'icon' => 'fas fa-user-tie',
                'items' => $this->prepareItems($user, [
                    ['name' => 'pro_dashboard', 'label' => 'Dashboard', 'route' => 'professional.dashboard', 'icon' => 'fas fa-th-large'],
                    ['name' => 'patients', 'label' => 'Pacientes', 'route' => 'professional.patients.index', 'icon' => 'fas fa-users'],
                    ['name' => 'exercise', 'label' => 'Treinos', 'route' => 'exercise', 'icon' => 'fas fa-dumbbell'],
                    ['name' => 'plans', 'label' => 'Progressão (Planos)', 'route' => 'progression.plans.index', 'icon' => 'fas fa-layer-group'],
                    ['name' => 'charts', 'label' => 'Gráficos', 'route' => 'progression.charts', 'icon' => 'fas fa-chart-line', 'premium' => true],
                    ['name' => 'nutrition', 'label' => 'Nutrição', 'route' => 'nutrition.index', 'icon' => 'fas fa-utensils'],
                    ['name' => 'assessments', 'label' => 'Avaliações', 'route' => 'assessments.index', 'icon' => 'fas fa-clipboard-check'],
                    ['name' => 'weight', 'label' => 'Peso', 'route' => 'weight', 'icon' => 'fas fa-weight'],
                    ['name' => 'hydration', 'label' => 'Hidratação', 'route' => 'hydration.index', 'icon' => 'fas fa-tint'],

                    ['name' => 'calendar', 'label' => 'Agenda', 'route' => 'agenda.index', 'icon' => 'fas fa-calendar-alt'],
                    ['name' => 'medical_records', 'label' => 'Prontuário / Laudos', 'route' => 'professional.patients.index', 'icon' => 'fas fa-file-medical-alt'],
                    ['name' => 'report', 'label' => 'Relatórios', 'route' => 'professional.reports.index', 'icon' => 'fas fa-file-pdf'],
                    ['name' => 'ai_wizard', 'label' => 'IA Wizard', 'route' => 'professional.ai-wizard.index', 'icon' => 'fas fa-magic', 'premium' => true],
                    ['name' => 'ai_credits', 'label' => 'Saldo de IA', 'route' => 'ai-credits.dashboard', 'icon' => 'fas fa-coins'],
                    ['name' => 'ai_templates', 'label' => 'Meus Templates (IA)', 'route' => 'professional.templates.index', 'icon' => 'fas fa-file-code'],
                    ['name' => 'branding', 'label' => 'Branding', 'route' => 'professional.branding', 'icon' => 'fas fa-id-card', 'premium' => true],
                ], $isPremium),
            ];
        }

        // 2. Painel do Aluno (Resumo de Treino e Saúde)
        if ($user->hasRole('aluno') || $user->isAdministrator()) {
            $alunoItems = [
                ['name' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-th-large'],
                ['name' => 'chat', 'label' => 'Chat IA', 'route' => 'chat.page', 'icon' => 'fas fa-robot', 'premium' => true],
            ];

            $groups[] = [
                'id' => 'aluno_panel',
                'label' => 'Painel do Aluno',
                'icon' => 'fas fa-running',
                'items' => $this->prepareItems($user, $alunoItems, $isPremium),
            ];

            // Subgrupos de Treino e Saúde (Continuam dentro do escopo Aluno)
            $trainingItems = [
                ['name' => 'progression.plans', 'label' => 'Meus Treinos', 'route' => 'progression.plans.index', 'icon' => 'fas fa-clipboard-list'],
                ['name' => 'exercise', 'label' => 'Registro de Treino', 'route' => 'exercise', 'icon' => 'fas fa-history'],
                ['name' => 'active-rest', 'label' => 'Descanso Ativo', 'route' => 'active-rest.index', 'icon' => 'fas fa-leaf', 'premium' => true],
            ];

            if ($user->isAdministrator() || $user->professionals()->exists()) {
                $trainingItems[] = ['name' => 'calendar', 'label' => 'Agenda de Treino', 'route' => 'agenda.index', 'icon' => 'fas fa-calendar-alt'];
            }

            $groups[] = [
                'id' => 'training',
                'label' => 'Treinamento',
                'icon' => 'fas fa-dumbbell',
                'items' => $this->prepareItems($user, $trainingItems, $isPremium),
            ];

            $groups[] = [
                'id' => 'nutrition_health',
                'label' => 'Nutrição e Saúde',
                'icon' => 'fas fa-heartbeat',
                'items' => $this->prepareItems($user, [
                    ['name' => 'nutrition', 'label' => 'Hub de Nutrição', 'route' => 'nutrition.index', 'icon' => 'fas fa-utensils'],
                    ['name' => 'weight', 'label' => 'Peso Corporal', 'route' => 'weight', 'icon' => 'fas fa-weight-hanging'],
                    ['name' => 'hydration', 'label' => 'Hidratação', 'route' => 'hydration.index', 'icon' => 'fas fa-tint'],
                ], $isPremium),
            ];

            $groups[] = [
                'id' => 'my_progress',
                'label' => 'Meu Progresso',
                'icon' => 'fas fa-chart-line',
                'items' => $this->prepareItems($user, [
                    ['name' => 'report', 'label' => 'Relatórios', 'route' => 'patient.reports.index', 'icon' => 'fas fa-file-invoice'],
                    ['name' => 'progression.charts', 'label' => 'Evolução de Força', 'route' => 'progression.charts', 'icon' => 'fas fa-dumbbell', 'premium' => true],
                    ['name' => 'evolution', 'label' => 'Galeria de Fotos', 'route' => 'evolution.index', 'icon' => 'fas fa-images'],
                    ['name' => 'assessments', 'label' => 'Evolução Corporal', 'route' => 'assessments.index', 'icon' => 'fas fa-clipboard-check'],
                    ['name' => 'body-analysis', 'label' => 'Análise Corporal (IA)', 'route' => 'body-analysis.index', 'icon' => 'fas fa-brain', 'premium' => true],
                    ['name' => 'leaderboard', 'label' => 'Ranking Geral', 'route' => 'leaderboard.index', 'icon' => 'fas fa-trophy', 'premium' => true],
                    ['name' => 'trophies', 'label' => 'Conquistas', 'route' => 'trophies.index', 'icon' => 'fas fa-medal', 'premium' => true],
                ], $isPremium),
            ];

            if ($user->hasRole(['aluno']) || $user->isAdministrator()) {
                $groups[] = [
                    'id' => 'user_account',
                    'label' => 'Sua Conta',
                    'icon' => 'fas fa-user-cog',
                    'items' => $this->prepareItems($user, [
                        ['name' => 'profile', 'label' => 'Meu Perfil', 'route' => 'profile', 'icon' => 'fas fa-user-circle'],
                        ['name' => 'ai_credits', 'label' => 'Saldo de IA', 'route' => 'ai-credits.dashboard', 'icon' => 'fas fa-coins'],
                        ['name' => 'subscription', 'label' => 'Financeiro & Plano', 'route' => 'patient.subscription.index', 'icon' => 'fas fa-wallet'],
                        ['name' => 'link_professional', 'label' => 'Buscar Profissional', 'route' => 'patient.professionals.search', 'icon' => 'fas fa-user-plus'],
                    ], $isPremium),
                ];
            }
        }
        // 3. Painel do Paciente (Prescrições e Acompanhamento Profissional)
        if ($user->hasRole('paciente') || $user->isAdministrator()) {
            $patientItems = [
                ['name' => 'patient.unified.dashboard', 'label' => 'Meu Painel de Saúde', 'route' => 'patient.unified.dashboard', 'icon' => 'fas fa-heartbeat'],
                ['name' => 'patient_reports', 'label' => 'Prontuário / Laudos', 'route' => 'patient.medical-records.index', 'icon' => 'fas fa-file-medical-alt'],
            ];

            if (!$user->hasRole('aluno')) {
                $patientItems[] = ['name' => 'patient_plans', 'label' => 'Planos e Assinaturas', 'route' => 'patient.plans.index', 'icon' => 'fas fa-crown'];
            }

            $groups[] = [
                'id' => 'patient_panel',
                'label' => 'Painel do Paciente',
                'icon' => 'fas fa-user-md',
                'items' => $this->prepareItems($user, $patientItems, $isPremium),
            ];
        }

        // 3. Recepção
        if ($user->isAdministrator() || $user->hasRole(['receptionist', 'manager'])) {
            $groups[] = [
                'id' => 'reception',
                'label' => 'Menu da Recepção',
                'icon' => 'fas fa-concierge-bell',
                'items' => $this->prepareItems($user, [
                    ['name' => 'recep_dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-home'],
                    ['name' => 'user_registration', 'label' => 'Cadastro de Usuário', 'route' => 'dashboard', 'icon' => 'fas fa-user-plus'],
                    ['name' => 'presence', 'label' => 'Controle de Presença', 'route' => 'dashboard', 'icon' => 'fas fa-id-card'],
                    ['name' => 'calendar', 'label' => 'Agenda Geral', 'route' => 'agenda.index', 'icon' => 'fas fa-calendar-day'],
                    ['name' => 'clinic_settings', 'label' => 'Gestão da Clínica', 'route' => 'admin.clinic.settings', 'icon' => 'fas fa-building'],
                    ['name' => 'clinic_protocols', 'label' => 'Protocolos Padrão', 'route' => 'admin.clinic.protocols.index', 'icon' => 'fas fa-notes-medical'],
                    ['name' => 'clinic_billing', 'label' => 'Faturamento/Assinatura', 'route' => 'admin.clinic.billing', 'icon' => 'fas fa-credit-card'],
                ], $isPremium),
            ];
        }

        // 4. Portal do Representante
        if ($user->isAdministrator() || $user->is_representative) {
            $groups[] = [
                'id' => 'representative_portal',
                'label' => 'Portal do Representante',
                'icon' => 'fas fa-handshake',
                'items' => $this->prepareItems($user, [
                    ['name' => 'rep_dashboard', 'label' => 'Dashboard', 'route' => 'representative.dashboard', 'icon' => 'fas fa-chart-pie'],
                    ['name' => 'rep_commissions', 'label' => 'Minhas Comissões', 'route' => 'representative.commissions', 'icon' => 'fas fa-dollar-sign'],
                    ['name' => 'rep_referrals', 'label' => 'Minhas Indicações', 'route' => 'representative.referrals', 'icon' => 'fas fa-users'],
                    ['name' => 'rep_withdraw', 'label' => 'Resgates e Saques', 'route' => 'representative.withdraw.form', 'icon' => 'fas fa-wallet'],
                ], $isPremium),
            ];
        }

        // 5. Admin
        if ($user->isAdministrator()) {
            $groups[] = [
                'id' => 'administration',
                'label' => 'Menu de Administração',
                'icon' => 'fas fa-tools',
                'items' => $this->prepareItems($user, [
                    ['name' => 'admin_dashboard', 'label' => 'Painel Admin', 'route' => 'admin.dashboard', 'icon' => 'fas fa-shield-alt'],
                    ['name' => 'users_manage', 'label' => 'Gestão de Usuários', 'route' => 'admin.users', 'icon' => 'fas fa-users-cog'],
                    ['name' => 'pdf_companies', 'label' => 'Empresas & Unidades', 'route' => 'admin.pdf-companies.index', 'icon' => 'fas fa-building'],
                    ['name' => 'settings', 'label' => 'Configurações', 'route' => 'admin.settings', 'icon' => 'fas fa-cogs'],
                    ['name' => 'finance_sys', 'label' => 'Financeiro SaaS', 'route' => 'admin.financial.dashboard', 'icon' => 'fas fa-university'],
                    ['name' => 'billing_credits', 'label' => 'Cobrança / Créditos', 'route' => 'admin.billing.credits', 'icon' => 'fas fa-credit-card'],
                    ['name' => 'finance_mgmt', 'label' => 'Gestão de Cobrança', 'route' => 'admin.financial.management', 'icon' => 'fas fa-file-invoice-dollar'],
                    ['name' => 'report_sys', 'label' => 'Relatórios Globais', 'route' => 'admin.financial.reports', 'icon' => 'fas fa-chart-pie'],
                ], $isPremium),
            ];
        }

        // 5. Atendimento & Ajuda
        if ($user) {
            $supportItems = [
                ['name' => 'support_tech', 'label' => 'Suporte', 'route' => 'support.tickets.index', 'icon' => 'fas fa-life-ring'],
                ['name' => 'help_center', 'label' => 'Base de Conhecimento', 'route' => 'kb.index', 'icon' => 'fas fa-book'],
                ['name' => 'manual', 'label' => 'Academia NexShape', 'route' => 'training.index', 'icon' => 'fas fa-graduation-cap'],
                ['name' => 'community', 'label' => 'Comunidade NexShape', 'route' => 'groups.index', 'icon' => 'fas fa-users'],
                ['name' => 'sys_status', 'label' => 'Status do Sistema', 'route' => 'system.status', 'icon' => 'fas fa-signal'],
                ['name' => 'legal_terms', 'label' => 'Termos & Privacidade', 'route' => 'legal.terms', 'icon' => 'fas fa-shield-alt'],
            ];

            // Filtro específico para o perfil Paciente
            if ($user->hasRole('paciente') && !$user->isAdministrator()) {
                $supportItems = [
                    ['name' => 'support_tech', 'label' => 'Suporte', 'route' => 'support.tickets.index', 'icon' => 'fas fa-life-ring'],
                    ['name' => 'legal_terms', 'label' => 'Termos & Privacidade', 'route' => 'legal.terms', 'icon' => 'fas fa-shield-alt'],
                ];
            }

            $groups[] = [
                'id' => 'support',
                'label' => 'Atendimento & Ajuda',
                'icon' => 'fas fa-headset',
                'items' => $this->prepareItems($user, $supportItems, $isPremium),
            ];
        }

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
