@extends('layouts.app')

@section('title', 'Central de Nutrição — NexShape')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Header Clean -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white">Central de Nutrição</h1>
            <p class="text-zinc-500 text-sm mt-1">Gestão inteligente de metas e balanço calórico.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('diary') }}" class="px-5 py-2.5 bg-zinc-900 text-zinc-300 font-bold text-xs rounded-xl border border-white/5 hover:text-white transition-all">
                Ver Diário
            </a>
            <button class="px-5 py-2.5 bg-blue-600 text-white font-black text-xs rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/10" data-bs-toggle="modal" data-bs-target="#goalModalClean">
                Ajustar Estratégia
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs font-bold animate-fade-in">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Column 1: Metabolic & Consistency -->
        <div class="space-y-8">
            <!-- Metabolic Analysis -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Análise Metabólica
                </h3>

                @if($stats['ok'])
                <div class="space-y-6">
                    <div>
                        <span class="text-[10px] text-zinc-600 font-bold uppercase block mb-1">Taxa Basal (TMB)</span>
                        <p class="text-xl font-black text-white">{{ number_format($stats['bmr'], 0, ',', '.') }} <small class="text-zinc-500 text-[10px]">kcal</small></p>
                    </div>
                    <div>
                        <span class="text-[10px] text-zinc-600 font-bold uppercase block mb-1">Gasto Total (TDEE)</span>
                        <p class="text-xl font-black text-amber-500">{{ number_format($stats['tdee'], 0, ',', '.') }} <small class="text-zinc-600 text-[10px]">kcal</small></p>
                    </div>
                    <div class="pt-6 border-t border-white/5">
                        <span class="text-[10px] text-blue-400 font-black uppercase block mb-1">Meta Diária</span>
                        <p class="text-3xl font-black text-white tracking-tight">{{ number_format($targetKcal, 0, ',', '.') }} <small class="text-zinc-500 text-xs italic font-normal">kcal</small></p>
                    </div>
                </div>
                @else
                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-500 text-[10px] italic">
                    {{ $stats['message'] }}
                </div>
                @endif
            </div>

            <!-- Consistency Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Consistência Semanal
                </h3>
                <div class="text-center py-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-4">
                        <span class="text-2xl font-black text-emerald-400">{{ $consistencyCount }}<small class="text-xs">/7</small></span>
                    </div>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase">Dias no alvo</p>
                    <p class="text-xs text-zinc-400 mt-2">
                        @if($consistencyCount >= 6) Excelente disciplina!
                        @elseif($consistencyCount >= 4) Boa constância.
                        @else Foco no objetivo! @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Column 2 & 3: Main Charts -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Calorie Trend Chart -->
            <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Tendência de Calorias (15 dias)
                    </h3>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-500">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Real
                        </span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-zinc-500">
                            <span class="w-2 h-2 rounded-full border border-dashed border-zinc-500"></span> Meta
                        </span>
                    </div>
                </div>
                <div style="height: 250px;">
                    <canvas id="calorieTrendChart"></canvas>
                </div>
            </div>

            <!-- Macro Distribution with Donut -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Distribuição de Macros
                    </h3>
                    <div class="flex items-center justify-center p-4">
                        <div style="width: 180px; height: 180px;">
                            <canvas id="macroDonutChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6 flex flex-col justify-center">
                    <div class="space-y-6">
                        @foreach([
                            ['label' => 'Proteínas', 'val' => $macroTargets['p'], 'color' => '#fb7185', 'icon' => 'P'],
                            ['label' => 'Carbo', 'val' => $macroTargets['c'], 'color' => '#60a5fa', 'icon' => 'C'],
                            ['label' => 'Gorduras', 'val' => $macroTargets['f'], 'color' => '#fbbf24', 'icon' => 'G']
                        ] as $m)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded bg-zinc-950 flex items-center justify-center text-[10px] font-black" style="color: {{ $m['color'] }}; border: 1px solid {{ $m['color'] }}40;">{{ $m['icon'] }}</span>
                                <span class="text-xs font-bold text-zinc-400">{{ $m['label'] }}</span>
                            </div>
                            <span class="text-sm font-black text-white">{{ $m['val'] ?? '—' }}g</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 4: Water & Insights -->
        <div class="space-y-8">
            <!-- Water Intake -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Hidratação
                </h3>
                <div class="relative flex flex-col items-center">
                    @php 
                        $waterPerc = min(($waterToday / ($waterTarget ?: 2500)) * 100, 100);
                    @endphp
                    <div class="w-32 h-32 rounded-full border-4 border-zinc-800 flex flex-col items-center justify-center overflow-hidden relative">
                        <div class="absolute bottom-0 left-0 w-full transition-all duration-1000 bg-blue-500/40" style="height: {{ $waterPerc }}%"></div>
                        <span class="relative z-10 text-xl font-black text-white">{{ number_format($waterToday / 1000, 1) }}L</span>
                        <span class="relative z-10 text-[10px] text-zinc-500 font-bold uppercase">de {{ number_format($waterTarget / 1000, 1) }}L</span>
                    </div>
                    <p class="text-xs text-zinc-400 mt-4 text-center px-4 italic">
                        @if($waterPerc >= 100) Meta atingida!
                        @else Mantenha-se hidratado. @endif
                    </p>
                </div>
            </div>

            <!-- Insights Card -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    Insights Nutricionais
                </h3>
                <div class="space-y-4">
                    @php
                        $diffAverages = ($averages->cal ?? 0) - $targetKcal;
                    @endphp
                    <div class="flex gap-3">
                        <i class="fas fa-lightbulb text-purple-400 mt-1"></i>
                        <p class="text-xs text-zinc-400">
                            @if(abs($diffAverages) < 100)
                                Balanço excelente! Você está quase idêntico à sua meta.
                            @elseif($diffAverages > 0)
                                Consumo médio está {{ number_format($diffAverages) }} kcal acima da meta. 
                            @else
                                Consumo médio está {{ number_format(abs($diffAverages)) }} kcal abaixo da meta.
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                        <p class="text-xs text-zinc-400">
                            {{ $currentGoal == 'lose' ? 'Priorize alimentos com baixa densidade calórica.' : ($currentGoal == 'gain' ? 'Garanta superávit e bom aporte de proteínas.' : 'Mantenha a consistência atual.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Strategy -->
    <div class="modal fade" id="goalModalClean" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-zinc-900 border border-white/10 rounded-[2rem] overflow-hidden">
                <div class="modal-header border-white/5 p-6">
                    <h5 class="modal-title text-white font-black">Ajustar Estratégia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('nutrition.update-goal') }}" method="POST">
                    @csrf
                    <div class="modal-body p-8 space-y-6">
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Objetivo</label>
                            <select name="goal" class="w-full bg-zinc-950 border border-white/10 rounded-xl p-4 text-white text-sm focus:ring-1 focus:ring-blue-500 outline-none">
                                <option value="lose" {{ $currentGoal == 'lose' ? 'selected' : '' }}>Queima de Gordura (Cutting)</option>
                                <option value="maintain" {{ $currentGoal == 'maintain' ? 'selected' : '' }}>Manutenção e Saúde</option>
                                <option value="gain" {{ $currentGoal == 'gain' ? 'selected' : '' }}>Ganho de Massa (Bulking)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Distribuição de Macros</label>
                            <div class="space-y-2">
                                @foreach([['split' => 'cutting', 'label' => 'High Protein'], ['split' => 'bulking', 'label' => 'High Carb'], ['split' => 'maintenance', 'label' => 'Equilibrado']] as $s)
                                <label class="flex items-center gap-3 p-4 rounded-xl bg-zinc-950 border border-white/5 cursor-pointer hover:bg-zinc-900 transition-all">
                                    <input type="radio" name="split" value="{{ $s['split'] }}" class="form-check-input" {{ $s['split'] == 'maintenance' ? 'checked' : '' }}>
                                    <span class="text-white text-sm font-bold">{{ $s['label'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-white/5 p-6">
                        <button type="button" class="px-5 py-2 text-zinc-500 font-bold text-xs" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-black text-xs rounded-xl hover:bg-blue-500 transition-all">Salvar Estratégia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Calorie Trend Chart
    const trendCtx = document.getElementById('calorieTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($historyData->map(fn($d) => \Carbon\Carbon::parse($d->entry_date)->format('d/m'))) !!},
            datasets: [{
                label: 'Real',
                data: {!! json_encode($historyData->pluck('total_cal')) !!},
                borderColor: '#3b82f6',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                pointRadius: 0,
                pointHoverRadius: 4
            }, {
                label: 'Meta',
                data: Array({{ $historyData->count() }}).fill({{ $targetKcal }}),
                borderColor: 'rgba(255, 255, 255, 0.2)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.02)' },
                    ticks: { color: '#52525b', font: { size: 9 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#52525b', font: { size: 9 } }
                }
            }
        }
    });

    // Macro Donut Chart
    const donutCtx = document.getElementById('macroDonutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Proteínas', 'Carbos', 'Gorduras'],
            datasets: [{
                data: [{{ $macroTargets['p'] }}, {{ $macroTargets['c'] }}, {{ $macroTargets['f'] }}],
                backgroundColor: ['#fb7185', '#60a5fa', '#fbbf24'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });
</script>

<style>
    body { background-color: #0c0f16; }
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
