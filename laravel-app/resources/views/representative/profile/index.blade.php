@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="space-y-8 animate-fade-in pb-10">
    <div>
        <h1 class="text-3xl font-black text-white uppercase italic">Meu <span class="text-emerald-500">Perfil</span></h1>
        <p class="text-zinc-500 text-sm mt-1">Gerencie seus dados pessoais, credenciais e privacidade.</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 p-4 rounded-2xl font-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('representative.profile.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        {{-- Bloco 1: Dados Pessoais --}}
        <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] p-8">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight flex items-center gap-2">
                <i data-lucide="user" class="w-5 h-5 text-emerald-500"></i> Dados Pessoais
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome Completo</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">E-mail</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Chave PIX (Para Recebimento de Comissões)</label>
                    <input type="text" name="pix_key" value="{{ auth()->user()->pix_key ?? '' }}" placeholder="CPF, E-mail, Celular ou Aleatória" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
            </div>
        </div>

        {{-- Bloco 2: Segurança & Acesso --}}
        <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] p-8">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight flex items-center gap-2">
                <i data-lucide="lock" class="w-5 h-5 text-emerald-500"></i> Segurança & Acesso
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nova Senha (deixe em branco para não alterar)</label>
                    <input type="password" name="password" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
            </div>
        </div>

        {{-- Bloco 3: Privacidade (LGPD) --}}
        <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] p-8">
            <h2 class="text-xl font-black text-white mb-6 uppercase tracking-tight flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i> Privacidade (LGPD)
            </h2>
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl">
                    <div class="pt-1">
                        <input type="checkbox" name="marketing_consent" value="1" {{ auth()->user()->marketing_consent ? 'checked' : '' }} class="w-4 h-4 text-emerald-500 bg-zinc-900 border-zinc-700 rounded focus:ring-emerald-500 focus:ring-2">
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">Comunicações e Ofertas</p>
                        <p class="text-xs text-zinc-500 mt-1">Concordo em receber e-mails sobre novidades, campanhas de incentivo e comunicações do sistema.</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4 p-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl">
                    <div class="pt-1">
                        <i data-lucide="download" class="w-4 h-4 text-zinc-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">Exportação de Dados</p>
                        <p class="text-xs text-zinc-500 mt-1 mb-2">Você tem o direito de solicitar uma cópia de todas as suas informações armazenadas na plataforma.</p>
                        <a href="#" class="text-[10px] font-black uppercase tracking-widest text-emerald-500 hover:text-emerald-400">Solicitar Meus Dados</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-zinc-950 px-8 py-4 rounded-xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg shadow-emerald-500/20">
                Salvar Todas as Alterações
            </button>
        </div>
    </form>
</div>
@endsection
