@extends('layouts.onboarding')

@section('title', 'Bem-vindo ao NexShape')
@section('step_number', 'Início')

@section('onboarding_content')
<div class="space-y-12 animate-fade-up">
    <header class="space-y-6">
        <p class="text-blue-400 font-bold uppercase text-[10px] tracking-[0.4em]">A jornada começa hoje</p>
        <h2 class="text-5xl md:text-6xl font-black text-white tracking-tight leading-tight">
            Evolua com <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">NexShape</span>
        </h2>
        <p class="text-zinc-400 text-lg font-medium max-w-md mx-auto leading-relaxed">
            Estamos prontos para criar o seu plano de saúde inteligente. Vamos começar personalizando seu perfil para resultados precisos.
        </p>
    </header>

    <div class="grid grid-cols-1 gap-4 pt-4">
        <div class="p-6 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-md flex items-center gap-6 group transition-all hover:bg-white/10">
            <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div class="text-left">
                <h4 class="text-white font-bold">Plano Personalizado</h4>
                <p class="text-zinc-500 text-sm">Cálculos precisos baseados na sua biometria.</p>
            </div>
        </div>

        <div class="p-6 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-md flex items-center gap-6 group transition-all hover:bg-white/10">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div class="text-left">
                <h4 class="text-white font-bold">Foco em Performance</h4>
                <p class="text-zinc-500 text-sm">Otimize seus treinos e sua alimentação diária.</p>
            </div>
        </div>
    </div>

    <div class="pt-8">
        <a href="{{ route('onboarding.step1') }}" class="group relative inline-flex items-center justify-center w-full py-5 font-black text-white transition-all duration-300 ease-in-out bg-blue-600 rounded-2xl hover:bg-blue-700 shadow-[0_0_40px_rgba(37,99,235,0.4)] hover:shadow-[0_0_60px_rgba(37,99,235,0.6)] transform hover:-translate-y-1 overflow-hidden">
            <span class="relative z-10 text-base uppercase tracking-[0.2em]">Começar Jornada</span>
            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-400/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
        </a>
    </div>
</div>
@endsection
