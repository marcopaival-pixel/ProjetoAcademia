@extends('layouts.app')

@section('title', 'NexShape — Planos e Performance')

@section('content')
<div class="py-12 space-y-20 animate-fade-in-up max-w-[1400px] mx-auto px-6" 
     x-data="{ 
        activeType: (new URLSearchParams(window.location.search)).get('type') || 'student', 
        selectedPlan: null,
        pagamentoAtivo: {{ $pagamentoAtivo ? 'true' : 'false' }},
        openModal(plan) {
            this.selectedPlan = plan;
        }
     }">
    
    <!-- Hero Section -->
    <div class="text-center space-y-6 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] shadow-inner">
            <i data-lucide="zap" class="w-3 h-3 fill-current"></i>
            Performance Evolutiva
        </div>
        <h1 class="text-7xl font-black text-white tracking-tighter leading-tight uppercase">Escolha seu nível de <span class="text-emerald-500">Poder</span></h1>
        <p class="text-zinc-500 text-lg font-medium leading-relaxed">Planos desenhados para sustentar sua evolução com tecnologia de ponta e infraestrutura de alta precisão.</p>
    </div>

    @if ($mpFlash !== '')
        <div class="p-6 bg-rose-500/10 border border-rose-500/20 rounded-[2rem] text-rose-400 text-xs font-black animate-fade-in flex items-center gap-4 shadow-xl">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ $mpFlash }}
        </div>
    @endif

    <!-- Type Selector -->
    <div class="flex justify-center p-2 bg-zinc-900 border border-zinc-800 rounded-[2.5rem] max-w-xl mx-auto shadow-2xl">
        <button @click="activeType = 'student'" :class="activeType === 'student' ? 'bg-emerald-500 text-zinc-950 shadow-xl' : 'text-zinc-500 hover:text-white'" class="flex-1 py-4 px-6 rounded-[2rem] font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-3">
            <i data-lucide="user-round" class="w-4 h-4"></i> Aluno
        </button>
        <button @click="activeType = 'professional'" :class="activeType === 'professional' ? 'bg-emerald-500 text-zinc-950 shadow-xl' : 'text-zinc-500 hover:text-white'" class="flex-1 py-4 px-6 rounded-[2rem] font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-3">
            <i data-lucide="stethoscope" class="w-4 h-4"></i> Profissional
        </button>
        <button @click="activeType = 'clinic'" :class="activeType === 'clinic' ? 'bg-emerald-500 text-zinc-950 shadow-xl' : 'text-zinc-500 hover:text-white'" class="flex-1 py-4 px-6 rounded-[2rem] font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-3">
            <i data-lucide="building-2" class="w-4 h-4"></i> Clínica
        </button>
    </div>

    <!-- Pricing Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <template x-for="plan in (plans[activeType] || [])" :key="plan.id">
            <div class="group relative bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] flex flex-col items-start transition-all hover:bg-zinc-950 hover:border-emerald-500/30 shadow-2xl relative overflow-hidden"
                 :class="plan.name.includes('Premium') || plan.name.includes('Pro') ? 'border-emerald-500/40' : ''">
                
                <div x-show="plan.name.includes('Premium') || plan.name.includes('Pro')" class="absolute top-0 right-0 p-8">
                    <span class="bg-emerald-500 text-zinc-950 text-[9px] font-black px-5 py-2 rounded-full shadow-2xl tracking-widest uppercase italic">Elite</span>
                </div>

                <div class="flex justify-between items-start w-full mb-8">
                    <div class="space-y-1">
                        <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest italic" x-text="plan.name"></h3>
                        <p class="text-[9px] text-zinc-700 font-bold uppercase" x-text="activeType"></p>
                    </div>
                </div>
                
                <div class="flex items-baseline gap-2 mb-10">
                    <span class="text-zinc-600 text-xs font-black uppercase">R$</span>
                    <span class="text-6xl font-black text-white tracking-tighter" x-text="pagamentoAtivo ? parseFloat(plan.price).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '0,00'"></span>
                    <span class="text-zinc-700 text-[10px] font-black uppercase tracking-widest">/ mês</span>
                </div>

                <div class="w-full space-y-4 mb-12">
                    <div class="p-6 rounded-3xl bg-zinc-950 border border-zinc-800 shadow-inner group-hover:border-emerald-500/10 transition-all">
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-3">Créditos de IA Mensais</p>
                        <div class="flex items-center gap-4">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                            <span class="text-white font-black text-2xl tabular-nums" x-text="plan.ai_credits"></span>
                            <span class="text-zinc-700 text-[10px] font-black uppercase tracking-widest">Sincronizações</span>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 px-2">
                        <template x-if="plan.max_workouts > 0 && plan.max_workouts < 1000">
                            <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                                <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                                <span class="text-xs font-black uppercase tracking-tighter" x-text="plan.max_workouts + ' treinos ativos'"></span>
                            </div>
                        </template>
                        <template x-if="plan.max_workouts >= 9999">
                            <div class="flex items-center gap-4 text-emerald-400">
                                <i data-lucide="infinity" class="w-5 h-5"></i>
                                <span class="text-xs font-black uppercase tracking-widest italic">Treinos Ilimitados</span>
                            </div>
                        </template>

                        <template x-if="plan.max_patients > 0">
                            <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                                <i data-lucide="users" class="w-4 h-4 text-emerald-500"></i>
                                <span class="text-xs font-black uppercase tracking-tighter" x-text="plan.max_patients + ' pacientes'"></span>
                            </div>
                        </template>

                        <template x-if="plan.max_professionals > 0">
                            <div class="flex items-center gap-4 text-zinc-500 group-hover:text-zinc-300 transition-colors">
                                <i data-lucide="user-cog" class="w-4 h-4 text-emerald-500"></i>
                                <span class="text-xs font-black uppercase tracking-tighter" x-text="plan.max_professionals + ' profissionais'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-auto w-full pt-10 border-t border-zinc-800">
                    @auth
                        <template x-if="{{ $user->plan_id }} == plan.id">
                            <div class="text-center py-5 bg-zinc-950 border border-emerald-500/20 rounded-[2rem] text-emerald-500 text-[10px] font-black uppercase tracking-[0.3em] italic shadow-inner">
                                Plano Atual
                            </div>
                        </template>
                        <template x-if="{{ $user->plan_id }} != plan.id">
                            <div class="flex flex-col gap-4">
                                <button @click="window.location.href = '{{ route('checkout.index', '') }}/' + plan.id" 
                                        class="w-full py-5 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] transition-all"
                                        :class="(plan.price > 0 || !pagamentoAtivo) ? 'bg-emerald-500 text-zinc-950 hover:bg-emerald-400 shadow-xl shadow-emerald-500/10' : 'bg-zinc-800 text-zinc-600 border border-zinc-700 cursor-not-allowed'">
                                    <span x-text="(plan.price > 0 || !pagamentoAtivo) ? 'COMEÇAR AGORA' : 'PLANO BASE'"></span>
                                </button>
                                <button @click="openModal(plan)" class="text-center text-[9px] text-zinc-600 font-black uppercase tracking-widest hover:text-white transition-colors">Detalhes do Protocolo</button>
                            </div>
                        </template>
                    @else
                        <button @click="window.location.href = '{{ route('checkout.index', '') }}/' + plan.id" class="w-full py-5 bg-emerald-500 text-zinc-950 font-black text-[11px] uppercase tracking-[0.3em] rounded-[2rem] hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10">
                            COMEÇAR AGORA
                        </button>
                    @endauth
                </div>
            </div>
        </template>
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
         class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-zinc-950/95 backdrop-blur-xl"
         @keydown.escape.window="selectedPlan = null"
         style="display: none;">
        
        <div class="bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-[3.5rem] overflow-hidden shadow-3xl relative animate-fade-in-up"
             @click.away="selectedPlan = null">
            <button @click="selectedPlan = null" class="absolute top-10 right-10 text-zinc-600 hover:text-rose-500 transition-all bg-zinc-950 border border-zinc-800 w-12 h-12 rounded-2xl flex items-center justify-center shadow-xl">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <div class="p-12 space-y-10">
                <header class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[8px] font-black uppercase tracking-widest border border-emerald-500/20" x-text="selectedPlan?.type"></span>
                    </div>
                    <h2 class="text-4xl font-black text-white tracking-tighter uppercase italic" x-text="selectedPlan?.name"></h2>
                </header>

                <div class="bg-zinc-950 border border-zinc-800 p-8 rounded-3xl shadow-inner">
                    <p class="text-zinc-500 leading-relaxed font-medium text-sm italic" x-text="'“' + selectedPlan?.description + '”'"></p>
                </div>

                <div class="space-y-6">
                    <h4 class="text-[10px] text-zinc-700 font-black uppercase tracking-[0.3em]">Recursos do Protocolo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="feature in (selectedPlan?.plan_features || [])">
                            <div class="flex items-center gap-4 p-5 bg-zinc-950/50 border border-zinc-800 rounded-2xl group hover:border-emerald-500/20 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center border border-emerald-500/20">
                                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                </div>
                                <span class="text-[10px] text-white font-black uppercase tracking-widest" x-text="feature.feature_key.replace(/_/g, ' ')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-10 border-t border-zinc-800 flex items-center justify-between">
                    <div class="flex items-baseline gap-2">
                        <span class="text-zinc-700 text-xs font-black uppercase">R$</span>
                        <span class="text-4xl font-black text-white tabular-nums" x-text="pagamentoAtivo ? parseFloat(selectedPlan?.price || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '0,00'"></span>
                    </div>
                    <button @click="selectedPlan = null" class="px-10 py-5 bg-zinc-950 border border-zinc-800 text-zinc-500 hover:text-white hover:bg-zinc-800 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl">
                        FECHAR DETALHES
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Credit Packages -->
    <div class="bg-zinc-900 border border-zinc-800 p-16 rounded-[4rem] relative overflow-hidden shadow-3xl">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/5 rounded-full blur-[100px]"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-16">
            <div class="max-w-xl space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i data-lucide="brain-circuit" class="w-8 h-8"></i>
                    </div>
                    <h2 class="text-4xl font-black text-white tracking-tighter uppercase italic">Rede Neural Ativa</h2>
                </div>
                <p class="text-zinc-500 text-lg leading-relaxed font-medium">Créditos de IA esgotados? Mantenha seu sistema alimentado com nossa rede de alta performance. Adquira pacotes adicionais instantaneamente.</p>
            </div>
            
            <div class="flex flex-wrap justify-center lg:justify-end gap-6">
                @foreach(\App\Models\AiCreditPackage::where('is_active', true)->get() as $pkg)
                <div class="bg-zinc-950 border border-zinc-800 p-8 rounded-[2.5rem] flex flex-col items-center gap-3 hover:border-emerald-500/30 transition-all cursor-pointer group shadow-inner min-w-[180px]">
                    <span class="text-[9px] text-zinc-700 font-black uppercase tracking-widest">{{ $pkg->name }}</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-white group-hover:text-emerald-500 transition-colors tabular-nums">{{ $pkg->credits }}</span>
                        <span class="text-[9px] text-zinc-700 font-black uppercase tracking-widest">Sinc</span>
                    </div>
                    <span class="text-emerald-500 text-[11px] font-black tabular-nums">R$ {{ number_format($pkg->price, 2, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Security & Trust -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 pt-16 border-t border-zinc-900">
        <div class="text-center p-10 space-y-5 bg-zinc-950/50 rounded-[3rem] border border-zinc-900 shadow-inner group hover:border-emerald-500/10 transition-all">
             <i data-lucide="shield-check" class="w-12 h-12 text-zinc-800 mx-auto group-hover:text-emerald-500 transition-colors"></i>
             <div class="space-y-2">
                 <h4 class="text-white font-black text-sm tracking-widest uppercase italic">Segurança Bancária</h4>
                 <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-black tracking-tighter">Processamento via Mercado Pago com criptografia de ponta a ponta.</p>
             </div>
        </div>
        <div class="text-center p-10 space-y-5 bg-zinc-950/50 rounded-[3rem] border border-zinc-900 shadow-inner group hover:border-emerald-500/10 transition-all">
             <i data-lucide="refresh-cw" class="w-12 h-12 text-zinc-800 mx-auto group-hover:text-emerald-500 transition-colors"></i>
             <div class="space-y-2">
                 <h4 class="text-white font-black text-sm tracking-widest uppercase italic">Infra Sustentável</h4>
                 <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-black tracking-tighter">Preços balanceados conforme custo real de infraestrutura e processamento IA.</p>
             </div>
        </div>
        <div class="text-center p-10 space-y-5 bg-zinc-950/50 rounded-[3rem] border border-zinc-900 shadow-inner group hover:border-emerald-500/10 transition-all">
             <i data-lucide="unplug" class="w-12 h-12 text-zinc-800 mx-auto group-hover:text-emerald-500 transition-colors"></i>
             <div class="space-y-2">
                 <h4 class="text-white font-black text-sm tracking-widest uppercase italic">Sem Fidelidade</h4>
                 <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-black tracking-tighter">Cancele ou mude de nível a qualquer momento sem taxas ocultas.</p>
             </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.plans = @json($plans);
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush

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
@endsection
