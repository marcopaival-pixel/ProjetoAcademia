@extends('layouts.onboarding')

@section('title', 'Você não está sozinho — NexShape')
@section('step_number', '06/12')

@section('onboarding_content')
<div class="space-y-12 animate-fade-up">
    <header class="space-y-6 text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-500/10 border-2 border-emerald-500/20 rounded-full text-emerald-400 mb-4">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Você não está sozinho.</h2>
        <p class="text-zinc-400 text-lg max-w-md mx-auto leading-relaxed">
            Muitas pessoas enfrentam os mesmos desafios. No NexShape, simplificamos a ciência para você.
        </p>
    </header>

    <div class="grid grid-cols-1 gap-6">
        <div class="p-8 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-md relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2 relative z-10">Nosso Método</h3>
            <p class="text-zinc-400 leading-relaxed relative z-10">
                Focamos no que realmente importa: o balanço energético. Sem dietas malucas, apenas inteligência e dados.
            </p>
        </div>
    </div>

    <div class="pt-8 text-center">
        <a href="{{ route('onboarding.step3') }}" class="group relative inline-flex items-center justify-center w-full py-5 font-black text-white transition-all duration-300 ease-in-out bg-blue-600 rounded-2xl hover:bg-blue-700 shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1 overflow-hidden">
            <span class="relative z-10">Continuar</span>
            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-400/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
        </a>
    </div>
</div>
@endsection
