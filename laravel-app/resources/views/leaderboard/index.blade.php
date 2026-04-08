@extends('layouts.app')

@section('title', 'Arena NexShape — Coliseu Digital')

@section('content')
<div class="py-10 space-y-12 animate-fade-in max-w-[1400px] mx-auto px-6">
    <!-- Header Hero -->
    <div class="relative p-12 rounded-[3.5rem] overflow-hidden border border-white/5 bg-zinc-900/40 backdrop-blur-3xl shadow-2xl">
        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-blue-600/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
            <div class="space-y-4 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Arena Global Ativa
                </div>
                <h1 class="text-6xl font-black text-white tracking-tighter leading-none">Ranking de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400 font-black">Atletas</span></h1>
                <p class="text-zinc-500 font-medium max-w-lg">A glória é para quem não para. Explore os líderes de constância, força e disciplina nutricional da comunidade NexShape.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-24 h-24 rounded-3xl bg-zinc-900/60 border border-white/5 flex flex-col items-center justify-center backdrop-blur-md">
                    <span class="text-3xl font-black text-white">{{ count($strengthRanking) + count($consistencyRanking) }}</span>
                    <span class="text-[8px] text-zinc-500 font-bold uppercase">Entradas</span>
                </div>
                <div class="w-24 h-24 rounded-3xl bg-blue-600 text-white flex flex-col items-center justify-center shadow-2xl shadow-blue-600/30">
                    <i class="fas fa-trophy text-2xl mb-1"></i>
                    <span class="text-[8px] font-black uppercase">Hall of Fame</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Triple Threat Ranking Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Consistency Ranking (Left) -->
        <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[3rem] overflow-hidden transition-all hover:bg-zinc-900/60">
            <div class="absolute top-0 right-0 p-8">
                <i class="fas fa-bolt text-blue-500/20 text-6xl group-hover:scale-110 transition-transform"></i>
            </div>
            
            <header class="mb-10">
                <h3 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-500 shadow-lg shadow-blue-500/20 flex items-center justify-center text-white text-sm">
                        <i class="fas fa-calendar-check text-xs"></i>
                    </span>
                    Constância
                </h3>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-2">Dias de treino (últimos 30 dias)</p>
            </header>

            <div class="space-y-4">
                @forelse($consistencyRanking as $index => $rank)
                <div class="flex items-center gap-4 p-4 bg-zinc-950/40 rounded-2xl border border-white/5 transition-all hover:border-blue-500/30 group/item">
                    <div class="w-10 h-10 flex items-center justify-center font-black text-lg @if($index == 0) text-amber-400 @elseif($index == 1) text-zinc-300 @elseif($index == 2) text-amber-700 @else text-zinc-600 @endif">
                        {{ $index + 1 }}º
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-bold text-sm">{{ $rank->name }}</p>
                        <div class="h-1.5 w-full bg-zinc-900 rounded-full mt-2 overflow-hidden border border-white/5">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $rank->workout_days > 0 ? ($rank->workout_days / 31) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="text-blue-400 font-black text-sm tabular-nums">
                        {{ $rank->workout_days }} <span class="text-[10px]">d</span>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center opacity-40 italic text-sm">Calculando dados...</div>
                @endforelse
            </div>
        </div>

        <!-- Power Ranking (Center) -->
        <div class="group relative bg-zinc-900/60 backdrop-blur-3xl border border-blue-500/20 p-8 rounded-[3rem] overflow-hidden shadow-2xl shadow-blue-500/5 transition-all">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-transparent pointer-events-none"></div>
            
            <header class="mb-10">
                <h3 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-500 shadow-lg shadow-amber-500/20 flex items-center justify-center text-zinc-900 text-sm">
                        <i class="fas fa-crown text-xs"></i>
                    </span>
                    Raw Power (1RM)
                </h3>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-2">Maior carga estimada por exercício</p>
            </header>

            <div class="space-y-4">
                @forelse($strengthRanking as $index => $rank)
                <div class="flex items-center gap-4 p-5 @if($index == 0) bg-amber-500/10 border-amber-500/30 @else bg-zinc-950/60 border-white/5 @endif rounded-[1.75rem] border transition-all hover:scale-[1.02]">
                    <div class="w-12 h-12 flex items-center justify-center text-2xl">
                        @if($index == 0) 🥇 @elseif($index == 1) 🥈 @elseif($index == 2) 🥉 @else <span class="text-zinc-700 font-black text-sm">{{ $index+1 }}º</span> @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-black text-base">{{ $rank->user_name }}</p>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">{{ $rank->exercise_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-amber-400 font-black text-lg tabular-nums">{{ number_format($rank->max_one_rm, 1, ',', '.') }}</p>
                        <p class="text-[9px] text-zinc-600 font-black uppercase">kg 1RM</p>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center opacity-40 italic text-sm text-white">Pronto para o pódio?</div>
                @endforelse
            </div>
        </div>

        <!-- Nutrition Ranking (Right) -->
        <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[3rem] overflow-hidden transition-all hover:bg-zinc-900/60">
            <div class="absolute top-0 right-0 p-8">
                <i class="fas fa-leaf text-emerald-500/20 text-6xl group-hover:scale-110 transition-transform"></i>
            </div>
            
            <header class="mb-10">
                <h3 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500 shadow-lg shadow-emerald-500/20 flex items-center justify-center text-white text-sm">
                        <i class="fas fa-apple-alt text-xs"></i>
                    </span>
                    Disciplina
                </h3>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-2">Logs de nutrição (última semana)</p>
            </header>

            <div class="space-y-4">
                @forelse($nutritionRanking as $index => $rank)
                <div class="flex items-center gap-4 p-4 bg-zinc-950/40 rounded-2xl border border-white/5 transition-all hover:border-emerald-500/30">
                    <div class="w-10 h-10 flex items-center justify-center font-black text-lg text-zinc-600">
                        {{ $index + 1 }}º
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-bold text-sm">{{ $rank->name }}</p>
                        <div class="flex gap-1 mt-2">
                            @for($i=0; $i<$rank->logs_count; $i++)
                                <div class="h-1 flex-1 bg-emerald-500/60 rounded-full"></div>
                            @endfor
                            @for($i=$rank->logs_count; $i<7; $i++)
                                <div class="h-1 flex-1 bg-zinc-900 rounded-full"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="text-emerald-400 font-black text-sm tabular-nums">
                        {{ $rank->logs_count }} <span class="text-[10px]">logs</span>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center opacity-40 italic text-sm">Aguardando registros...</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
