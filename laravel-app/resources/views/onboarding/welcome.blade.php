@extends('layouts.onboarding')

@section('title', 'Bem-vindo ao NexShape')
@section('step_number', 'Início')

@section('onboarding_content')
<div class="space-y-12 animate-fade-up text-center">
    <header class="space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-2">
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.2em]">Protocolo Inicial</span>
        </div>
        <h2 class="text-5xl md:text-6xl font-black text-white tracking-tighter leading-none italic uppercase">
            Evolua com <br>
            <span class="text-emerald-500 italic">NexShape.</span>
        </h2>
        <p class="text-zinc-500 text-lg font-medium max-w-md mx-auto leading-relaxed italic">
            Estamos prontos para arquitetar o seu plano de performance inteligente. Vamos configurar sua biometria.
        </p>
    </header>

    <div class="grid grid-cols-1 gap-4 pt-4">
        <div class="p-8 bg-zinc-900/30 border border-zinc-800 rounded-[2.5rem] backdrop-blur-3xl flex items-center gap-8 group transition-all hover:border-emerald-500/30">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                <i data-lucide="brain-circuit" class="w-7 h-7"></i>
            </div>
            <div class="text-left">
                <h4 class="text-white font-black uppercase tracking-tight italic">Plano Neural</h4>
                <p class="text-zinc-500 text-xs font-medium italic">Cálculos precisos baseados na sua biometria única.</p>
            </div>
        </div>

        <div class="p-8 bg-zinc-900/30 border border-zinc-800 rounded-[2.5rem] backdrop-blur-3xl flex items-center gap-8 group transition-all hover:border-emerald-500/30">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                <i data-lucide="zap" class="w-7 h-7"></i>
            </div>
            <div class="text-left">
                <h4 class="text-white font-black uppercase tracking-tight italic">Performance HUB</h4>
                <p class="text-zinc-500 text-xs font-medium italic">Otimize seus treinos e sua alimentação em segundos.</p>
            </div>
        </div>
    </div>

    <div class="pt-8">
        <a href="{{ route('onboarding.step1') }}" class="group relative inline-flex items-center justify-center w-full py-6 font-black text-zinc-950 transition-all duration-300 ease-in-out bg-emerald-500 rounded-[2rem] hover:bg-emerald-400 shadow-2xl shadow-emerald-500/20 transform hover:-translate-y-1 overflow-hidden uppercase tracking-[0.2em] text-xs">
            Começar Jornada
            <i data-lucide="chevron-right" class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
</div>
@endsection
