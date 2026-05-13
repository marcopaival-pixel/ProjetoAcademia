@extends('layouts.app')

@section('title', 'Detalhes da Avaliação')

@section('content')
<div class="py-10 max-w-6xl mx-auto px-4 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('assessments.index') }}" class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:border-emerald-500/50 transition-all">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tighter uppercase">Detalhes da <span class="text-emerald-500">Avaliação</span></h1>
                <p class="text-zinc-500 text-sm font-medium">Realizada em {{ $assessment->assessment_date->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-premium-button variant="secondary" size="md" href="{{ route('assessments.pdf', $assessment) }}" target="_blank">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> GERAR LAUDO PDF
            </x-premium-button>
            <x-premium-button variant="secondary" size="md" onclick="window.print()">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> IMPRIMIR
            </x-premium-button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna Esquerda: IA e Resumo -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Score de Saúde IA -->
            <x-premium-card title="NexShape Intelligence" icon="brain" iconColor="emerald">
                <div class="flex flex-col items-center py-6">
                    <div class="relative w-32 h-32 mb-4">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-800" />
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-emerald-500" stroke-dasharray="{{ 364 * ($healthScore / 100) }} 364" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-black text-white">{{ $healthScore }}%</span>
                            <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Health Score</span>
                        </div>
                    </div>
                    <p class="text-zinc-400 text-xs text-center px-4">Seu Score de Saúde é baseado na consistência dos seus dados e métricas físicas.</p>
                </div>
            </x-premium-card>

            <!-- Previsão de Evolução -->
            <x-premium-card title="Previsão de Meta" icon="trending-up" iconColor="emerald">
                @if($predictions['possible'])
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                            <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Data Estimada</div>
                            <div class="text-2xl font-black text-white tracking-tight">{{ $predictions['date'] }}</div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-zinc-500">Dias faltantes:</span>
                            <span class="text-white font-bold">{{ $predictions['days'] }} dias</span>
                        </div>
                        <p class="text-zinc-400 text-xs italic">{{ $predictions['message'] }}</p>
                    </div>
                @else
                    <p class="text-zinc-500 text-sm">{{ $predictions['message'] }}</p>
                @endif
            </x-premium-card>

            <!-- Alertas e Riscos -->
            @if(count($risks) > 0)
                <x-premium-card title="Alertas de Saúde" icon="alert-triangle" iconColor="amber">
                    <div class="space-y-3">
                        @foreach($risks as $risk)
                            <div class="flex gap-3 p-3 rounded-xl {{ $risk['type'] === 'danger' ? 'bg-red-500/5 border border-red-500/20' : 'bg-amber-500/5 border border-amber-500/20' }}">
                                <i data-lucide="{{ $risk['type'] === 'danger' ? 'x-circle' : 'alert-circle' }}" class="w-5 h-5 flex-shrink-0 {{ $risk['type'] === 'danger' ? 'text-red-500' : 'text-amber-500' }}"></i>
                                <p class="text-xs text-zinc-300 leading-relaxed">{{ $risk['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-premium-card>
            @endif

            <!-- Interpretação Técnica Bio -->
            @if(count($bioInsights) > 0)
                <x-premium-card title="NexBot Bio Interpretation" icon="microscope" iconColor="emerald">
                    <div class="space-y-4">
                        @foreach($bioInsights as $insight)
                            <div class="p-4 rounded-2xl {{ $insight['level'] === 'danger' ? 'bg-red-500/5 border border-red-500/10' : ($insight['level'] === 'warning' ? 'bg-amber-500/5 border border-amber-500/10' : 'bg-emerald-500/5 border border-emerald-500/10') }}">
                                <h5 class="text-[10px] font-black {{ $insight['level'] === 'danger' ? 'text-red-500' : ($insight['level'] === 'warning' ? 'text-amber-500' : 'text-emerald-500') }} uppercase tracking-widest mb-1">{{ $insight['title'] }}</h5>
                                <p class="text-xs text-zinc-300 leading-relaxed">{{ $insight['message'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-premium-card>
            @endif
        </div>

        <!-- Coluna Direita: Métricas Detalhadas -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Métricas Principais -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-6 hover:border-emerald-500/30 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="weight" class="w-5 h-5"></i>
                    </div>
                    <div class="text-2xl font-black text-white">{{ $assessment->weight_kg }}<span class="text-xs text-zinc-500 ml-1">kg</span></div>
                    <div class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Peso Atual</div>
                </div>
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-6 hover:border-emerald-500/30 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="percent" class="w-5 h-5"></i>
                    </div>
                    <div class="text-2xl font-black text-white">{{ $assessment->bf_percent ?? '--' }}<span class="text-xs text-zinc-500 ml-1">%</span></div>
                    <div class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Gordura (BF)</div>
                </div>
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-6 hover:border-emerald-500/30 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                    <div class="text-2xl font-black text-white">{{ $assessment->muscle_percent ?? '--' }}<span class="text-xs text-zinc-500 ml-1">%</span></div>
                    <div class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Massa Muscular</div>
                </div>
                <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-6 hover:border-emerald-500/30 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="heart" class="w-5 h-5"></i>
                    </div>
                    <div class="text-2xl font-black text-white text-sm">{{ $assessment->blood_pressure ?? '--' }}</div>
                    <div class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">P. Arterial</div>
                </div>
            </div>

            <!-- Medidas Antropométricas -->
            <x-premium-card title="Medidas Corporais (cm)" icon="ruler" iconColor="emerald">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-4">
                    @php
                        $measurements = [
                            'Pescoço' => $assessment->neck,
                            'Tórax' => $assessment->chest,
                            'Cintura' => $assessment->waist,
                            'Abdomen' => $assessment->abdomen,
                            'Quadril' => $assessment->hips,
                            'Braço E.' => $assessment->bicep_l,
                            'Braço D.' => $assessment->bicep_r,
                            'Ant. Esq.' => $assessment->forearm_l,
                            'Ant. Dir.' => $assessment->forearm_r,
                            'Coxa E.' => $assessment->thigh_l,
                            'Coxa D.' => $assessment->thigh_r,
                            'Pant. E.' => $assessment->calf_l,
                            'Pant. D.' => $assessment->calf_r,
                        ];
                    @endphp

                    @foreach($measurements as $label => $val)
                        <div class="flex flex-col border-l-2 border-zinc-800 pl-4 py-1">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $label }}</span>
                            <span class="text-lg font-bold text-white">{{ $val ?? '--' }}</span>
                        </div>
                    @endforeach
                </div>
            </x-premium-card>
            
            <!-- Bioimpedância Premium -->
            @if($assessment->icw_l || $assessment->segmental_lean_trunk)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <x-premium-card title="Composição Detalhada" icon="microscope" iconColor="emerald">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 rounded-xl bg-zinc-950 border border-zinc-800">
                                <span class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Água Total (ICW+ECW)</span>
                                <span class="text-lg font-black text-white">{{ ($assessment->icw_l ?? 0) + ($assessment->ecw_l ?? 0) }} L</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 rounded-xl bg-zinc-950 border border-zinc-800">
                                    <div class="text-[8px] text-zinc-500 font-black uppercase mb-1">Massa Magra Seca</div>
                                    <div class="text-sm font-black text-white">{{ $assessment->dry_lean_mass_kg ?? '--' }} kg</div>
                                </div>
                                <div class="p-3 rounded-xl bg-zinc-950 border border-zinc-800">
                                    <div class="text-[8px] text-zinc-500 font-black uppercase mb-1">Ângulo de Fase</div>
                                    <div class="text-sm font-black text-emerald-500">{{ $assessment->phase_angle ?? '--' }}°</div>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-zinc-950 border border-zinc-800">
                                <div class="flex justify-between mb-2">
                                    <span class="text-[10px] text-zinc-500 font-black uppercase">Gordura Visceral</span>
                                    <span class="text-xs font-black {{ ($assessment->visceral_fat_level ?? 0) > 10 ? 'text-red-500' : 'text-emerald-500' }}">Nível {{ $assessment->visceral_fat_level ?? '--' }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-zinc-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-emerald-500 to-red-500" style="width: {{ (($assessment->visceral_fat_level ?? 0) / 20) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </x-premium-card>

                    <x-premium-card title="Análise Segmental" icon="layout-grid" iconColor="emerald">
                        <div class="h-64 relative">
                            <canvas id="segmentalChart"></canvas>
                        </div>
                    </x-premium-card>
                </div>
            @endif

            @if($assessment->notes)
                <x-premium-card title="Observações" icon="message-square" iconColor="emerald">
                    <p class="text-zinc-400 text-sm leading-relaxed">{{ $assessment->notes }}</p>
                </x-premium-card>
            @endif

            @if($assessment->ai_suggestions)
                <x-premium-card title="Sugestões NexBot (IA)" icon="sparkles" iconColor="emerald">
                    <div class="space-y-6">
                        <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                            <p class="text-xs text-emerald-500 font-bold italic">"{{ $assessment->ai_suggestions['daily_summary'] ?? '' }}"</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($assessment->ai_suggestions['meals'] ?? [] as $meal)
                                <div class="p-4 rounded-2xl bg-zinc-950 border border-zinc-800">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ $meal['time'] }}</span>
                                        <span class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">{{ $meal['macros_est'] }}</span>
                                    </div>
                                    <h5 class="text-sm font-bold text-white mb-2">{{ $meal['name'] }}</h5>
                                    <ul class="space-y-1">
                                        @foreach($meal['suggestions'] ?? [] as $item)
                                            <li class="text-[11px] text-zinc-400 flex items-center gap-2">
                                                <div class="w-1 h-1 rounded-full bg-emerald-500/50"></div>
                                                {{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-premium-card>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        const ctx = document.getElementById('segmentalChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Braço E.', 'Braço D.', 'Perna D.', 'Perna E.', 'Tronco'],
                    datasets: [{
                        label: 'Massa Magra (kg)',
                        data: [
                            {{ $assessment->segmental_lean_arm_l ?? 0 }},
                            {{ $assessment->segmental_lean_arm_r ?? 0 }},
                            {{ $assessment->segmental_lean_leg_r ?? 0 }},
                            {{ $assessment->segmental_lean_leg_l ?? 0 }},
                            {{ $assessment->segmental_lean_trunk ?? 0 }}
                        ],
                        fill: true,
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgb(16, 185, 129)',
                        pointBackgroundColor: 'rgb(16, 185, 129)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(16, 185, 129)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { color: 'rgba(255,255,255,0.05)' },
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            pointLabels: { color: '#71717a', font: { size: 10, weight: '900' } },
                            ticks: { display: false },
                            suggestedMin: 0
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
