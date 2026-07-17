@extends('layouts.app')

@section('title', 'Troca de Senha Obrigatória')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center p-6">
    <div class="w-full max-w-md space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-500/20 mb-4">
                <i class="fas fa-user-shield text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Segurança da Conta</h1>
            <p class="text-zinc-500 text-sm">Sua senha foi resetada pelo administrador. Para sua segurança, você deve definir uma nova senha agora.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl">
            <form action="{{ route('password.change.force.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nova Senha</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required autofocus
                            class="w-full bg-zinc-950 border border-white/5 p-4 pl-12 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-amber-500/50 transition-all @error('password') border-red-500/50 @enderror">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 text-xs"></i>
                    </div>
                    @error('password')
                        <p class="text-[10px] text-red-500 font-bold uppercase mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar Nova Senha</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 pl-12 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                        <i class="fas fa-check-circle absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 text-xs"></i>
                    </div>
                </div>

                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                    <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                        <i class="fas fa-info-circle mr-1 text-amber-500"></i> Requisitos: Mínimo 8 caracteres, letra maiúscula, número e caractere especial.
                    </p>
                </div>

                <button type="submit" class="w-full py-4 bg-amber-600 text-black font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-amber-500 transition-all shadow-lg shadow-amber-600/10">
                    Atualizar Senha e Entrar
                </button>
            </form>
        </div>

        <!-- Support Info -->
        <p class="text-center text-[10px] text-zinc-600 uppercase font-bold tracking-widest">
            Dúvidas? Entre em contato com o suporte técnico.
        </p>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
