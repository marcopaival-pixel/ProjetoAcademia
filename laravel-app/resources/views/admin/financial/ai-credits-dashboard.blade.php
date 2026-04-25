@extends('layouts.admin')

@section('title', 'AI Credits Dashboard')

@section('content')
<div class="animate-fade-in space-y-6">
    
    <!-- Header -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-purple-600/10 border border-purple-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                    <span class="text-purple-400 text-[9px] font-black uppercase tracking-widest">Monitoramento Operacional de IA</span>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter">
                Consumo de <span class="text-purple-500">Inteligência Artificial</span>
            </h1>
        </div>

        <!-- Filtros -->
        <div class="flex flex-wrap gap-3">
            <div class="flex bg-[#11141b]/80 p-1 rounded-xl border border-white/5">
                <a href="?period=today" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == 'today' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Hoje</a>
                <a href="?period=7" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == '7' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">7 dias</a>
                <a href="?period=30" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == '30' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">30 dias</a>
                <a href="?period=all" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == 'all' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Total</a>
            </div>
            
            <a href="{{ route('admin.financial.ai-credits.report') }}" class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest text-white hover:bg-white/10 transition-all">
                <i class="fas fa-list-ul mr-2"></i> Relatório por Usuário
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <!-- Total Vendidos -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/10 rounded-full blur-2xl group-hover:bg-blue-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Créditos Vendidos</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">{{ number_format($metrics['total_sold'], 0, ',', '.') }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase">No período selecionado</div>
        </div>

        <!-- Total Consumidos -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:bg-rose-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Créditos Consumidos</span>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">{{ number_format($metrics['total_consumed'], 0, ',', '.') }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase">No período selecionado</div>
        </div>

        <!-- Disponíveis -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Saldo em Circulação</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">{{ number_format($metrics['available'], 0, ',', '.') }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase">Saldo total de usuários</div>
        </div>

        <!-- Receita -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-600/10 rounded-full blur-2xl group-hover:bg-amber-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Receita Gerada</span>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($metrics['revenue'], 2, ',', '.') }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase">Vendas de pacotes</div>
        </div>

        <!-- Usuários Ativos -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-600/10 rounded-full blur-2xl group-hover:bg-purple-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Usuários com Saldo</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">{{ $metrics['active_users_ia'] }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase">Possuem créditos hoje</div>
        </div>

        <!-- Consumo Médio -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-600/10 rounded-full blur-2xl group-hover:bg-indigo-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Consumo Médio</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">{{ number_format($metrics['avg_consumption'], 1, ',', '.') }}</div>
            <div class="mt-2 text-[9px] text-zinc-600 font-bold uppercase">Créditos / Usuário</div>
        </div>
    </div>

    <!-- Filtros Avançados -->
    <div class="glass-card p-6 rounded-2xl">
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <input type="hidden" name="period" value="{{ $period }}">
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Tipo de Usuário</label>
                <select name="user_type" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
                    <option value="">Todos</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $userType == $role->name ? 'selected' : '' }}>{{ $role->label ?? ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Status</label>
                <select name="status" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
                    <option value="">Todos</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="blocked" {{ $status == 'blocked' ? 'selected' : '' }}>Bloqueado</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Período Customizado</label>
                <div class="flex gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-1/2 bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-1/2 bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
                </div>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-purple-500 transition-all shadow-xl shadow-purple-600/20">
                Aplicar Filtros
            </button>
        </form>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Consumo por Dia -->
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-base font-black text-white tracking-tight mb-6 flex items-center gap-2">
                <i class="fas fa-chart-line text-rose-500"></i> Consumo de Créditos por Dia
            </h3>
            <div class="h-[300px]">
                <canvas id="consumptionChart"></canvas>
            </div>
        </div>

        <!-- Receita por Venda -->
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-base font-black text-white tracking-tight mb-6 flex items-center gap-2">
                <i class="fas fa-hand-holding-usd text-emerald-500"></i> Receita por Venda de Créditos
            </h3>
            <div class="h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Consumo por Tipo -->
        <div class="glass-card p-6 rounded-2xl lg:col-span-2">
            <h3 class="text-base font-black text-white tracking-tight mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-500"></i> Consumo por Tipo de Usuário
            </h3>
            <div class="h-[300px]">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="glass-card rounded-2xl overflow-hidden mt-6">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-base font-black text-white tracking-tight flex items-center gap-2">
                <i class="fas fa-history text-zinc-500"></i> Trilha de Auditoria Financeira (IA)
            </h3>
            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Últimas 10 Operações</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.01]">
                        <th class="px-6 py-4 text-[9px] font-black text-zinc-500 uppercase tracking-widest">Data/Hora</th>
                        <th class="px-6 py-4 text-[9px] font-black text-zinc-500 uppercase tracking-widest">Usuário</th>
                        <th class="px-6 py-4 text-[9px] font-black text-zinc-500 uppercase tracking-widest">Ação</th>
                        <th class="px-6 py-4 text-[9px] font-black text-zinc-500 uppercase tracking-widest">Valor/Qtd</th>
                        <th class="px-6 py-4 text-[9px] font-black text-zinc-500 uppercase tracking-widest">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="px-6 py-4 text-[10px] text-zinc-400 font-bold uppercase">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] font-black text-white uppercase">{{ $log->user->name ?? 'Sistema' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded bg-zinc-900 border border-white/5 text-[8px] font-black uppercase text-zinc-500">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[10px] font-black {{ $log->amount > 0 ? 'text-emerald-500' : 'text-zinc-400' }}">
                            {{ $log->amount ? 'R$ ' . number_format($log->amount, 2, ',', '.') : ($log->payload['credits'] ?? 'N/D') }}
                        </td>
                        <td class="px-6 py-4 text-[9px] text-zinc-600 font-mono">
                            {{ $log->ip_address ?? 'N/D' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-zinc-600 italic text-xs">Nenhuma operação registrada recentemente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0d0f14',
                padding: 12,
                cornerRadius: 12,
                titleFont: { size: 10, weight: 'bold' },
                bodyFont: { size: 12 }
            }
        },
        scales: {
            y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#52525b', font: { size: 10 } } },
            x: { grid: { display: false }, ticks: { color: '#52525b', font: { size: 10 } } }
        }
    };

    // Consumption Chart
    const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
    const consumptionData = @json($charts['consumption_by_day']);
    new Chart(consumptionCtx, {
        type: 'line',
        data: {
            labels: consumptionData.map(d => d.date),
            datasets: [{
                label: 'Créditos',
                data: consumptionData.map(d => d.total),
                borderColor: '#f43f5e',
                backgroundColor: 'rgba(244, 63, 94, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: commonOptions
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($charts['revenue_by_day']);
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueData.map(d => d.date),
            datasets: [{
                label: 'Receita (R$)',
                data: revenueData.map(d => d.total),
                backgroundColor: '#10b981',
                borderRadius: 8
            }]
        },
        options: commonOptions
    });

    // Type Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeData = @json($charts['consumption_by_type']);
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: typeData.map(d => d.type),
            datasets: [{
                data: typeData.map(d => d.total),
                backgroundColor: ['#a855f7', '#3b82f6', '#f59e0b', '#ef4444', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: { display: true, position: 'right', labels: { color: '#a1a1aa', font: { size: 10, weight: 'bold' } } }
            },
            scales: { x: { display: false }, y: { display: false } }
        }
    });
});
</script>
@endpush
@endsection
