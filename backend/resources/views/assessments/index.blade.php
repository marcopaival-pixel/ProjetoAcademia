@extends('layouts.app')

@section('title', 'Avaliações Físicas — NexShape')

@section('content')
<div x-data="{ tab: '{{ $tab }}' }" class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <x-plan-over-limit-banner resource="assessments" />
    
    <!-- Futuristic Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase">Hub de <span class="text-emerald-500">Evolução</span></h1>
            <p class="text-zinc-500 text-sm font-medium">Análise biométrica e histórico de composição corporal.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <x-premium-button variant="primary" icon="plus" href="{{ route('assessments.create') }}">
                NOVO REGISTRO
            </x-premium-button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Hub Navigation -->
    <div class="flex items-center gap-4 border-b border-zinc-900 pb-1">
        <button @click="tab = 'dashboard'" 
           :class="tab === 'dashboard' ? 'text-emerald-500' : 'text-zinc-500 hover:text-zinc-300'"
           class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative">
           Dashboard
           <div x-show="tab === 'dashboard'" class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 rounded-t-full shadow-[0_-4px_12px_rgba(16,185,129,0.5)]"></div>
        </button>
        <button {{ !$isPremium ? 'data-premium-locked' : "@click=tab = 'history'" }} 
           :class="tab === 'history' ? 'text-emerald-500' : 'text-zinc-500 hover:text-zinc-300'"
           class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative">
           Histórico Completo
           <i data-lucide="lock" class="w-2.5 h-2.5 absolute top-3 right-4 text-amber-500/50"></i>
           <div x-show="tab === 'history'" class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500 rounded-t-full shadow-[0_-4px_12px_rgba(16,185,129,0.5)]"></div>
        </button>
    </div>

    <!-- Tab: Dashboard -->
    <div x-show="tab === 'dashboard'" class="space-y-8 animate-fade-in">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Weight Chart -->
            <div class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-2xl relative overflow-hidden">
                @if(!$isPremium)
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center p-8 text-center bg-zinc-950/40 backdrop-blur-sm">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-500 flex items-center justify-center border border-amber-500/30 mb-3">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                    <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Gráficos de Evolução Premium</p>
                    <button data-premium-locked class="mt-4 px-6 py-2 bg-zinc-950 border border-zinc-800 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:border-emerald-500/50 transition-all">Desbloquear</button>
                </div>
                @endif
                <div class="flex items-center justify-between mb-8 {{ !$isPremium ? 'blur-sm select-none' : '' }}">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Evolução de Peso (kg)
                    </h3>
                </div>
                <div style="height: 300px;" class="{{ !$isPremium ? 'blur-sm select-none' : '' }}">
                    <canvas id="weightEvolutionChart"></canvas>
                </div>
            </div>

            <!-- NexBot Coach Feed -->
            <div class="lg:col-span-1 space-y-4">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    NexBot Coach Feed
                </h3>
                <div class="space-y-4 overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                    @php
                        $alerts = \App\Models\HealthAlert::where('user_id', auth()->id())->latest()->take(5)->get();
                    @endphp
                    @forelse($alerts as $alert)
                        <div class="p-4 rounded-2xl bg-zinc-900 border {{ $alert->severity === 'danger' ? 'border-red-500/20' : ($alert->severity === 'warning' ? 'border-amber-500/20' : 'border-blue-500/20') }} flex gap-4 animate-fade-in-up">
                            <div class="w-10 h-10 rounded-xl {{ $alert->severity === 'danger' ? 'bg-red-500/10 text-red-500' : ($alert->severity === 'warning' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500') }} flex items-center justify-center flex-shrink-0">
                                <i data-lucide="{{ $alert->severity === 'danger' ? 'alert-triangle' : ($alert->severity === 'warning' ? 'zap' : 'info') }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-300 leading-relaxed">{{ $alert->message }}</p>
                                <span class="text-[8px] text-zinc-600 font-black uppercase mt-1 block">{{ $alert->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center border-2 border-dashed border-zinc-800 rounded-3xl">
                            <i data-lucide="shield-check" class="w-8 h-8 text-zinc-800 mx-auto mb-2"></i>
                            <p class="text-xs text-zinc-600 font-bold uppercase">Tudo em dia!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Metrics & Status -->
            <div class="space-y-8">
                <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-2xl">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Status de Saúde IA
                    </h3>
                    <div class="flex items-center gap-6 mb-8">
                        <div class="relative w-20 h-20">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="6" fill="transparent" class="text-zinc-800" />
                                <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="6" fill="transparent" class="text-emerald-500" stroke-dasharray="{{ 226 * ((auth()->user()->health_score ?? 50) / 100) }} 226" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xl font-black text-white">{{ auth()->user()->health_score ?? '--' }}%</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">NexShape Score</div>
                            <div class="text-sm font-bold text-white">Excelente Progresso!</div>
                        </div>
                    </div>

                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Última Composição
                    </h3>
                    @if($assessments->first())
                        <div class="space-y-6">
                            <div class="flex justify-between items-end border-b border-zinc-800 pb-4">
                                <span class="text-xs text-zinc-500 font-bold uppercase">Gordura Corporal</span>
                                <span class="text-2xl font-black text-white">{{ $assessments->first()->bf_percent ?? '--' }}<small class="text-xs text-zinc-600 ml-1">%</small></span>
                            </div>
                            <div class="flex justify-between items-end border-b border-zinc-800 pb-4">
                                <span class="text-xs text-zinc-500 font-bold uppercase">Massa Muscular</span>
                                <span class="text-2xl font-black text-white">{{ $assessments->first()->muscle_percent ?? '--' }}<small class="text-xs text-zinc-600 ml-1">%</small></span>
                            </div>
                            <div class="pt-4">
                                <p class="text-[10px] text-zinc-600 font-bold uppercase mb-2">Resumo de Medidas</p>
                                <div class="grid grid-cols-2 gap-2 text-[10px] font-black text-zinc-400">
                                    <div class="flex justify-between"><span>Cintura:</span> <span>{{ $assessments->first()->waist }}cm</span></div>
                                    <div class="flex justify-between"><span>Tórax:</span> <span>{{ $assessments->first()->chest }}cm</span></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="py-12 text-center text-zinc-600 text-xs italic">
                            Nenhuma análise disponível.
                        </div>
                    @endif
                </div>

                <!-- Simple Weigh-in Link -->
                <div class="bg-gradient-to-br from-emerald-600/10 to-emerald-900/5 border border-emerald-500/10 rounded-[2rem] p-8 shadow-xl">
                    <h4 class="text-white font-black mb-2 leading-tight">Mantenha a <br>frequência!</h4>
                    <p class="text-zinc-500 text-xs mb-6">Registrar seu peso ao menos 3x por semana ajuda a IA a ajustar sua nutrição.</p>
                    <x-premium-button variant="primary" size="sm" class="w-full" href="{{ route('assessments.create') }}">
                        LANÇAR PESO DE HOJE
                    </x-premium-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: History -->
    <div x-show="tab === 'history'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in">
        @forelse($assessments as $index => $assessment)
            @php
                $isLocked = Auth::user()->isResourceOverLimit('assessments', $assessment->id);
            @endphp
            <div class="group relative bg-zinc-900 border {{ $isLocked ? 'border-rose-500/30' : 'border-zinc-800' }} p-8 rounded-[2rem] overflow-hidden transition-all hover:border-emerald-500/20 shadow-xl">
                @if($isLocked)
                    <div class="absolute inset-0 bg-zinc-950/40 backdrop-blur-[2px] z-10 flex flex-col items-center justify-center p-8 text-center pointer-events-none">
                        <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-500 flex items-center justify-center border border-rose-500/30 mb-4 shadow-lg shadow-rose-500/20">
                            <i data-lucide="lock" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-white font-black uppercase tracking-widest text-xs">Bloqueado</h4>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-2">Limite do plano atingido.</p>
                    </div>
                @endif
                <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-100 transition-opacity">
                    <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" data-confirm-delete>
                        @csrf @method('DELETE')
                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all shadow-lg shadow-emerald-500/10">
                        <i data-lucide="ruler" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h5 class="text-white font-black text-lg leading-none">{{ $assessment->assessment_date->translatedFormat('d \d\e F') }}</h5>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-1">{{ $assessment->assessment_date->format('Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-zinc-950 p-4 rounded-2xl border border-zinc-800 text-center">
                        <span class="block text-[8px] text-zinc-500 font-black uppercase tracking-widest mb-1">Peso</span>
                        <span class="text-base font-black text-white">{{ $assessment->weight_kg ?? '--' }}</span>
                    </div>
                    <div class="bg-zinc-950 p-4 rounded-2xl border border-zinc-800 text-center">
                        <span class="block text-[8px] text-zinc-500 font-black uppercase tracking-widest mb-1">BF</span>
                        <span class="text-base font-black text-emerald-400">{{ $assessment->bf_percent ?? '--' }}%</span>
                    </div>
                    <div class="bg-zinc-950 p-4 rounded-2xl border border-zinc-800 text-center">
                        <span class="block text-[8px] text-zinc-500 font-black uppercase tracking-widest mb-1">Massa</span>
                        <span class="text-base font-black text-emerald-400">{{ $assessment->muscle_percent ?? '--' }}%</span>
                    </div>
                </div>

                <x-premium-button variant="secondary" size="sm" class="w-full" href="{{ route('assessments.show', $assessment) }}">
                    DETALHES
                </x-premium-button>
            </div>
        @empty
            <div class="col-span-3 py-20 text-center">
                <i data-lucide="clipboard-list" class="w-12 h-12 text-zinc-800 mx-auto mb-4"></i>
                <p class="text-zinc-600 text-sm font-bold uppercase italic tracking-widest">Nenhuma avaliação detalhada registrada.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        const ctx = document.getElementById('weightEvolutionChart');
        if (!ctx) return;

        const data = @json($chartData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(i => i.date),
                datasets: [{
                    label: 'Peso (kg)',
                    data: data.map(i => i.weight),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#09090b',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#71717a', font: { family: 'Outfit', size: 9, weight: 'bold' } }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        ticks: { color: '#71717a', font: { family: 'Outfit', size: 9, weight: 'bold' } }
                    }
                }
            }
        });
    });
</script>
@endpush

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
