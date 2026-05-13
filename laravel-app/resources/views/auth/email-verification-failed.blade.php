@extends('layouts.app')

@section('title', 'Confirmação de e-mail — ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10 text-center space-y-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-red-500/30 mb-2 shadow-2xl backdrop-blur-xl">
            <i class="fas fa-link-slash text-3xl text-red-400"></i>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">Link inválido ou expirado.</h1>
        <p class="text-zinc-400 text-sm leading-relaxed">
            @if(($motivo ?? '') === 'expirado')
                O prazo deste link terminou. Use o reenvio de confirmação no cadastro ou no login.
            @else
                Este link não é válido ou já foi utilizado.
            @endif
        </p>
        <div class="space-y-4">
            @if(($motivo ?? '') === 'expirado')
                <div class="p-6 bg-zinc-900/50 border border-zinc-800 rounded-3xl backdrop-blur-xl">
                    <form action="{{ route('verification.resend.guest') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="text-left space-y-1.5">
                            <label for="email" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">E-mail para reenvio</label>
                            <input type="email" name="email" id="email" required placeholder="seu@email.com" 
                                value="{{ request()->query('email') }}"
                                class="w-full bg-zinc-800/50 border border-zinc-700/50 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-zinc-600">
                        </div>
                        <button type="submit" class="w-full py-4 bg-zinc-100 hover:bg-white text-zinc-900 font-black rounded-2xl transition-all active:scale-[0.98] text-[10px] uppercase tracking-widest shadow-xl">
                            Reenviar link agora
                        </button>
                    </form>
                </div>
            @endif

            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px]">
                Ir para o login
            </a>
            <a href="{{ route('register') }}" class="block text-[11px] text-zinc-500 font-bold uppercase tracking-widest hover:text-white transition-colors">
                Voltar ao cadastro
            </a>
        </div>
    </div>
</div>
<style>body { background-color: #0b0e14; }</style>
@endsection
