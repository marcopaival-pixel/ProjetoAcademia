@extends('layouts.app')

@section('title', 'Criar Conta — NexShape')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative animate-fade-in">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-user-astronaut text-3xl text-indigo-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Comece sua jornada</h2>
            <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest">Crie sua conta e acesse o próximo nível.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold animate-fade-in">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl">
            <form method="post" action="{{ route('register') }}" class="space-y-6" novalidate>
                @csrf
                <div class="space-y-2">
                    <label for="name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Completo</label>
                    <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-700" 
                        placeholder="Como deseja ser chamado?">
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-700" 
                        placeholder="exemplo@email.com">
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Senha (Mín. 8 caracteres)</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-700" 
                        placeholder="••••••••••••">
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar Senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all placeholder:text-zinc-700" 
                        placeholder="••••••••••••">
                </div>

                <label class="flex items-start gap-4 cursor-pointer group pt-2">
                    <div class="relative flex items-center justify-center mt-0.5">
                        <input type="checkbox" id="terms" name="terms" required class="peer sr-only">
                        <div class="w-5 h-5 rounded bg-zinc-950 border border-white/10 peer-checked:bg-indigo-600 peer-checked:border-indigo-500 transition-colors flex items-center justify-center">
                            <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <span class="text-xs text-zinc-400 leading-tight">
                            Ao me cadastrar, concordo com os <a href="{{ route('legal.terms') }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 font-bold transition-colors">Termos de Uso</a> e a 
                            <a href="{{ route('legal.privacy') }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 font-bold transition-colors">Política de Privacidade (LGPD)</a>.
                        </span>
                    </div>
                </label>

                <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-indigo-600/20 uppercase tracking-[0.2em] text-[10px] mt-4">
                    Cadastrar Agora
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest">
                Já tem uma conta? 
                <a href="{{ route('login') }}" class="text-indigo-500 hover:text-indigo-400 ml-1">Fazer login &rarr;</a>
            </p>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
