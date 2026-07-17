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
<div class="min-h-screen bg-[#06080c] text-white pb-32">
    <div class="py-10 px-6 max-w-lg mx-auto space-y-10">
        <!-- Header -->
        <header class="flex items-center gap-4">
            <a href="{{ route('patient.portal') }}" class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-zinc-400">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic">Prescrições</h1>
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Treinos e Dietas Personalizadas</p>
            </div>
        </header>

        <!-- Treinos -->
        <section class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.4em]">Planos de Treino</h3>
                <span class="text-[8px] font-black px-2 py-0.5 bg-zinc-800 text-zinc-500 rounded">{{ $trainings->count() }} Ativos</span>
            </div>
            
            @forelse($trainings as $training)
            <a href="{{ route('progression.plans.show', $training->id) }}" class="glass-card p-6 rounded-[2.5rem] flex items-center gap-5 border-l-4 border-l-[var(--brand-primary)] hover:scale-[1.02] transition-all">
                <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-[var(--brand-primary)]">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-black text-white italic uppercase tracking-wider">{{ $training->name }}</h4>
                    <p class="text-[9px] font-bold text-zinc-500 uppercase">Criado em {{ $training->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-zinc-400">
                    <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>
            @empty
            <x-patient.empty-state 
                icon="fas fa-dumbbell" 
                title="Sem Treinos" 
                description="Seu plano de treinamento personalizado será exibido aqui assim que for prescrito pelo seu treinador."
            />
            @endforelse
        </section>

        <!-- Dietas -->
        <section class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.4em]">Planos Alimentares</h3>
                <span class="text-[8px] font-black px-2 py-0.5 bg-zinc-800 text-zinc-500 rounded">{{ $diets->count() }} Registros</span>
            </div>
            
            @forelse($diets as $diet)
            <a href="{{ route('nutrition.index', ['tab' => 'diary']) }}" class="glass-card p-6 rounded-[2.5rem] flex items-center gap-5 border-l-4 border-l-[var(--brand-accent)] hover:scale-[1.02] transition-all">
                <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-[var(--brand-accent)]">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-black text-white italic uppercase tracking-wider">{{ $diet->name ?? 'Dieta Personalizada' }}</h4>
                    <p class="text-[9px] font-bold text-zinc-500 uppercase">Última atualização: {{ $diet->updated_at->format('d/m/Y') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-zinc-400">
                    <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </a>
            @empty
            <x-patient.empty-state 
                icon="fas fa-utensils" 
                title="Sem Dieta" 
                description="Sua estratégia nutricional e diário alimentar estarão disponíveis assim que seu nutricionista liberar o plano."
            />
            @endforelse
        </section>
    </div>
</div>
@endsection
