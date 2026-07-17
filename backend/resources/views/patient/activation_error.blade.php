@extends('layouts.app')

@section('title', 'Erro na Ativação — NexShape')

@section('content')
<div class="h-screen flex items-center justify-center px-4 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-rose-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-rose-500/10 border border-rose-500/20 mb-6 shadow-2xl backdrop-blur-2xl">
            <i class="fas fa-exclamation-triangle text-4xl text-rose-500"></i>
        </div>
        
        <h2 class="text-3xl font-black text-white tracking-tight">Ops! Algo deu errado.</h2>
        
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2rem] shadow-2xl">
            <p class="text-zinc-400 text-sm font-medium leading-relaxed">
                {{ $error ?? 'O link de ativação que você utilizou é inválido ou já expirou.' }}
            </p>
            
            <div class="mt-8 space-y-4">
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-loose">
                    Para resolver, entre em contato com seu profissional e solicite um novo link de ativação.
                </p>
                
                <a href="{{ route('home') }}" class="inline-block w-full py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px]">
                    Voltar para o Início
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; overflow: hidden !important; }
</style>
@endsection
