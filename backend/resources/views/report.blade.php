@extends('layouts.app')

@section('title', 'Relatório de Evolução e Desempenho - NexShape')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $measureLabels = [
        'chest' => 'Tórax',
        'waist' => 'Cintura',
        'abdomen' => 'Abdômen',
        'hips' => 'Quadril',
        'bicep' => 'Bíceps',
        'thigh' => 'Coxa',
    ];
    $hasAnyData = $reportState['has_food_data'] || $reportState['has_training_data'] || $reportState['has_weight_comparison'] || $reportState['has_physical_data'];
@endphp

<div class="py-10 space-y-10 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    <div class="flex flex-col gap-8 pb-6 border-b border-zinc-800/80">
        <a href="{{ route('patient.reports.index') }}" class="inline-flex w-fit items-center gap-3 text-[10px] font-black uppercase tracking-[0.22em] text-zinc-500 hover:text-emerald-400 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Relatórios
        </a>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
            <div class="flex items-start gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-zinc-950 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                    <i data-lucide="brain-circuit" class="w-8 h-8"></i>
                </div>
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight uppercase italic">Relatório de Evolução e Desempenho</h1>
                        <span class="px-3 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-black uppercase tracking-widest">Análise inteligente</span>
                    </div>
                    <p class="text-sm text-zinc-400 font-semibold max-w-3xl">
                        Visão consolidada de treino, nutrição e biometria no período de {{ $periodLabel }}.
                    </p>
                    <div class="flex flex-wrap gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-zinc-950 border border-zinc-800">
                            <i data-lucide="calendar-days" class="w-4 h-4 text-emerald-500"></i>
                            {{ $periodDays }} dias analisados
                        </span>
                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-zinc-950 border border-zinc-800">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-emerald-500"></i>
                            Dados gerados agora
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="flex bg-zinc-950 p-1.5 rounded-2xl border border-zinc-800 shadow-inner">
                    @foreach([7 => '7D', 14 => '14D', 30 => '30D', 90 => '90D'] as $val => $label)
                        <a href="{{ route('report', ['range' => $val]) }}"
                           class="px-5 py-2 rounded-xl text-[10px] font-black transition-all uppercase tracking-widest {{ $range == $val ? 'bg-emerald-500 text-zinc-950 shadow-xl' : 'text-zinc-500 hover:text-white' }}">
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
    </div>

    @unless($hasAnyData)
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 flex flex-col md:flex-row gap-5 md:items-center">
            <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-amber-400 shrink-0">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-white uppercase italic">Sem dados neste período</h2>
                <p class="text-sm text-zinc-400 mt-2 max-w-3xl">Registre ou sincronize treinos, refeições e medidas corporais para gerar esta análise. Os indicadores abaixo ficam em estado neutro até existirem registros suficientes.</p>
            </div>
        </div>
    @endunless

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="report-card p-7">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <i data-lucide="scale" class="w-4 h-4 text-emerald-500"></i>
                    <span class="metric-label">Peso e evolução</span>
                </div>
                <i data-lucide="info" class="w-4 h-4 text-zinc-500" title="Comparação entre a primeira e a última medição disponível no período."></i>
            </div>
            @if($reportState['has_weight_comparison'])
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black {{ $deltaWeight < 0 ? 'text-emerald-400' : ($deltaWeight > 0 ? 'text-amber-400' : 'text-white') }} italic tracking-tight tabular-nums">
                        {{ $deltaWeight > 0 ? '+' : '' }}{{ number_format($deltaWeight, 2, ',', '.') }}
                    </span>
                    <span class="text-xs font-black text-zinc-500 uppercase">kg</span>
                </div>
                <p class="metric-note">De {{ number_format($firstWeight, 2, ',', '.') }} kg para {{ number_format($lastWeight, 2, ',', '.') }} kg</p>
            @else
                <p class="empty-value">Sem comparação</p>
                <p class="metric-note">Registre ao menos duas pesagens para calcular a variação.</p>
            @endif
        </div>

        <div class="report-card p-7">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <i data-lucide="utensils" class="w-4 h-4 text-emerald-500"></i>
                    <span class="metric-label">Adesão nutricional</span>
                </div>
                <i data-lucide="info" class="w-4 h-4 text-zinc-500" title="Dias com alimentação registrada divididos pelo total de dias do período."></i>
            </div>
            @if($reportState['has_food_data'])
                <span class="text-5xl font-black text-white italic tracking-tight tabular-nums">{{ $adherence['food'] }}%</span>
                <div class="progress-track"><div class="progress-fill" style="width: {{ $adherence['food'] }}%"></div></div>
                <p class="metric-note">{{ $totals->days_food }} de {{ $periodDays }} dias com registro alimentar</p>
            @else
                <p class="empty-value">Sem dados</p>
                <p class="metric-note">Registre refeições para calcular adesão e médias.</p>
            @endif
        </div>

        <div class="report-card p-7">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <i data-lucide="dumbbell" class="w-4 h-4 text-emerald-500"></i>
                    <span class="metric-label">Frequência nos treinos</span>
                </div>
                <i data-lucide="info" class="w-4 h-4 text-zinc-500" title="Dias com treino registrado divididos pelo total de dias do período."></i>
            </div>
            @if($reportState['has_training_data'])
                <span class="text-5xl font-black text-emerald-400 italic tracking-tight tabular-nums">{{ $adherence['training'] }}%</span>
                <div class="progress-track"><div class="progress-fill" style="width: {{ $adherence['training'] }}%"></div></div>
                <p class="metric-note">{{ $totals->days_ex }} de {{ $periodDays }} dias com treino registrado</p>
            @else
                <p class="empty-value">Sem dados</p>
                <p class="metric-note">Registre treinos para avaliar frequência.</p>
            @endif
        </div>

        <div class="report-card p-7">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <i data-lucide="flame" class="w-4 h-4 text-emerald-500"></i>
                    <span class="metric-label">Média calórica</span>
                </div>
                <i data-lucide="info" class="w-4 h-4 text-zinc-500" title="Média calculada apenas nos dias com alimentação registrada."></i>
            </div>
            @if($reportState['has_food_data'])
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black text-white italic tracking-tight tabular-nums">{{ number_format($avgKcal, 0, ',', '.') }}</span>
                    <span class="text-xs font-black text-zinc-500 uppercase">kcal/dia</span>
                </div>
                <p class="metric-note">Base: {{ $totals->days_food }} dias registrados</p>
            @else
                <p class="empty-value">Sem dados</p>
                <p class="metric-note">A média não é exibida sem registros nutricionais.</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-8">
            <div class="report-card p-8 space-y-8">
                <div class="section-title">
                    <div class="section-icon text-emerald-500"><i data-lucide="ruler" class="w-6 h-6"></i></div>
                    <h2>Biometria</h2>
                </div>

                @if($reportState['has_physical_data'])
                    <div class="grid grid-cols-2 gap-4">
                        <div class="inner-panel">
                            <span class="metric-label">Gordura corporal</span>
                            <p class="panel-value">{{ $physical['bf'] ?? '--' }}<small>%</small></p>
                        </div>
                        <div class="inner-panel">
                            <span class="metric-label">Massa muscular esquelética</span>
                            <p class="panel-value">{{ $physical['muscle'] ?? '--' }}<small>%</small></p>
                        </div>
                    </div>

                    @if($physical['measures'])
                        <div class="space-y-4">
                            <p class="metric-label border-l-2 border-emerald-500 pl-2">Circunferências atuais (cm)</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($physical['measures'] as $label => $value)
                                    @if($value !== null)
                                        <div class="inner-panel text-center p-4">
                                            <span class="text-[9px] font-black text-zinc-400 uppercase block mb-1">{{ $measureLabels[$label] ?? $label }}</span>
                                            <span class="text-sm font-black text-white italic tabular-nums">{{ number_format($value, 1, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="empty-panel">
                        <p>Sem biometria neste período</p>
                        <span>Registre uma avaliação física para acompanhar composição corporal.</span>
                    </div>
                @endif
            </div>

            <div class="report-card p-8 space-y-6">
                <div class="section-title">
                    <div class="section-icon text-amber-500"><i data-lucide="target" class="w-6 h-6"></i></div>
                    <h2>Metas</h2>
                </div>

                @if($reportState['has_goals'])
                    <div class="bg-amber-500/5 p-6 rounded-2xl border-l-4 border-amber-500/60">
                        <p class="text-sm text-zinc-300 italic leading-relaxed font-medium">"{{ $goals['objectives'] }}"</p>
                    </div>
                @else
                    <div class="empty-panel border-amber-500/20">
                        <p>Nenhuma meta definida</p>
                        <span>Defina uma meta para contextualizar peso, treino e nutrição.</span>
                    </div>
                @endif

                @if($goals['care_plan'])
                    <div class="space-y-3">
                        <p class="metric-label border-l-2 border-amber-500/50 pl-2">Plano de cuidado</p>
                        <p class="text-xs text-zinc-400 font-medium leading-relaxed">{{ Str::limit($goals['care_plan'], 220) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="report-card p-8 space-y-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="section-title">
                        <div class="section-icon text-emerald-500"><i data-lucide="trending-up" class="w-6 h-6"></i></div>
                        <h2>Treinos</h2>
                    </div>
                    <div class="text-left md:text-right">
                        <span class="text-3xl font-black text-white italic tracking-tight tabular-nums leading-none">{{ $totals->ex_min }}</span>
                        <p class="metric-label mt-1">Minutos acumulados</p>
                    </div>
                </div>

                <div class="chart-box">
                    @if($reportState['has_training_data'])
                        <canvas id="performanceChart"></canvas>
                    @else
                        <div class="empty-chart">
                            <p>Sem dados neste período</p>
                            <span>Registre ou sincronize treinos para gerar o gráfico.</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="report-card p-8 space-y-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="section-title">
                        <div class="section-icon text-emerald-500"><i data-lucide="pie-chart" class="w-6 h-6"></i></div>
                        <h2>Nutrição</h2>
                    </div>
                    <div class="flex gap-8">
                        <div>
                            <span class="text-2xl font-black text-white italic tracking-tight tabular-nums leading-none">{{ $reportState['has_food_data'] ? number_format($avgP, 0, ',', '.') . 'g' : '--' }}</span>
                            <p class="metric-label mt-1">Proteína média</p>
                        </div>
                        <div class="border-l border-zinc-800 pl-8">
                            <span class="text-2xl font-black text-white italic tracking-tight tabular-nums leading-none">{{ $reportState['has_food_data'] ? number_format($avgC, 0, ',', '.') . 'g' : '--' }}</span>
                            <p class="metric-label mt-1">Carboidrato médio</p>
                        </div>
                    </div>
                </div>

                <div class="chart-box">
                    @if($reportState['has_food_data'])
                        <canvas id="nutritionChart"></canvas>
                    @else
                        <div class="empty-chart">
                            <p>Sem dados neste período</p>
                            <span>Registre refeições para gerar a análise nutricional.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="report-card p-8 space-y-5">
        <div class="section-title">
            <div class="section-icon text-emerald-500"><i data-lucide="sparkles" class="w-6 h-6"></i></div>
            <h2>Conclusão inteligente</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($insights as $insight)
                <div class="inner-panel p-5">
                    <p class="text-sm text-zinc-300 leading-relaxed font-semibold">{{ $insight }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div id="daily-log" class="report-card overflow-hidden">
        <div class="p-8 border-b border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-zinc-900/50">
            <div class="space-y-2">
                <h3 class="text-2xl font-black text-white italic uppercase tracking-tight">Registros do período</h3>
                <p class="text-zinc-400 text-xs font-semibold">Detalhamento diário de alimentação, treino e composição de macros.</p>
            </div>

            <div class="flex items-center gap-6 bg-zinc-950 px-5 py-3 rounded-2xl border border-zinc-800">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <span class="metric-label">Registrado</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-zinc-700"></div>
                    <span class="metric-label">Ausente</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-950/50">
                        <th class="table-head">Data</th>
                        <th class="table-head">Ingestão</th>
                        <th class="table-head">Treino</th>
                        <th class="table-head">Macros</th>
                        <th class="table-head text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach(array_reverse($days) as $date => $day)
                        <tr class="hover:bg-emerald-500/[0.03] transition-all group">
                            <td class="table-cell">
                                <span class="text-sm font-black text-white italic uppercase">{{ $day['label'] }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-3">
                                    <div class="status-icon {{ $day['kcal_in'] > 0 ? 'is-active' : '' }}"><i data-lucide="utensils" class="w-4 h-4"></i></div>
                                    <span class="text-sm font-black {{ $day['kcal_in'] > 0 ? 'text-white' : 'text-zinc-600' }} italic tabular-nums">
                                        {{ $day['kcal_in'] > 0 ? number_format($day['kcal_in'], 0, ',', '.') . ' kcal' : 'Sem registro' }}
                                    </span>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center gap-3">
                                    <div class="status-icon {{ $day['ex_min'] > 0 ? 'is-active' : '' }}"><i data-lucide="zap" class="w-4 h-4"></i></div>
                                    <span class="text-sm font-black {{ $day['ex_min'] > 0 ? 'text-white' : 'text-zinc-600' }} italic tabular-nums">
                                        {{ $day['ex_min'] > 0 ? $day['ex_min'] . ' min' : 'Sem registro' }}
                                    </span>
                                </div>
                            </td>
                            <td class="table-cell">
                                @if($day['kcal_in'] > 0)
                                    <div class="flex items-center gap-2">
                                        <span class="macro-pill">{{ number_format($day['p'], 0, ',', '.') }}P</span>
                                        <span class="macro-pill">{{ number_format($day['c'], 0, ',', '.') }}C</span>
                                        <span class="macro-pill">{{ number_format($day['f'], 0, ',', '.') }}G</span>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-zinc-600">Sem macros</span>
                                @endif
                            </td>
                            <td class="table-cell text-right">
                                @if($day['kcal_in'] > 0 && $day['ex_min'] > 0)
                                    <span class="status-pill text-emerald-400 bg-emerald-500/10 border-emerald-500/20">Completo</span>
                                @elseif($day['kcal_in'] > 0 || $day['ex_min'] > 0)
                                    <span class="status-pill text-amber-400 bg-amber-500/10 border-amber-500/20">Parcial</span>
                                @else
                                    <span class="status-pill text-zinc-500 bg-zinc-950 border-zinc-800">Ausente</span>
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
        const trainingData = {!! json_encode(array_values(array_map(fn($d) => $d['ex_min'], $days))) !!};
        const nutritionData = {!! json_encode(array_values(array_map(fn($d) => $d['kcal_in'], $days))) !!};

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#09090b',
                    titleColor: '#f4f4f5',
                    bodyColor: '#d4d4d8',
                    titleFont: { size: 12, weight: '900' },
                    bodyFont: { size: 11, weight: 'bold' },
                    padding: 12,
                    displayColors: false,
                    borderColor: '#10b98133',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.06)' },
                    ticks: { color: '#71717a', font: { size: 11, weight: '800' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#71717a', font: { size: 10, weight: '800' } }
                }
            }
        };

        const performanceChart = document.getElementById('performanceChart');
        if (performanceChart) {
            new Chart(performanceChart, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Minutos de treino',
                        data: trainingData,
                        borderColor: '#10b981',
                        backgroundColor: (context) => {
                            const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
                            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                            return gradient;
                        },
                        borderWidth: 4,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#09090b',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8
                    }]
                },
                options: chartOptions
            });
        }

        const nutritionChart = document.getElementById('nutritionChart');
        if (nutritionChart) {
            new Chart(nutritionChart, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Calorias consumidas',
                        data: nutritionData,
                        backgroundColor: '#10b981',
                        borderRadius: 10,
                        hoverBackgroundColor: '#34d399',
                        barThickness: labels.length > 30 ? 6 : 16
                    }]
                },
                options: chartOptions
            });
        }
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

    .report-card {
        background: #18181b;
        border: 1px solid #27272a;
        border-radius: 2rem;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.24);
        position: relative;
        overflow: hidden;
    }

    .metric-label {
        color: #a1a1aa;
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .metric-note {
        color: #a1a1aa;
        font-size: 12px;
        font-weight: 700;
        margin-top: 14px;
    }

    .empty-value {
        color: #d4d4d8;
        font-size: 28px;
        font-style: italic;
        font-weight: 900;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .progress-track {
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 999px;
        height: 8px;
        margin-top: 22px;
        overflow: hidden;
    }

    .progress-fill {
        background: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        height: 100%;
    }

    .section-title {
        align-items: center;
        display: flex;
        gap: 16px;
    }

    .section-title h2 {
        color: white;
        font-size: 24px;
        font-style: italic;
        font-weight: 900;
        letter-spacing: 0;
        line-height: 1;
        text-transform: uppercase;
    }

    .section-icon {
        align-items: center;
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 16px;
        display: flex;
        height: 48px;
        justify-content: center;
        width: 48px;
    }

    .inner-panel {
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 1rem;
    }

    .inner-panel:not(.text-center) {
        padding: 24px;
    }

    .panel-value {
        color: white;
        font-size: 30px;
        font-style: italic;
        font-weight: 900;
        margin-top: 8px;
    }

    .panel-value small {
        color: #71717a;
        font-size: 12px;
        font-style: normal;
        margin-left: 4px;
    }

    .empty-panel,
    .empty-chart {
        align-items: center;
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 160px;
        padding: 24px;
        text-align: center;
    }

    .empty-panel p,
    .empty-chart p {
        color: white;
        font-size: 15px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .empty-panel span,
    .empty-chart span {
        color: #a1a1aa;
        font-size: 13px;
        font-weight: 600;
        margin-top: 8px;
        max-width: 420px;
    }

    .chart-box {
        height: 320px;
        position: relative;
        width: 100%;
    }

    .table-head {
        color: #a1a1aa;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.18em;
        padding: 24px 32px;
        text-transform: uppercase;
    }

    .table-cell {
        padding: 24px 32px;
    }

    .status-icon {
        align-items: center;
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 12px;
        color: #52525b;
        display: flex;
        height: 40px;
        justify-content: center;
        width: 40px;
    }

    .status-icon.is-active {
        background: #10b981;
        border-color: #10b981;
        color: #09090b;
    }

    .macro-pill {
        background: #09090b;
        border: 1px solid #27272a;
        border-radius: 8px;
        color: #d4d4d8;
        font-size: 10px;
        font-weight: 900;
        padding: 4px 8px;
    }

    .status-pill {
        border: 1px solid;
        border-radius: 999px;
        display: inline-flex;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.14em;
        padding: 6px 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.16); border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.28); }
</style>
@endsection
