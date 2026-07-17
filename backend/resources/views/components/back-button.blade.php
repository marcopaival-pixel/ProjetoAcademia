@props([
    'route' => null, 
    'label' => null
])

@php
    $currentRoute = request()->route()?->getName();
    
    /**
     * Mapeamento Inteligente de Hierarquia
     * Define para onde o utilizador deve voltar caso não haja histórico ou se o histórico for inválido.
     */
    $parentMapping = [
        // Treinos e Progressão
        'exercise' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'exercise.catalog' => ['route' => 'exercise', 'label' => 'Meus Treinos'],
        'exercise.show' => ['route' => 'exercise.catalog', 'label' => 'Catálogo'],
        'progression.plans.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'progression.plans.show' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        'progression.plans.edit' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        'progression.plans.create' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        'progression.plans.target-selection' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        'progression.charts' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        'progression.log' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        'progression.session-log' => ['route' => 'progression.plans.index', 'label' => 'Planos de Treino'],
        
        // Saúde e Avaliações
        'assessments.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'assessments.show' => ['route' => 'assessments.index', 'label' => 'Avaliações'],
        'assessments.create' => ['route' => 'assessments.index', 'label' => 'Avaliações'],
        'active-rest.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'active-rest.show' => ['route' => 'active-rest.index', 'label' => 'Descanso Ativo'],
        'hydration.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'weight' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'body-analysis.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        
        // Nutrição
        'nutrition.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'diary' => ['route' => 'nutrition.index', 'label' => 'Nutrição'],
        
        // Social e Comunicação
        'leaderboard.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'messages.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'messages.show' => ['route' => 'messages.index', 'label' => 'Mensagens'],
        'messages.create' => ['route' => 'messages.index', 'label' => 'Mensagens'],
        'groups.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        
        // Suporte e Sistema
        'support.tickets.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'support.tickets.show' => ['route' => 'support.tickets.index', 'label' => 'Suporte'],
        'support.tickets.create' => ['route' => 'support.tickets.index', 'label' => 'Suporte'],
        'system.status' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        
        // Financeiro
        'plano' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'checkout.index' => ['route' => 'plano', 'label' => 'Planos'],
        'credits.buy' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'ai-credits.packages' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        
        // Perfil
        'profile' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'menu.preferences.index' => ['route' => 'profile', 'label' => 'Meu Perfil'],
        
        // Portal do Paciente
        'patient.portal' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'patient.plans.index' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.evolution' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.prescriptions' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.documents' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.agenda' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.messages' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.treatment-plan' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.medical-records.index' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'patient.reports.index' => ['route' => 'patient.portal', 'label' => 'Portal'],
        'report' => ['route' => 'patient.reports.index', 'label' => 'Relatórios'],

        // Base de Conhecimento
        'kb.index' => ['route' => 'dashboard', 'label' => 'Dashboard'],
        'kb.show' => ['route' => 'kb.index', 'label' => 'Central de Ajuda'],
        'admin.kb.index' => ['route' => 'admin.dashboard', 'label' => 'Painel Admin'],
        'admin.kb.create' => ['route' => 'admin.kb.index', 'label' => 'Help Center'],
        'admin.kb.edit' => ['route' => 'admin.kb.index', 'label' => 'Help Center'],
    ];

    $parentInfo = $parentMapping[$currentRoute] ?? null;
    
    // Se for uma página de "Novo" ou "Editar" genérica que não está no mapeamento
    if (!$parentInfo && $currentRoute && (str_contains($currentRoute, '.create') || str_contains($currentRoute, '.edit') || str_contains($currentRoute, '.show'))) {
        $parts = explode('.', $currentRoute);
        array_pop($parts);
        $baseRoute = implode('.', $parts) . '.index';
        if (Route::has($baseRoute)) {
            $parentInfo = ['route' => $baseRoute, 'label' => 'Anterior'];
        }
    }

    $fallbackRouteName = $route ?? ($parentInfo['route'] ?? 'dashboard');
    
    // Tenta gerar a URL. Se falhar (ex: rota com parâmetros obrigatórios), cai para dashboard.
    try {
        $fallbackUrl = route($fallbackRouteName);
    } catch (\Exception $e) {
        $fallbackUrl = route('dashboard');
    }

    $displayLabel = $label ?? (($parentInfo['label'] ?? null) ? 'Voltar para ' . $parentInfo['label'] : 'Voltar');
    
    // Evita redundância "Voltar para Voltar" ou "Voltar para Anterior"
    if (str_contains($displayLabel, 'Voltar para Voltar') || str_contains($displayLabel, 'Voltar para Anterior')) {
        $displayLabel = 'Voltar';
    }

    // Define se a rota atual é um índice principal onde o history.back() causaria confusão
    $forceFallbackRoutes = [
        'support.tickets.index', 'kb.index', 'exercise', 'nutrition.index', 
        'messages.index', 'assessments.index', 'active-rest.index', 
        'body-analysis.index', 'leaderboard.index', 
        'groups.index', 'system.status', 'plano', 'credits.buy',
        'admin.kb.index', 'patient.plans.index', 'patient.medical-records.index', 'patient.reports.index'
    ];
    $forceFallback = in_array($currentRoute, $forceFallbackRoutes);
@endphp

<div class="back-navigation-container mb-8 animate-fade-in">
    <button 
        type="button"
        onclick="if(!{{ $forceFallback ? 'true' : 'false' }} && document.referrer && document.referrer.includes(window.location.hostname) && !document.referrer.includes(window.location.pathname)) { history.back(); } else { window.location.href = '{{ $fallbackUrl }}'; }"
        class="group flex items-center gap-4 px-5 py-2.5 rounded-2xl bg-zinc-950/40 border border-zinc-900 text-zinc-400 hover:text-white hover:border-emerald-500/30 hover:bg-zinc-900 transition-all shadow-sm active:scale-95"
    >
        <div class="w-8 h-8 rounded-xl bg-zinc-900 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all duration-300 shadow-inner">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform"></i>
        </div>
        <span class="text-[10px] font-black uppercase tracking-[0.1em]">{{ $displayLabel }}</span>
    </button>
</div>
