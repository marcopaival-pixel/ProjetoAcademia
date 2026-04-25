@extends('layouts.app')

@section('title', 'Visão Geral da Minha Saúde — NexShape')

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --brand-primary-glow: {{ $branding['primary_color'] }}40;
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
    }

    .btn-brand-glow {
        background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        box-shadow: 0 10px 30px -5px var(--brand-primary-glow);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-brand-glow:hover {
        box-shadow: 0 15px 40px -2px var(--brand-primary-glow);
        transform: translateY(-4px) scale(1.02);
    }

    .context-pill {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .context-pill.active {
        background: var(--brand-primary);
        color: white;
        box-shadow: 0 8px 20px -4px var(--brand-primary-glow);
    }

    .nav-bar-blur {
        background: rgba(10, 12, 18, 0.85);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
    }

    .animate-entry {
        animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .horizontal-scroll::-webkit-scrollbar {
        display: none;
    }
    .horizontal-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] relative overflow-hidden pb-40 animate-entry">
    <!-- Background Effects -->
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-[var(--brand-primary)] opacity-[0.08] blur-[150px] rounded-full"></div>
    <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] bg-[var(--brand-accent)] opacity-[0.05] blur-[120px] rounded-full"></div>

    <div class="relative z-10 py-10 px-6 max-w-2xl mx-auto space-y-12">
        <!-- Header -->
        <header class="flex flex-col gap-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[var(--brand-primary)] to-[var(--brand-accent)] flex items-center justify-center text-white font-black text-2xl shadow-2xl relative border border-white/10 group overflow-hidden">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-white tracking-tighter leading-none mb-1">
                            {{ $activeProfessional ? $activeProfessional->name : 'Visão Geral' }}
                        </h1>
                        <div class="flex items-center gap-3">
                            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">
                                {{ $activeProfessional ? ($activeProfessional->professionalProfile->specialty ?? 'Profissional') : 'Minha Saúde Centralizada' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('patient.dashboard.choice') }}" class="px-4 h-12 rounded-2xl glass-card flex items-center justify-center text-zinc-400 hover:text-white hover:border-blue-500/50 transition-all gap-2">
                        <i class="fas fa-th-large text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Professional Selector (Pills) -->
            <div class="flex gap-3 overflow-x-auto pb-2 horizontal-scroll">
                <a href="{{ route('patient.unified.dashboard') }}" 
                   class="context-pill whitespace-nowrap px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/5 {{ !$activeProfessional ? 'active' : 'bg-white/5 text-zinc-500' }}">
                   Visão Geral
                </a>
                @foreach($professionals as $prof)
                <a href="{{ route('patient.unified.dashboard', ['professional_id' => $prof->id]) }}" 
                   class="context-pill whitespace-nowrap px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/5 {{ $activeProfessional && $activeProfessional->id == $prof->id ? 'active' : 'bg-white/5 text-zinc-500' }}">
                   {{ $prof->name }}
                </a>
                @endforeach
            </div>
        </header>

        @if(!$activeProfessional)
            <!-- MODO VISÃO GERAL -->
            
            <!-- Resumo Geral do Paciente -->
            <section class="glass-card rounded-[3rem] p-8 relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-[var(--brand-primary)] opacity-[0.05] blur-3xl group-hover:opacity-[0.1] transition-opacity"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-8">
                    <div class="w-24 h-24 rounded-full bg-zinc-800 border-4 border-zinc-900 shadow-2xl flex items-center justify-center text-4xl text-zinc-600 overflow-hidden">
                        @if($patient->avatar)
                            <img src="{{ asset('storage/' . $patient->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    
                    <div class="flex-1 text-center sm:text-left space-y-4">
                        <div>
                            <h2 class="text-3xl font-black text-white tracking-tighter leading-tight">{{ $summary['name'] }}</h2>
                            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">
                                {{ $summary['age'] ? $summary['age'] . ' anos' : 'Idade não informada' }} • 
                                <span class="{{ $patient->hasRole('aluno') ? 'text-[var(--brand-primary)]' : 'text-zinc-500' }}">
                                    {{ $summary['profile_type'] }}
                                </span>
                            </p>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-white/5 rounded-2xl p-3 border border-white/5">
                                <span class="block text-[8px] font-black text-zinc-500 uppercase tracking-widest">Peso</span>
                                <span class="text-white font-bold">{{ $summary['weight'] ? $summary['weight'] . ' kg' : '--' }}</span>
                            </div>
                            <div class="bg-white/5 rounded-2xl p-3 border border-white/5">
                                <span class="block text-[8px] font-black text-zinc-500 uppercase tracking-widest">Altura</span>
                                <span class="text-white font-bold">{{ $summary['height'] ? $summary['height'] . ' cm' : '--' }}</span>
                            </div>
                            <div class="bg-white/5 rounded-2xl p-3 border border-white/5 col-span-2">
                                <span class="block text-[8px] font-black text-zinc-500 uppercase tracking-widest">Objetivo</span>
                                <span class="text-white font-bold">{{ $summary['goal'] ? \App\Models\UserProfile::getAvailableGoals()[$summary['goal']]['label'] ?? $summary['goal'] : 'Não definido' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Alertas e Pendências -->
            <section class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="glass-card p-4 rounded-3xl flex flex-col items-center justify-center gap-2 text-center {{ $alerts['workouts'] > 0 ? 'border-amber-500/30' : '' }}">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                        <i class="fas fa-running"></i>
                    </div>
                    <div>
                        <span class="block text-white font-black text-sm">{{ $alerts['workouts'] }}</span>
                        <span class="text-[8px] font-black text-zinc-500 uppercase">Treinos</span>
                    </div>
                </div>
                <div class="glass-card p-4 rounded-3xl flex flex-col items-center justify-center gap-2 text-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <span class="block text-white font-black text-sm">{{ $alerts['assessments'] }}</span>
                        <span class="text-[8px] font-black text-zinc-500 uppercase">Avaliações</span>
                    </div>
                </div>
                <div class="glass-card p-4 rounded-3xl flex flex-col items-center justify-center gap-2 text-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="block text-white font-black text-sm">{{ $alerts['appointments'] }}</span>
                        <span class="text-[8px] font-black text-zinc-500 uppercase">Consultas</span>
                    </div>
                </div>
                <div class="glass-card p-4 rounded-3xl flex flex-col items-center justify-center gap-2 text-center {{ $alerts['messages'] > 0 ? 'border-red-500/30' : '' }}">
                    <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <span class="block text-white font-black text-sm">{{ $alerts['messages'] }}</span>
                        <span class="text-[8px] font-black text-zinc-500 uppercase">Mensagens</span>
                    </div>
                </div>
                <div class="glass-card p-4 rounded-3xl flex flex-col items-center justify-center gap-2 text-center col-span-2 sm:col-span-1">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <span class="block text-white font-black text-sm">{{ $alerts['documents'] }}</span>
                        <span class="text-[8px] font-black text-zinc-500 uppercase">Docs</span>
                    </div>
                </div>
            </section>

            <!-- Recovery Recommendation (Biohacking) -->
            @if($recommendedRoutine)
            <section class="glass-card rounded-[2.5rem] p-1 border-white/5 bg-zinc-900/20 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-[var(--brand-primary-glow)] to-transparent opacity-20"></div>
                <div class="relative p-6 flex flex-col sm:flex-row items-center gap-8">
                    <div class="w-full sm:w-32 aspect-square rounded-2xl overflow-hidden border border-white/5 relative shrink-0">
                        <img src="{{ $recommendedRoutine->thumbnail ?? '/images/tutorials/hip_mobility.png' }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-zinc-950/40 flex items-center justify-center">
                            <i class="fas fa-play text-white/50"></i>
                        </div>
                    </div>
                    
                    <div class="flex-1 space-y-3 text-center sm:text-left">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[8px] font-black uppercase tracking-widest">Recomendação IA</span>
                            @if($recoveryCountThisWeek > 0)
                                <span class="px-2 py-0.5 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[8px] font-black uppercase tracking-widest">
                                    {{ $recoveryCountThisWeek }} sessões esta semana
                                </span>
                            @endif
                            @if($lastWorkout && $lastWorkout->rpe_score >= 8)
                                <span class="px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[8px] font-black uppercase tracking-widest">Treino Intenso</span>
                            @endif
                        </div>
                        <h4 class="text-white font-black text-lg tracking-tight leading-tight">
                            {{ $recommendedRoutine->title }}
                        </h4>
                        <p class="text-zinc-500 text-[11px] font-medium leading-relaxed line-clamp-2">
                            Com base no seu último treino, recomendamos este protocolo de {{ $recommendedRoutine->category }} para acelerar a sua recuperação.
                        </p>
                    </div>

                    <a href="{{ route('active-rest.show', $recommendedRoutine->id) }}" class="px-8 py-4 rounded-2xl btn-brand-glow text-white text-[10px] font-black uppercase tracking-widest shadow-2xl shrink-0">
                        Recuperar Agora
                    </a>
                </div>
            </section>
            @endif

            <!-- Banner Promoção Aluno -->
            @if(!Auth::user()->hasRole('aluno'))
            <section class="glass-card rounded-[2.5rem] p-8 border-l-4 border-l-[var(--brand-primary)] bg-gradient-to-r from-[var(--brand-primary-glow)] to-transparent relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-[var(--brand-primary)] opacity-[0.05] blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="relative z-10 space-y-4">
                    <h4 class="text-white font-black text-lg tracking-tight leading-tight">Quer evoluir ainda mais?</h4>
                    <p class="text-zinc-400 text-xs font-medium leading-relaxed">Torne-se aluno e tenha acesso a treinos personalizados e acompanhamento completo com desconto exclusivo para pacientes.</p>
                    <a href="{{ route('patient.plans.index') }}" class="inline-flex items-center gap-2 px-8 py-4 btn-brand-glow text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-[var(--brand-primary-glow)]">
                        <i class="fas fa-crown text-[8px]"></i>
                        Ver Planos e Assinaturas
                    </a>
                </div>
            </section>
            @endif

            <!-- Lista de Profissionais Vinculados -->
            <section class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-white font-black text-sm uppercase tracking-widest">Meus Profissionais</h3>
                    <span class="text-zinc-500 text-[10px] font-bold">{{ $professionals->count() }} ativos</span>
                </div>
                
                <div class="flex gap-6 overflow-x-auto pb-4 horizontal-scroll">
                    @foreach($professionals as $prof)
                    <a href="{{ route('patient.unified.dashboard', ['professional_id' => $prof->id]) }}" class="glass-card p-6 rounded-[2.5rem] min-w-[280px] space-y-6 group shrink-0 hover:border-[var(--brand-primary)] transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-zinc-800 border border-white/5 overflow-hidden">
                                @if($prof->avatar)
                                    <img src="{{ asset('storage/' . $prof->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-zinc-600">
                                        <i class="fas fa-user-md text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-white font-black text-md leading-none">{{ $prof->name }}</h4>
                                <p class="text-[var(--brand-primary)] text-[10px] font-black uppercase tracking-widest mt-1">{{ $prof->professionalProfile->specialty ?? 'Especialista' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-wider text-zinc-500">
                            <span>Desde</span>
                            <span class="text-white">{{ $prof->pivot->data_cadastro ? \Carbon\Carbon::parse($prof->pivot->data_cadastro)->format('d/m/Y') : '--' }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
        @else
            <!-- MODO PROFISSIONAL ESPECÍFICO -->
            
            <!-- Plano de Tratamento Ativo -->
            @if($treatmentPlan)
            <section class="glass-card rounded-[2.5rem] p-8 border-t-4 border-t-[var(--brand-primary)] space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-black text-xl tracking-tight">Plano de Tratamento</h3>
                        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Objetivo: {{ $treatmentPlan->title }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-[var(--brand-primary-glow)] flex items-center justify-center text-[var(--brand-primary)]">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <p class="text-zinc-400 text-xs leading-relaxed line-clamp-3">
                        {{ $treatmentPlan->description }}
                    </p>
                    <a href="{{ route('patient.treatment-plan') }}" class="inline-flex items-center gap-2 text-[var(--brand-primary)] text-[10px] font-black uppercase tracking-widest">
                        Ler Plano Completo <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </section>
            @endif

            <!-- Prescrições Recentes -->
            <section class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-white font-black text-sm uppercase tracking-widest">Prescrições & Orientações</h3>
                    <a href="{{ route('patient.prescriptions') }}" class="text-[var(--brand-primary)] text-[10px] font-black uppercase">Ver Todas</a>
                </div>
                
                <div class="grid gap-4">
                    @forelse($prescriptions as $pres)
                    <div class="glass-card p-5 rounded-3xl flex items-center gap-5 group">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-500 group-hover:text-white transition-colors">
                            <i class="fas fa-prescription text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-black text-sm tracking-tight">{{ $pres->title }}</h4>
                            <p class="text-zinc-500 text-[9px] font-bold uppercase">{{ $pres->created_at->format('d/m/Y') }} • {{ $pres->type }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-zinc-800"></i>
                    </div>
                    @empty
                    <div class="glass-card p-10 rounded-[2rem] text-center">
                        <p class="text-zinc-600 text-xs font-bold uppercase tracking-widest">Nenhuma prescrição registrada.</p>
                    </div>
                    @endforelse
                </div>
            </section>

            <!-- Evoluções Médicas -->
            <section class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-white font-black text-sm uppercase tracking-widest">Minha Evolução</h3>
                    <a href="{{ route('patient.medical-records.evolutions') }}" class="text-[var(--brand-primary)] text-[10px] font-black uppercase">Histórico</a>
                </div>
                
                <div class="space-y-4">
                    @foreach($evolutions as $evo)
                    <div class="glass-card p-6 rounded-[2rem] space-y-4 border-l-2 border-l-[var(--brand-primary)]">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-white uppercase">{{ $evo->date->format('d/m/Y') }}</span>
                            <span class="px-3 py-1 rounded-full bg-white/5 text-zinc-500 text-[8px] font-black uppercase">{{ $evo->status ?? 'Concluída' }}</span>
                        </div>
                        <p class="text-zinc-400 text-[11px] leading-relaxed line-clamp-2">
                            {{ $evo->evolution_text }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Agenda (Comum a ambos, mas filtrada se profissional selecionado) -->
        <section class="space-y-6">
            <h3 class="text-white font-black text-sm uppercase tracking-widest">Agenda Próxima</h3>
            
            <div class="space-y-4">
                @forelse($appointments as $app)
                    <div class="glass-card p-5 rounded-[2rem] flex items-center gap-5 border-l-4 border-l-[var(--brand-accent)]">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex flex-col items-center justify-center leading-none">
                            <span class="text-white font-black text-lg">{{ $app->appointment_at->format('d') }}</span>
                            <span class="text-zinc-500 text-[8px] font-black uppercase">{{ $app->appointment_at->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white font-black text-sm tracking-tight">{{ $app->title ?? 'Consulta' }}</h4>
                            <p class="text-zinc-500 text-[10px] font-bold">{{ $app->appointment_at->format('H:i') }} • {{ $app->professional->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="glass-card p-10 rounded-[2rem] text-center">
                        <p class="text-zinc-600 text-xs font-bold uppercase tracking-widest">Nenhum compromisso agendado.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 6) Documentos e Laudos -->
        <section class="space-y-6">
            <h3 class="text-white font-black text-sm uppercase tracking-widest">Meus Documentos</h3>
            
            <div class="space-y-8">
                @forelse($documents as $profName => $docs)
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-[1px] flex-1 bg-zinc-800/50"></div>
                            <span class="text-zinc-600 text-[8px] font-black uppercase tracking-[0.2em]">{{ $profName }}</span>
                            <div class="h-[1px] flex-1 bg-zinc-800/50"></div>
                        </div>

                        <div class="grid gap-3">
                            @foreach($docs as $doc)
                                <a href="#" class="glass-card p-4 rounded-2xl flex items-center gap-4 hover:bg-white/5 transition-colors group">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="text-white font-bold text-xs">{{ $doc->title }}</h5>
                                        <p class="text-zinc-500 text-[9px] font-bold uppercase tracking-wider">{{ $doc->created_at->format('d/m/Y') }} • {{ $doc->type ?? 'Documento' }}</p>
                                    </div>
                                    <div class="text-zinc-700 group-hover:text-white transition-colors">
                                        <i class="fas fa-download text-xs"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="glass-card p-10 rounded-[2rem] text-center space-y-4">
                        <div class="text-zinc-800 text-4xl">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <p class="text-zinc-500 text-xs font-bold">Nenhum documento disponível no momento.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Premium Tab Bar Navigation -->
    <nav class="fixed bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-md nav-bar-blur border border-white/10 p-3 rounded-[3rem] flex items-center justify-around shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)] z-50">
        <a href="{{ route('patient.unified.dashboard') }}" class="flex flex-col items-center gap-1 {{ !$activeProfessional ? 'text-[var(--brand-primary)]' : 'text-zinc-500' }} transition-colors">
            <i class="fas fa-home-alt text-xl"></i>
            <span class="text-[8px] font-black uppercase">Home</span>
        </a>
        
        <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-chart-pie text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Evolução</span>
        </a>

        <div class="relative">
            <a href="{{ route('patient.unified.dashboard') }}" class="w-[4.5rem] h-[4.5rem] btn-brand-glow text-white rounded-full flex items-center justify-center -mt-20 border-[6px] border-[#06080c] shadow-2xl relative">
                <i class="fas fa-heartbeat text-2xl"></i>
            </a>
        </div>

        <a href="{{ route('patient.medical-records.index') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-file-medical-alt text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Clínico</span>
        </a>

        <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-user-circle text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Perfil</span>
        </a>
    </nav>
</div>
@endsection
