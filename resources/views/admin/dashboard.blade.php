@extends('layouts.admin')

@section('title', 'Dashboard Executivo')

@section('content')
<div class="animate-fade-in space-y-6">
    <!-- App Promotion Banner (Marketing) -->
    <x-marketing.promo-banner />
    
    <!-- Saudação NexShape Pattern -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-emerald-600/10 border border-emerald-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-emerald-400 text-[9px] font-black uppercase tracking-widest">Controlo Administrativo Ativo</span>
                </div>
                <span class="text-zinc-600 text-[10px] font-bold tracking-tight">• {{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                @if(auth()->user()->academy_company_id && auth()->user()->academyCompany)
                    <span class="block text-zinc-600 text-sm font-black uppercase tracking-[0.3em] mb-2">{{ auth()->user()->academyCompany->name }}</span>
                @endif
                Gestão, <span class="text-emerald-500">{{ explode(' ', auth()->user()->name)[0] }}!</span>
            </h1>
        </div>

        <!-- Barra de Progresso Estilo Portal -->
        <div class="w-full max-w-md pb-2">
            <div class="flex justify-between items-end mb-2">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em]">Setup da Infraestrutura</span>
                <span class="text-[10px] text-emerald-500 font-black tracking-widest">94%</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                <div class="h-full bg-emerald-600 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]" style="width: 94%"></div>
            </div>
        </div>
    </div>
    
    <!-- IA Insight Alert (Floating Premium Card) -->
    <div class="mb-10 animate-fade-in-up" style="animation-delay: 200ms">
        <div class="bg-gradient-to-r from-emerald-600/10 via-zinc-900/50 to-blue-600/10 backdrop-blur-xl border border-white/5 rounded-[2rem] p-6 flex items-center gap-6 shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>
            <div class="w-14 h-14 bg-zinc-950/80 rounded-2xl flex items-center justify-center border border-emerald-500/20 shadow-inner shrink-0">
                <i data-lucide="brain-circuit" class="w-7 h-7 text-emerald-500 animate-pulse"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.4em] mb-1 block italic">NexBot Executive Intelligence</span>
                <p class="text-zinc-100 text-sm font-bold tracking-tight italic">
                    "{{ $metrics['ai_insight'] }}"
                </p>
            </div>
            <div class="hidden md:block">
                <button class="px-5 py-2.5 bg-zinc-950/50 hover:bg-emerald-500 hover:text-zinc-950 border border-emerald-500/20 rounded-xl text-emerald-500 text-[9px] font-black uppercase tracking-[0.2em] transition-all duration-500">
                    Ver Otimizações
                </button>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas (Estilo Portal) -->
    <div class="flex flex-wrap gap-4 mb-12">
        <a href="{{ route('admin.users.create') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 rounded-[1.25rem] font-black transition-all shadow-2xl shadow-emerald-500/20 group">
            <i data-lucide="user-plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm uppercase tracking-widest">Novo Usuário</span>
        </a>
        <a href="{{ route('admin.plans.index') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-zinc-900/80 hover:bg-zinc-800 text-white rounded-[1.25rem] font-black transition-all border border-white/5 group">
            <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-500 group-hover:rotate-90 transition-transform"></i>
            <span class="text-sm uppercase tracking-widest">Nova Assinatura</span>
        </a>
        <a href="{{ route('admin.users.create') }}?role=professional" class="flex items-center justify-center gap-3 px-8 py-4 bg-zinc-900/80 hover:bg-zinc-800 text-white rounded-[1.25rem] font-black transition-all border border-white/5 group">
            <i data-lucide="shield-check" class="w-4 h-4 text-amber-500 group-hover:-translate-y-1 transition-transform"></i>
            <span class="text-sm uppercase tracking-widest">Registrar Profissional</span>
        </a>
        <a href="{{ route('admin.supplements.index') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-zinc-900/80 hover:bg-zinc-800 text-white rounded-[1.25rem] font-black transition-all border border-white/5 group">
            <i data-lucide="beaker" class="w-4 h-4 text-emerald-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm uppercase tracking-widest">Cadastrar Suplemento</span>
        </a>
        <a href="{{ route('admin.operations.index') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-zinc-900/80 hover:bg-zinc-800 text-white rounded-[1.25rem] font-black transition-all border border-amber-500/20 group">
            <i data-lucide="hammer" class="w-4 h-4 text-amber-500 group-hover:rotate-12 transition-transform"></i>
            <span class="text-sm uppercase tracking-widest">Manutenção</span>
        </a>
        <a href="{{ route('admin.kb.index') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-zinc-900/80 hover:bg-zinc-800 text-white rounded-[1.25rem] font-black transition-all border border-blue-500/20 group">
            <i data-lucide="help-circle" class="w-4 h-4 text-blue-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm uppercase tracking-widest">Help Center</span>
        </a>
        <div class="hidden xl:flex items-center gap-4 ml-auto bg-[#0b0e14] p-2 rounded-2xl border border-white/5">
            <div class="flex flex-col items-end px-3">
                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Status da Operação</span>
                <span class="text-[11px] text-emerald-500 font-black">98.4% Estável</span>
            </div>
            <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 font-black text-xs">
                OK
            </div>
        </div>
    </div>

    <!-- Hierarquia de KPIs (Principais) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Faturamento Total -->
        <a href="{{ route('admin.financial.dashboard') }}" class="glass-card p-6 rounded-3xl relative overflow-hidden group hover:border-emerald-500/30 transition-all block border border-white/5">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Faturamento Total</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight italic uppercase">R$ {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</div>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="text-[10px] {{ $metrics['revenue_growth'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-bold">
                    {{ $metrics['revenue_growth'] >= 0 ? '↑' : '↓' }} {{ number_format(abs($metrics['revenue_growth']), 1) }}%
                </span>
                <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">vs mês anterior</span>
            </div>
        </a>

        <!-- MRR -->
        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group border border-white/5">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Receita Mensal (MRR)</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight italic uppercase">R$ {{ number_format($metrics['mrr'], 2, ',', '.') }}</div>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="text-[10px] text-emerald-400 font-bold tracking-widest uppercase">↑ {{ number_format(max(0, $metrics['revenue_growth']), 1) }}% Estável</span>
            </div>
        </div>

        <!-- Usuários Ativos -->
        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group border border-white/5">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/10 rounded-full blur-2xl group-hover:bg-blue-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Usuários Ativos</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight italic uppercase">{{ $metrics['total_premium'] }}</div>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="text-[10px] text-blue-400 font-bold tracking-widest uppercase">{{ $overview['active_users_30d'] }} ativos / 30d</span>
            </div>
        </div>
    </div>

    <!-- KPIs Secundários -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-6 rounded-3xl border border-white/5">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Crescimento Receita</span>
            <div class="text-xl font-black {{ $metrics['revenue_growth'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                {{ $metrics['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenue_growth'], 1) }}%
            </div>
        </div>
        <div class="glass-card p-6 rounded-3xl border border-white/5">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Risco de Churn</span>
            <div class="text-xl font-black text-amber-500">{{ $metrics['expiring_soon'] }} expirações</div>
        </div>
        <div class="glass-card p-6 rounded-3xl border border-white/5">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Conversão Premium</span>
            <div class="text-xl font-black text-emerald-400">{{ round(($metrics['total_premium'] / max(1, $metrics['total_users'])) * 100, 1) }}%</div>
        </div>
        <div class="glass-card p-6 rounded-3xl border border-white/5">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Total de Usuários</span>
            <div class="text-xl font-black text-blue-400">{{ $metrics['total_users'] }} cadastrados</div>
        </div>
    </div>

    <!-- Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Gráfico de Receita -->
        <div class="lg:col-span-2 glass-card rounded-[2.5rem] p-8 border border-white/5">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-white tracking-tighter uppercase italic">Evolução de Receita</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.3em] mt-1 italic">Inteligência Financeira NexShape</p>
                </div>
            </div>
            <div class="h-[300px] flex items-center justify-center">
                @if(count($metrics['monthly_revenue']) > 0)
                    <canvas id="revenueChart"></canvas>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-white/5 shadow-xl">
                            <i data-lucide="bar-chart-3" class="w-8 h-8 text-zinc-800"></i>
                        </div>
                        <p class="text-xs text-zinc-600 font-black uppercase tracking-widest italic">Nenhum dado processado</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Resumo do Dia -->
        <div class="glass-card rounded-[2.5rem] p-8 border border-white/5">
            <h3 class="text-lg font-black text-white tracking-tighter uppercase italic mb-8">Fluxo de Hoje</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-zinc-950/50 border border-white/5 rounded-2xl group hover:border-emerald-500/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-colors">Novos Usuários</span>
                    </div>
                    <span class="text-base font-black text-white">{{ $metrics['daily_summary']['new_users'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-zinc-950/50 border border-white/5 rounded-2xl group hover:border-emerald-500/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all">
                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-colors">Recebimentos</span>
                    </div>
                    <span class="text-base font-black text-white">{{ $metrics['daily_summary']['payments'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-zinc-950/50 border border-white/5 rounded-2xl group hover:border-amber-500/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-zinc-950 transition-all">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-colors">Expirações</span>
                    </div>
                    <span class="text-base font-black text-white">{{ $metrics['daily_summary']['expiring'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-zinc-950/50 border border-white/5 rounded-2xl group hover:border-rose-500/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 group-hover:bg-rose-500 group-hover:text-zinc-950 transition-all relative">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-colors">Chamados</span>
                    </div>
                    <span class="text-base font-black text-white">{{ $metrics['daily_summary']['messages'] ?? 0 }}</span>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-zinc-900">
                <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-[0.2em] text-zinc-700">
                    <span>Sistema Operacional</span>
                    <span class="text-emerald-500 flex items-center gap-2"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Estável</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Lists Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-12">
        <!-- Recent Logs -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl">
            <div class="p-8 border-b border-zinc-900 flex items-center justify-between">
                <h3 class="text-lg font-black text-white tracking-tighter uppercase italic">Inteligência Operacional</h3>
                <a href="{{ route('admin.system-errors') }}" class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] hover:text-white transition-colors">Relatório Completo</a>
            </div>
            <div class="divide-y divide-zinc-900">
                @forelse($metrics['recent_logs'] as $log)
                <div class="p-6 flex items-center gap-6 hover:bg-emerald-500/[0.02] transition-all group">
                    <div class="w-11 h-11 rounded-2xl bg-zinc-950 flex items-center justify-center border border-white/5 text-zinc-700 group-hover:text-emerald-500 group-hover:border-emerald-500/20 transition-all shadow-inner">
                        <i data-lucide="fingerprint" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-black text-white uppercase tracking-widest truncate">{{ $log->action }}</p>
                        <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-[0.2em] mt-1 italic">{{ $log->user?->name ?? 'Core System' }}</p>
                    </div>
                    <span class="text-[9px] text-zinc-800 font-black uppercase tracking-widest shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="py-16 text-center text-zinc-700 font-black uppercase tracking-[0.3em] text-[10px] italic">Silêncio Operacional</div>
                @endforelse
            </div>
        </div>

        <!-- Expiring Users (Churn Risk) -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden border border-rose-500/10 shadow-2xl">
            <div class="p-8 border-b border-zinc-900 flex items-center justify-between">
                <h3 class="text-lg font-black text-white tracking-tighter uppercase italic">Risco de Evasão</h3>
                <span class="px-3 py-1 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-500 text-[9px] font-black uppercase tracking-widest">{{ count($metrics['expiring_users']) }} ALERTAS</span>
            </div>
            <div class="divide-y divide-zinc-900">
                @forelse($metrics['expiring_users'] as $u)
                <div class="p-6 flex items-center gap-6 hover:bg-rose-500/[0.02] transition-all group">
                    <div class="w-11 h-11 rounded-2xl overflow-hidden border border-zinc-900 group-hover:border-rose-500/30 transition-all shadow-2xl">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=09090b&color=f43f5e&bold=true" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-black text-white uppercase tracking-widest truncate">{{ $u->name }}</p>
                        <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-[0.2em] mt-1 italic">{{ $u->email }}</p>
                    </div>
                    <div class="px-4 py-1.5 bg-rose-500/5 border border-rose-500/10 text-rose-500 text-[9px] font-black uppercase tracking-widest rounded-xl">
                        {{ \Carbon\Carbon::parse($u->premium_expires_at)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="py-16 text-center text-zinc-700 font-black uppercase tracking-[0.3em] text-[10px] italic">Retenção Máxima Ativa</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(count($metrics['monthly_revenue']) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const data = @json($metrics['monthly_revenue']);
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.month),
                datasets: [{
                    label: 'Receita Mensal (R$)',
                    data: data.map(d => d.total),
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0d0f14',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 16,
                        cornerRadius: 16,
                        displayColors: false,
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.03)', drawBorder: false },
                        ticks: { color: '#52525b', font: { size: 10, weight: 'bold' }, padding: 8 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#52525b', font: { size: 10, weight: 'bold' }, padding: 8 }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
@endsection
