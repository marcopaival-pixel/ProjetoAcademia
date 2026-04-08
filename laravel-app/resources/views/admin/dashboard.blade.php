@extends('layouts.admin')

@section('title', 'Painel de Controlo Central')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Stats Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        @php
            $statBlocks = [
                ['label' => 'Faturamento Total', 'val' => 'R$ '.number_format($metrics['total_revenue'], 2, ',', '.'), 'sub' => 'Acumulado histórico', 'color' => 'blue', 'icon' => 'fas fa-wallet'],
                ['label' => 'MRR Estimado', 'val' => 'R$ '.number_format($metrics['mrr'], 2, ',', '.'), 'sub' => 'Receita recorrente mensal', 'color' => 'emerald', 'icon' => 'fas fa-sync'],
                ['label' => 'Receita/Mês', 'val' => 'R$ '.number_format($metrics['this_month_revenue'], 2, ',', '.'), 'sub' => ($metrics['revenue_growth'] >= 0 ? '↑ ' : '↓ ').number_format(abs($metrics['revenue_growth']), 1).'% vs anterior', 'color' => $metrics['revenue_growth'] >= 0 ? 'emerald' : 'red', 'icon' => 'fas fa-chart-line'],
                ['label' => 'Risco de Churn', 'val' => $metrics['expiring_soon'], 'sub' => 'Expirações nos próximos 15d', 'color' => 'amber', 'icon' => 'fas fa-exclamation-triangle'],
                ['label' => 'Conversão Premium', 'val' => round(($metrics['total_premium'] / max(1, $metrics['total_users'])) * 100, 1).'%', 'sub' => $metrics['total_premium'].' usuários ativos', 'color' => 'purple', 'icon' => 'fas fa-crown'],
                ['label' => 'Usuários/30d', 'val' => $overview['active_users_30d'], 'sub' => 'Média de engajamento', 'color' => 'blue', 'icon' => 'fas fa-users'],
            ];
        @endphp

        @foreach($statBlocks as $s)
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] hover:bg-zinc-900/60 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $s['label'] }}</span>
                <div class="w-8 h-8 rounded-lg bg-{{ $s['color'] }}-500/10 flex items-center justify-center text-{{ $s['color'] }}-500 text-xs">
                    <i class="{{ $s['icon'] }}"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">{{ $s['val'] }}</div>
            <div class="text-[9px] text-{{ $s['color'] }}-400 font-bold mt-2 uppercase tracking-wide">{{ $s['sub'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Main Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Evolution -->
        <div class="lg:col-span-2 bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-white tracking-tight">Evolução de Receita</h3>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Série histórica mensal (BRL)</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-white/5 rounded-xl text-[10px] text-zinc-400 font-black uppercase tracking-widest hover:bg-white/10 transition-all">Exportar Dados</button>
                </div>
            </div>
            <div class="h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Student Goals Distribution -->
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 overflow-hidden relative">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-600/5 rounded-full blur-3xl"></div>
            
            <h3 class="text-xl font-black text-white tracking-tight mb-8">Objetivos dos Alunos</h3>
            
            <div class="space-y-6">
                @php
                    $goalLabels = ['maintain' => 'Manter Peso', 'lose' => 'Emagrecer', 'gain' => 'Ganhar Massa'];
                    $maxGoal = $metrics['goals']->max('total') ?: 1;
                @endphp
                @foreach($metrics['goals'] as $g)
                <div class="group/bar">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $goalLabels[$g->goal] ?? ucfirst($g->goal) }}</span>
                        <span class="text-lg font-black text-white">{{ $g->total }}</span>
                    </div>
                    <div class="h-2 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                        <div class="h-full bg-blue-600 rounded-full transition-all duration-1000 group-hover/bar:bg-blue-400" style="width: {{ ($g->total / $maxGoal) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if($metrics['goals']->isEmpty())
                    <p class="text-center text-zinc-500 py-20 italic text-sm">Aguardando dados de perfil...</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Secondary Lists Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-xl font-black text-white tracking-tight">Atividade Recente</h3>
                <i class="fas fa-stream text-zinc-700 text-sm"></i>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($metrics['recent_logs'] as $log)
                <div class="p-6 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
                    <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center border border-white/5 text-xs text-zinc-500">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-zinc-200">{{ $log->action }}</p>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">{{ $log->user?->name ?? 'Sistema' }}</p>
                    </div>
                    <span class="text-[10px] text-zinc-600 font-black uppercase">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="py-20 text-center text-zinc-600 italic text-sm">Nenhuma atividade registrada hoje.</div>
                @endforelse
            </div>
        </div>

        <!-- Churn Risks / Expiring Users -->
        <div class="bg-zinc-900/40 border border-red-500/10 rounded-[2.5rem] overflow-hidden">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-xl font-black text-white tracking-tight">Risco de Churn (15d)</h3>
                <span class="px-2 py-1 rounded bg-red-500/10 text-red-500 text-[8px] font-black uppercase">{{ count($metrics['expiring_users']) }} Alertas</span>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($metrics['expiring_users'] as $u)
                <div class="p-6 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=18181b&color=a1a1aa" class="w-10 h-10 rounded-full border border-white/5">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-zinc-200">{{ $u->name }}</p>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">{{ $u->email }}</p>
                    </div>
                    <div class="px-3 py-1 bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[10px] font-black uppercase rounded-lg">
                        {{ \Carbon\Carbon::parse($u->premium_expires_at)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="py-20 text-center text-zinc-600 italic text-sm">Céu limpo. Ninguém expira nos próximos 15 dias.</div>
                @endforelse
            </div>
            <div class="p-6 bg-zinc-950/40 text-center">
                <a href="{{ route('admin.users') }}?premium=yes" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-white transition-colors">Ver Todos os Assinantes &rarr;</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const data = @json($metrics['monthly_revenue']);
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
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
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#18181b',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.02)' },
                        ticks: { color: '#52525b', font: { size: 10, weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#52525b', font: { size: 10, weight: 'bold' } }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
