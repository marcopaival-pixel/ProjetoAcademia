@extends('layouts.app')

@section('title', 'Conteúdo Premium NexElite')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-6">
    <div class="max-w-xl w-full text-center space-y-10 animate-fade-in">
        <!-- Icon & Badge -->
        <div class="relative inline-block">
            <div class="w-32 h-32 bg-amber-500/10 rounded-[2.5rem] flex items-center justify-center border border-amber-500/20 shadow-2xl shadow-amber-500/10">
                <i class="fas fa-crown text-amber-500 text-5xl"></i>
            </div>
            <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-zinc-950 border border-amber-500 rounded-full flex items-center justify-center">
                <i class="fas fa-lock text-amber-500 text-xs"></i>
            </div>
        </div>

        <!-- Text -->
        <div class="space-y-4">
            <h1 class="text-4xl font-black text-white tracking-tighter">Protocolo <span class="text-amber-500">NexElite</span></h1>
            <p class="text-zinc-500 font-medium">Você tentou acessar: <span class="text-zinc-300 font-bold">"{{ $routine['title'] }}"</span>. Este protocolo faz parte do nosso acervo avançado de bio-recuperação.</p>
        </div>

        <!-- Benefits List -->
        <div class="grid grid-cols-1 gap-3 text-left">
            <div class="p-4 bg-zinc-900/60 border border-white/5 rounded-2xl flex items-center gap-4 group hover:border-amber-500/30 transition-all">
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 text-xs"><i class="fas fa-video"></i></span>
                <span class="text-zinc-400 text-xs font-bold uppercase tracking-widest">Tutoriais em Full HD</span>
            </div>
            <div class="p-4 bg-zinc-900/60 border border-white/5 rounded-2xl flex items-center gap-4 group hover:border-amber-500/30 transition-all">
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 text-xs"><i class="fas fa-bolt"></i></span>
                <span class="text-zinc-400 text-xs font-bold uppercase tracking-widest">Recuperação 2x mais Rápida</span>
            </div>
            <div class="p-4 bg-zinc-900/60 border border-white/5 rounded-2xl flex items-center gap-4 group hover:border-amber-500/30 transition-all">
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 text-xs"><i class="fas fa-chart-line"></i></span>
                <span class="text-zinc-400 text-xs font-bold uppercase tracking-widest">Métricas de Bio-feedback</span>
            </div>
        </div>

        <!-- CTA -->
        <div class="pt-6 space-y-6">
            <button onclick="document.getElementById('premiumModal').style.display='flex'" 
                    class="w-full py-5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-black text-xs uppercase tracking-[0.3em] rounded-2xl shadow-2xl shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all">
                Fazer Upgrade Agora
            </button>
            <a href="{{ route('active-rest.index') }}" class="block text-zinc-600 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">
                Voltar aos Protocolos Gratuitos
            </a>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
