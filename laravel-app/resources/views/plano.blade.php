@extends('layouts.app')

@section('title', 'NexShape Premium — Ultra Performance')

@section('content')
<div class="py-12 space-y-16 animate-fade-in max-w-[1400px] mx-auto px-6">
    <!-- Hero Section -->
    <div class="text-center space-y-6 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest">
            <i class="fas fa-crown text-[8px]"></i>
            Acesso Ilimitado
        </div>
        <h1 class="text-6xl font-black text-white tracking-tighter leading-none">Desbloqueie seu <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Potencial Máximo</span></h1>
        <p class="text-zinc-500 text-lg font-medium">Recursos avançados para quem leva treino, dieta e evolução estética como uma ciência. Simplicidade NexShape, poder absoluto.</p>
    </div>

    @if ($mpFlash !== '')
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $mpFlash }}
        </div>
    @endif

    @if ($isAdministrator)
        <div class="bg-gradient-to-r from-emerald-600/20 to-blue-600/20 border border-emerald-500/20 p-8 rounded-[2.5rem] flex items-center justify-between shadow-2xl">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-black text-2xl tracking-tight">Status: Administrador Master</h3>
                    <p class="text-zinc-500 text-sm">Você tem acesso total a todos os recursos Premium sem necessidade de assinatura.</p>
                </div>
            </div>
            <i class="fas fa-check-double text-4xl text-emerald-500/30"></i>
        </div>
    @elseif ($isPremium)
        <div class="bg-gradient-to-r from-blue-600/20 to-indigo-600/20 border border-blue-500/20 p-8 rounded-[2.5rem] flex items-center justify-between shadow-2xl">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-black text-2xl tracking-tight">NexShape Premium Ativo</h3>
                    <p class="text-zinc-500 text-sm">Obrigado por apoiar a evolução contínua da nossa plataforma!</p>
                </div>
            </div>
            <i class="fas fa-rocket text-4xl text-blue-500/30"></i>
        </div>
    @endif

    <!-- Pricing Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Free Plan -->
        <div class="group bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[3.5rem] flex flex-col items-start transition-all hover:bg-zinc-900/60 ring-1 ring-white/0 hover:ring-white/5">
            <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Plano Standard</h3>
            <div class="flex items-baseline gap-2 mb-10">
                <span class="text-5xl font-black text-white">Grátis</span>
                <span class="text-zinc-600 text-sm font-bold uppercase tracking-widest">Para sempre</span>
            </div>
            
            <div class="flex-grow space-y-6 w-full">
                @foreach(['Diário alimentar e exercícios', 'Metas automáticas padrão', 'Acompanhamento de peso e hidratação', 'Assistente IA (limite diário)'] as $feature)
                <div class="flex items-center gap-4 text-zinc-400 group-hover:text-zinc-300 transition-colors">
                    <i class="fas fa-check text-[10px] text-zinc-700"></i>
                    <span class="text-sm font-medium">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
            
            <div class="mt-12 w-full pt-8 border-t border-white/5">
                <div class="text-center p-4 bg-zinc-950 rounded-2xl text-zinc-500 text-[10px] font-black uppercase tracking-widest border border-white/5 italic">
                    Plano Ativo por Padrão
                </div>
            </div>
        </div>

        <!-- Premium Plan -->
        <div class="group relative bg-zinc-900/60 backdrop-blur-3xl border border-blue-500/30 p-12 rounded-[3.5rem] flex flex-col items-start shadow-2xl transition-all hover:scale-[1.01]">
            <div class="absolute -top-5 -right-5 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white text-[10px] font-black px-6 py-2 rounded-full shadow-2xl tracking-widest uppercase">
                Recomendado
            </div>
            
            <h3 class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-4 italic">Performance Elite</h3>
            <div class="flex items-baseline gap-3 mb-10">
                <div class="flex flex-col">
                    <span class="text-sm text-zinc-500 font-bold line-through">R$ 29,90</span>
                    <span class="text-5xl font-black text-white">R$ 19,90</span>
                </div>
                <span class="text-zinc-600 text-sm font-bold uppercase tracking-widest">/ mensal</span>
            </div>

            <div class="flex-grow space-y-6 w-full mb-12">
                @php
                    $pfeatures = [
                        ['icon' => 'fas fa-bullseye', 't' => 'Macros Personalizados', 'd' => 'Defina metas Exatas de P/C/G.'],
                        ['icon' => 'fas fa-file-export', 't' => 'Exportação VIP', 'd' => 'CSV para Nutricionistas e Médicos.'],
                        ['icon' => 'fas fa-robot', 't' => 'Chat IA Ilimitado', 'd' => 'Consultoria 24h sem restrições.'],
                        ['icon' => 'fas fa-copy', 't' => 'Modelos Dinâmicos', 'd' => 'Copie dias inteiros com 1 clique.'],
                        ['icon' => 'fas fa-file-pdf', 't' => 'NexShape BI (PDF)', 'd' => 'Relatórios profissionais mensais.']
                    ];
                @endphp

                @foreach($pfeatures as $f)
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 transition-all group-hover:bg-white/10 group-hover:shadow-lg group-hover:shadow-blue-600/5">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/10 flex items-center justify-center text-blue-400 flex-shrink-0">
                        <i class="{{ $f['icon'] }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-white font-black text-sm leading-tight">{{ $f['t'] }}</p>
                        <p class="text-zinc-500 text-[10px] font-medium leading-relaxed">{{ $f['d'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            @if (! $isPremium && ! $isAdministrator)
            <div class="w-full space-y-4">
                <form method="post" action="{{ route('mp.start') }}">
                    @csrf
                    <input type="hidden" name="plan" value="monthly">
                    <input type="hidden" name="checkout" value="subscribe">
                    <button type="submit" class="w-full py-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-blue-400 hover:text-white transition-all shadow-2xl flex items-center justify-center gap-3">
                        <i class="fas fa-credit-card text-xs"></i>
                        ASSINAR NEXSHAPE PREMIUM
                    </button>
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest text-center mt-4">
                        <span class="text-blue-500">Checkout Seguro </span> • Cancela quando quiser no Mercado Pago
                    </p>
                </form>

                <div class="pt-6 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Plano Anual (Econômico)</span>
                    <form method="post" action="{{ route('mp.start') }}">
                        @csrf
                        <input type="hidden" name="plan" value="yearly">
                        <input type="hidden" name="checkout" value="subscribe">
                        <button type="submit" class="text-blue-400 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">
                            Ver Anual (R$ 149,90) &rarr;
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Security & Support -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-10 border-t border-white/5">
        <div class="text-center p-8 space-y-3">
             <i class="fas fa-shield-alt text-3xl text-zinc-800"></i>
             <h4 class="text-white font-black text-sm tracking-tight">Segurança Total</h4>
             <p class="text-zinc-600 text-xs leading-relaxed">Assinatura processada via Mercado Pago. Seus dados nunca tocam nossos servidores.</p>
        </div>
        <div class="text-center p-8 space-y-3">
             <i class="fas fa-undo text-3xl text-zinc-800"></i>
             <h4 class="text-white font-black text-sm tracking-tight">Cancelamento Flexível</h4>
             <p class="text-zinc-600 text-xs leading-relaxed">Um clique para parar. Simples, justo e direto, sem letras miúdas ou taxas ocultas.</p>
        </div>
        <div class="text-center p-8 space-y-3">
             <i class="fas fa-headset text-3xl text-zinc-800"></i>
             <h4 class="text-white font-black text-sm tracking-tight">Suporte Prioritário</h4>
             <p class="text-zinc-600 text-xs leading-relaxed">Membros Premium têm fila fast-track no suporte técnico e sugestão de recursos.</p>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
