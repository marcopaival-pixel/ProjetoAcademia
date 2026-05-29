@extends('layouts.onboarding-premium')

@section('title', 'Planos')
@section('step_title', 'Escolha seu Plano')
@section('step_description', 'Selecione o plano que melhor atende ao seu volume de operações e recursos desejados.')

@section('content')
<form action="{{ route('onboarding-premium.step.save', 7) }}" method="POST" class="space-y-12" x-data="{ 
    selectedPlan: null,
    plans: [
        { id: 1, name: 'Start', price: 'R$ 97', features: ['Até 50 Alunos', 'Suporte Básico', 'IA Limitada'] },
        { id: 2, name: 'Pro', price: 'R$ 197', features: ['Alunos Ilimitados', 'Suporte Prioritário', 'IA Avançada', 'Marca Branca'], recommended: true },
        { id: 3, name: 'Elite', price: 'R$ 497', features: ['Múltiplas Unidades', 'Gestão de Franquia', 'API Customizada', 'Consultoria'] }
    ]
}">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <template x-for="plan in plans" :key="plan.id">
            <div class="relative group cursor-pointer h-full" @click="selectedPlan = plan.id">
                <input type="radio" name="plan_id" :value="plan.id" class="hidden" :checked="selectedPlan === plan.id" required>
                
                <div class="h-full p-8 rounded-[40px] glass transition-all flex flex-col border-2"
                     :class="selectedPlan === plan.id ? 'border-blue-500 bg-blue-500/5' : 'border-transparent'">
                    
                    <div x-show="plan.recommended" class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-blue-600 to-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg">
                        Recomendado
                    </div>

                    <div class="mb-8">
                        <h3 class="text-2xl font-black text-white" x-text="plan.name"></h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-4xl font-black text-white" x-text="plan.price"></span>
                            <span class="text-zinc-500 text-sm ml-2">/mês</span>
                        </div>
                    </div>

                    <ul class="space-y-4 mb-8 flex-grow">
                        <template x-for="feature in plan.features">
                            <li class="flex items-center text-sm text-zinc-400">
                                <i class="fas fa-check text-emerald-500 mr-3 text-xs"></i>
                                <span x-text="feature"></span>
                            </li>
                        </template>
                    </ul>

                    <button type="button" class="w-full py-4 rounded-2xl font-bold transition-all"
                            :class="selectedPlan === plan.id ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-white/5 text-zinc-400 hover:bg-white/10'">
                        <span x-text="selectedPlan === plan.id ? 'Selecionado' : 'Escolher Plano'"></span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Resumo Final -->
    <div x-show="selectedPlan" class="animate-in glass p-8 rounded-[32px] border-emerald-500/20 bg-emerald-500/5">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-500 text-2xl">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <h4 class="text-white font-bold text-lg">Tudo pronto para começar!</h4>
                <p class="text-zinc-400 text-sm">Ao finalizar, sua conta será ativada e você poderá acessar o painel administrativo.</p>
            </div>
        </div>
    </div>

    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <a href="{{ route('onboarding-premium.step', 6) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3" :disabled="!selectedPlan">
            Finalizar e Ativar Conta <i class="fas fa-rocket"></i>
        </button>
    </div>
</form>
@endsection
