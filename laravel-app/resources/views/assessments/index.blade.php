@extends('layouts.app')

@section('title', 'Avaliações Físicas — NexShape')

@section('content')
<div x-data="{ tab: '{{ $tab }}' }" class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    
    <!-- Futuristic Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tighter uppercase">Hub de <span class="text-blue-500">Evolução</span></h1>
            <p class="text-zinc-500 text-sm font-medium">Análise biométrica e histórico de composição corporal.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('assessments.create') }}" class="px-6 py-3 bg-blue-600 text-white font-black text-xs rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Novo Registro
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Hub Navigation -->
    <div class="flex items-center gap-4 border-b border-white/5 pb-1">
        <button @click="tab = 'dashboard'" 
           :class="tab === 'dashboard' ? 'text-blue-500' : 'text-zinc-500 hover:text-zinc-300'"
           class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative">
           Dashboard
           <div x-show="tab === 'dashboard'" class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 rounded-t-full shadow-[0_-4px_12px_rgba(59,130,246,0.5)]"></div>
        </button>
        <button @click="tab = 'history'" 
           :class="tab === 'history' ? 'text-blue-500' : 'text-zinc-500 hover:text-zinc-300'"
           class="px-8 py-4 text-xs font-black uppercase tracking-[0.2em] transition-all relative">
           Histórico Completo
           <div x-show="tab === 'history'" class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 rounded-t-full shadow-[0_-4px_12px_rgba(59,130,246,0.5)]"></div>
        </button>
    </div>

    <!-- Tab: Dashboard -->
    <div x-show="tab === 'dashboard'" class="space-y-8 animate-fade-in">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Weight Chart -->
            <div class="lg:col-span-2 bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Evolução de Peso (kg)
                    </h3>
                </div>
                <div style="height: 300px;">
                    <canvas id="weightEvolutionChart"></canvas>
                </div>
            </div>

            <!-- Quick Metrics & Status -->
            <div class="space-y-8">
                <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Última Composição
                    </h3>
                    @if($assessments->first())
                        <div class="space-y-6">
                            <div class="flex justify-between items-end border-b border-white/5 pb-4">
                                <span class="text-xs text-zinc-500 font-bold uppercase">Gordura Corporal</span>
                                <span class="text-2xl font-black text-white">{{ $assessments->first()->bf_percent ?? '--' }}<small class="text-xs text-zinc-600 ml-1">%</small></span>
                            </div>
                            <div class="flex justify-between items-end border-b border-white/5 pb-4">
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
                <div class="bg-gradient-to-br from-blue-600/10 to-purple-600/10 border border-white/5 rounded-[2.5rem] p-8">
                    <h4 class="text-white font-black mb-2 leading-tight">Mantenha a <br>frequência!</h4>
                    <p class="text-zinc-500 text-xs mb-6">Registrar seu peso ao menos 3x por semana ajuda a IA a ajustar sua nutrição.</p>
                    <a href="{{ route('assessments.create') }}" class="flex items-center justify-center w-full py-4 bg-white text-zinc-950 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all hover:scale-[1.02]">
                        Lançar Peso de Hoje
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: History -->
    <div x-show="tab === 'history'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in">
        @forelse($assessments as $index => $assessment)
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] overflow-hidden transition-all hover:bg-zinc-900/60 hover:border-blue-500/20 shadow-xl">
                <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-100 transition-opacity">
                    <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" data-confirm-delete>
                        @csrf @method('DELETE')
                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 flex items-center justify-center text-blue-500 border border-blue-500/20 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <div>
                        <h5 class="text-white font-black text-lg leading-none">{{ $assessment->assessment_date->translatedFormat('d \d\e F') }}</h5>
                        <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest mt-1">{{ $assessment->assessment_date->format('Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                        <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">Peso</span>
                        <span class="text-base font-black text-white">{{ $assessment->weight_kg ?? '--' }}</span>
                    </div>
                    <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                        <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">BF</span>
                        <span class="text-base font-black text-blue-400">{{ $assessment->bf_percent ?? '--' }}%</span>
                    </div>
                    <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                        <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">Massa</span>
                        <span class="text-base font-black text-emerald-400">{{ $assessment->muscle_percent ?? '--' }}%</span>
                    </div>
                </div>

                <a href="{{ route('assessments.show', $assessment) }}" class="flex items-center justify-center w-full py-4 bg-zinc-950 border border-white/10 text-zinc-400 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all group-hover:bg-zinc-900 group-hover:text-white group-hover:border-blue-500/30">
                    Detalhes &rarr;
                </a>
            </div>
        @empty
            <div class="col-span-3 py-20 text-center">
                <p class="text-zinc-600 text-sm font-bold uppercase italic tracking-widest">Nenhuma avaliação detalhada registrada.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
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
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#0b0e14',
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
                        ticks: { color: '#3f3f46', font: { size: 9, weight: 'bold' } }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        ticks: { color: '#3f3f46', font: { size: 9, weight: 'bold' } }
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
