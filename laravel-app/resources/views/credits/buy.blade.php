@extends('layouts.app')

@section('title', 'Comprar Créditos — NexShape')

@section('content')
<div class="py-12 space-y-16 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Hero Section -->
    <div class="text-center space-y-6 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] shadow-inner">
            <i data-lucide="zap" class="w-3 h-3 fill-current"></i>
            Energia do Sistema
        </div>
        <h1 class="text-7xl font-black text-white tracking-tighter leading-tight uppercase">Abasteça sua <span class="text-emerald-500">Evolução</span></h1>
        <p class="text-zinc-500 text-lg font-medium leading-relaxed italic">Adquira pacotes de créditos para processamentos de IA, diagnósticos avançados e recursos exclusivos da plataforma.</p>
    </div>

    @if(session('error'))
        <div class="max-w-xl mx-auto p-6 bg-rose-500/10 border border-rose-500/20 rounded-[2rem] text-rose-400 text-xs font-black animate-fade-in flex items-center gap-4 shadow-xl">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Créditos Atuais -->
    <div class="flex justify-center">
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl flex items-center gap-8 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl"></div>
            <div class="w-16 h-16 bg-zinc-950 border border-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500 shadow-inner">
                <i data-lucide="wallet" class="w-8 h-8"></i>
            </div>
            <div>
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em]">Saldo Atual</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black text-white tabular-nums">{{ number_format(auth()->user()->creditos ?? 0, 0, ',', '.') }}</span>
                    <span class="text-emerald-500 text-xs font-black uppercase tracking-widest italic">Créditos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pacotes Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @foreach($packages as $pkg)
        <div class="group relative bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] flex flex-col items-start transition-all hover:bg-zinc-950 hover:border-emerald-500/30 shadow-2xl relative overflow-hidden">
            
            <div class="absolute -right-4 -top-4 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
            
            <div class="flex justify-between items-start w-full mb-8 relative z-10">
                <div class="space-y-1">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest italic">{{ $pkg->nome }}</h3>
                    <div class="w-12 h-12 bg-zinc-950 border border-zinc-800 rounded-2xl flex items-center justify-center text-emerald-500 mt-4 group-hover:rotate-12 transition-all">
                        <i data-lucide="package" class="w-6 h-6"></i>
                    </div>
                </div>
                @if($pkg->quantidade >= 1000)
                    <span class="bg-emerald-500 text-zinc-950 text-[9px] font-black px-5 py-2 rounded-full shadow-2xl tracking-widest uppercase italic">Melhor Valor</span>
                @endif
            </div>
            
            <div class="flex items-baseline gap-3 mb-10 relative z-10">
                <span class="text-6xl font-black text-white tracking-tighter tabular-nums">{{ number_format($pkg->quantidade, 0, ',', '.') }}</span>
                <span class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">Créditos</span>
            </div>

            <div class="w-full space-y-4 mb-12 relative z-10">
                <div class="p-6 rounded-3xl bg-zinc-950 border border-zinc-800 shadow-inner group-hover:border-emerald-500/10 transition-all">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-3">Investimento Único</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-zinc-500 text-xs font-black uppercase">R$</span>
                        <span class="text-4xl font-black text-white tabular-nums">{{ number_format($pkg->valor, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-4 pt-4 px-2">
                    <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-xs font-black uppercase tracking-tighter italic">Liberação Instantânea</span>
                    </div>
                    <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-xs font-black uppercase tracking-tighter italic">Sem validade de expiração</span>
                    </div>
                    <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-xs font-black uppercase tracking-tighter italic">Válido para todos recursos IA</span>
                    </div>
                </div>
            </div>

            <div class="mt-auto w-full pt-10 border-t border-zinc-800 relative z-10">
                <form action="{{ route('credits.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button type="submit" class="w-full py-5 bg-emerald-500 text-zinc-950 font-black text-[11px] uppercase tracking-[0.3em] rounded-[2rem] hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 flex items-center justify-center gap-3">
                        COMPRAR AGORA
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Security & Trust -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 pt-16 border-t border-zinc-900">
        <div class="text-center p-10 space-y-5 bg-zinc-950/50 rounded-[3rem] border border-zinc-900 shadow-inner group hover:border-emerald-500/10 transition-all">
             <i data-lucide="shield-check" class="w-12 h-12 text-zinc-800 mx-auto group-hover:text-emerald-500 transition-colors"></i>
             <div class="space-y-2">
                 <h4 class="text-white font-black text-sm tracking-widest uppercase italic">Pagamento Blindado</h4>
                 <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-black tracking-tighter">Transações processadas via Mercado Pago com certificado de segurança SSL.</p>
             </div>
        </div>
        <div class="text-center p-10 space-y-5 bg-zinc-950/50 rounded-[3rem] border border-zinc-900 shadow-inner group hover:border-emerald-500/10 transition-all">
             <i data-lucide="zap" class="w-12 h-12 text-zinc-800 mx-auto group-hover:text-emerald-500 transition-colors"></i>
             <div class="space-y-2">
                 <h4 class="text-white font-black text-sm tracking-widest uppercase italic">Entrega Flash</h4>
                 <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-black tracking-tighter">Os créditos são adicionados à sua conta assim que o pagamento for aprovado.</p>
             </div>
        </div>
        <div class="text-center p-10 space-y-5 bg-zinc-950/50 rounded-[3rem] border border-zinc-900 shadow-inner group hover:border-emerald-500/10 transition-all">
             <i data-lucide="help-circle" class="w-12 h-12 text-zinc-800 mx-auto group-hover:text-emerald-500 transition-colors"></i>
             <div class="space-y-2">
                 <h4 class="text-white font-black text-sm tracking-widest uppercase italic">Dúvidas?</h4>
                 <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-black tracking-tighter">Nossa equipe de suporte está pronta para ajudar com qualquer questão técnica ou financeira.</p>
             </div>
        </div>
    </div>
</div>

<style>
    body { 
        background-color: #080a0f;
        background-image:
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }
    
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
