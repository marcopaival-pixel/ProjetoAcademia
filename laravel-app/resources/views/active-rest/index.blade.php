@extends('layouts.app')

@section('title', 'Descanso Ativo — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in max-w-[1400px] mx-auto px-6">
    <!-- Header Hero -->
    <div class="relative p-12 rounded-[3.5rem] overflow-hidden border border-white/5 bg-zinc-900/40 backdrop-blur-3xl shadow-2xl">
        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-emerald-600/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-leaf text-[8px]"></i>
                    Recovery Protocol Active
                </div>
                <h1 class="text-6xl font-black text-white tracking-tighter leading-none">Descanso <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400 font-black">Ativo</span></h1>
                <p class="text-zinc-500 font-medium max-w-xl">Hoje é dia de cuidar da máquina. Melhore sua mobilidade, oxigenação muscular e acelere a recuperação com nossos protocolos guiados.</p>
            </div>
            
            <div class="flex bg-zinc-950/60 p-6 rounded-3xl border border-white/5 items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-heartbeat text-3xl"></i>
                </div>
                <div>
                    <h4 class="text-white font-black text-lg leading-tight">Bio-Recovery</h4>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Status: Otimizado</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Routines Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($routines as $index => $routine)
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[3rem] overflow-hidden transition-all hover:bg-zinc-900/60 hover:border-emerald-500/30 flex flex-col shadow-xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-white tracking-tight">{{ $routine['title'] }}</h3>
                    <span class="px-3 py-1 rounded-lg bg-zinc-950 border border-white/10 text-zinc-500 text-[10px] font-black uppercase">{{ $routine['duration'] }}</span>
                </div>
                
                <p class="text-zinc-500 text-sm font-medium mb-8 leading-relaxed">{{ $routine['benefit'] }}</p>

                <div class="flex-grow space-y-3 mb-10">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] block mb-4">Protocolo de Execução</span>
                    @foreach($routine['exercises'] as $ex)
                        <div class="flex items-center gap-3 p-3 bg-zinc-950/40 rounded-xl border border-white/5 group-hover:border-emerald-500/10 transition-colors">
                            <i class="fas fa-check-circle text-emerald-500/40 text-xs"></i>
                            <span class="text-zinc-300 text-xs font-bold leading-tight">{{ $ex }}</span>
                        </div>
                    @endforeach
                </div>

                <button class="w-full py-5 bg-zinc-950 border border-white/10 text-emerald-400 font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all hover:bg-emerald-600 hover:text-white hover:border-emerald-600 shadow-xl group-hover:shadow-emerald-600/10">
                    Abrir Tutorial &rarr;
                </button>
            </div>
        @endforeach
    </div>

    <!-- Recovery Insights Footer -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-10">
        @php
            $tips = [
                ['icon' => '💧', 'title' => 'Hidratação Extra', 'msg' => 'Beba 500ml a mais para ajudar na síntese proteica.'],
                ['icon' => '🚶', 'title' => 'Caminhada Leve', 'msg' => '15 min ao ar livre ajudam na oxigenação e circulação.'],
                ['icon' => '💤', 'title' => 'Sono Profundo', 'msg' => 'Tente dormir 30 min mais cedo. O músculo cresce em repouso.']
            ];
        @endphp

        @foreach($tips as $tip)
        <div class="flex gap-6 p-6 bg-emerald-500/5 border border-emerald-500/10 rounded-[2rem] items-start group hover:bg-emerald-500/10 transition-all">
            <span class="text-4xl group-hover:scale-110 transition-transform">{{ $tip['icon'] }}</span>
            <div class="space-y-1">
                <h5 class="text-white font-black text-sm tracking-tight">{{ $tip['title'] }}</h5>
                <p class="text-zinc-500 text-xs leading-relaxed font-medium">{{ $tip['msg'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
