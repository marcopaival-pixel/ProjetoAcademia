@extends('layouts.admin')

@section('content')
<div class="p-6" x-data="{ selectedPlan: null }">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                Gestão de Planos de Assinatura
            </h1>
            <p class="text-gray-400 mt-1 uppercase text-[10px] font-black tracking-widest">Configure os níveis de acesso e preços do sistema</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="fas fa-plus"></i> Novo Plano
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group overflow-hidden relative flex flex-col h-full">
            <!-- Decorative Background Icon -->
            <div class="absolute -right-4 -bottom-4 text-white/5 text-8xl transform -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fas fa-gem"></i>
            </div>

            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-12 h-12 {{ $plan->status === 'active' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-gray-500/10 text-gray-500 border-gray-500/20' }} rounded-2xl flex items-center justify-center text-xl border">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="flex gap-2">
                    <button @click="selectedPlan = @js($plan->load('planFeatures'))" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-indigo-600 transition-all" title="Ver Detalhes">
                        <i class="fas fa-eye text-xs"></i>
                    </button>
                    <a href="{{ route('admin.plans.edit', $plan->id) }}" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-blue-600 transition-all" title="Editar">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.plans.toggle-status', $plan->id) }}" method="POST"
                          @if($plan->status === 'active')
                          data-confirm-delete="true"
                          data-confirm-title="Desativar Plano"
                          data-confirm-message="Tem certeza que deseja desativar o plano '{{ $plan->name }}'? Novos usuários não poderão assinar este plano."
                          @endif>
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center {{ $plan->status === 'active' ? 'text-emerald-400 hover:bg-emerald-600' : 'text-red-400 hover:bg-red-600' }} hover:text-white transition-all" title="{{ $plan->status === 'active' ? 'Desativar' : 'Ativar' }}">
                            <i class="fas {{ $plan->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }} text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="relative z-10 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $plan->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                        {{ $plan->status === 'active' ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-6 uppercase tracking-wider font-medium">Tipo: {{ strtoupper($plan->type) }}</p>
                
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-gray-400 text-xs font-bold uppercase">R$</span>
                    <span class="text-4xl font-black text-white leading-none">{{ number_format($plan->price, 2, ',', '.') }}</span>
                    <span class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">/mês</span>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-white/10 mt-auto">
                    <div class="flex flex-col">
                        <span class="text-2xl font-black text-white leading-none">{{ $plan->plan_features_count }}</span>
                        <span class="text-[9px] text-gray-500 uppercase font-black mt-1">Funcionalidades</span>
                    </div>
                    <div class="flex flex-col text-right">
                        <span class="text-2xl font-black text-blue-400 leading-none">{{ $plan->ai_credits }}</span>
                        <span class="text-[9px] text-gray-500 uppercase font-black mt-1">Créditos IA</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal de Visualização de Detalhes (Admin) -->
    <div x-show="selectedPlan" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-6 bg-black/80 backdrop-blur-md"
         style="display: none;">
        
        <div class="bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl relative"
             @click.away="selectedPlan = null">
            
            <button @click="selectedPlan = null" class="absolute top-6 right-6 text-gray-500 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>

            <div class="p-10 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-500/10 rounded-[1.5rem] flex items-center justify-center text-3xl text-blue-400 border border-blue-500/20">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-white tracking-tight" x-text="selectedPlan?.name"></h2>
                        <div class="flex gap-2 items-center mt-1">
                            <span class="text-[10px] text-blue-400 font-black uppercase tracking-widest" x-text="selectedPlan?.type"></span>
                            <span class="w-1 h-1 rounded-full bg-zinc-700"></span>
                            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest" x-text="'ID: ' + selectedPlan?.id"></span>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white/5 border border-white/5 rounded-3xl space-y-4">
                    <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Descrição do Plano</h4>
                    <p class="text-zinc-300 text-sm leading-relaxed" x-text="selectedPlan?.description || 'Nenhuma descrição informada para este plano.'"></p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Preço</span>
                        <span class="text-lg font-black text-white" x-text="'R$ ' + parseFloat(selectedPlan?.price || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})"></span>
                    </div>
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Créditos IA</span>
                        <span class="text-lg font-black text-blue-400" x-text="selectedPlan?.ai_credits"></span>
                    </div>
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Max Treinos</span>
                        <span class="text-lg font-black text-white" x-text="selectedPlan?.max_workouts"></span>
                    </div>
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Max Dietas</span>
                        <span class="text-lg font-black text-white" x-text="selectedPlan?.max_diets"></span>
                    </div>
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Max Avaliações</span>
                        <span class="text-lg font-black text-white" x-text="selectedPlan?.max_assessments"></span>
                    </div>
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Max Pacientes</span>
                        <span class="text-lg font-black text-white" x-text="selectedPlan?.max_patients"></span>
                    </div>
                    <div class="p-4 bg-zinc-950/50 border border-white/5 rounded-2xl col-span-2 md:col-span-1">
                        <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Max Profissionais</span>
                        <span class="text-lg font-black text-white" x-text="selectedPlan?.max_professionals"></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Recursos Ativos</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="feature in (selectedPlan?.plan_features || [])">
                            <div class="flex items-center gap-3 p-3 bg-zinc-950/50 border border-white/5 rounded-xl">
                                <i class="fas fa-check-circle text-[10px] text-emerald-500"></i>
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest" x-text="feature.feature_key.replace(/_/g, ' ')"></span>
                            </div>
                        </template>
                        <template x-if="!selectedPlan?.plan_features || selectedPlan?.plan_features.length === 0">
                            <div class="col-span-2 text-center py-4 text-zinc-600 text-[10px] font-black uppercase tracking-widest italic border border-dashed border-white/10 rounded-xl">
                                Nenhuma funcionalidade específica registrada.
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-6 border-t border-white/5 flex justify-end gap-4">
                    <a :href="'{{ url('admin/plans') }}/' + selectedPlan?.id + '/edit'" class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all">
                        Editar Plano
                    </a>
                    <button @click="selectedPlan = null" class="px-8 py-3 bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-400 hover:text-white transition-all shadow-xl">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
