@extends('layouts.app')

@section('title', 'Acompanhar Cadastro — NexShape')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-10 relative z-10">
        <div class="text-center space-y-6">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-[2rem] bg-zinc-900 border border-emerald-500/20 shadow-3xl backdrop-blur-xl transform -rotate-6">
                <i data-lucide="search" class="w-10 h-10 text-emerald-500"></i>
            </div>
            
            <div class="space-y-4">
                <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Acompanhar <span class="text-emerald-500">Cadastro</span></h2>
                <p class="text-sm text-zinc-500 font-medium leading-relaxed italic">
                    Insira seu E-mail ou CPF para consultar o andamento do seu cadastro de representante.
                </p>
            </div>
        </div>

        <div class="p-8 rounded-[2.5rem] bg-zinc-900/50 border border-zinc-800 backdrop-blur-xl space-y-8">
            <form action="{{ route('registration.search') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <div class="relative group">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-4 mb-2 block">E-mail ou CPF</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="ex: 000.000.000-00" required
                                class="w-full bg-zinc-950/50 border border-zinc-800 focus:border-emerald-500/50 rounded-2xl px-6 py-4 text-zinc-300 placeholder:text-zinc-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/5 transition-all text-sm font-medium">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2">
                                <i data-lucide="user" class="w-4 h-4 text-zinc-700 group-focus-within:text-emerald-500 transition-colors"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-[11px] font-black uppercase tracking-[0.3em] rounded-2xl shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-3">
                        Consultar Status
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            @if(isset($user))
                <div class="pt-8 border-t border-zinc-800 animate-slide-up">
                    <div class="text-center space-y-6">
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Resultado da Consulta</p>
                        
                        @if($user->status === 'APROVADO' || $user->status === 'active' || $user->status === 'ATIVO')
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-xs text-emerald-500 font-black uppercase tracking-[0.2em]">Cadastro Aprovado</span>
                                </div>
                                <p class="text-sm text-zinc-400 font-medium italic leading-relaxed px-4">
                                    Parabéns! Seu cadastro foi aprovado e você já pode acessar o painel do representante.
                                </p>
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-zinc-950 border border-zinc-800 hover:border-emerald-500/30 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl transition-all">
                                    Acessar Painel
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-500"></i>
                                </a>
                            </div>
                        @elseif($user->isRegistrationRejected())
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-red-500/10 border border-red-500/20">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    <span class="text-xs text-red-500 font-black uppercase tracking-[0.2em]">Cadastro Reprovado</span>
                                </div>
                                <p class="text-sm text-zinc-400 font-medium italic leading-relaxed px-4">
                                    Infelizmente seu cadastro não foi aprovado no momento. Entre em contato com nosso suporte para mais detalhes.
                                </p>
                                <a href="mailto:suporte@nexshape.com.br" class="inline-flex items-center gap-3 px-8 py-4 bg-zinc-950 border border-zinc-800 hover:border-red-500/30 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl transition-all">
                                    Contactar Suporte
                                    <i data-lucide="mail" class="w-4 h-4 text-red-500"></i>
                                </a>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-amber-500/10 border border-amber-500/20">
                                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                                    <span class="text-xs text-amber-500 font-black uppercase tracking-[0.2em]">Aguardando Aprovação</span>
                                </div>
                                <p class="text-sm text-zinc-400 font-medium italic leading-relaxed px-4">
                                    Seu cadastro ainda está sendo analisado por nossa equipe. Você receberá um e-mail assim que houver uma atualização.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif(isset($search))
                <div class="pt-8 border-t border-zinc-800 text-center animate-slide-up">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-950 border border-zinc-800 mb-4">
                        <i data-lucide="help-circle" class="w-6 h-6 text-zinc-600"></i>
                    </div>
                    <p class="text-sm text-zinc-500 font-medium italic leading-relaxed">
                        Nenhum cadastro encontrado para os dados informados. <br>
                        Verifique se digitou corretamente ou realize um novo cadastro.
                    </p>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-center gap-8">
            <a href="{{ route('login') }}" class="text-[10px] text-zinc-500 hover:text-emerald-500 font-black uppercase tracking-[0.3em] transition-colors">Voltar ao Login</a>
            <div class="w-1 h-1 rounded-full bg-zinc-800"></div>
            <a href="{{ route('register') }}" class="text-[10px] text-zinc-500 hover:text-emerald-500 font-black uppercase tracking-[0.3em] transition-colors">Novo Cadastro</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
