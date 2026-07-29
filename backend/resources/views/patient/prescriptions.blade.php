@extends('layouts.app')

@section('title', 'Prescrições — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
            <i class="fas fa-clipboard-list text-8xl text-[var(--brand-primary)]"></i>
        </div>
        
        <div class="relative z-10 flex items-center gap-6">
            <a href="{{ route('patient.portal') }}" class="w-12 h-12 bg-zinc-800 hover:bg-zinc-700 rounded-2xl flex items-center justify-center text-white transition-colors">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight mb-2">Minhas <span class="text-[var(--brand-primary)]">Prescrições</span></h1>
                <p class="text-zinc-400 font-medium max-w-2xl">Acesse seus planos de treinamento e estratégias nutricionais ativas.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Treinos -->
        <section class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-zinc-400 uppercase tracking-widest">Planos de Treino</h3>
                <span class="text-[10px] font-black px-3 py-1 bg-zinc-800 text-zinc-300 rounded-lg">{{ $trainings->count() }} Ativos</span>
            </div>
            
            <div class="space-y-4">
                @forelse($trainings as $training)
                <a href="{{ route('progression.plans.show', $training->id) }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] flex items-center gap-5 border-l-4 border-l-[var(--brand-primary)] hover:border-[var(--brand-primary)] transition-all group">
                    <div class="w-16 h-16 rounded-2xl bg-[var(--brand-primary)]/10 flex items-center justify-center text-[var(--brand-primary)] group-hover:scale-110 transition-transform">
                        <i class="fas fa-dumbbell text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-black text-white italic uppercase tracking-wider mb-1">{{ $training->name }}</h4>
                        <p class="text-xs font-medium text-zinc-500"><i class="far fa-calendar-alt mr-1"></i> Criado em {{ $training->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:bg-zinc-700 group-hover:text-white transition-colors">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                @empty
                <x-patient.empty-state 
                    icon="fas fa-dumbbell" 
                    title="Sem Treinos" 
                    description="Seu plano de treinamento personalizado será exibido aqui assim que for prescrito pelo seu treinador."
                />
                @endforelse
            </div>
        </section>

        <!-- Dietas -->
        <section class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-zinc-400 uppercase tracking-widest">Planos Alimentares</h3>
                <span class="text-[10px] font-black px-3 py-1 bg-zinc-800 text-zinc-300 rounded-lg">{{ $diets->count() }} Registros</span>
            </div>
            
            <div class="space-y-4">
                @forelse($diets as $diet)
                <a href="{{ route('nutrition.index', ['tab' => 'diary']) }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] flex items-center gap-5 border-l-4 border-l-[var(--brand-accent)] hover:border-[var(--brand-accent)] transition-all group">
                    <div class="w-16 h-16 rounded-2xl bg-[var(--brand-accent)]/10 flex items-center justify-center text-[var(--brand-accent)] group-hover:scale-110 transition-transform">
                        <i class="fas fa-utensils text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-black text-white italic uppercase tracking-wider mb-1">{{ $diet->name ?? 'Dieta Personalizada' }}</h4>
                        <p class="text-xs font-medium text-zinc-500"><i class="far fa-clock mr-1"></i> Atualizado em {{ $diet->updated_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:bg-zinc-700 group-hover:text-white transition-colors">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                @empty
                <x-patient.empty-state 
                    icon="fas fa-utensils" 
                    title="Sem Dieta" 
                    description="Sua estratégia nutricional e diário alimentar estarão disponíveis assim que seu nutricionista liberar o plano."
                />
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
