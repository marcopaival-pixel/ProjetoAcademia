@extends('layouts.app')

@section('title', 'Relatório de Performance — NexShape')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Header Strategy -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20 shadow-lg shadow-indigo-500/10">
                 <i class="fas fa-chart-bar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">NexShape Intelligence</h1>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-1">
                    Análise de Performance • {{ $range }} Dias
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex bg-zinc-900 p-1 rounded-xl border border-white/5">
                @foreach([7 => '7D', 14 => '14D', 30 => '30D'] as $val => $label)
                    <a href="{{ route('report', ['range' => $val]) }}" 
                       class="px-4 py-1.5 rounded-lg text-[10px] font-black transition-all {{ $range == $val ? 'bg-indigo-600 text-white' : 'text-zinc-500 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('report.monthly.pdf') }}" class="px-5 py-2.5 bg-white text-zinc-900 font-black rounded-xl hover:bg-zinc-200 transition-all text-[10px] tracking-widest uppercase items-center flex gap-2">
                <i class="fas fa-file-pdf"></i> Exportar BI
            </a>
        </div>
    </div>

    <!-- Top Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl space-y-4">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block">Média Calórica</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-white">{{ number_format($avgKcal, 0) }}</span>
                <span class="text-xs text-zinc-500 font-bold uppercase">kcal/dia</span>
            </div>
            <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500" style="width: 70%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl space-y-4">
            <span class="text-[10px] text-emerald-500/70 font-black uppercase tracking-widest block">Treino Total</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-white">{{ $totals->ex_min }}</span>
                <span class="text-xs text-zinc-500 font-bold uppercase">minutos</span>
            </div>
            <p class="text-[10px] text-zinc-500 font-bold italic">~{{ number_format($totals->ex_kcal, 0) }} kcal burned</p>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl space-y-4">
            <span class="text-[10px] text-indigo-500/70 font-black uppercase tracking-widest block">Variação Peso</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black {{ $deltaWeight < 0 ? 'text-emerald-400' : ($deltaWeight > 0 ? 'text-amber-400' : 'text-white') }}">
                    {{ $deltaWeight > 0 ? '+' : '' }}{{ $deltaWeight ?? '0' }}
                </span>
                <span class="text-xs text-zinc-500 font-bold uppercase">kg</span>
            </div>
            <p class="text-[10px] text-zinc-500 font-bold italic">No período selecionado</p>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl space-y-4">
            <span class="text-[10px] text-purple-500/70 font-black uppercase tracking-widest block">Hidratação Total</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-white">{{ number_format($totals->water / 1000, 1) }}</span>
                <span class="text-xs text-zinc-500 font-bold uppercase">litros</span>
            </div>
            <div class="flex gap-1">
                @foreach(array_slice($days, -$range) as $day)
                    <div class="h-1.5 flex-1 rounded-full {{ $day['water'] > 0 ? 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]' : 'bg-zinc-800' }}"></div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Energy Balance Chart -->
        <div class="lg:col-span-8 bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-white">Balanço Energético</h3>
                    <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-1">Ingestão vs Gasto por Dia</p>
                </div>
                <div class="flex gap-6">
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-500"></span> <span class="text-[9px] text-zinc-400 font-black uppercase">Consumo</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> <span class="text-[9px] text-zinc-400 font-black uppercase">Gasto</span></div>
                </div>
            </div>
            
            <div style="height: 350px;">
                <canvas id="energyBalanceChart"></canvas>
            </div>
        </div>

        <!-- Macro Breakdown -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem]">
                <h3 class="text-xl font-black text-white mb-8">Média de Macros</h3>
                <div style="height: 220px;" class="mb-8">
                    <canvas id="macroDistributionChart"></canvas>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-[9px] text-zinc-500 font-black mb-1">PROT</p>
                        <p class="text-lg font-black text-white">{{ round($avgP) }}g</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] text-zinc-500 font-black mb-1">CARB</p>
                        <p class="text-lg font-black text-white">{{ round($avgC) }}g</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[9px] text-zinc-500 font-black mb-1">GORD</p>
                        <p class="text-lg font-black text-white">{{ round($avgF) }}g</p>
                    </div>
                </div>
            </div>

            <!-- Activity Recap -->
            <div class="bg-zinc-900/20 border border-white/5 p-8 rounded-[2.5rem] relative overflow-hidden group">
                <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-all"></div>
                <h3 class="text-xl font-black text-white flex items-center justify-between relative z-10">
                    Sessões
                    <span class="text-emerald-400 text-3xl">{{ $totals->days_ex }}</span>
                </h3>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1 relative z-10">Treinos registrados no período</p>
            </div>
        </div>
    </div>

    <!-- Daily Log Section -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden">
        <div class="p-8 border-b border-white/5">
            <h3 class="text-xl font-black text-white">Detalhamento Diário</h3>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Registros brutos processados</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5">
                        <th class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Data</th>
                        <th class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Peso</th>
                        <th class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Kcal In</th>
                        <th class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Macros (P/C/G)</th>
                        <th class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Treino</th>
                        <th class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Água</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach(array_reverse($days) as $date => $info)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="p-5">
                            <span class="text-white font-bold text-sm">{{ $info['label'] }}</span>
                        </td>
                        <td class="p-5 text-zinc-400 font-bold text-sm">
                            {{ $info['weight'] ? $info['weight'].' kg' : '--' }}
                        </td>
                        <td class="p-5">
                            <span class="text-blue-400 font-black text-sm">{{ number_format($info['kcal_in'], 0) }}</span>
                        </td>
                        <td class="p-5 text-zinc-500 text-xs font-mono">
                            {{ round($info['p']) }} / {{ round($info['c']) }} / {{ round($info['f']) }}
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-400 font-bold text-sm">{{ $info['ex_min'] }}m</span>
                                <span class="text-zinc-600 text-[10px]">/ {{ $info['ex_kcal'] }} kcal</span>
                            </div>
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600" style="width: {{ min(($info['water'] / 2500) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-[10px] text-zinc-500 font-bold">{{ $info['water'] }}ml</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    body { background-color: #0c0f16; }
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    const days = @json(array_values($days));
    const labels = days.map(d => d.label);

    const commonLineOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(255, 255, 255, 0.02)' }, ticks: { color: '#52525b', font: { size: 9 } } },
            x: { grid: { display: false }, ticks: { color: '#52525b', font: { size: 9 } } }
        }
    };

    // Energy Balance Chart
    new Chart(document.getElementById('energyBalanceChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ingestão',
                    data: days.map(d => d.kcal_in),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderRadius: 4
                },
                {
                    label: 'Gasto',
                    data: days.map(d => d.ex_kcal),
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderRadius: 4
                }
            ]
        },
        options: commonLineOptions
    });

    // Macro Distribution Chart (Pie)
    new Chart(document.getElementById('macroDistributionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Prot', 'Carb', 'Gord'],
            datasets: [{
                data: [{{ $avgP }}, {{ $avgC }}, {{ $avgF }}],
                backgroundColor: ['#3b82f6', '#8b5cf6', '#f59e0b'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection
