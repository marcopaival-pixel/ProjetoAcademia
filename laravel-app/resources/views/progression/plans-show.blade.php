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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Foco Principal</span>
            <span class="text-white font-black">{{ $plan->goal ?: 'Geral' }}</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Lista de Passos</span>
            <span class="text-white font-black">{{ $plan->exercises->count() }} Exercícios</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Volume de Séries</span>
            @php($totalSets = $plan->exercises->sum(fn($ex) => $ex->sets->count()))
            <span class="text-white font-black">{{ $totalSets }} Séries Totais</span>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl shadow-xl hover:bg-zinc-900/60 transition-colors">
            <span class="block text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Última Atualização</span>
            <span class="text-white font-black">{{ $plan->updated_at->format('d/m/Y') }}</span>
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
            @forelse($plan->exercises as $index => $exercise)
                <div class="group bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-[2rem] p-6 shadow-xl hover:border-blue-500/20 transition-all flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex items-center justify-center w-12 h-12 bg-zinc-950 rounded-2xl border border-white/5 text-zinc-500 font-black text-lg group-hover:text-blue-500 group-hover:border-blue-500/20 transition-colors shrink-0 shadow-inner">
                        {{ $index + 1 }}
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="text-white font-black text-lg mb-1">{{ $exercise->catalogExercise->name }}</h3>
                        <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest">{{ $exercise->catalogExercise->muscle_group }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2 md:justify-end">
                        @foreach($exercise->sets as $setIndex => $set)
                            <div class="flex flex-col items-center justify-center p-3 bg-zinc-950/80 border border-white/5 rounded-xl min-w-[70px] group-hover:border-white/10 transition-colors">
                                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Série {{ $set->set_number }}</span>
                                <span class="text-white font-bold text-sm">{{ $set->reps_target ?: '-' }} <span class="text-[10px] text-zinc-600">reps</span></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-10 text-center bg-zinc-900/20 border border-white/5 border-dashed rounded-[3rem]">
                    <div class="w-16 h-16 mx-auto bg-zinc-900 rounded-full flex items-center justify-center mb-4 border border-white/10 shadow-xl">
                        <svg class="w-8 h-8 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <p class="text-zinc-500 font-medium">Rotina vazia. Nenhum exercício configurado no plano.</p>
                </div>
            @endforelse
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
