@extends('layouts.admin')

@section('title', 'Dashboard Executivo')

@section('content')
<div class="animate-fade-in space-y-6">
    
    <!-- Saudação NexShape Pattern -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-blue-600/10 border border-blue-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    <span class="text-blue-400 text-[9px] font-black uppercase tracking-widest">Controlo Administrativo Ativo</span>
                </div>
                <span class="text-zinc-600 text-[10px] font-bold tracking-tight">• {{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter">
                @if(auth()->user()->academy_company_id && auth()->user()->academyCompany)
                    <span class="block text-zinc-600 text-sm font-black uppercase tracking-[0.3em] mb-2">{{ auth()->user()->academyCompany->name }}</span>
                @endif
                Gestão, <span class="text-blue-500">{{ explode(' ', auth()->user()->name)[0] }}!</span>
            </h1>
        </div>

        <!-- Barra de Progresso Estilo Portal -->
        <div class="w-full max-w-md pb-2">
            <div class="flex justify-between items-end mb-2">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em]">Setup da Infraestrutura</span>
                <span class="text-[10px] text-blue-500 font-black tracking-widest">94%</span>
            </div>
            <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                <div class="h-full bg-blue-600 rounded-full shadow-[0_0_15px_rgba(59,130,246,0.5)]" style="width: 94%"></div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas (Estilo Portal) -->
    <div class="flex flex-wrap gap-4 mb-12">
        <a href="{{ route('admin.users.create') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-[1.25rem] font-black transition-all shadow-2xl shadow-blue-500/20 group">
            <i class="fas fa-user-plus text-sm group-hover:scale-110 transition-transform"></i>
            <span class="text-sm">Novo Usuário</span>
        </a>
        <a href="{{ route('admin.plans.index') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-[#11141b]/80 hover:bg-[#1a1e28] text-white rounded-[1.25rem] font-black transition-all border border-white/5 group">
            <i class="fas fa-plus-circle text-sm text-blue-500 group-hover:rotate-90 transition-transform"></i>
            <span class="text-sm">Nova Assinatura</span>
        </a>
        <a href="{{ route('admin.users.create') }}?role=professional" class="flex items-center justify-center gap-3 px-8 py-4 bg-[#11141b]/80 hover:bg-[#1a1e28] text-white rounded-[1.25rem] font-black transition-all border border-white/5 group">
            <i class="fas fa-user-tie text-sm text-amber-500 group-hover:-translate-y-1 transition-transform"></i>
            <span class="text-sm">Registrar Profissional</span>
        </a>
        <a href="{{ route('admin.supplements.index') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-[#11141b]/80 hover:bg-[#1a1e28] text-white rounded-[1.25rem] font-black transition-all border border-white/5 group">
            <i class="fas fa-pills text-sm text-emerald-500 group-hover:scale-110 transition-transform"></i>
            <span class="text-sm">Cadastrar Suplemento</span>
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
        <a href="{{ route('admin.financial.dashboard') }}" class="glass-card p-5 rounded-2xl relative overflow-hidden group hover:border-emerald-500/30 transition-all block">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/10 rounded-full blur-2xl group-hover:bg-blue-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Faturamento Total</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</div>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="text-[10px] {{ $metrics['revenue_growth'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-bold">
                    {{ $metrics['revenue_growth'] >= 0 ? '↑' : '↓' }} {{ number_format(abs($metrics['revenue_growth']), 1) }}%
                </span>
                <span class="text-[10px] text-zinc-600 font-bold uppercase">vs mês anterior</span>
            </div>
        </a>

        <!-- MRR -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Receita Mensal (MRR)</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="fas fa-sync"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($metrics['mrr'], 2, ',', '.') }}</div>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="text-[10px] text-emerald-400 font-bold">↑ {{ number_format(max(0, $metrics['revenue_growth']), 1) }}%</span>
                <span class="text-[10px] text-zinc-600 font-bold uppercase">Projeção Baseada em Ativos</span>
            </div>
        </div>

        <!-- Usuários Ativos -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-600/10 rounded-full blur-2xl group-hover:bg-purple-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Usuários Ativos</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">{{ $metrics['total_premium'] }}</div>
            <div class="flex items-center gap-1.5 mt-3">
                <span class="text-[10px] text-blue-400 font-bold">{{ $overview['active_users_30d'] }} ativos / 30d</span>
                <span class="text-[10px] text-zinc-600 font-bold uppercase">Engajamento crescente</span>
            </div>
        </div>
    </div>

    <!-- KPIs Secundários -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-4 rounded-2xl">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Crescimento Receita</span>
            <div class="text-lg font-black text-white {{ $metrics['revenue_growth'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                {{ $metrics['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenue_growth'], 1) }}%
            </div>
        </div>
        <div class="glass-card p-4 rounded-2xl">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Risco de Churn</span>
            <div class="text-lg font-black text-amber-500">{{ $metrics['expiring_soon'] }} expirações</div>
        </div>
        <div class="glass-card p-4 rounded-2xl">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Conversão Premium</span>
            <div class="text-lg font-black text-purple-400">{{ round(($metrics['total_premium'] / max(1, $metrics['total_users'])) * 100, 1) }}%</div>
        </div>
        <div class="glass-card p-4 rounded-2xl">
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Total de Usuários</span>
            <div class="text-lg font-black text-blue-400">{{ $metrics['total_users'] }} cadastrados</div>
        </div>
    </div>

    <!-- Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Gráfico de Receita -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-black text-white tracking-tight">Evolução de Receita</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Últimos 6 Meses (BRL)</p>
                </div>
            </div>
            <div class="h-[280px] flex items-center justify-center">
                @if(count($metrics['monthly_revenue']) > 0)
                    <canvas id="revenueChart"></canvas>
                @else
                    <div class="text-center py-10">
                        <div class="w-12 h-12 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                            <i class="fas fa-chart-line text-zinc-700"></i>
                        </div>
                        <p class="text-sm text-zinc-500 font-medium italic">Nenhum dado disponível ainda.<br>Comece cadastrando usuários ou assinaturas.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Resumo do Dia -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-base font-black text-white tracking-tight mb-6">Resumo do Dia</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-white/[0.02] border border-white/5 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 text-xs">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <span class="text-xs font-bold text-zinc-300">Novos Usuários</span>
                    </div>
                    <span class="text-sm font-black text-white">{{ $metrics['daily_summary']['new_users'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-white/[0.02] border border-white/5 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xs">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="text-xs font-bold text-zinc-300">Pagamentos Recebidos</span>
                    </div>
                    <span class="text-sm font-black text-white">{{ $metrics['daily_summary']['payments'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-white/[0.02] border border-white/5 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 text-xs">
                            <i class="fas fa-history"></i>
                        </div>
                        <span class="text-xs font-bold text-zinc-300">Assinaturas Vencendo</span>
                    </div>
                    <span class="text-sm font-black text-white">{{ $metrics['daily_summary']['expiring'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-white/[0.02] border border-white/5 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-500 text-xs relative">
                            <i class="fas fa-comment-alt"></i>
                            @if(($metrics['daily_summary']['messages'] ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 w-2 h-2 bg-rose-500 rounded-full"></span>
                            @endif
                        </div>
                        <span class="text-xs font-bold text-zinc-300">Mensagens Pendentes</span>
                    </div>
                    <span class="text-sm font-black text-white">{{ $metrics['daily_summary']['messages'] ?? 0 }}</span>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-white/5">
                <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-zinc-600">
                    <span>Status Monitoramento</span>
                    <span class="text-emerald-500 flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Sistema OK</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Lists Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-12">
        <!-- Recent Logs -->
        <div class="glass-card rounded-2xl overflow-hidden border border-white/5">
            <div class="p-5 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-base font-black text-white tracking-tight">Logs de Atividade</h3>
                <a href="{{ route('admin.system-errors') }}" class="text-[9px] font-black text-blue-500 uppercase tracking-widest hover:text-white transition-colors">Ver Todos</a>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($metrics['recent_logs'] as $log)
                <div class="p-4 flex items-center gap-4 hover:bg-white/[0.01] transition-colors">
                    <div class="w-9 h-9 rounded-full bg-zinc-950 flex items-center justify-center border border-white/10 text-[10px] text-zinc-500">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-zinc-200 truncate">{{ $log->action }}</p>
                        <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">{{ $log->user?->name ?? 'Sistema' }}</p>
                    </div>
                    <span class="text-[9px] text-zinc-600 font-black uppercase shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="py-12 text-center text-zinc-600 italic text-xs">Sem atividade relevante no momento.</div>
                @endforelse
            </div>
        </div>

        <!-- Expiring Users (Churn Risk) -->
        <div class="glass-card rounded-2xl overflow-hidden border border-red-500/10">
            <div class="p-5 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-base font-black text-white tracking-tight">Risco de Churn (15d)</h3>
                <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-500 text-[8px] font-black uppercase">{{ count($metrics['expiring_users']) }} Alertas</span>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($metrics['expiring_users'] as $u)
                <div class="p-4 flex items-center gap-4 hover:bg-white/[0.01] transition-colors">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=18181b&color=a1a1aa" class="w-9 h-9 rounded-full border border-white/10">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-zinc-200 truncate">{{ $u->name }}</p>
                        <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">{{ $u->email }}</p>
                    </div>
                    <div class="px-2.5 py-1 bg-amber-500/5 border border-amber-500/10 text-amber-500 text-[9px] font-black uppercase rounded-lg">
                        {{ \Carbon\Carbon::parse($u->premium_expires_at)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-zinc-600 italic text-xs">Operação estável. Nenhum cancelamento iminente.</div>
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
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.month),
                datasets: [{
                    label: 'Receita Mensal (R$)',
                    data: data.map(d => d.total),
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.45,
                    borderWidth: 4,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#0d0f14',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3b82f6',
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
