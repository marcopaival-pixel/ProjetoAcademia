@extends('layouts.app')

@section('title', 'Cadastro não aprovado — NexShape')

@section('content')
<div class="h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 relative animate-fade-in overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-red-500/20 mb-6 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-user-slash text-3xl text-red-400"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Cadastro não aprovado</h2>
            <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                O pedido de acesso à plataforma não foi aceite.
            </p>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl">
            @if (auth()->user()->registration_rejection_note)
                <p class="text-zinc-400 text-sm mb-6 leading-relaxed text-center">
                    <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest block mb-2">Nota do administrador</span>
                    {{ auth()->user()->registration_rejection_note }}
                </p>
            @else
                <p class="text-zinc-400 text-sm mb-6 leading-relaxed text-center">
                    Se acredita que isto foi um erro, contacte o suporte ou o seu estabelecimento.
                </p>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-5 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-3xl transition-all text-[10px] uppercase tracking-widest border border-white/10">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
