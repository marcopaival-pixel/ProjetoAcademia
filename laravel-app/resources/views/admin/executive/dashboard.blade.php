@extends('layouts.admin')

@section('title', 'Dashboard Executivo Inteligente — NexShape')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    @keyframes exec-fade-up {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes exec-pulse-ring {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(99, 102, 241, 0); }
    }
    @keyframes exec-shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .exec-animate { animation: exec-fade-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .exec-delay-1 { animation-delay: 0.1s; }
    .exec-delay-2 { animation-delay: 0.2s; }
    .exec-delay-3 { animation-delay: 0.3s; }
    .exec-delay-4 { animation-delay: 0.4s; }
    .exec-kpi:hover { transform: translateY(-2px); }
    .exec-kpi { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
    .exec-ai-pulse { animation: exec-pulse-ring 2s ease-in-out infinite; }
    .exec-shimmer-bar {
        background: linear-gradient(90deg, transparent, rgba(99,102,241,0.15), transparent);
        background-size: 200% 100%;
        animation: exec-shimmer 3s infinite;
    }
    #executiveMap { height: 380px; border-radius: 1.5rem; z-index: 1; }
    .leaflet-container { background: #0a0c12 !important; font-family: 'Outfit', sans-serif; }
    .leaflet-popup-content-wrapper { background: #18181b; color: #fafafa; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
    .leaflet-popup-tip { background: #18181b; }
    .chart-container { position: relative; height: 280px; }
    .chart-container-sm { position: relative; height: 220px; }
</style>
@endpush

@section('content')
@php
    $k = $data['kpis'];
    $ai = $data['ai'];
    $ops = $data['operations'] ?? [];
@endphp

<div class="animate-fade-in space-y-8 pb-12">
    {{-- Header --}}
    <div class="exec-animate flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <div class="px-3 py-1.5 rounded-xl bg-indigo-600/10 border border-indigo-500/20 flex items-center gap-2 exec-ai-pulse">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                    <span class="text-indigo-400 text-[9px] font-black uppercase tracking-[0.25em]">NexBot Executive Intelligence</span>
                </div>
                <span class="text-zinc-600 text-[10px] font-bold tracking-tight">• Atualizado: {{ $data['last_updated'] }}</span>
                <button id="refreshMetrics" class="px-3 py-1 rounded-lg bg-zinc-900 border border-white/5 text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:text-indigo-400 hover:border-indigo-500/30 transition-all flex items-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3 h-3"></i> Tempo Real
                </button>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tighter italic uppercase">
                Dashboard <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-violet-400 to-emerald-400">Executivo</span>
            </h1>
            <p class="text-zinc-500 text-sm font-medium mt-2 max-w-2xl">
                Visão consolidada do ecossistema NexShape — métricas, inteligência preditiva e gestão profissional em tempo quasi-real.
            </p>
        </div>
        <div class="glass-card px-6 py-4 rounded-2xl border border-indigo-500/20 max-w-md exec-shimmer-bar">
            <span class="text-[9px] text-indigo-400 font-black uppercase tracking-[0.3em] block mb-1">Resumo Executivo IA</span>
            <p class="text-sm text-zinc-200 font-bold italic leading-snug">{{ $ai['summary'] }}</p>
        </div>
    </div>

    {{-- KPI Cards — Row 1 --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 exec-animate exec-delay-1">
        @php
            $kpiRow1 = [
                ['label' => 'Usuários Totais', 'value' => number_format($k['total_users']), 'icon' => 'users', 'glow' => 'bg-indigo-600/10 group-hover:bg-indigo-600/20', 'iconColor' => 'text-indigo-500', 'sub' => $k['new_users_7d'] . ' novos / 7d'],
                ['label' => 'Ativos', 'value' => number_format($k['active_users']), 'icon' => 'user-check', 'glow' => 'bg-emerald-600/10 group-hover:bg-emerald-600/20', 'iconColor' => 'text-emerald-500', 'sub' => $k['active_users_30d'] . ' engajados / 30d'],
                ['label' => 'Inativos', 'value' => number_format($k['inactive_users']), 'icon' => 'user-x', 'glow' => 'bg-rose-600/10 group-hover:bg-rose-600/20', 'iconColor' => 'text-rose-500', 'sub' => $k['pending_users'] . ' pendentes'],
                ['label' => 'Academias', 'value' => number_format($k['total_academies']), 'icon' => 'building-2', 'glow' => 'bg-violet-600/10 group-hover:bg-violet-600/20', 'iconColor' => 'text-violet-500', 'sub' => $k['active_academies'] . ' ativas'],
                ['label' => 'Clínicas/Unidades', 'value' => number_format($k['total_clinics']), 'icon' => 'hospital', 'glow' => 'bg-blue-600/10 group-hover:bg-blue-600/20', 'iconColor' => 'text-blue-500', 'sub' => 'Cadastradas'],
                ['label' => 'Frequência de Uso', 'value' => $k['usage_frequency_pct'] . '%', 'icon' => 'activity', 'glow' => 'bg-amber-600/10 group-hover:bg-amber-600/20', 'iconColor' => 'text-amber-500', 'sub' => 'Atividade 30 dias'],
            ];
        @endphp
        @foreach($kpiRow1 as $card)
        <div class="exec-kpi glass-card p-5 rounded-3xl relative overflow-hidden group border border-white/5">
            <div class="absolute -right-6 -top-6 w-20 h-20 {{ $card['glow'] }} rounded-full blur-2xl transition-all"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $card['label'] }}</span>
                <i data-lucide="{{ $card['icon'] }}" class="w-4 h-4 {{ $card['iconColor'] }}"></i>
            </div>
            <div class="text-2xl font-black text-white tracking-tight italic">{{ $card['value'] }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase tracking-widest">{{ $card['sub'] }}</div>
        </div>
        @endforeach
    </div>

    @if(!empty($ops))
    {{-- Saúde operacional --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 exec-animate exec-delay-2">
        @php
            $opsCards = [
                ['label' => 'Saúde Sistema', 'value' => strtoupper($ops['status'] ?? '—'), 'icon' => 'heart-pulse', 'color' => ($ops['status'] ?? '') === 'healthy' ? 'text-emerald-400' : 'text-amber-400'],
                ['label' => 'Jobs Pendentes', 'value' => number_format($ops['pending_jobs'] ?? 0), 'icon' => 'layers', 'color' => 'text-indigo-400'],
                ['label' => 'Jobs Falhados', 'value' => number_format($ops['failed_jobs'] ?? 0), 'icon' => 'alert-triangle', 'color' => 'text-rose-400'],
                ['label' => 'Erros 24h', 'value' => number_format($ops['errors_24h'] ?? 0), 'icon' => 'bug', 'color' => 'text-orange-400'],
                ['label' => 'Disco Usado', 'value' => isset($ops['disk_used_percent']) ? $ops['disk_used_percent'].'%' : '—', 'icon' => 'hard-drive', 'color' => 'text-violet-400'],
            ];
        @endphp
        @foreach($opsCards as $card)
        <div class="exec-kpi glass-card p-5 rounded-3xl border border-white/5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $card['label'] }}</span>
                <i data-lucide="{{ $card['icon'] }}" class="w-4 h-4 {{ $card['color'] }}"></i>
            </div>
            <div class="text-xl font-black text-white italic">{{ $card['value'] }}</div>
            <a href="{{ route('admin.operations.index') }}" class="text-[9px] text-zinc-600 hover:text-indigo-400 uppercase tracking-widest mt-2 inline-block">Ver operações →</a>
        </div>
        @endforeach
    </div>
    @endif

    {{-- KPI Cards — Row 2 Financeiro --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 exec-animate exec-delay-2">
        @php
            $kpiRow2 = [
                ['label' => 'Faturamento Mensal', 'value' => 'R$ ' . number_format($k['monthly_revenue'], 2, ',', '.'), 'icon' => 'wallet', 'trendColor' => 'text-emerald-400', 'trend' => ($k['revenue_growth'] >= 0 ? '↑' : '↓') . ' ' . abs($k['revenue_growth']) . '% vs mês ant.'],
                ['label' => 'Receita Anual', 'value' => 'R$ ' . number_format($k['annual_revenue'], 2, ',', '.'), 'icon' => 'trending-up', 'trendColor' => 'text-emerald-400', 'trend' => now()->year . ' acumulado'],
                ['label' => 'MRR', 'value' => 'R$ ' . number_format($k['mrr'], 2, ',', '.'), 'icon' => 'refresh-cw', 'trendColor' => 'text-indigo-400', 'trend' => 'Recorrente'],
                ['label' => 'LTV Estimado', 'value' => 'R$ ' . number_format($k['estimated_ltv'] ?? 0, 2, ',', '.'), 'icon' => 'gem', 'trendColor' => 'text-emerald-400', 'trend' => 'Por pagante'],
                ['label' => 'CAC Estimado', 'value' => 'R$ ' . number_format($k['estimated_cac'] ?? 0, 2, ',', '.'), 'icon' => 'target', 'trendColor' => 'text-amber-400', 'trend' => 'Comissões / novas subs'],
                ['label' => 'Retenção', 'value' => $k['retention_rate'] . '%', 'icon' => 'heart-pulse', 'trendColor' => 'text-violet-400', 'trend' => 'Engajamento 30d'],
            ];
        @endphp
        @foreach($kpiRow2 as $card)
        <div class="exec-kpi glass-card p-6 rounded-3xl relative overflow-hidden border border-white/5">
            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest block mb-3">{{ $card['label'] }}</span>
            <div class="text-2xl font-black text-white tracking-tight italic">{{ $card['value'] }}</div>
            <div class="mt-2 text-[9px] {{ $card['trendColor'] }} font-bold uppercase tracking-widest flex items-center gap-1">
                <i data-lucide="{{ $card['icon'] }}" class="w-3 h-3"></i> {{ $card['trend'] }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- KPI Cards — Row 3 Comissões --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 exec-animate exec-delay-2">
        @php
            $kpiCommissions = [
                ['label' => 'Comissões Pendentes', 'value' => 'R$ ' . number_format($k['commissions_pending_total'] ?? 0, 2, ',', '.'), 'icon' => 'clock', 'trendColor' => 'text-amber-400', 'trend' => ($k['commissions_pending_count'] ?? 0) . ' registos'],
                ['label' => 'Disponível p/ Saque', 'value' => 'R$ ' . number_format($k['commissions_available_total'] ?? 0, 2, ',', '.'), 'icon' => 'wallet', 'trendColor' => 'text-emerald-400', 'trend' => 'Liberadas'],
                ['label' => 'Pagas no Mês', 'value' => 'R$ ' . number_format($k['commissions_paid_month'] ?? 0, 2, ',', '.'), 'icon' => 'banknote', 'trendColor' => 'text-indigo-400', 'trend' => now()->translatedFormat('F Y')],
                ['label' => 'Geradas no Mês', 'value' => 'R$ ' . number_format($k['commissions_generated_month'] ?? 0, 2, ',', '.'), 'icon' => 'plus-circle', 'trendColor' => 'text-violet-400', 'trend' => 'Novas comissões'],
                ['label' => 'Clawback Mês', 'value' => 'R$ ' . number_format($k['commissions_clawback_month'] ?? 0, 2, ',', '.'), 'icon' => 'undo-2', 'trendColor' => 'text-rose-400', 'trend' => 'Estornos auto'],
                ['label' => 'Canceladas Mês', 'value' => number_format($k['commissions_cancelled_month'] ?? 0), 'icon' => 'x-circle', 'trendColor' => 'text-zinc-400', 'trend' => 'Comissões'],
            ];
        @endphp
        @foreach($kpiCommissions as $card)
        <div class="exec-kpi glass-card p-6 rounded-3xl relative overflow-hidden border border-white/5">
            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest block mb-3">{{ $card['label'] }}</span>
            <div class="text-2xl font-black text-white tracking-tight italic">{{ $card['value'] }}</div>
            <div class="mt-2 text-[9px] {{ $card['trendColor'] }} font-bold uppercase tracking-widest flex items-center gap-1">
                <i data-lucide="{{ $card['icon'] }}" class="w-3 h-3"></i> {{ $card['trend'] }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- KPI Cards — Row 4 Riscos --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 exec-animate exec-delay-2">
        @php
            $kpiRow3 = [
                ['label' => 'Inadimplência', 'value' => $k['delinquency_count'], 'icon' => 'alert-circle', 'border' => 'border-rose-500/10 hover:border-rose-500/30', 'iconColor' => 'text-rose-500'],
                ['label' => 'Cancelamentos (Mês)', 'value' => $k['cancellations_month'], 'icon' => 'user-minus', 'border' => 'border-amber-500/10 hover:border-amber-500/30', 'iconColor' => 'text-amber-500'],
                ['label' => 'Churn Rate', 'value' => $k['churn_rate'] . '%', 'icon' => 'trending-down', 'border' => 'border-rose-500/10 hover:border-rose-500/30', 'iconColor' => 'text-rose-500'],
                ['label' => 'Alunos em Risco', 'value' => $k['at_risk_count'], 'icon' => 'shield-alert', 'border' => 'border-orange-500/10 hover:border-orange-500/30', 'iconColor' => 'text-orange-500'],
            ];
        @endphp
        @foreach($kpiRow3 as $card)
        <div class="exec-kpi glass-card p-5 rounded-3xl {{ $card['border'] }} transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $card['label'] }}</span>
                <i data-lucide="{{ $card['icon'] }}" class="w-4 h-4 {{ $card['iconColor'] }}"></i>
            </div>
            <div class="text-3xl font-black text-white mt-2 italic">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 exec-animate exec-delay-3">
        <div class="glass-card rounded-[2rem] p-6 border border-white/5">
            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter mb-1">Crescimento Mensal</h3>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-4">Novos usuários por mês</p>
            <div class="chart-container"><canvas id="growthChart"></canvas></div>
        </div>
        <div class="glass-card rounded-[2rem] p-6 border border-white/5">
            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter mb-1">Faturamento Mensal</h3>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-4">Receita consolidada (R$)</p>
            <div class="chart-container"><canvas id="revenueChart"></canvas></div>
        </div>
    </div>

    {{-- Evolution + Active/Inactive --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 exec-animate exec-delay-3">
        <div class="xl:col-span-2 glass-card rounded-[2rem] p-6 border border-white/5">
            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter mb-1">Evolução do Sistema</h3>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-4">Usuários vs receita — tendência dual</p>
            <div class="chart-container"><canvas id="evolutionChart"></canvas></div>
        </div>
        <div class="glass-card rounded-[2rem] p-6 border border-white/5">
            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter mb-1">Ativos vs Inativos</h3>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-4">Distribuição da base</p>
            <div class="chart-container-sm"><canvas id="statusChart"></canvas></div>
        </div>
    </div>

    {{-- Map + Users by City --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 exec-animate exec-delay-4">
        <div class="glass-card rounded-[2rem] p-6 border border-white/5 overflow-hidden">
            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter mb-1">Mapa Interativo</h3>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-4">Usuários por região (Brasil)</p>
            <div id="executiveMap" class="border border-white/5"></div>
            @if(count($data['map_markers']) === 0)
            <p class="text-[10px] text-zinc-600 mt-3 italic">Cadastre cidades nos perfis para visualização no mapa.</p>
            @endif
        </div>
        <div class="glass-card rounded-[2rem] p-6 border border-white/5">
            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter mb-1">Usuários por Cidade</h3>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-4">Top 15 localizações</p>
            <div class="chart-container"><canvas id="cityChart"></canvas></div>
        </div>
    </div>

    {{-- Rankings --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 exec-animate exec-delay-4">
        <div class="glass-card rounded-[2rem] p-6 border border-white/5">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-white uppercase italic tracking-tighter">Ranking de Cidades</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Por volume de usuários</p>
                </div>
                <i data-lucide="map-pin" class="w-5 h-5 text-indigo-500"></i>
            </div>
            <div class="space-y-3">
                @forelse($data['city_ranking'] as $i => $city)
                <div class="flex items-center gap-4 p-3 rounded-2xl bg-zinc-950/50 border border-white/5 hover:border-indigo-500/20 transition-all group">
                    <span class="w-8 h-8 rounded-xl bg-indigo-600/20 flex items-center justify-center text-indigo-400 text-xs font-black">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-white truncate">{{ $city->city ?: 'N/D' }}</p>
                    </div>
                    <span class="text-sm font-black text-indigo-400">{{ $city->total }}</span>
                </div>
                @empty
                <p class="text-zinc-600 text-sm italic py-8 text-center">Sem dados de cidade cadastrados.</p>
                @endforelse
            </div>
        </div>
        <div class="glass-card rounded-[2rem] p-6 border border-white/5">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-white uppercase italic tracking-tighter">Ranking de Clínicas</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Por base de alunos</p>
                </div>
                <i data-lucide="trophy" class="w-5 h-5 text-amber-500"></i>
            </div>
            <div class="space-y-3">
                @forelse($data['clinic_ranking'] as $i => $clinic)
                <div class="flex items-center gap-4 p-3 rounded-2xl bg-zinc-950/50 border border-white/5 hover:border-amber-500/20 transition-all">
                    <span class="w-8 h-8 rounded-xl {{ $i < 3 ? 'bg-amber-600/20 text-amber-400' : 'bg-zinc-800 text-zinc-500' }} flex items-center justify-center text-xs font-black">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-white truncate">{{ $clinic->name }}</p>
                        <p class="text-[9px] text-zinc-600 font-bold uppercase">{{ $clinic->city ?? '—' }} • {{ $clinic->is_active ? 'Ativa' : 'Inativa' }}</p>
                    </div>
                    <span class="text-sm font-black text-emerald-400">{{ $clinic->users_count }} alunos</span>
                </div>
                @empty
                <p class="text-zinc-600 text-sm italic py-8 text-center">Nenhuma clínica cadastrada.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- AI Intelligence Panel --}}
    <div class="glass-card rounded-[2.5rem] p-8 border border-indigo-500/10 shadow-2xl relative overflow-hidden exec-animate exec-delay-4">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-600/5 rounded-full blur-3xl"></div>
        <div class="flex items-center gap-4 mb-8 relative z-10">
            <div class="w-14 h-14 bg-indigo-600/20 rounded-2xl flex items-center justify-center border border-indigo-500/30 exec-ai-pulse">
                <i data-lucide="brain-circuit" class="w-7 h-7 text-indigo-400"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter">Centro de Inteligência Administrativa</h3>
                <p class="text-[9px] text-indigo-400 font-black uppercase tracking-[0.3em]">Análise automática • Previsões • Soluções</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 relative z-10">
            @php
                $aiPanels = [
                    ['title' => 'Pontos Positivos', 'items' => $ai['positives'], 'icon' => 'check-circle', 'dot' => 'bg-emerald-500', 'border' => 'hover:border-emerald-500/20', 'iconColor' => 'text-emerald-500'],
                    ['title' => 'Pontos Negativos', 'items' => $ai['negatives'], 'icon' => 'x-circle', 'dot' => 'bg-rose-500', 'border' => 'hover:border-rose-500/20', 'iconColor' => 'text-rose-500'],
                    ['title' => 'Riscos Detectados', 'items' => $ai['risks'], 'icon' => 'alert-triangle', 'dot' => 'bg-amber-500', 'border' => 'hover:border-amber-500/20', 'iconColor' => 'text-amber-500'],
                    ['title' => 'Previsão de Cancelamentos', 'items' => $ai['predictions'], 'icon' => 'scan-eye', 'dot' => 'bg-violet-500', 'border' => 'hover:border-violet-500/20', 'iconColor' => 'text-violet-500'],
                    ['title' => 'Soluções Automáticas', 'items' => $ai['solutions'], 'icon' => 'zap', 'dot' => 'bg-indigo-500', 'border' => 'hover:border-indigo-500/20', 'iconColor' => 'text-indigo-500'],
                    ['title' => 'Insights Administrativos', 'items' => $ai['insights'], 'icon' => 'lightbulb', 'dot' => 'bg-blue-500', 'border' => 'hover:border-blue-500/20', 'iconColor' => 'text-blue-500'],
                ];
            @endphp
            @foreach($aiPanels as $panel)
            <div class="bg-zinc-950/60 rounded-2xl p-5 border border-white/5 {{ $panel['border'] }} transition-all">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="{{ $panel['icon'] }}" class="w-4 h-4 {{ $panel['iconColor'] }}"></i>
                    <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $panel['title'] }}</span>
                </div>
                <ul class="space-y-2">
                    @foreach($panel['items'] as $item)
                    <li class="flex items-start gap-2 text-[11px] text-zinc-400 font-medium leading-relaxed">
                        <span class="w-1 h-1 rounded-full {{ $panel['dot'] }} mt-2 shrink-0"></span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>

    {{-- At Risk Students --}}
    <div class="glass-card rounded-[2.5rem] p-8 border border-rose-500/10 exec-animate exec-delay-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Alunos em Risco</h3>
                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Predição de evasão — ação preventiva</p>
            </div>
            <a href="{{ route('admin.ai-intelligence.dashboard') }}" class="px-4 py-2 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-indigo-500/30">
                IA de Retenção →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="py-3 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Aluno</th>
                        <th class="py-3 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Risco</th>
                        <th class="py-3 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Health Score</th>
                        <th class="py-3 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Última Atividade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($data['at_risk_students'] as $student)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3 px-4">
                            <p class="text-sm font-black text-white">{{ $student->name }}</p>
                            <p class="text-[9px] text-zinc-600">{{ $student->email }}</p>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $riskClasses = match($student->churn_risk) {
                                    'High' => 'bg-rose-500/10 text-rose-400',
                                    'Medium' => 'bg-amber-500/10 text-amber-400',
                                    default => 'bg-zinc-500/10 text-zinc-400',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-lg {{ $riskClasses }} text-[9px] font-black uppercase">{{ $student->churn_risk ?? '—' }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm font-black text-white">{{ $student->health_score ?? '—' }}</td>
                        <td class="py-3 px-4 text-[10px] text-zinc-500">{{ $student->last_activity_at ? \Carbon\Carbon::parse($student->last_activity_at)->diffForHumans() : 'Sem registro' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-zinc-600 italic">Nenhum aluno em risco identificado no momento.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#71717a', font: { family: 'Outfit', size: 10, weight: '700' } } } },
        scales: {
            x: { ticks: { color: '#52525b', font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
            y: { ticks: { color: '#52525b', font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.03)' } }
        }
    };

    const growthData = @json($data['monthly_growth']);
    const revenueData = @json($data['monthly_revenue']);
    const evolutionData = @json($data['system_evolution']);
    const cityData = @json($data['users_by_city']->take(10)->values());
    const statusData = {
        active: {{ $k['active_users'] }},
        inactive: {{ $k['inactive_users'] }},
        pending: {{ $k['pending_users'] }}
    };
    const mapMarkers = @json($data['map_markers']);

    // Growth Chart
    if (document.getElementById('growthChart') && growthData.length) {
        new Chart(document.getElementById('growthChart'), {
            type: 'bar',
            data: {
                labels: growthData.map(d => d.label),
                datasets: [{
                    label: 'Novos Usuários',
                    data: growthData.map(d => d.total),
                    backgroundColor: 'rgba(99, 102, 241, 0.6)',
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } } }
        });
    }

    // Revenue Chart
    if (document.getElementById('revenueChart') && revenueData.length) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.label),
                datasets: [{
                    label: 'Receita (R$)',
                    data: revenueData.map(d => d.total),
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } } }
        });
    }

    // Evolution Chart
    if (document.getElementById('evolutionChart') && evolutionData.length) {
        new Chart(document.getElementById('evolutionChart'), {
            type: 'line',
            data: {
                labels: evolutionData.map(d => d.label),
                datasets: [
                    {
                        label: 'Usuários',
                        data: evolutionData.map(d => d.users),
                        borderColor: '#6366f1',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Receita (R$)',
                        data: evolutionData.map(d => d.revenue),
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                ...chartDefaults,
                scales: {
                    x: chartDefaults.scales.x,
                    y: { type: 'linear', position: 'left', ...chartDefaults.scales.y },
                    y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#52525b' } }
                }
            }
        });
    }

    // Status Doughnut
    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Ativos', 'Inativos', 'Pendentes'],
                datasets: [{
                    data: [statusData.active, statusData.inactive, statusData.pending],
                    backgroundColor: ['#10b981', '#f43f5e', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { position: 'bottom', labels: { color: '#71717a', padding: 16, font: { size: 10, weight: '700' } } } }
            }
        });
    }

    // City Bar Chart
    if (document.getElementById('cityChart') && cityData.length) {
        new Chart(document.getElementById('cityChart'), {
            type: 'bar',
            data: {
                labels: cityData.map(d => d.city || 'N/D'),
                datasets: [{
                    label: 'Usuários',
                    data: cityData.map(d => d.total),
                    backgroundColor: 'rgba(139, 92, 246, 0.5)',
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                ...chartDefaults,
                plugins: { ...chartDefaults.plugins, legend: { display: false } }
            }
        });
    }

    // Leaflet Map
    if (typeof L !== 'undefined' && document.getElementById('executiveMap')) {
        const map = L.map('executiveMap', { scrollWheelZoom: false }).setView([-14.2350, -51.9253], 4);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const maxUsers = mapMarkers.length ? Math.max(...mapMarkers.map(m => m.users)) : 1;
        mapMarkers.forEach(marker => {
            const radius = 8 + (marker.users / maxUsers) * 24;
            L.circleMarker([marker.lat, marker.lng], {
                radius: radius,
                fillColor: '#6366f1',
                color: '#818cf8',
                weight: 2,
                opacity: 0.9,
                fillOpacity: 0.6
            }).addTo(map).bindPopup(
                `<strong>${marker.city}</strong><br>${marker.users} usuário(s)`
            );
        });
    }

    // Refresh metrics
    document.getElementById('refreshMetrics')?.addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.classList.add('opacity-50');
        fetch('{{ route("admin.executive.metrics") }}')
            .then(r => r.json())
            .then(() => window.location.reload())
            .catch(() => { btn.disabled = false; btn.classList.remove('opacity-50'); });
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endpush
@endsection
