@extends('layouts.app')

@section('title', 'Autenticação — NexShape')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative animate-fade-in">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-fingerprint text-3xl text-blue-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Bem-vindo de volta</h2>
            <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest">Acesse sua conta para continuar sua evolução.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold flex items-center gap-3">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl">
            <form method="post" action="{{ route('login') }}" class="space-y-6" novalidate>
                @csrf
                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Identidade (E-mail)</label>
                    <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                        placeholder="atleta@nexshape.com">
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Chave de Acesso</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                        placeholder="••••••••••••">
                </div>

                <div class="flex items-center justify-end">
                    <a href="#" class="text-[10px] text-blue-500 font-bold uppercase tracking-widest hover:text-blue-400 transition-colors">Esqueceu sua senha?</a>
                </div>

                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px]">
                    Autenticar no Sistema
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/5"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-[#0b0e14] text-zinc-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-white/5">Acesso Expresso</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" class="flex items-center justify-center gap-3 px-4 py-4 border border-white/5 bg-zinc-950/50 rounded-2xl hover:bg-white/5 transition-all group">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5" alt="Google">
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest group-hover:text-white">Google</span>
                </button>
                <button type="button" class="flex items-center justify-center gap-3 px-4 py-4 border border-white/5 bg-zinc-950/50 rounded-2xl hover:bg-white/5 transition-all group">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" class="w-5" alt="Facebook">
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest group-hover:text-white">Facebook</span>
                </button>
            </div>
        </div>

        <div class="text-center mt-8">
            <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest">
                Ainda não tem acesso? 
                <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-400 ml-1">Cadastre-se Agora &rarr;</a>
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
