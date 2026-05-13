@extends('layouts.app')

@section('title', 'Planos de Assinatura — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --brand-primary-glow: {{ $branding['primary_color'] }}30;
    }
    
    .glass-card {
        background: rgba(10, 12, 18, 0.8);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    }

    .btn-upgrade {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        box-shadow: 0 15px 35px -5px var(--brand-primary-glow);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-upgrade:hover {
        box-shadow: 0 20px 45px -2px var(--brand-primary-glow);
        transform: translateY(-4px) scale(1.02);
    }

    .nav-bar-blur {
        background: rgba(10, 12, 18, 0.85);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
    }

    .animate-entry {
        animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div class="py-12 space-y-16 animate-entry mx-auto px-4 md:px-6"
     x-data="{ 
        selectedPlan: null,
        pagamentoAtivo: {{ $pagamentoAtivo ? 'true' : 'false' }},
        availablePlans: {{ Js::from($availablePlans) }}
     }">
    <!-- Background Effects -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-blue-500/10 blur-[150px] rounded-full"></div>
        <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] bg-emerald-500/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="relative z-10 py-12 px-6 max-w-[1200px] mx-auto space-y-16 animate-entry">
        <!-- Header -->
        <header class="flex flex-col items-center text-center space-y-6">
            <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 text-[9px] font-black uppercase tracking-[0.2em] shadow-inner">
                <i data-lucide="crown" class="w-3 h-3"></i>
                Performance Exclusiva
            </div>
            <h1 class="text-5xl md:text-6xl font-black text-white tracking-tighter leading-tight uppercase italic text-center">
                Escolha seu nível de <span class="text-blue-500">Evolução</span>
            </h1>
            <p class="text-zinc-500 text-sm font-medium max-w-2xl leading-relaxed text-center">
                Além do acompanhamento clínico com {{ $professional->name }}, desbloqueie o ecossistema completo de alta performance da NexShape com descontos exclusivos para pacientes.
            </p>
        </header>

        <!-- Pricing Matrix (Grid) -->
        <div x-show="availablePlans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="plan in availablePlans" :key="plan.id">
                <div class="group glass-card rounded-[3.5rem] p-10 flex flex-col items-start transition-all hover:bg-zinc-900 hover:border-[var(--brand-primary)]/30 relative overflow-hidden"
                     :class="plan.recommended ? 'border-[var(--brand-primary)]/40 ring-1 ring-[var(--brand-primary)]/20' : ''">
                    
                    <!-- Recommendation Badge -->
                    <div x-show="plan.recommended" class="absolute top-0 right-0 p-8">
                        <span class="bg-[var(--brand-primary)] text-zinc-950 text-[8px] font-black px-4 py-1.5 rounded-full shadow-2xl tracking-widest uppercase italic">Elite</span>
                    </div>

                    <div class="space-y-1 mb-8">
                        <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest italic" x-text="plan.name"></h3>
                        <p class="text-[8px] text-zinc-700 font-black uppercase tracking-widest">Protocolo de Aluno</p>
                    </div>
                    
                    <!-- Price -->
                    <div class="flex items-baseline gap-2 mb-10">
                        <template x-if="pagamentoAtivo">
                            <div class="flex flex-col">
                                <span x-show="plan.original_price > plan.patient_price" 
                                      class="text-[10px] text-zinc-600 font-black line-through mb-1" 
                                      x-text="'R$ ' + parseFloat(plan.original_price).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-zinc-600 text-xs font-black uppercase">R$</span>
                                    <span class="text-5xl font-black text-white tracking-tighter" x-text="parseFloat(plan.patient_price).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!pagamentoAtivo">
                            <div class="flex flex-col">
                                <span class="text-2xl font-black text-zinc-500 uppercase tracking-tighter">Sob Consulta</span>
                                <span class="text-[8px] text-zinc-700 font-black uppercase tracking-widest mt-1">Cobrança desativada</span>
                            </div>
                        </template>
                        <span x-show="pagamentoAtivo" class="text-zinc-700 text-[9px] font-black uppercase tracking-widest">/ mês</span>
                    </div>

                    <!-- AI Credits Badge -->
                    <div class="w-full p-6 rounded-3xl bg-zinc-950/50 border border-white/5 shadow-inner mb-10 group-hover:border-[var(--brand-primary)]/10 transition-all">
                        <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-3">Créditos de IA Mensais</p>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-[var(--brand-primary)] animate-pulse shadow-[0_0_8px_var(--brand-primary-glow)]"></div>
                            <span class="text-white font-black text-xl tabular-nums" x-text="plan.ai_credits"></span>
                            <span class="text-zinc-700 text-[9px] font-black uppercase tracking-widest">Créditos de IA</span>
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="w-full space-y-4 mb-12 flex-grow">
                        <template x-for="feature in plan.features.slice(0, 5)" :key="feature.label">
                            <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                                <div class="w-5 h-5 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 flex-shrink-0">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-tight" x-text="feature.label"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="w-full pt-8 border-t border-white/5 space-y-4">
                        <button @click="pagamentoAtivo ? window.location.href = '{{ route('checkout.index', '') }}/' + plan.id : null" 
                                :disabled="!pagamentoAtivo"
                                :class="!pagamentoAtivo ? 'opacity-50 grayscale cursor-not-allowed' : ''"
                                class="w-full py-5 rounded-[2rem] btn-upgrade text-white font-black text-[10px] uppercase tracking-[0.2em] shadow-xl">
                            <span x-text="pagamentoAtivo ? 'COMEÇAR AGORA' : 'SISTEMA EM MANUTENÇÃO'"></span>
                        </button>
                        <button @click="selectedPlan = plan" class="w-full text-center text-[8px] text-zinc-600 font-black uppercase tracking-widest hover:text-white transition-colors">
                            Ver Detalhes do Protocolo
                        </button>
                    </div>
                    
                    <div class="mt-6 w-full text-center" x-show="plan.savings > 0">
                        <p class="text-[8px] text-[var(--brand-primary)] font-black uppercase tracking-[0.2em] italic opacity-50 group-hover:opacity-100 transition-opacity">
                            Economia de R$ <span x-text="parseFloat(plan.savings).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                        </p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="availablePlans.length === 0" class="glass-card rounded-[3.5rem] p-16 text-center space-y-6">
            <div class="w-20 h-20 bg-zinc-950 rounded-3xl flex items-center justify-center mx-auto border border-white/5 shadow-2xl">
                <i class="fas fa-crown text-3xl text-zinc-800"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Planos Indisponíveis</h3>
                <p class="text-zinc-500 text-sm font-medium">No momento não há planos de upgrade disponíveis para o seu perfil. Entre em contato com a clínica para mais informações.</p>
            </div>
            <a href="{{ route('patient.portal') }}" class="inline-flex px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all">
                Voltar ao Início
            </a>
        </div>

        <!-- Benefits/Trust Footer -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-10 pt-20 border-t border-white/5">
            <div class="text-center p-8 space-y-4 glass-card rounded-[2.5rem] group hover:border-[var(--brand-primary)]/10 transition-all">
                <i class="fas fa-shield-alt text-2xl text-zinc-800 group-hover:text-[var(--brand-primary)] transition-colors text-center block mx-auto"></i>
                <div class="space-y-1">
                    <h4 class="text-white font-black text-[10px] tracking-widest uppercase italic">Segurança Total</h4>
                    <p class="text-zinc-600 text-[8px] leading-relaxed uppercase font-black tracking-tighter">Processamento criptografado via Mercado Pago.</p>
                </div>
            </div>
            <div class="text-center p-8 space-y-4 glass-card rounded-[2.5rem] group hover:border-[var(--brand-primary)]/10 transition-all">
                <i class="fas fa-sync text-2xl text-zinc-800 group-hover:text-[var(--brand-primary)] transition-colors text-center block mx-auto"></i>
                <div class="space-y-1">
                    <h4 class="text-white font-black text-[10px] tracking-widest uppercase italic">Sem Fidelidade</h4>
                    <p class="text-zinc-600 text-[8px] leading-relaxed uppercase font-black tracking-tighter">Cancele ou altere seu plano quando desejar.</p>
                </div>
            </div>
            <div class="text-center p-8 space-y-4 glass-card rounded-[2.5rem] group hover:border-[var(--brand-primary)]/10 transition-all">
                <i class="fas fa-rocket text-2xl text-zinc-800 group-hover:text-[var(--brand-primary)] transition-colors text-center block mx-auto"></i>
                <div class="space-y-1">
                    <h4 class="text-white font-black text-[10px] tracking-widest uppercase italic">Acesso Unificado</h4>
                    <p class="text-zinc-600 text-[8px] leading-relaxed uppercase font-black tracking-tighter">Mantenha todo seu histórico clínico em um só lugar.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal de Descrição do Plano -->
    <div x-show="selectedPlan" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-zinc-950/95 backdrop-blur-xl"
         @keydown.escape.window="selectedPlan = null">
        
        <div class="glass-card w-full max-w-2xl rounded-[3.5rem] overflow-hidden relative"
             @click.away="selectedPlan = null">
            
            <button @click="selectedPlan = null" class="absolute top-8 right-8 text-zinc-500 hover:text-white transition-all w-10 h-10 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>

            <div class="p-12 space-y-10">
                <header class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full bg-[var(--brand-primary)]/10 text-[var(--brand-primary)] text-[8px] font-black uppercase tracking-widest border border-[var(--brand-primary)]/20">Protocolo Detalhado</span>
                    </div>
                    <h2 class="text-4xl font-black text-white tracking-tighter uppercase italic" x-text="selectedPlan?.name"></h2>
                    <p class="text-zinc-500 text-sm font-medium leading-relaxed italic" x-text="selectedPlan?.description"></p>
                </header>

                <div class="space-y-6">
                    <h4 class="text-[10px] text-zinc-700 font-black uppercase tracking-[0.3em]">Recursos Inclusos</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="feature in (selectedPlan?.features || [])">
                            <div class="flex items-center gap-4 p-5 bg-zinc-950/50 border border-white/5 rounded-2xl group hover:border-[var(--brand-primary)]/20 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-[var(--brand-primary)]/10 text-[var(--brand-primary)] flex items-center justify-center border border-[var(--brand-primary)]/20">
                                    <i class="fas fa-star text-xs"></i>
                                </div>
                                <span class="text-[10px] text-white font-black uppercase tracking-widest" x-text="feature.label"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-10 border-t border-white/5 flex items-center justify-between">
                    <div class="flex items-baseline gap-2">
                        <template x-if="pagamentoAtivo">
                            <div class="flex items-baseline gap-2">
                                <span class="text-zinc-600 text-xs font-black uppercase">R$</span>
                                <span class="text-4xl font-black text-white tabular-nums" x-text="parseFloat(selectedPlan?.patient_price || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                                <span class="text-zinc-700 text-[10px] font-black uppercase tracking-widest">/ mês</span>
                            </div>
                        </template>
                        <template x-if="!pagamentoAtivo">
                            <span class="text-xl font-black text-zinc-500 uppercase tracking-tighter">Sob Consulta</span>
                        </template>
                    </div>
                    <button @click="pagamentoAtivo ? window.location.href = '{{ route('checkout.index', '') }}/' + selectedPlan.id : null" 
                            :disabled="!pagamentoAtivo"
                            :class="!pagamentoAtivo ? 'opacity-50 grayscale cursor-not-allowed' : ''"
                            class="px-10 py-5 btn-upgrade text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl">
                        <span x-text="pagamentoAtivo ? 'ASSINAR AGORA' : 'INDISPONÍVEL'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Tab Bar Navigation -->
    <nav class="fixed bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-md nav-bar-blur border border-white/10 p-3 rounded-[3rem] flex items-center justify-around shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)] z-50">
        <a href="{{ route('patient.portal') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-home-alt text-xl"></i>
            <span class="text-[8px] font-black uppercase">Home</span>
        </a>
        
        @if($hasEvolutionData)
        <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-chart-pie text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Bio</span>
        </a>
        @endif

        <div class="relative">
            <a href="{{ route('patient.plans.index') }}" class="w-[4.5rem] h-[4.5rem] btn-upgrade text-white rounded-full flex items-center justify-center -mt-20 border-[6px] border-[#06080c] shadow-2xl relative group overflow-hidden">
                <i class="fas fa-crown text-2xl"></i>
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>
        </div>

        <a href="{{ route('patient.prescriptions') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-running text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Treino</span>
        </a>

        <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-user-circle text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Perfil</span>
        </a>
    </nav>
</div>
@endsection
