@extends('layouts.app')

@section('title', 'Sala de Troféus')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center border border-amber-500/20">
                    <i class="fa-solid fa-medal text-amber-500"></i>
                </div>
                Minhas Conquistas
            </h1>
            <p class="text-zinc-400 mt-2 font-medium">Bata metas de treino e dieta para colecionar prêmios (Exclusivo Premium).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($badges as $key => $badge)
        <div class="bg-zinc-900 border {{ $badge['unlocked'] ? 'border-zinc-700' : 'border-zinc-800/50' }} rounded-[2rem] p-6 relative overflow-hidden group transition-all {{ $badge['unlocked'] ? 'hover:scale-105 shadow-xl shadow-zinc-950' : 'opacity-60 grayscale' }}">
            @if($badge['unlocked'])
                <div class="absolute -right-10 -top-10 w-32 h-32 {{ str_replace('text', 'bg', $badge['color']) }}/20 rounded-full blur-2xl group-hover:blur-3xl transition-all"></div>
                <div class="absolute top-4 right-4 text-[10px] text-zinc-500 font-bold uppercase tracking-widest"><i class="fa-solid fa-check text-emerald-500 mr-1"></i> Desbloqueado</div>
            @else
                <div class="absolute top-4 right-4 text-[10px] text-zinc-600 font-bold uppercase tracking-widest"><i class="fa-solid fa-lock mr-1"></i> Trancado</div>
            @endif

            <div class="w-16 h-16 {{ $badge['unlocked'] ? $badge['bg'] : 'bg-zinc-800' }} rounded-2xl flex items-center justify-center text-2xl {{ $badge['unlocked'] ? $badge['color'] : 'text-zinc-600' }} mb-5 border {{ $badge['unlocked'] ? '' : 'border-zinc-700/50' }}">
                <i class="{{ $badge['icon'] }}"></i>
            </div>

            <h3 class="text-xl font-black text-white mb-2">{{ $badge['title'] }}</h3>
            <p class="text-zinc-400 text-sm leading-relaxed">{{ $badge['description'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="mt-16 bg-zinc-900/50 border border-zinc-800 rounded-3xl p-8 text-center max-w-2xl mx-auto">
        <i class="fa-solid fa-star text-4xl text-amber-500 mb-4 opacity-50"></i>
        <h3 class="text-lg font-bold text-white mb-2">Novos desafios em breve!</h3>
        <p class="text-zinc-400 text-sm">A cada mês liberamos novas badges temporárias para você colecionar. Mantenha seu ritmo e preste atenção aos desafios da comunidade.</p>
    </div>
</div>
@endsection
