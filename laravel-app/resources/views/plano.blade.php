@extends('layouts.app')

@section('title', 'NexShape — Planos e Performance')

@section('content')
<div class="py-12 space-y-16 animate-fade-in max-w-[1400px] mx-auto px-6" 
     x-data="{ 
        activeType: 'student', 
        selectedPlan: null,
        openModal(plan) {
            this.selectedPlan = plan;
        }
     }">
    
    <!-- Hero Section -->
    <div class="text-center space-y-6 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest">
            <i class="fas fa-bolt text-[8px]"></i>
            Performance Evolutiva
        </div>
        <h1 class="text-6xl font-black text-white tracking-tighter leading-none">Escolha seu nível de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Poder</span></h1>
        <p class="text-zinc-500 text-lg font-medium">Planos desenhados para sustentar sua evolução com tecnologia de ponta e IA de alta precisão.</p>
    </div>

    @if ($mpFlash !== '')
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $mpFlash }}
        </div>
    @endif

    <!-- Type Selector -->
    <div class="flex justify-center p-2 bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-[2rem] max-w-xl mx-auto">
        <button @click="activeType = 'student'" :class="activeType === 'student' ? 'bg-white text-zinc-900 shadow-xl' : 'text-zinc-500 hover:text-white'" class="flex-1 py-4 px-6 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest transition-all">
            <i class="fas fa-user-graduate mr-2"></i> Aluno
        </button>
        <button @click="activeType = 'professional'" :class="activeType === 'professional' ? 'bg-white text-zinc-900 shadow-xl' : 'text-zinc-500 hover:text-white'" class="flex-1 py-4 px-6 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest transition-all">
            <i class="fas fa-user-md mr-2"></i> Profissional
        </button>
        <button @click="activeType = 'clinic'" :class="activeType === 'clinic' ? 'bg-white text-zinc-900 shadow-xl' : 'text-zinc-500 hover:text-white'" class="flex-1 py-4 px-6 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest transition-all">
            <i class="fas fa-hospital-alt mr-2"></i> Clínica
        </button>
    </div>

    <!-- Pricing Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <template x-for="plan in (plans[activeType] || [])" :key="plan.id">
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] flex flex-col items-start transition-all hover:bg-zinc-900/60 ring-1 ring-white/0 hover:ring-white/10"
                 :class="plan.name.includes('Premium') || plan.name.includes('Pro') ? 'border-blue-500/30' : ''">
                
                <div x-show="plan.name.includes('Premium') || plan.name.includes('Pro')" class="absolute -top-4 -right-4 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white text-[9px] font-black px-5 py-2 rounded-full shadow-2xl tracking-widest uppercase">
                    Elite
                </div>

                <div class="flex justify-between items-start w-full mb-6">
                    <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest italic" x-text="plan.name"></h3>
                    <button @click="openModal(plan)" class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest hover:bg-blue-400 transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                        <i class="fas fa-info-circle"></i>
                        Ver Detalhes
                    </button>
                </div>
                
                <div class="flex items-baseline gap-2 mb-8">
                    <span class="text-gray-400 text-xs font-bold uppercase">R$</span>
                    <span class="text-5xl font-black text-white" x-text="parseFloat(plan.price).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                    <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">/ mês</span>
                </div>

                <div class="w-full space-y-4 mb-10">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                        <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-2">Créditos de IA Mensais</p>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                            <span class="text-white font-black text-lg" x-text="plan.ai_credits"></span>
                            <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">Créditos</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-if="plan.max_workouts > 0 && plan.max_workouts < 1000">
                            <div class="flex items-center gap-3 text-zinc-400">
                                <i class="fas fa-check text-[10px] text-blue-500"></i>
                                <span class="text-xs font-medium" x-text="plan.max_workouts + ' treinos ativos'"></span>
                            </div>
                        </template>
                        <template x-if="plan.max_workouts >= 9999">
                            <div class="flex items-center gap-3 text-zinc-300">
                                <i class="fas fa-check text-[10px] text-emerald-500"></i>
                                <span class="text-xs font-bold uppercase tracking-tighter">Treinos Ilimitados</span>
                            </div>
                        </template>

                        <template x-if="plan.max_patients > 0">
                            <div class="flex items-center gap-3 text-zinc-400">
                                <i class="fas fa-users text-[10px] text-blue-500"></i>
                                <span class="text-xs font-medium" x-text="plan.max_patients + ' pacientes'"></span>
                            </div>
                        </template>

                        <template x-if="plan.max_professionals > 0">
                            <div class="flex items-center gap-3 text-zinc-400">
                                <i class="fas fa-user-md text-[10px] text-blue-500"></i>
                                <span class="text-xs font-medium" x-text="plan.max_professionals + ' profissionais'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-auto w-full pt-8 border-t border-white/5">
                    @auth
                        <template x-if="{{ $user->plan_id }} == plan.id">
                            <div class="text-center p-4 bg-zinc-950 rounded-2xl text-emerald-500 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 italic">
                                Plano Atual
                            </div>
                        </template>
                        <template x-if="{{ $user->plan_id }} != plan.id">
                            <button @click="window.location.href = '{{ route('mp.start') }}?plan_id=' + plan.id" 
                                    class="w-full py-5 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest transition-all"
                                    :class="plan.price > 0 ? 'bg-white text-zinc-900 hover:bg-blue-400 hover:text-white shadow-xl shadow-white/5' : 'bg-zinc-800 text-zinc-500 border border-white/5'">
                                <span x-text="plan.price > 0 ? 'Fazer Upgrade' : 'Plano Gratuito'"></span>
                            </button>
                        </template>
                    @else
                        <button onclick="window.location.href='{{ route('login') }}'" class="w-full py-5 bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest rounded-[1.5rem] hover:bg-blue-400 hover:text-white transition-all shadow-xl shadow-white/5">
                            Começar Agora
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
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-6 bg-black/90 backdrop-blur-xl"
         @keydown.escape.window="selectedPlan = null"
         style="display: none;">
        
        <div class="bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl relative animate-in fade-in zoom-in duration-300"
             @click.away="selectedPlan = null">
            <button @click="selectedPlan = null" class="absolute top-8 right-8 text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>

            <div class="p-12 space-y-8">
                <header class="space-y-2">
                    <span class="text-[10px] text-blue-400 font-black uppercase tracking-widest italic" x-text="selectedPlan?.type"></span>
                    <h2 class="text-4xl font-black text-white tracking-tighter" x-text="selectedPlan?.name"></h2>
                </header>

                <div class="bg-white/5 border border-white/5 p-6 rounded-3xl">
                    <p class="text-zinc-400 leading-relaxed font-medium" x-text="selectedPlan?.description"></p>
                </div>

                <div class="space-y-6">
                    <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">O que está incluído:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="feature in (selectedPlan?.plan_features || [])">
                            <div class="flex items-center gap-3 p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                                <i class="fas fa-star text-[10px] text-blue-500"></i>
                                <span class="text-xs text-white font-bold uppercase tracking-tighter" x-text="feature.feature_key.replace(/_/g, ' ')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-8 border-t border-white/5 flex items-center justify-between">
                    <div class="flex items-baseline gap-1">
                        <span class="text-zinc-500 text-xs font-bold uppercase tracking-widest">R$</span>
                        <span class="text-3xl font-black text-white" x-text="parseFloat(selectedPlan?.price || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                    </div>
                    <button @click="selectedPlan = null" class="px-10 py-4 bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-400 hover:text-white transition-all shadow-xl">
                        Fechar Detalhes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Credit Packages -->
    <div class="bg-zinc-950/40 border border-white/5 p-12 rounded-[4rem] relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="max-w-xl space-y-4">
                <h2 class="text-3xl font-black text-white tracking-tight">Créditos de IA Esgotados?</h2>
                <p class="text-zinc-500">Mantenha seu sistema alimentado com nossa rede neural de alta performance. Adquira pacotes adicionais sem mudar seu plano.</p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-4">
                @foreach(\App\Models\AiCreditPackage::where('is_active', true)->get() as $pkg)
                <div class="bg-zinc-900 border border-white/5 p-6 rounded-[2rem] flex flex-col items-center gap-2 hover:border-blue-500/30 transition-all cursor-pointer group">
                    <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $pkg->name }}</span>
                    <span class="text-2xl font-black text-white group-hover:text-blue-400 transition-colors">{{ $pkg->credits }} <span class="text-xs text-zinc-600 font-bold uppercase tracking-widest">un</span></span>
                    <span class="text-emerald-500 text-xs font-black">R$ {{ number_format($pkg->price, 2, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Security & Trust -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-10 border-t border-white/5">
        <div class="text-center p-8 space-y-3">
             <i class="fas fa-shield-alt text-3xl text-zinc-800"></i>
             <h4 class="text-white font-black text-sm tracking-tight uppercase tracking-widest">Segurança Bancária</h4>
             <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-bold">Processamento via Mercado Pago com criptografia de ponta a ponta.</p>
        </div>
        <div class="text-center p-8 space-y-3">
             <i class="fas fa-sync text-3xl text-zinc-800"></i>
             <h4 class="text-white font-black text-sm tracking-tight uppercase tracking-widest">IA Sustentável</h4>
             <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-bold">Preços balanceados conforme custo real de infraestrutura.</p>
        </div>
        <div class="text-center p-8 space-y-3">
             <i class="fas fa-infinity text-3xl text-zinc-800"></i>
             <h4 class="text-white font-black text-sm tracking-tight uppercase tracking-widest">Sem Fidelidade</h4>
             <p class="text-zinc-600 text-[10px] leading-relaxed uppercase font-bold">Cancele ou mude de plano a qualquer momento sem taxas extras.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.plans = @json($plans);
</script>
@endpush

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
