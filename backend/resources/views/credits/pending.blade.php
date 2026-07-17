@extends('layouts.app')

@section('title', 'Pagamento em Processamento — NexShape')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-6">
    <div class="max-w-xl w-full text-center space-y-10 animate-fade-in-up">
        
        <!-- Icon Pending -->
        <div class="relative inline-block">
            <div class="w-32 h-32 bg-amber-500 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-amber-500/20 rotate-12 group">
                <i data-lucide="clock" class="w-16 h-16 text-zinc-950 animate-pulse"></i>
            </div>
            <div class="absolute -top-4 -right-4 w-12 h-12 bg-zinc-900 border border-zinc-800 rounded-2xl flex items-center justify-center text-amber-500 shadow-xl">
                <i data-lucide="refresh-cw" class="w-6 h-6 animate-spin-slow"></i>
            </div>
        </div>

        <div class="space-y-4">
            <h1 class="text-6xl font-black text-white tracking-tighter uppercase italic leading-none">Quase <span class="text-amber-500">Lá!</span></h1>
            <p class="text-zinc-500 text-lg font-medium leading-relaxed italic">Seu pagamento está sendo processado pelo gateway. Assim que for confirmado, seus créditos serão liberados automaticamente.</p>
        </div>

        <!-- Info Card -->
        <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3rem] shadow-3xl text-left space-y-8 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/5 rounded-full blur-[80px]"></div>
            
            <div class="flex items-center justify-between border-b border-zinc-800 pb-6 relative z-10">
                <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em]">Status da Transação</span>
                <span class="text-[9px] text-zinc-700 font-bold uppercase tracking-widest">{{ $compra->id }}</span>
            </div>

            <div class="space-y-6 relative z-10">
                <div class="flex items-center gap-4 text-zinc-400">
                    <div class="w-8 h-8 rounded-lg bg-zinc-950 border border-zinc-800 flex items-center justify-center text-amber-500">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-widest leading-relaxed">
                        Pagamentos via PIX costumam ser confirmados em instantes. Boletos podem levar até 48h.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-12 py-5 bg-amber-500 text-zinc-950 font-black text-[11px] uppercase tracking-[0.3em] rounded-[2rem] hover:bg-amber-400 transition-all shadow-xl shadow-amber-500/10">
                VOLTAR AO PAINEL
            </a>
            <a href="{{ route('credits.buy') }}" class="w-full sm:w-auto px-12 py-5 bg-zinc-900 text-white font-black text-[11px] uppercase tracking-[0.3em] rounded-[2rem] hover:bg-zinc-800 transition-all border border-zinc-800">
                VER OUTROS PACOTES
            </a>
        </div>
    </div>
</div>

<style>
    body { background-color: #080a0f; }
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-spin-slow { animation: spin 4s linear infinite; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
