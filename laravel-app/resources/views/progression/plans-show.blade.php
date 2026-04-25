@extends('layouts.app')

@section('title', 'Gerenciar Treino - ' . $plan->name)

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
        <div class="space-y-2">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('progression.plans.index') }}" class="text-zinc-500 hover:text-blue-400 transition-colors flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Voltar aos Planos
                </a>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight flex items-center gap-3">
                {{ $plan->name }}
                @if($plan->plan_label)
                    <span class="inline-block px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20 shadow-[0_0_15px_rgba(59,130,246,0.1)] align-middle">
                        {{ $plan->plan_label }}
                    </span>
                @endif
            </h1>
            <p class="text-sm text-zinc-400 font-bold max-w-2xl">{{ $plan->description ?: 'Sem descrição detalhada configurada para este protocolo.' }}</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('progression.plans.pdf', $plan) }}" class="px-5 py-3 bg-zinc-900 border border-white/10 text-zinc-300 font-bold rounded-2xl hover:bg-zinc-800 transition-all text-xs flex items-center gap-2 shadow-xl hover:text-white">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Exportar PDF
            </a>
            <form action="{{ route('progression.plans.duplicate', $plan) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-5 py-3 bg-zinc-900 border border-white/10 text-zinc-300 font-bold rounded-2xl hover:bg-zinc-800 transition-all text-xs flex items-center gap-2 shadow-xl hover:text-white">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Duplicar
                </button>
            </form>
            <a href="{{ route('progression.log', $plan) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-95 text-xs flex items-center gap-2 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Começar Sessão
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Dificuldade</span>
            <span class="text-white font-black">{{ $plan->difficulty ?: 'Não def.' }}</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Frequência</span>
            <span class="text-white font-black">{{ $plan->frequency ? $plan->frequency . 'x/semana' : 'Variável' }}</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Duração Est.</span>
            <span class="text-white font-black">{{ $plan->estimated_duration ?: '--' }} min</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Volume</span>
            @php
                $totalSets = $plan->exercises->sum(function($ex) {
                    return $ex->sets->count();
                });
            @endphp
            <span class="text-white font-black">{{ $totalSets }} Séries</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors hidden lg:block">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Foco</span>
            <span class="text-white font-black">{{ $plan->goal ?: 'Geral' }}</span>
        </div>
    </div>

    <!-- Exercises List -->
    <div class="space-y-6">
        <h2 class="text-sm font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center gap-3">
            <span class="w-8 h-px bg-white/10"></span>
            Fluxo de Execução
            <span class="w-full max-w-[200px] h-px bg-gradient-to-r from-white/10 to-transparent"></span>
        </h2>

        <div class="grid grid-cols-1 gap-4">
            @if(count($plan->exercises) > 0)
                @foreach($plan->exercises as $index => $exercise)
                    <div class="group bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-[2rem] p-6 shadow-xl hover:border-blue-500/20 transition-all flex flex-col md:flex-row md:items-center gap-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-zinc-950 rounded-2xl border border-white/5 text-zinc-500 font-black text-lg group-hover:text-blue-500 group-hover:border-blue-500/20 transition-colors shrink-0 shadow-inner">
                            {{ $index + 1 }}
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-white font-black text-lg mb-1">{{ $exercise->catalogExercise->name }}</h3>
                            <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest">{{ $exercise->catalogExercise->muscle_group }}</p>
                        </div>

                        <div class="flex flex-wrap gap-3 md:justify-end">
                            @foreach($exercise->sets as $set)
                                @php
                                    $typeColor = match($set->set_type) {
                                        'warmup' => 'border-amber-500/30 text-amber-500 bg-amber-500/5',
                                        'drop' => 'border-purple-500/30 text-purple-500 bg-purple-500/5',
                                        'failure' => 'border-red-500/30 text-red-500 bg-red-500/5',
                                        'rest-pause' => 'border-orange-500/30 text-orange-500 bg-orange-500/5',
                                        default => 'border-white/5 text-zinc-500 bg-zinc-950/80'
                                    };
                                    $typeLabel = match($set->set_type) {
                                        'warmup' => 'Aquecimento',
                                        'drop' => 'Drop Set',
                                        'failure' => 'Até a Falha',
                                        'rest-pause' => 'Rest-Pause',
                                        default => $set->set_number . 'ª Série'
                                    };
                                @endphp
                                <div class="flex flex-col p-4 border {{ $typeColor }} rounded-2xl min-w-[110px] group-hover:scale-[1.02] transition-all shadow-lg relative overflow-hidden">
                                    <span class="text-[9px] font-black uppercase tracking-widest mb-2">{{ $typeLabel }}</span>
                                    <div class="space-y-1">
                                        <div class="text-white font-black text-sm flex items-baseline gap-1">
                                            {{ $set->reps_target ?: '-' }} 
                                            <span class="text-[9px] text-zinc-600 font-bold uppercase">Repetições</span>
                                        </div>
                                        @if($set->weight_target > 0)
                                            <div class="text-zinc-400 font-bold text-[10px] flex items-center gap-1">
                                                <svg class="w-3 h-3 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 7h10M5 10h14m-2 7H7"></path></svg>
                                                {{ number_format($set->weight_target, 1) }}kg Carga
                                            </div>
                                        @endif
                                        @if($set->rpe_target)
                                            <div class="text-[9px] text-blue-400 font-bold flex items-center gap-1 mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                Esforço: {{ $set->rpe_target }}/10
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-white/5">
                                        <span class="text-[8px] text-zinc-500 font-bold flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $set->rest_seconds }}s descanso
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-10 text-center bg-zinc-900/20 border border-white/5 border-dashed rounded-[3rem]">
                    <div class="w-16 h-16 mx-auto bg-zinc-900 rounded-full flex items-center justify-center mb-4 border border-white/10 shadow-xl">
                        <svg class="w-8 h-8 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <p class="text-zinc-500 font-medium">Rotina vazia. Nenhum exercício configurado no plano.</p>
                </div>
            @endif
        </div>

        <!-- Legend for Beginners -->
        <div class="mt-12 p-8 bg-zinc-900/20 border border-white/5 rounded-[2.5rem] backdrop-blur-sm">
            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Guia de Treino para Iniciantes
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="space-y-2">
                    <span class="text-[10px] font-black text-amber-500 uppercase">Aquecimento</span>
                    <p class="text-[11px] text-zinc-500 leading-relaxed">Série leve para preparar as articulações e o músculo. Não deve chegar perto do cansaço extremo.</p>
                </div>
                <div class="space-y-2">
                    <span class="text-[10px] font-black text-white uppercase">Série Normal</span>
                    <p class="text-[11px] text-zinc-500 leading-relaxed">Onde o trabalho real acontece. Mantenha a carga desafiadora mas com boa execução.</p>
                </div>
                <div class="space-y-2">
                    <span class="text-[10px] font-black text-blue-500 uppercase">Esforço (RPE)</span>
                    <p class="text-[11px] text-zinc-500 leading-relaxed">Escala de 1 a 10. 10 é o seu limite total, onde não conseguiria fazer mais nenhuma repetição.</p>
                </div>
                <div class="space-y-2">
                    <span class="text-[10px] font-black text-zinc-400 uppercase">Descanso</span>
                    <p class="text-[11px] text-zinc-500 leading-relaxed">Tempo entre uma série e outra. Essencial para que o músculo recupere energia para a próxima.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    body { background-color: #0b0e14; }
</style>
@endsection
