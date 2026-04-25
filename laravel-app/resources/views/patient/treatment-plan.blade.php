@extends('layouts.app')

@section('title', 'Plano de Tratamento — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --brand-primary-glow: {{ $branding['primary_color'] }}30;
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
    }

    .plan-section {
        border-left: 2px solid var(--brand-primary);
        padding-left: 1.5rem;
        position: relative;
    }

    .plan-section::before {
        content: '';
        position: absolute;
        left: -5px;
        top: 0;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand-primary);
        box-shadow: 0 0 10px var(--brand-primary);
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
                <h1 class="text-xl font-black tracking-tighter uppercase italic">Plano de Tratamento</h1>
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Prescrito por {{ $professional->name }}</p>
            </div>
        </header>

        @if($plan)
        <div class="space-y-8">
            <!-- Diagnóstico -->
            <section class="plan-section space-y-3">
                <h3 class="text-[10px] font-black text-[var(--brand-primary)] uppercase tracking-[0.3em]">Diagnóstico / Avaliação Inicial</h3>
                <div class="glass-card p-6 rounded-3xl">
                    <p class="text-xs text-zinc-300 leading-relaxed font-medium">
                        {{ $plan->diagnosis ?? 'Informação não disponível.' }}
                    </p>
                </div>
            </section>

            <!-- Objetivos -->
            <section class="plan-section space-y-3" style="border-color: var(--brand-accent);">
                <h3 class="text-[10px] font-black text-[var(--brand-accent)] uppercase tracking-[0.3em]">Objetivos Estratégicos</h3>
                <div class="glass-card p-6 rounded-3xl">
                    <p class="text-xs text-zinc-300 leading-relaxed font-medium">
                        {{ $plan->objectives ?? 'A definir com o profissional.' }}
                    </p>
                </div>
            </section>

            <!-- Plano de Cuidados -->
            <section class="plan-section space-y-3">
                <h3 class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Plano de Cuidados</h3>
                <div class="glass-card p-6 rounded-3xl">
                    <p class="text-xs text-zinc-300 leading-relaxed font-medium">
                        {{ $plan->care_plan ?? 'Não há um plano de cuidados específico no momento.' }}
                    </p>
                </div>
            </section>

            <!-- Orientações -->
            <section class="plan-section space-y-3" style="border-color: #f59e0b;">
                <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-[0.3em]">Orientações Gerais</h3>
                <div class="glass-card p-6 rounded-3xl bg-amber-500/5 border-amber-500/10">
                    <p class="text-xs text-zinc-300 leading-relaxed font-medium italic">
                        {{ $plan->orientations ?? 'Siga as recomendações passadas em consulta.' }}
                    </p>
                </div>
            </section>
        </div>
        @else
        <div class="glass-card p-12 rounded-[3rem] text-center space-y-4">
            <div class="w-20 h-20 bg-zinc-900 rounded-full mx-auto flex items-center justify-center text-zinc-700">
                <i class="fas fa-file-medical-alt text-3xl"></i>
            </div>
            <p class="text-zinc-500 text-xs font-bold px-6">Seu plano de tratamento ainda está sendo elaborado pelo seu profissional.</p>
        </div>
        @endif
    </div>
</div>
@endsection
