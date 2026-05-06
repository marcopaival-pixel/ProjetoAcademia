@extends('layouts.app')

@section('title', 'Cadastro de Representante — NexShape')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-10 relative z-10">
        <div class="text-center space-y-6">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-[2rem] bg-zinc-900 border border-emerald-500/20 shadow-3xl backdrop-blur-xl transform -rotate-6">
                <i data-lucide="handshake" class="w-10 h-10 text-emerald-500"></i>
            </div>
            
            <div class="space-y-4">
                <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Cadastro <span class="text-emerald-500">recebido</span></h2>
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-amber-500/10 border border-amber-500/20">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <span class="text-[10px] text-amber-500 font-black uppercase tracking-[0.2em]">Status: Aguardando aprovação</span>
                </div>
            </div>

            <p class="text-sm text-zinc-500 font-medium leading-relaxed italic">
                Seu cadastro foi enviado para análise. Você será notificado quando for aprovado e seu acesso for liberado.
            </p>
        </div>

        <div class="p-8 rounded-[2.5rem] bg-zinc-900/50 border border-zinc-800 backdrop-blur-xl space-y-6">
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center shrink-0">
                        <i data-lucide="mail" class="w-4 h-4 text-emerald-500"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Próximo Passo</p>
                        <p class="text-[11px] text-zinc-500 font-medium leading-relaxed">Fique atento ao seu e-mail corporativo para a confirmação de acesso.</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('home') }}" class="block w-full py-5 bg-zinc-950 border border-zinc-800 hover:border-emerald-500/30 text-zinc-500 hover:text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl text-center transition-all">
                Voltar ao Início
            </a>
        </div>

        <p class="text-center text-[10px] text-zinc-700 font-black uppercase tracking-[0.3em]">
            Dúvidas? <a href="mailto:suporte@nexshape.com.br" class="text-emerald-500 hover:text-emerald-400 transition-colors">Contacte o Suporte</a>
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
