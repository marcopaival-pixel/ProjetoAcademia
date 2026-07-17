@extends('layouts.app')

@section('title', 'Em Breve — NexShape')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-6">
    <div class="relative group max-w-2xl w-full">
        <!-- Glow Effect Backdrop -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[3rem] blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
        
        <!-- Main Content Glass Card -->
        <div class="relative bg-zinc-950/80 backdrop-blur-3xl border border-white/10 p-12 md:p-20 rounded-[3rem] shadow-2xl text-center space-y-10 overflow-hidden">
            <!-- Animated Background Shapes -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

            <!-- Icon/Visual -->
            <div class="relative inline-flex mb-4">
                <div class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl flex items-center justify-center text-white shadow-2xl shadow-blue-500/40 animate-bounce-slow">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="absolute -top-2 -right-2 flex h-6 w-6">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-6 w-6 bg-blue-500"></span>
                </span>
            </div>

            <!-- Typography -->
            <div class="space-y-4">
                <h1 class="text-5xl md:text-6xl font-black text-white italic tracking-tighter leading-tight">
                    Módulo em <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Construção</span>
                </h1>
                <p class="text-zinc-500 text-lg font-medium max-w-md mx-auto">
                    Estamos refinando os algoritmos de inteligência para entregar a melhor experiência em gestão de performance.
                </p>
            </div>

            <!-- Progress Indicator -->
            <div class="max-w-xs mx-auto space-y-3">
                <div class="flex justify-between text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600">
                    <span>Otimização em curso</span>
                    <span class="text-blue-400">85% Concluído</span>
                </div>
                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full animate-progress-load"></div>
                </div>
            </div>

            <!-- Action -->
            <div class="pt-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-white text-black font-black rounded-2xl hover:bg-zinc-200 transition-all shadow-xl hover:scale-105 group/btn">
                    <svg class="w-5 h-5 transition-transform group-hover/btn:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes progress-load {
    0% { width: 0%; }
    100% { width: 85%; }
}
.animate-progress-load {
    animation: progress-load 2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
.animate-bounce-slow {
    animation: bounce 3s infinite;
}
@keyframes bounce {
    0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
    50% { transform: none; animation-timing-function: cubic-bezier(0,0,0.2,1); }
}
</style>
@endsection
