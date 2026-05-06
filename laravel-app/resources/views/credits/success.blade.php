@extends('layouts.app')

@section('title', 'Pagamento Confirmado — NexShape')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-6">
    <div class="max-w-xl w-full text-center space-y-10 animate-fade-in-up">
        
        <!-- Icon Success -->
        <div class="relative inline-block">
            <div class="w-32 h-32 bg-emerald-500 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-emerald-500/20 rotate-12 group">
                <i data-lucide="check-circle" class="w-16 h-16 text-zinc-950 animate-bounce"></i>
            </div>
            <div class="absolute -top-4 -right-4 w-12 h-12 bg-zinc-900 border border-zinc-800 rounded-2xl flex items-center justify-center text-emerald-500 shadow-xl">
                <i data-lucide="zap" class="w-6 h-6 fill-current"></i>
            </div>
        </div>

        <div class="space-y-4">
            <h1 class="text-6xl font-black text-white tracking-tighter uppercase italic leading-none">Energia <span class="text-emerald-500">Restaurada!</span></h1>
            <p class="text-zinc-500 text-lg font-medium leading-relaxed italic">Seu pagamento foi confirmado e os créditos foram injetados em sua conta com sucesso.</p>
        </div>

        <!-- Receipt Card -->
        <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3rem] shadow-3xl text-left space-y-8 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-[80px]"></div>
            
            <div class="flex items-center justify-between border-b border-zinc-800 pb-6 relative z-10">
                <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em]">Resumo da Recarga</span>
                <span class="text-[9px] text-zinc-700 font-bold uppercase tracking-widest">{{ $compra->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="space-y-6 relative z-10">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 text-xs font-black uppercase tracking-widest italic">Pacote Adquirido</span>
                    <span class="text-white text-base font-black uppercase italic">{{ number_format($compra->quantidade, 0, ',', '.') }} Créditos</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 text-xs font-black uppercase tracking-widest italic">Valor Investido</span>
                    <span class="text-emerald-500 text-xl font-black tabular-nums">R$ {{ number_format($compra->valor, 2, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between pt-4 border-t border-zinc-800">
                    <span class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">Gateway</span>
                    <span class="text-zinc-400 text-[10px] font-black uppercase tracking-widest">{{ $compra->gateway }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-12 py-5 bg-emerald-500 text-zinc-950 font-black text-[11px] uppercase tracking-[0.3em] rounded-[2rem] hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10">
                VOLTAR AO PAINEL
            </a>
            <a href="{{ route('chat.page') }}" class="w-full sm:w-auto px-12 py-5 bg-zinc-900 text-white font-black text-[11px] uppercase tracking-[0.3em] rounded-[2rem] hover:bg-zinc-800 transition-all border border-zinc-800">
                USAR IA AGORA
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
