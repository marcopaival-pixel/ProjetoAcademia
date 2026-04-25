@extends('layouts.app')

@section('title', 'Intelligence Analytics — NexShape')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="py-8 space-y-12 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-white/5">
        <div class="flex items-center gap-5">
            <a href="{{ route('patient.reports.index') }}" class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-500 border border-white/5 hover:text-white transition-all">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-indigo-600/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20 shadow-lg shadow-indigo-500/10">
                 <i class="fas fa-microchip text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight uppercase italic">Intelligence <span class="text-indigo-500">Analytics</span></h1>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Relatório Consolidado • {{ $range }} Dias
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex bg-zinc-900 p-1 rounded-xl border border-white/5">
                @foreach([7 => '7D', 14 => '14D', 30 => '30D', 90 => '90D'] as $val => $label)
                    <a href="{{ route('report', ['range' => $val]) }}" 
                       class="px-4 py-1.5 rounded-lg text-[10px] font-black transition-all {{ $range == $val ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-zinc-500 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            
            @if($isPremium)
                <a href="{{ route('report.monthly.pdf') }}" class="px-5 py-2.5 bg-white text-zinc-900 font-black rounded-xl hover:bg-zinc-200 transition-all text-[10px] tracking-widest uppercase flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 blur-2xl rounded-full"></div>
            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em] block mb-4">Peso & Evolução</span>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black {{ $deltaWeight < 0 ? 'text-emerald-400' : ($deltaWeight > 0 ? 'text-amber-400' : 'text-white') }} italic tracking-tighter">
                    {{ $deltaWeight > 0 ? '+' : '' }}{{ $deltaWeight ?? '0' }}<small class="text-xs ml-1 not-italic opacity-50 uppercase">kg</small>
                </span>
            </div>
            <p class="text-[10px] font-bold text-zinc-600 mt-2">Variação no período</p>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 blur-2xl rounded-full"></div>
            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em] block mb-4">Aderência Nutricional</span>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-indigo-400 italic tracking-tighter">{{ $adherence['food'] }}%</span>
            </div>
            <div class="w-full bg-white/5 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-indigo-500" style="width: {{ $adherence['food'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 blur-2xl rounded-full"></div>
            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em] block mb-4">Assiduidade Treino</span>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-400 italic tracking-tighter">{{ $adherence['training'] }}%</span>
            </div>
            <div class="w-full bg-white/5 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: {{ $adherence['training'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-purple-500/10 blur-2xl rounded-full"></div>
            <span class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em] block mb-4">Média Calórica</span>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-purple-400 italic tracking-tighter">{{ number_format($avgKcal, 0) }}</span>
                <span class="text-[10px] font-black text-zinc-600 uppercase">kcal/dia</span>
            </div>
            <p class="text-[10px] font-bold text-zinc-600 mt-2">Consistência de ingestão</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Column 1: Physical & Goals -->
        <div class="space-y-8">
            <!-- 1. Evolução Física -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Evolução <span class="text-blue-500">Física</span></h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 p-6 rounded-3xl space-y-1">
                        <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Gordura Corporal</span>
                        <p class="text-2xl font-black text-white italic">{{ $physical['bf'] ?? '--' }}<small class="text-xs ml-0.5 not-italic opacity-50">%</small></p>
                    </div>
                    <div class="bg-white/5 p-6 rounded-3xl space-y-1">
                        <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Massa Muscular</span>
                        <p class="text-2xl font-black text-white italic">{{ $physical['muscle'] ?? '--' }}<small class="text-xs ml-0.5 not-italic opacity-50">%</small></p>
                    </div>
                </div>

                @if($physical['measures'])
                <div class="space-y-4">
                    <p class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em] mb-4">Últimas Circunferências (cm)</p>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($physical['measures'] as $label => $value)
                            <div class="bg-white/5 p-3 rounded-2xl text-center">
                                <span class="text-[7px] font-black text-zinc-500 uppercase block mb-1">{{ $label }}</span>
                                <span class="text-xs font-black text-white italic">{{ $value ?? '--' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- 5. Metas & Planejamento -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-10 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Metas & <span class="text-amber-500">Objetivos</span></h2>
                </div>

                <div class="bg-white/5 p-8 rounded-[2rem] border-l-4 border-amber-500/50">
                    <p class="text-sm text-zinc-300 italic leading-relaxed">
                        "{{ $goals['objectives'] }}"
                    </p>
                </div>
                
                @if($goals['care_plan'])
                <div class="space-y-3">
                    <p class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Foco Estratégico</p>
                    <p class="text-xs text-zinc-500 font-medium leading-relaxed">{{ Str::limit($goals['care_plan'], 150) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Column 2 & 3: Analytical Charts -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- 2. Desempenho no Treino -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-10 space-y-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Desempenho <span class="text-emerald-400">Treino</span></h2>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-white italic leading-none">{{ $totals->ex_min }}</span>
                        <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Minutos Totais</p>
                    </div>
                </div>

                <div class="h-64">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- 3. Relatório Nutricional -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-10 space-y-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                            <i class="fas fa-apple-alt"></i>
                        </div>
                        <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Análise <span class="text-indigo-400">Nutricional</span></h2>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-center">
                            <span class="text-xl font-black text-white italic leading-none">{{ number_format($avgP, 0) }}g</span>
                            <p class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Proteína</p>
                        </div>
                        <div class="text-center border-l border-white/10 pl-6">
                            <span class="text-xl font-black text-white italic leading-none">{{ number_format($avgC, 0) }}g</span>
                            <p class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Carbs</p>
                        </div>
                    </div>
                </div>

                <div class="h-64">
                    <canvas id="nutritionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Frequência & 6. Aderência -->
    <div id="daily-log" class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h3 class="text-2xl font-black text-white italic uppercase tracking-tight">Frequência & <span class="text-indigo-500">Assiduidade</span></h3>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] mt-2">Detalhamento dos registros diários no período</p>
            </div>
            
            <div class="flex gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Refeição</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Treino</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5">
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Consumo (Kcal)</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Gasto Treino</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Macros (P/C/G)</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach(array_reverse($days) as $date => $day)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="py-6 px-10">
                            <span class="text-sm font-black text-white italic tracking-tight">{{ $day['label'] }}</span>
                        </td>
                        <td class="py-6 px-10">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $day['kcal_in'] > 0 ? 'bg-blue-500/10 text-blue-400' : 'bg-zinc-800 text-zinc-600' }} flex items-center justify-center">
                                    <i class="fas fa-utensils text-[10px]"></i>
                                </div>
                                <span class="text-sm font-black {{ $day['kcal_in'] > 0 ? 'text-white' : 'text-zinc-700' }} italic">
                                    {{ $day['kcal_in'] > 0 ? number_format($day['kcal_in'], 0) : '---' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-6 px-10">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $day['ex_min'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-zinc-800 text-zinc-600' }} flex items-center justify-center">
                                    <i class="fas fa-running text-[10px]"></i>
                                </div>
                                <span class="text-sm font-black {{ $day['ex_min'] > 0 ? 'text-white' : 'text-zinc-700' }} italic">
                                    {{ $day['ex_min'] > 0 ? $day['ex_min'] . 'm' : '---' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-6 px-10">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-zinc-600 uppercase">{{ number_format($day['p'], 0) }}p</span>
                                <span class="text-zinc-800">/</span>
                                <span class="text-[10px] font-black text-zinc-600 uppercase">{{ number_format($day['c'], 0) }}c</span>
                                <span class="text-zinc-800">/</span>
                                <span class="text-[10px] font-black text-zinc-600 uppercase">{{ number_format($day['f'], 0) }}g</span>
                            </div>
                        </td>
                        <td class="py-6 px-10 text-right">
                            @if($day['kcal_in'] > 0 && $day['ex_min'] > 0)
                                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase tracking-widest rounded-md">Total</span>
                            @elseif($day['kcal_in'] > 0 || $day['ex_min'] > 0)
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[8px] font-black uppercase tracking-widest rounded-md">Parcial</span>
                            @else
                                <span class="px-3 py-1 bg-zinc-800 text-zinc-600 text-[8px] font-black uppercase tracking-widest rounded-md">Ausente</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = {!! json_encode(array_values(array_map(fn($d) => $d['label'], $days))) !!};
        
        // Performance Chart
        new Chart(document.getElementById('performanceChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Minutos de Treino',
                    data: {!! json_encode(array_values(array_map(fn($d) => $d['ex_min'], $days))) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#525252', font: { size: 10, weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { color: '#525252', font: { size: 8, weight: 'bold' } } }
                }
            }
        });

        // Nutrition Chart
        new Chart(document.getElementById('nutritionChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Calorias Consumidas',
                    data: {!! json_encode(array_values(array_map(fn($d) => $d['kcal_in'], $days))) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 8,
                    barThickness: labels.length > 30 ? 4 : 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#525252', font: { size: 10, weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { color: '#525252', font: { size: 8, weight: 'bold' } } }
                }
            }
        });
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #06080c; }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #06080c; }
    ::-webkit-scrollbar-thumb { background: #1a1c24; border-radius: 10px; }
</style>
@endsection
