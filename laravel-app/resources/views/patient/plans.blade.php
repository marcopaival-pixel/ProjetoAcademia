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
        background: rgba(20, 22, 28, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
    }

    .btn-upgrade {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        box-shadow: 0 10px 30px -5px var(--brand-primary-glow);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-upgrade:hover {
        box-shadow: 0 15px 40px -2px var(--brand-primary-glow);
        transform: translateY(-4px) scale(1.02);
    }

    .plan-feature-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    .plan-feature-card:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: var(--brand-primary);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] relative overflow-hidden pb-40">
    <!-- Background Effects -->
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-[var(--brand-primary)] opacity-[0.08] blur-[150px] rounded-full"></div>
    <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] bg-[var(--brand-accent)] opacity-[0.05] blur-[120px] rounded-full"></div>

    <div class="relative z-10 py-10 px-6 max-w-lg mx-auto space-y-10">
        <!-- Header -->
        <header class="flex items-center gap-4">
            <a href="{{ route('patient.portal') }}" class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">Evolua sua Experiência</h1>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Planos de Assinatura</p>
            </div>
        </header>

        <!-- Intro -->
        <section class="space-y-4">
            <h2 class="text-3xl font-black text-white tracking-tighter leading-tight">Torne-se Aluno + Paciente</h2>
            <p class="text-zinc-500 text-sm font-medium leading-relaxed">
                Além do acompanhamento clínico com {{ $professional->name }}, você pode ter acesso ao ecossistema completo de treinos e dieta da NexShape.
            </p>
        </section>

        <!-- Plans List -->
        <div class="space-y-8">
            @foreach($availablePlans as $plan)
            <div class="glass-card rounded-[2.5rem] p-8 border-t-2 border-t-[var(--brand-primary)] relative overflow-hidden group">
                @if($plan['recommended'])
                <div class="absolute -top-1 -right-1 bg-[var(--brand-primary)] text-white text-[8px] font-black px-4 py-1 rounded-bl-2xl tracking-widest uppercase">
                    Exclusivo para Pacientes
                </div>
                @endif

                <div class="space-y-6">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tight">{{ $plan['name'] }}</h3>
                        <p class="text-zinc-500 text-xs font-medium">{{ $plan['description'] }}</p>
                    </div>

                    <div class="flex items-baseline gap-3">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-zinc-600 font-bold line-through">R$ {{ number_format($plan['original_price'], 2, ',', '.') }}</span>
                            <span class="text-4xl font-black text-white">R$ {{ number_format($plan['patient_price'], 2, ',', '.') }}</span>
                        </div>
                        <span class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">/ mês</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($plan['features'] as $feature)
                        <div class="flex items-center gap-3 text-zinc-400">
                            <div class="w-5 h-5 rounded-full bg-[var(--brand-primary-glow)] flex items-center justify-center text-[var(--brand-primary)] flex-shrink-0">
                                <i class="fas fa-check text-[8px]"></i>
                            </div>
                            <span class="text-[11px] font-bold">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>

                    <form method="post" action="{{ route('mp.start') }}">
                        @csrf
                        <input type="hidden" name="plan" value="monthly">
                        <input type="hidden" name="checkout" value="subscribe">
                        <button type="submit" class="w-full py-4 btn-upgrade text-white font-black rounded-2xl flex items-center justify-center gap-2 tracking-wide uppercase text-xs">
                            <i class="fas fa-crown text-[10px]"></i>
                            Assinar Agora
                        </button>
                    </form>

                    <p class="text-[8px] text-zinc-600 font-black uppercase tracking-[0.2em] text-center italic">
                        Economia de R$ {{ number_format($plan['savings'], 2, ',', '.') }} por ser paciente
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Benefits Grid -->
        <section class="grid grid-cols-2 gap-4 pt-10 border-t border-white/5">
            <div class="text-center space-y-2">
                <i class="fas fa-rocket text-xl text-zinc-800"></i>
                <h4 class="text-white font-black text-[10px] tracking-tight uppercase">Liberação Imediata</h4>
                <p class="text-zinc-600 text-[9px] leading-relaxed">Painel do Aluno desbloqueado na hora.</p>
            </div>
            <div class="text-center space-y-2">
                <i class="fas fa-shield-alt text-xl text-zinc-800"></i>
                <h4 class="text-white font-black text-[10px] tracking-tight uppercase">Acesso Unificado</h4>
                <p class="text-zinc-600 text-[9px] leading-relaxed">Mantenha seu histórico de paciente.</p>
            </div>
        </section>
    </div>

    <!-- Tab Bar -->
    <nav class="fixed bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-md bg-zinc-950/80 backdrop-blur-3xl border border-white/10 p-3 rounded-[3rem] flex items-center justify-around shadow-2xl z-50">
        <a href="{{ route('patient.portal') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-home-alt text-xl"></i>
            <span class="text-[8px] font-black uppercase">Home</span>
        </a>
        
        <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-chart-pie text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Bio</span>
        </a>

        <div class="relative">
            <a href="{{ route('patient.plans.index') }}" class="w-[4.5rem] h-[4.5rem] btn-upgrade text-white rounded-full flex items-center justify-center -mt-20 border-[6px] border-[#06080c] shadow-2xl relative">
                <i class="fas fa-crown text-2xl"></i>
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
