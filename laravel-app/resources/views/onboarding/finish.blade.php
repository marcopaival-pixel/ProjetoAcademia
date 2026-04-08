@extends('layouts.onboarding')

@section('title', 'Parabéns! — NexShape')
@section('step_number', 'Concluído')

@section('onboarding_content')
<div class="space-y-12 animate-fade-up text-center">
    <header class="space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-500/20 border-2 border-emerald-500/30 rounded-full text-emerald-400 mb-4 animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-5xl font-black text-white tracking-tight">Você conseguiu!</h2>
        <div class="space-y-2">
            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.4em]">Sua meta diária personalizada</p>
            @php
                $calories = $data['daily_calorie_target'] ?? 1500;
            @endphp
            <div class="flex flex-col items-center">
                <span class="text-8xl font-black text-blue-500 tracking-tighter shadow-blue-500/20 drop-shadow-2xl">{{ number_format($calories, 0, ',', '.') }}</span>
                <span class="px-4 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black rounded-lg uppercase mt-2 tracking-widest border border-blue-500/20">calorias / dia</span>
            </div>
        </div>
    </header>

    <div class="p-8 bg-white/5 rounded-[32px] border border-white/10 space-y-6 backdrop-blur-md relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
        </div>
        
        <p class="text-base text-zinc-300 leading-relaxed relative z-10">
            Com este plano, você está no caminho para atingir sua meta de <span class="text-white font-black italic">{{ $data['target_weight'] ?? 65 }} kg</span> com foco em <span class="text-blue-400 font-bold">{{ $data['weekly_goal'] ?? '0,5 kg por semana' }}</span>.
        </p>
        
        <div class="h-2 w-full bg-white/10 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full w-[25%] shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
        </div>
    </div>

    <div class="pt-8">
        <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center justify-center w-full py-5 font-black text-white transition-all duration-300 ease-in-out bg-emerald-600 rounded-2xl hover:bg-emerald-700 shadow-[0_0_40px_rgba(16,185,129,0.3)] hover:shadow-[0_0_60px_rgba(16,185,129,0.5)] transform hover:-translate-y-1 overflow-hidden">
            <span class="relative z-10 uppercase tracking-[0.2em]">Explorar Dashboard</span>
        </a>
        <p class="text-zinc-600 text-[10px] font-bold mt-8 uppercase tracking-widest leading-loose">
            Seu plano foi gerado dinamicamente com base em<br>biometria e nível de atividade física.
        </p>
    </div>
</div>
@endsection
