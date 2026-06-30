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
                'admin' => ['dashboard', 'users', 'pdf_companies', 'settings', 'report', 'finance_admin'],
                'professional' => [
                    'dashboard', 'patients', 'exercise', 'diary', 'assessments', 'calendar', 'report',
                    'messages', 'presence', 'plano', 'nutrition', 'weight', 'hydration',
                    'chat', 'leaderboard', 'active-rest', 'evolution', 'trophies', 'body-analysis', 
                    'patient.professionals.search', 'community', 'hydration', 'health-metrics',
                    'finance_dashboard', 'finance_entries', 'finance_categories', 'finance_reports'
                ],
                'aluno' => [
                    'progression.plans', 'diary', 'assessments', 'calendar', 'plano',
                    'export', 'messages', 'presence', 'nutrition', 'weight',
                    'hydration', 'chat', 'leaderboard', 'active-rest', 'exercise', 'evolution', 
                    'trophies', 'body-analysis', 'patient.professionals.search', 'report', 'community', 'health-metrics'
                ],
                'paciente' => [
                    'patient.unified.dashboard', 'patient.portal', 'plano', 'report'
                ],
                'receptionist' => ['dashboard', 'user_registration', 'presence', 'plano', 'clinic_settings', 'clinic_billing'],
                'finance' => ['dashboard', 'billing', 'financial_reports', 'plano'],
                'manager' => [
                    'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                    'report', 'export', 'messages', 'settings', 'clinic_settings', 'clinic_billing'
                ],
                'instructor' => [
                    'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                    'report', 'export', 'messages', 'settings',
                ],
                'supervisor' => [
                    'dashboard', 'patients', 'assessments', 'exercise', 'diary', 'calendar',
                    'report', 'export', 'messages', 'settings',
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
                'icon' => 'shield-check',
                'items' => $this->prepareItems($user, [
                    ['name' => 'admin_dashboard', 'label' => 'Painel Admin', 'route' => 'admin.dashboard', 'icon' => 'shield'],
                    ['name' => 'executive_dashboard', 'label' => 'Dashboard Executivo IA', 'route' => 'admin.executive.dashboard', 'icon' => 'sparkles'],
                    ['name' => 'users_manage', 'label' => 'Gestão de Usuários', 'route' => 'admin.users', 'icon' => 'users'],
                    ['name' => 'pdf_companies', 'label' => 'Empresas & Unidades', 'route' => 'admin.pdf-companies.index', 'icon' => 'building'],
                    ['name' => 'settings', 'label' => 'Configurações', 'route' => 'admin.settings', 'icon' => 'settings'],
                    ['name' => 'finance_sys', 'label' => 'Financeiro SaaS', 'route' => 'admin.financial.dashboard', 'icon' => 'landmark'],
                    ['name' => 'monetization_features', 'label' => 'Monetização: Funções', 'route' => 'admin.monetization.features', 'icon' => 'list-check'],
                    ['name' => 'monetization_limits', 'label' => 'Monetização: Limites', 'route' => 'admin.monetization.limits', 'icon' => 'gauge'],
                    ['name' => 'monetization_popups', 'label' => 'Monetização: Popups', 'route' => 'admin.monetization.popups', 'icon' => 'layout'],
                    ['name' => 'billing_credits', 'label' => 'Cobrança / Créditos', 'route' => 'admin.billing.credits', 'icon' => 'credit-card'],
                    ['name' => 'finance_mgmt', 'label' => 'Gestão de Cobrança', 'route' => 'admin.financial.management', 'icon' => 'receipt'],
                    ['name' => 'report_sys', 'label' => 'Relatórios Globais', 'route' => 'admin.financial.reports', 'icon' => 'pie-chart'],
                    ['name' => 'marketing_banners', 'label' => 'Marketing: Banners', 'route' => 'admin.marketing.banners.index', 'icon' => 'megaphone'],
                    ['name' => 'training_mgmt', 'label' => 'Academia: Gestão', 'route' => 'admin.training.index', 'icon' => 'graduation-cap'],
                    ['name' => 'shop_admin', 'label' => 'Shopping Fitness', 'route' => 'admin.shop.products.index', 'icon' => 'shopping-bag'],
                    ['name' => 'omnichat', 'label' => 'OmniChat (Real-time)', 'route' => 'admin.omnichannel', 'icon' => 'message-square'],
                ], $isPremium),
            ];
        }

        // 2. Menu da Recepção / Clínica (Para recepção, gestor ou admin explorando)
        if (($user->hasRole(['receptionist', 'manager']) && (!$activeRole || in_array($activeRole, ['receptionist', 'manager', 'gestor']))) || ($isAdmin && $activeRole === 'admin')) {
             // Admin vê recepção mesmo em 'admin' mode por ser parte da gestão clínica
            $groups[] = [
                'id' => 'reception',
                'label' => 'Menu da Recepção',
                'icon' => 'bell',
                'items' => $this->prepareItems($user, [
                    ['name' => 'recep_dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home'],
                    ['name' => 'user_registration', 'label' => 'Cadastro de Usuário', 'route' => 'admin.registrations.index', 'icon' => 'user-plus'],
                    ['name' => 'presence', 'label' => 'Controle de Presença', 'route' => 'admin.users', 'icon' => 'contact-2'],
                    ['name' => 'calendar', 'label' => 'Agenda Geral', 'route' => 'agenda.index', 'icon' => 'calendar-days'],
                    ['name' => 'clinic_settings', 'label' => 'Gestão da Clínica', 'route' => 'admin.clinic.settings', 'icon' => 'building'],
                    ['name' => 'clinic_protocols', 'label' => 'Protocolos Padrão', 'route' => 'admin.clinic.protocols.index', 'icon' => 'clipboard-list'],
                    ['name' => 'clinic_billing', 'label' => 'Faturamento/Assinatura', 'route' => 'admin.clinic.billing', 'icon' => 'credit-card'],
                ], $isPremium),
            ];
        }

        // 3. Portal do Profissional (Profissional, instrutor ou admin explorando)
        if (($user->hasRole(['professional', 'instructor']) && (!$activeRole || $activeRole === 'professional')) || ($isAdmin && $activeRole === 'professional')) {
            
            $enabledModules = [];
            $hasSpecialtyConfig = false;
            
            if ($user->professionalProfile) {
                $specialtyId = session('active_specialty_id');
                $specialty = null;
                
                if ($specialtyId) {
                    $specialty = \App\Models\Especialidade::find($specialtyId);
                } else {
                    $specialty = $user->professionalProfile->especialidade;
                }

                if ($specialty && !empty($specialty->enabled_modules)) {
                    $enabledModules = is_string($specialty->enabled_modules) ? json_decode($specialty->enabled_modules, true) : $specialty->enabled_modules;
                    $hasSpecialtyConfig = true;
                }
            }

            $moduleMapping = [
                'prof_agenda' => 'agenda',
                'prof_patients' => 'patients',
                'prof_assessments' => 'assessments',
                'prof_protocols' => 'protocols',
                'prof_library' => 'library',
                'prof_import_workout' => 'ai_tools',
                'hydration' => 'ai_tools',
                'prof_evolution' => 'evolution',
            ];

            $filterByModule = function($items) use ($enabledModules, $hasSpecialtyConfig, $moduleMapping) {
                if (!$hasSpecialtyConfig) return $items;
                return array_values(array_filter($items, function($item) use ($enabledModules, $moduleMapping) {
                    $moduleName = $moduleMapping[$item['name']] ?? null;
                    if (!$moduleName) return true; // Mostra por padrão se não tiver mapeamento restrito
                    return in_array($moduleName, $enabledModules);
                }));
            };

            // GESTÃO
            $gestaoItems = $filterByModule([
                ['name' => 'prof_dashboard', 'label' => 'Dashboard', 'route' => 'professional.dashboard', 'icon' => 'layout-dashboard'],
                ['name' => 'prof_agenda', 'label' => 'Agenda', 'route' => 'agenda.index', 'icon' => 'calendar'],
                ['name' => 'prof_patients', 'label' => 'Pacientes', 'route' => 'professional.patients.index', 'icon' => 'users'],
            ]);
            if (!empty($gestaoItems)) {
                $groups[] = [
                    'id' => 'professional_management',
                    'label' => 'Gestão',
                    'icon' => 'briefcase',
                    'items' => $this->prepareItems($user, $gestaoItems, $isPremium),
                ];
            }

            // ATENDIMENTO
            $atendimentoItems = $filterByModule([
                ['name' => 'prof_assessments', 'label' => 'Avaliações', 'route' => 'assessments.index', 'icon' => 'clipboard-list'],
                ['name' => 'prof_protocols', 'label' => 'Protocolos de Treino', 'route' => 'progression.plans.index', 'icon' => 'dumbbell'],
                ['name' => 'prof_library', 'label' => 'Biblioteca de Exercícios', 'route' => 'exercise.catalog', 'icon' => 'book-open-check'],
                ['name' => 'prof_files', 'label' => 'Arquivos do Paciente', 'route' => 'professional.patients.index', 'icon' => 'folder-open'],
                ['name' => 'prof_evolution', 'label' => 'Evolução Clínica', 'route' => 'professional.patients.index', 'icon' => 'trending-up'],
            ]);
            if (!empty($atendimentoItems)) {
                $groups[] = [
                    'id' => 'professional_clinical',
                    'label' => 'Atendimento',
                    'icon' => 'stethoscope',
                    'items' => $this->prepareItems($user, $atendimentoItems, $isPremium),
                ];
            }

            // INTELIGÊNCIA ARTIFICIAL
            $aiItems = $filterByModule([
                ['name' => 'prof_import_workout', 'label' => 'Importar Treino IA', 'route' => 'progression.plans.import-photo', 'icon' => 'camera', 'premium' => true],
                ['name' => 'hydration', 'label' => 'Nex Hydra', 'route' => 'hydration.index', 'icon' => 'droplet'],
            ]);
            if (!empty($aiItems)) {
                $groups[] = [
                    'id' => 'professional_ai',
                    'label' => 'Inteligência Artificial',
                    'icon' => 'sparkles',
                    'items' => $this->prepareItems($user, $aiItems, $isPremium),
                ];
            }

            // COMUNICAÇÃO
            $commsItems = $filterByModule([
                ['name' => 'messages', 'label' => 'Mensagens', 'route' => 'messages.index', 'icon' => 'mail'],
                ['name' => 'omnichat_prof', 'label' => 'Atendimento Omni', 'route' => 'admin.omnichannel', 'icon' => 'messages-square'],
            ]);
            if (!empty($commsItems)) {
                $groups[] = [
                    'id' => 'professional_communication',
                    'label' => 'Comunicação',
                    'icon' => 'message-square',
                    'items' => $this->prepareItems($user, $commsItems, $isPremium),
                ];
            }

            // FINANCEIRO
            if ($user->professionalProfile && $user->professionalProfile->use_finance_module && (!$hasSpecialtyConfig || in_array('finance', $enabledModules))) {
                $financeItems = [
                    ['name' => 'finance_dashboard', 'label' => 'Financeiro (Resumo)', 'route' => 'professional.finance.dashboard', 'icon' => 'bar-chart-2'],
                    ['name' => 'finance_entries', 'label' => 'Lançamentos', 'route' => 'professional.finance.entries.index', 'icon' => 'list'],
                    ['name' => 'finance_categories', 'label' => 'Categorias', 'route' => 'professional.finance.categories.index', 'icon' => 'tags'],
                    ['name' => 'finance_reports', 'label' => 'Relatórios', 'route' => 'professional.finance.reports.index', 'icon' => 'pie-chart'],
                ];
                $groups[] = [
                    'id' => 'professional_finance',
                    'label' => 'Financeiro',
                    'icon' => 'dollar-sign',
                    'items' => $this->prepareItems($user, $financeItems, $isPremium),
                ];
            }

            // CONFIGURAÇÕES
            $settingsItems = $filterByModule([
                ['name' => 'prof_profile', 'label' => 'Perfil', 'route' => 'professional.profile.edit', 'icon' => 'user'],
                ['name' => 'prof_preferences', 'label' => 'Preferências', 'route' => 'professional.profile.edit', 'icon' => 'sliders'],
                ['name' => 'prof_integrations', 'label' => 'Integrações', 'route' => 'professional.profile.edit', 'icon' => 'link'],
            ]);
            if (!empty($settingsItems)) {
                $groups[] = [
                    'id' => 'professional_settings',
                    'label' => 'Configurações',
                    'icon' => 'settings',
                    'items' => $this->prepareItems($user, $settingsItems, $isPremium),
                ];
            }
        }

        // 4. Painel do Aluno / Atleta (Aluno ou admin explorando)
        if (($user->hasRole('aluno') && (!$activeRole || $activeRole === 'aluno')) || ($isAdmin && $activeRole === 'aluno')) {
            $athleteItems = [
                    ['name' => 'dashboard', 'label' => 'Visão Geral', 'route' => 'dashboard', 'icon' => 'layout-grid'],
                    ['name' => 'import_workout', 'label' => 'Importar Treino (IA)', 'route' => 'progression.plans.import-photo', 'icon' => 'camera', 'premium' => true],
                    ['name' => 'training', 'label' => 'Meus Treinos', 'route' => 'progression.plans.index', 'icon' => 'footprints'],
                    ['name' => 'exercise', 'label' => 'Registro de Treino', 'route' => 'exercise', 'icon' => 'activity'],
                    ['name' => 'diet', 'label' => 'Alimentação', 'route' => 'diary', 'icon' => 'utensils', 'premium' => true],
                    ['name' => 'evolution', 'label' => 'Minha Evolução', 'route' => 'evolution.index', 'icon' => 'trending-up', 'premium' => true],
                    ['name' => 'community', 'label' => 'Comunidade NexShape', 'route' => 'community.index', 'icon' => 'users'],
                    ['name' => 'assessments', 'label' => 'Exames e Medidas', 'route' => 'body-analysis.index', 'icon' => 'heart-pulse', 'premium' => true],
                    ['name' => 'hydration', 'label' => 'Nex Hydra', 'route' => 'hydration.index', 'icon' => 'droplet'],
                    ['name' => 'chat', 'label' => 'NexBot (IA)', 'route' => 'chat.page', 'icon' => 'sparkles', 'premium' => true],
                    ['name' => 'messages', 'label' => 'Mensagens', 'route' => 'messages.index', 'icon' => 'mail'],
                    ['name' => 'calendar', 'label' => 'Agenda', 'route' => 'calendar', 'icon' => 'calendar-days'],
                    ['name' => 'leaderboard', 'label' => 'Ranking Global', 'route' => 'leaderboard.index', 'icon' => 'award'],
                    ['name' => 'trophies', 'label' => 'Conquistas', 'route' => 'trophies.index', 'icon' => 'trophy', 'premium' => true],
                    ['name' => 'plano', 'label' => 'Central Financeira', 'route' => 'patient.subscription.index', 'icon' => 'credit-card'],
                    ['name' => 'report', 'label' => 'Relatórios PDF', 'route' => 'report', 'icon' => 'file-text', 'premium' => true],
                    ['name' => 'active-rest', 'label' => 'Descanso Ativo', 'route' => 'active-rest.index', 'icon' => 'refresh-cw'],
                    ['name' => 'academia', 'label' => 'Academia NexShape', 'route' => 'training.index', 'icon' => 'play-circle'],
                    ['name' => 'health-metrics', 'label' => 'Saúde (Wearables)', 'route' => 'health-metrics.index', 'icon' => 'heart', 'premium' => true],
                    ['name' => 'access-logs', 'label' => 'Logs de Acesso (LGPD)', 'route' => 'patient.access-logs', 'icon' => 'shield-check'],
            ];

            if ($user->academy_company_id) {
                array_splice($athleteItems, -1, 0, [
                    ['name' => 'shopping_store', 'label' => 'Shopping Fitness', 'route' => 'shopping.index', 'icon' => 'shopping-bag'],
                    ['name' => 'shopping_orders', 'label' => 'Meus Pedidos', 'route' => 'shopping.orders.index', 'icon' => 'package'],
                    ['name' => 'shopping_points', 'label' => 'Pontos & Cashback', 'route' => 'shopping.points.index', 'icon' => 'coins'],
                ]);
            }

            $groups[] = [
                'id' => 'athlete',
                'label' => 'Painel do Aluno',
                'icon' => 'dumbbell',
                'items' => $this->prepareItems($user, $athleteItems, $isPremium),
            ];
        }

        // 5. Painel do Paciente (Paciente ou admin explorando)
        if (($user->hasRole('paciente') && (!$activeRole || $activeRole === 'paciente')) || ($isAdmin && $activeRole === 'paciente')) {
            $groups[] = [
                'id' => 'patient',
                'label' => 'Painel do Paciente',
                'icon' => 'user-minus',
                'items' => $this->prepareItems($user, [
                    ['name' => 'patient_dashboard', 'label' => 'Visão Geral', 'route' => 'patient.unified.dashboard', 'icon' => 'user-round'],
                    ['name' => 'patient_records', 'label' => 'Prontuário', 'route' => 'patient.medical-records.index', 'icon' => 'file-text'],
                    ['name' => 'patient_exams', 'label' => 'Exames & Docs', 'route' => 'patient.documents', 'icon' => 'file-input'],
                    ['name' => 'patient_appointments', 'label' => 'Consultas', 'route' => 'patient.agenda', 'icon' => 'calendar-check'],
                    ['name' => 'access-logs', 'label' => 'Logs de Acesso (LGPD)', 'route' => 'patient.access-logs', 'icon' => 'shield-check'],
                ], $isPremium),
            ];
        }

        // 6. Portal do Representante
        if ($user->hasRole('representative') && (!$activeRole || $activeRole === 'representative')) {
            $groups[] = [
                'id' => 'representative',
                'label' => 'Portal do Representante',
                'icon' => 'briefcase',
                'items' => $this->prepareItems($user, [
                    ['name' => 'rep_dashboard', 'label' => 'Dashboard', 'route' => 'representative.dashboard', 'icon' => 'bar-chart-3'],
                    ['name' => 'rep_leads', 'label' => 'Meus Leads', 'route' => 'representative.leads.index', 'icon' => 'target'],
                    ['name' => 'rep_proposals', 'label' => 'Propostas', 'route' => 'representative.proposals.index', 'icon' => 'file-text'],
                    ['name' => 'rep_contracts', 'label' => 'Contratos', 'route' => 'representative.contracts.index', 'icon' => 'file-signature'],
                    ['name' => 'rep_clinics', 'label' => 'Clínicas Vendidas', 'route' => 'representative.clinics.index', 'icon' => 'hospital'],
                    ['name' => 'rep_agenda', 'label' => 'Agenda', 'route' => 'representative.agenda.index', 'icon' => 'calendar'],
                    ['name' => 'rep_reports', 'label' => 'Relatórios', 'route' => 'representative.reports.index', 'icon' => 'pie-chart'],
                ], $isPremium),
            ];
            
            // Adicionar grupo secundário para Comissões e Indicações
            $groups[] = [
                'id' => 'representative_finance',
                'label' => 'Financeiro Comercial',
                'icon' => 'dollar-sign',
                'items' => $this->prepareItems($user, [
                    ['name' => 'rep_commissions', 'label' => 'Comissões', 'route' => 'representative.commissions', 'icon' => 'wallet'],
                    ['name' => 'rep_withdraw', 'label' => 'Saques', 'route' => 'representative.withdraw.form', 'icon' => 'arrow-up-right'],
                    ['name' => 'rep_clients', 'label' => 'Indicações', 'route' => 'representative.referrals', 'icon' => 'users'],
                ], $isPremium),
            ];
        }

        // 7. Atendimento & Ajuda
        $isActingAsPatient = ($user->hasRole('paciente') && (!$activeRole || $activeRole === 'paciente')) || ($isAdmin && $activeRole === 'paciente');
        $isActingAsRepresentative = ($user->hasRole('representative') && (!$activeRole || $activeRole === 'representative'));

        if (!$isActingAsPatient && !$isActingAsRepresentative) {
            $groups[] = [
                'id' => 'support',
                'label' => 'Suporte e Ajuda',
                'icon' => 'help-circle',
                'items' => $this->prepareItems($user, [
                    ['name' => 'support_tech', 'label' => 'Suporte', 'route' => 'support.tickets.index', 'icon' => 'life-buoy'],
                    ['name' => 'kb_index', 'label' => 'Ajuda', 'route' => 'kb.index', 'icon' => 'book-open'],
                    ['name' => 'legal_terms', 'label' => 'Privacidade', 'route' => 'legal.terms', 'icon' => 'shield'],
                ], $isPremium),
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
                $clientTerm = __t('Paciente');
                $clientTermPlural = $clientTerm === 'Aluno' ? 'Alunos' : ($clientTerm === 'Cliente' ? 'Clientes' : 'Pacientes');

                $medicalMap = [
                    'Pacientes' => $clientTermPlural,
                    'Membros' => $clientTermPlural,
                    'Alunos' => $clientTermPlural,
                    'Meus Alunos' => 'Meus ' . $clientTermPlural,
                    'Agenda' => 'Agenda de ' . ($clientTerm === 'Paciente' ? 'Consultas' : 'Atendimentos'),
                    'Treinos' => $clientTerm === 'Paciente' ? 'Prescrições / Condutas' : 'Treinos',
                    'Meus Treinos' => $clientTerm === 'Paciente' ? 'Conduta Clínica' : 'Meus Treinos',
                    'Dashboard' => $clientTerm === 'Paciente' ? 'Painel Clínico' : 'Painel Principal',
                    'Meu Perfil' => 'Perfil Profissional',
                    'Evolução' => $clientTerm === 'Paciente' ? 'Evolução Clínica' : 'Evolução',
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
