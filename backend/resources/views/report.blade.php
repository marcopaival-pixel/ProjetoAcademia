@extends('layouts.app')

@section('title', 'Intelligence Analytics — NexShape Pro')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('patient.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-xl">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-zinc-950 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                 <i data-lucide="brain-circuit" class="w-8 h-8"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Intelligence <span class="text-emerald-500">Analytics</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Relatório Consolidado • {{ $range }} Dias Sincronizados
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex bg-zinc-950 p-1.5 rounded-2xl border border-zinc-800 shadow-inner">
                @foreach([7 => '7D', 14 => '14D', 30 => '30D', 90 => '90D'] as $val => $label)
                    <a href="{{ route('report', ['range' => $val]) }}" 
                       class="px-5 py-2 rounded-xl text-[10px] font-black transition-all uppercase tracking-widest {{ $range == $val ? 'bg-emerald-500 text-zinc-950 shadow-xl' : 'text-zinc-600 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            
            @if($isPremium)
                <a href="{{ route('report.monthly.pdf') }}" class="px-6 py-3 bg-white text-zinc-900 font-black rounded-2xl hover:bg-emerald-500 hover:text-zinc-950 transition-all text-[10px] tracking-widest uppercase flex items-center gap-3 shadow-xl">
                    <i data-lucide="file-down" class="w-4 h-4"></i> Exportar PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group shadow-2xl transition-all hover:border-emerald-500/20">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="scale" class="w-4 h-4 text-emerald-500/50"></i>
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] block">Peso & Evolução</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black {{ $deltaWeight < 0 ? 'text-emerald-400' : ($deltaWeight > 0 ? 'text-amber-400' : 'text-white') }} italic tracking-tighter tabular-nums">
                    {{ $deltaWeight > 0 ? '+' : '' }}{{ $deltaWeight ?? '0' }}<small class="text-xs ml-1 not-italic opacity-30 uppercase font-black">kg</small>
                </span>
            </div>
            <p class="text-[10px] font-black text-zinc-700 mt-4 uppercase tracking-widest">Variação Líquida</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group shadow-2xl transition-all hover:border-emerald-500/20">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="utensils" class="w-4 h-4 text-emerald-500/50"></i>
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] block">Aderência Nutri</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-white italic tracking-tighter tabular-nums">{{ $adherence['food'] }}%</span>
            </div>
            <div class="w-full bg-zinc-950 h-2 rounded-full mt-6 overflow-hidden shadow-inner border border-zinc-800">
                <div class="h-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] transition-all duration-1000" style="width: {{ $adherence['food'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group shadow-2xl transition-all hover:border-emerald-500/20">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="dumbbell" class="w-4 h-4 text-emerald-500/50"></i>
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] block">Assiduidade Treino</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-emerald-500 italic tracking-tighter tabular-nums">{{ $adherence['training'] }}%</span>
            </div>
            <div class="w-full bg-zinc-950 h-2 rounded-full mt-6 overflow-hidden shadow-inner border border-zinc-800">
                <div class="h-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] transition-all duration-1000" style="width: {{ $adherence['training'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group shadow-2xl transition-all hover:border-emerald-500/20">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="flame" class="w-4 h-4 text-emerald-500/50"></i>
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] block">Média Calórica</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-white italic tracking-tighter tabular-nums">{{ number_format($avgKcal, 0) }}</span>
                <span class="text-[10px] font-black text-zinc-700 uppercase tracking-widest">kcal/dia</span>
            </div>
            <p class="text-[10px] font-black text-zinc-700 mt-4 uppercase tracking-widest">Consistência Metabólica</p>
        </div>
    </div>

    <!-- Main Content Analytical Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Column 1: Physical & Goals -->
        <div class="space-y-10">
            <!-- Physical Evolution Card -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[3rem] p-10 space-y-10 shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-emerald-500 shadow-inner">
                        <i data-lucide="ruler" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter">Bio <span class="text-emerald-500">Métricas</span></h2>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-zinc-950 p-6 rounded-3xl space-y-2 border border-zinc-800 shadow-inner">
                        <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.2em]">Body Fat</span>
                        <p class="text-3xl font-black text-white italic tabular-nums">{{ $physical['bf'] ?? '--' }}<small class="text-xs ml-1 not-italic opacity-30">%</small></p>
                    </div>
                    <div class="bg-zinc-950 p-6 rounded-3xl space-y-2 border border-zinc-800 shadow-inner">
                        <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.2em]">Skeletal Muscle</span>
                        <p class="text-3xl font-black text-white italic tabular-nums">{{ $physical['muscle'] ?? '--' }}<small class="text-xs ml-1 not-italic opacity-30">%</small></p>
                    </div>
                </div>

                @if($physical['measures'])
                <div class="space-y-6">
                    <p class="text-[9px] font-black text-zinc-700 uppercase tracking-[0.3em] pl-2 border-l-2 border-emerald-500">Circunferências Atuais (cm)</p>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($physical['measures'] as $label => $value)
                            <div class="bg-zinc-950/50 p-4 rounded-2xl text-center border border-zinc-800 transition-all hover:border-emerald-500/20 group">
                                <span class="text-[7px] font-black text-zinc-700 uppercase block mb-1 group-hover:text-emerald-500/50 transition-colors">{{ $label }}</span>
                                <span class="text-sm font-black text-white italic tabular-nums">{{ $value ?? '--' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Goals Card -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[3rem] p-10 space-y-8 shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-amber-500 shadow-inner">
                        <i data-lucide="target" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter">Estratégia & <span class="text-amber-500">Metas</span></h2>
                </div>

                <div class="bg-amber-500/5 p-8 rounded-[2.5rem] border-l-4 border-amber-500/50 shadow-inner">
                    <p class="text-sm text-zinc-400 italic leading-relaxed font-medium">
                        "{{ $goals['objectives'] }}"
                    </p>
                </div>
                
                @if($goals['care_plan'])
                <div class="space-y-3">
                    <p class="text-[9px] font-black text-zinc-700 uppercase tracking-[0.3em] pl-2 border-l-2 border-amber-500/30">Foco do Plano de Cuidado</p>
                    <p class="text-xs text-zinc-500 font-medium leading-relaxed italic">{{ Str::limit($goals['care_plan'], 200) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Column 2 & 3: Analytical Charts -->
        <div class="lg:col-span-2 space-y-10">
            
            <!-- Training Performance Chart -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] p-10 space-y-10 shadow-2xl relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-emerald-500 shadow-inner">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                        <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter">Desempenho <span class="text-emerald-500">Treino</span></h2>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black text-white italic tracking-tighter tabular-nums leading-none">{{ $totals->ex_min }}</span>
                        <p class="text-[9px] font-black text-zinc-700 uppercase tracking-widest mt-1">Minutos Acumulados</p>
                    </div>
                </div>

                <div class="h-80 w-full">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Nutritional Report Chart -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] p-10 space-y-10 shadow-2xl relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-emerald-500 shadow-inner">
                            <i data-lucide="pie-chart" class="w-6 h-6"></i>
                        </div>
                        <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter">Análise <span class="text-emerald-500">Nutricional</span></h2>
                    </div>
                    <div class="flex gap-10">
                        <div class="text-center">
                            <span class="text-2xl font-black text-white italic tracking-tighter tabular-nums leading-none">{{ number_format($avgP, 0) }}g</span>
                            <p class="text-[8px] font-black text-zinc-700 uppercase tracking-widest mt-1">Prot Média</p>
                        </div>
                        <div class="text-center border-l border-zinc-800 pl-10">
                            <span class="text-2xl font-black text-white italic tracking-tighter tabular-nums leading-none">{{ number_format($avgC, 0) }}g</span>
                            <p class="text-[8px] font-black text-zinc-700 uppercase tracking-widest mt-1">Carb Médio</p>
                        </div>
                    </div>
                </div>

                <div class="h-80 w-full">
                    <canvas id="nutritionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Log Table Section -->
    <div id="daily-log" class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-2xl relative">
        <div class="p-10 border-b border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-8 bg-zinc-900/50 backdrop-blur-md">
            <div class="space-y-2">
                <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Audit de <span class="text-emerald-500">Frequência</span></h3>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em]">Mapeamento granular de registros hídricos, nutricionais e físicos.</p>
            </div>
            
            <div class="flex items-center gap-8 bg-zinc-950 px-8 py-4 rounded-3xl border border-zinc-800 shadow-inner">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Sincronizado</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-zinc-800"></div>
                    <span class="text-[10px] font-black text-zinc-700 uppercase tracking-widest">Ausente</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-950/50">
                        <th class="py-8 px-12 text-[10px] font-black text-zinc-700 uppercase tracking-[0.3em]">Timeline</th>
                        <th class="py-8 px-12 text-[10px] font-black text-zinc-700 uppercase tracking-[0.3em]">Ingestão (Kcal)</th>
                        <th class="py-8 px-12 text-[10px] font-black text-zinc-700 uppercase tracking-[0.3em]">Carga Treino</th>
                        <th class="py-8 px-12 text-[10px] font-black text-zinc-700 uppercase tracking-[0.3em]">Composição (P/C/G)</th>
                        <th class="py-8 px-12 text-[10px] font-black text-zinc-700 uppercase tracking-[0.3em] text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach(array_reverse($days) as $date => $day)
                    <tr class="hover:bg-emerald-500/[0.02] transition-all group">
                        <td class="py-8 px-12">
                            <span class="text-base font-black text-white italic tracking-tight uppercase">{{ $day['label'] }}</span>
                        </td>
                        <td class="py-8 px-12">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl {{ $day['kcal_in'] > 0 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/10' : 'bg-zinc-950 border border-zinc-800 text-zinc-800 shadow-inner' }} flex items-center justify-center transition-all group-hover:scale-110">
                                    <i data-lucide="utensils-crosseyed" class="w-4 h-4"></i>
                                </div>
                                <span class="text-base font-black {{ $day['kcal_in'] > 0 ? 'text-white' : 'text-zinc-800' }} italic tabular-nums">
                                    {{ $day['kcal_in'] > 0 ? number_format($day['kcal_in'], 0) : '---' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-8 px-12">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl {{ $day['ex_min'] > 0 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/10' : 'bg-zinc-950 border border-zinc-800 text-zinc-800 shadow-inner' }} flex items-center justify-center transition-all group-hover:scale-110">
                                    <i data-lucide="zap" class="w-4 h-4"></i>
                                </div>
                                <span class="text-base font-black {{ $day['ex_min'] > 0 ? 'text-white' : 'text-zinc-800' }} italic tabular-nums">
                                    {{ $day['ex_min'] > 0 ? $day['ex_min'] . 'm' : '---' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-8 px-12">
                            <div class="flex items-center gap-3">
                                <div class="px-3 py-1 bg-zinc-950 border border-zinc-800 rounded-lg text-[10px] font-black text-zinc-600 uppercase tabular-nums shadow-inner">{{ number_format($day['p'], 0) }}P</div>
                                <div class="px-3 py-1 bg-zinc-950 border border-zinc-800 rounded-lg text-[10px] font-black text-zinc-600 uppercase tabular-nums shadow-inner">{{ number_format($day['c'], 0) }}C</div>
                                <div class="px-3 py-1 bg-zinc-950 border border-zinc-800 rounded-lg text-[10px] font-black text-zinc-600 uppercase tabular-nums shadow-inner">{{ number_format($day['f'], 0) }}G</div>
                            </div>
                        </td>
                        <td class="py-8 px-12 text-right">
                            @if($day['kcal_in'] > 0 && $day['ex_min'] > 0)
                                <span class="px-4 py-1.5 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase tracking-[0.2em] rounded-full border border-emerald-500/20 shadow-xl">Full Sync</span>
                            @elseif($day['kcal_in'] > 0 || $day['ex_min'] > 0)
                                <span class="px-4 py-1.5 bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase tracking-[0.2em] rounded-full border border-amber-500/20 shadow-xl">Partial</span>
                            @else
                                <span class="px-4 py-1.5 bg-zinc-950 text-zinc-800 text-[9px] font-black uppercase tracking-[0.2em] rounded-full border border-zinc-800 shadow-inner">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        const labels = {!! json_encode(array_values(array_map(fn($d) => $d['label'], $days))) !!};
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#09090b',
                    titleFont: { size: 12, weight: '900' },
                    bodyFont: { size: 10, weight: 'bold' },
                    padding: 12,
                    displayColors: false,
                    borderColor: '#10b98133',
                    borderWidth: 1
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(255,255,255,0.02)', borderDash: [5, 5] }, 
                    ticks: { color: '#3f3f46', font: { size: 9, weight: '900' } } 
                },
                x: { 
                    grid: { display: false }, 
                    ticks: { color: '#3f3f46', font: { size: 8, weight: '900' } } 
                }
            }
        };

        // Performance Chart
        new Chart(document.getElementById('performanceChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Minutos de Treino',
                    data: {!! json_encode(array_values(array_map(fn($d) => $d['ex_min'], $days))) !!},
                    borderColor: '#10b981',
                    backgroundColor: (context) => {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                        return gradient;
                    },
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#09090b',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8
                }]
            },
            options: chartOptions
        });

        // Nutrition Chart
        new Chart(document.getElementById('nutritionChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Calorias Consumidas',
                    data: {!! json_encode(array_values(array_map(fn($d) => $d['kcal_in'], $days))) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 12,
                    hoverBackgroundColor: '#34d399',
                    barThickness: labels.length > 30 ? 6 : 16
                }]
            },
            options: chartOptions
        });
    });
</script>
@endpush

<style>
    body { 
        background-color: #080a0f;
        background-image:
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }
    
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.1); border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.2); }
</style>
@endsection
