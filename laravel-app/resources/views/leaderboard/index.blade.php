@extends('layouts.app')

@section('title', 'Arena NexShape — Coliseu Digital')

@section('content')
@if(auth()->user()->hasPremiumAccess())
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

    <!-- Elite Global Hall of Fame (New Section) -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 rounded-[4rem] p-10 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/10 rounded-full blur-[120px]"></div>
        
        <div class="relative z-10 flex flex-col xl:flex-row gap-12">
            <div class="xl:w-1/3 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[9px] font-black uppercase tracking-widest">
                    <i class="fas fa-crown"></i>
                    Elite Global Hall of Fame
                </div>
                <h2 class="text-4xl font-black text-white leading-tight">Os <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-orange-400">NexElite</span> de Hoje</h2>
                <p class="text-zinc-500 text-sm font-medium leading-relaxed">Este ranking pondera Constância, Força e Disciplina em um único índice de performance humana. Suba de nível e torne-se uma lenda.</p>
                
                <!-- Levels Legend -->
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['Diamond' => 'text-cyan-400', 'Platinum' => 'text-zinc-300', 'Gold' => 'text-amber-400', 'Silver' => 'text-zinc-500'] as $lvl => $col)
                        <div class="flex items-center gap-2 text-[9px] font-black uppercase tracking-tighter">
                            <span class="w-1.5 h-1.5 rounded-full {{ $col }} shadow-[0_0_8px_currentColor]"></span>
                            <span class="text-zinc-600">{{ $lvl }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="xl:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($eliteRanking as $index => $rank)
                    <div class="group relative flex items-center gap-6 p-6 rounded-[2.5rem] border transition-all hover:scale-[1.02] border-white/5 bg-black/30 hover:bg-black/50 overflow-hidden">
                        <!-- Medal Glow -->
                        @if(!empty($rank->medals))
                            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-{{ $rank->medals[0]['color'] }}-500/5 blur-3xl rounded-full"></div>
                        @endif

                        <div class="relative">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl @if($index == 0) bg-amber-500 text-zinc-900 shadow-[0_0_20px_rgba(245,158,11,0.3)] @else bg-zinc-900 text-white @endif">
                                {{ $index + 1 }}
                            </div>
                            @if($rank->is_premium)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center border-2 border-black" title="NexShape Pro">
                                    <i class="fas fa-star text-[8px] text-white"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-white font-black text-lg leading-tight break-words">{{ $rank->name }}</p>
                                <span class="px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[7px] font-black uppercase tracking-widest rounded-md border border-white/5 whitespace-nowrap">
                                    {{ $rank->category }}
                                </span>
                            </div>
                            
                            <!-- Medals Container -->
                            <div class="flex items-center gap-2 mt-2">
                                @forelse($rank->medals as $medal)
                                    <div class="flex items-center gap-1.5 px-2 py-1 bg-{{ $medal['color'] }}-500/10 border border-{{ $medal['color'] }}-500/20 rounded-lg" title="{{ $medal['label'] }}">
                                        <i class="fas fa-{{ $medal['icon'] }} text-[8px] text-{{ $medal['color'] }}-400"></i>
                                        <span class="text-[7px] font-black text-{{ $medal['color'] }}-400 uppercase tracking-tighter">{{ $medal['label'] }}</span>
                                    </div>
                                @empty
                                    <span class="text-[8px] text-zinc-700 font-bold uppercase italic tracking-widest">Sem medalhas</span>
                                @endforelse
                            </div>

                            <div class="flex items-end gap-1 mt-3">
                                <span class="text-2xl font-black text-white tabular-nums">{{ $rank->score }}</span>
                                <span class="text-[9px] text-zinc-600 font-bold uppercase mb-1.5">Elite Score</span>
                            </div>
                        </div>

                        <div class="h-10 w-px bg-white/5 mx-2"></div>

                        <div class="flex flex-col items-center">
                            <span class="text-[8px] font-black uppercase tracking-widest @if($rank->level == 'Diamond') text-cyan-400 @elseif($rank->level == 'Platinum') text-zinc-300 @elseif($rank->level == 'Gold') text-amber-500 @else text-zinc-600 @endif mb-1">
                                {{ $rank->level }}
                            </span>
                            <i class="fas fa-shield-alt text-zinc-800 text-2xl group-hover:text-amber-500/20 transition-colors"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
        
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
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-sm leading-tight break-words">{{ $rank->name }}</p>
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
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-black text-base leading-tight break-words">{{ $rank->user_name }}</p>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest break-words">{{ $rank->exercise_name }}</p>
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
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-sm leading-tight break-words">{{ $rank->name }}</p>
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
@else
{{-- LAYOUT PAYWALL RANKING --}}
<div class="max-w-4xl mx-auto py-20 px-4 text-center">
    <div class="mb-10 inline-flex items-center justify-center w-24 h-24 rounded-[2.5rem] bg-amber-500/10 border border-amber-500/20 text-amber-500 shadow-2xl shadow-amber-500/20">
        <i class="fas fa-trophy text-4xl"></i>
    </div>
    
    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter mb-6 leading-tight">
        Domina a <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">NexArena</span>
    </h1>
    
    <p class="text-xl text-zinc-400 mb-12 max-w-2xl mx-auto font-medium leading-relaxed">
        A glória está reservada para a Elite. Descobre quem são os atletas mais consistentes e fortes da nossa comunidade.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-16 text-left">
        <div class="p-8 rounded-[2.5rem] bg-zinc-900/50 border border-white/5 hover:border-amber-500/30 transition-all flex gap-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                <i class="fas fa-crown"></i>
            </div>
            <div>
                <h3 class="text-white font-bold mb-2">Hall of Fame</h3>
                <p class="text-sm text-zinc-500 leading-relaxed">Acesso ao ranking global ponderado. Vê o teu nível (Diamond, Gold) e compara o teu score com os melhores.</p>
            </div>
        </div>
        
        <div class="p-8 rounded-[2.5rem] bg-zinc-900/50 border border-white/5 hover:border-blue-500/30 transition-all flex gap-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h3 class="text-white font-bold mb-2">Métricas de Força</h3>
                <p class="text-sm text-zinc-500 leading-relaxed">Descobre quem lidera em carga 1RM por exercício e usa a competição como combustível para os teus treinos.</p>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-b from-amber-500 to-orange-600 p-[1px] rounded-2xl inline-block shadow-2xl shadow-amber-600/20">
        <a href="{{ route('plano') }}" class="flex items-center gap-3 px-10 py-5 bg-[#0b0e14] rounded-[15px] text-white hover:bg-transparent transition-all group">
            <i class="fas fa-star text-amber-400"></i>
            <span class="font-black uppercase tracking-[0.2em] text-sm">Entrar no Coliseu Premium</span>
            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    
    <p class="mt-8 text-zinc-600 text-[10px] uppercase font-bold tracking-widest italic">Apenas para atletas de alta performance</p>
</div>
@endif
@endsection
