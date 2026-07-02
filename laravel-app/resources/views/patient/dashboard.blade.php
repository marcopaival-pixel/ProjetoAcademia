@extends('layouts.app')

@section('title', 'Meu Portal — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --brand-primary-glow: {{ $branding['primary_color'] }}30;
        --brand-primary-dim: {{ $branding['primary_color'] }}10;
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

    .progress-circle {
        transition: stroke-dashoffset 1s ease-in-out;
    }
</style>
@endsection

@section('content')
<div class="py-10 space-y-12 animate-entry mx-auto px-4 md:px-6">
    <!-- Background Effects (Now integrated with body background but enhanced) -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-blue-500/10 blur-[150px] rounded-full"></div>
        <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] bg-emerald-500/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="relative z-10 py-10 px-6 max-w-lg mx-auto space-y-10">
        <!-- Header -->
        <header class="flex items-center justify-between">
            <div class="flex items-center gap-5">
                @if($branding['logo_url'])
                    <img src="{{ $branding['logo_url'] }}" alt="Logo" class="h-12 w-auto drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]">
                @else
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-emerald-500 flex items-center justify-center text-white font-black text-2xl shadow-2xl relative border border-white/10 group overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <i data-lucide="stethoscope" class="w-7 h-7"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tighter leading-none mb-1">
                        {{ $branding['clinic_name'] }}
                    </h1>
                    <div class="flex items-center gap-3">
                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Portal Oficial</span>
                        @if($professional)
                            <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                            <span class="text-[9px] font-bold text-[var(--brand-primary)] uppercase tracking-widest">Atendimento Ativo</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                @if($links->count() > 1)
                    <a href="{{ route('patient.professional.selection') }}" class="px-4 h-12 rounded-2xl glass-card flex items-center justify-center text-zinc-400 hover:text-white hover:border-blue-500/50 transition-all gap-2">
                        <i class="fas fa-exchange-alt text-xs"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest hidden sm:inline">Trocar</span>
                    </a>
                @endif
                <a href="{{ route('patient.messages') }}" class="w-12 h-12 rounded-2xl glass-card flex items-center justify-center text-zinc-400 hover:text-blue-500 transition-all">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </a>
                <button class="w-12 h-12 rounded-2xl glass-card flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <!-- Welcome & Health Score -->
        <section class="glass-card rounded-[3rem] p-8 relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-[var(--brand-primary)] opacity-[0.05] blur-3xl group-hover:opacity-[0.1] transition-opacity"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div class="space-y-4">
                    <span class="px-3 py-1 bg-[var(--brand-primary-dim)] text-[var(--brand-primary)] text-[9px] font-black uppercase tracking-widest rounded-full border border-[var(--brand-primary)]/20">Dashboard</span>
                    <h2 class="text-3xl font-black text-white tracking-tighter leading-tight">Olá, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
                    
                    <div class="flex flex-col gap-2">
                        <span class="text-zinc-500 text-[9px] font-black uppercase tracking-[0.2em]">Profissionais Vinculados</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($links as $link)
                                @php $isActive = $link->profissional_id == session('active_professional_id'); @endphp
                                <div class="flex items-center gap-2 {{ $isActive ? 'bg-[var(--brand-primary-dim)] border-[var(--brand-primary)]/20' : 'bg-zinc-800/50 border-white/5' }} rounded-lg px-2 py-1 border">
                                    <div class="w-6 h-6 rounded-full {{ $isActive ? 'bg-[var(--brand-primary)] text-white' : 'bg-zinc-950 text-zinc-500' }} flex items-center justify-center text-[8px] font-bold">
                                        {{ substr($link->professional->name, 0, 1) }}
                                    </div>
                                    <span class="{{ $isActive ? 'text-white' : 'text-zinc-500' }} text-[10px] font-bold">{{ $link->professional->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                        <div class="flex items-center gap-4 pt-2">
                            <a href="{{ route('patient.export-laudo') }}" class="px-4 py-2 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase tracking-widest rounded-xl border border-emerald-500/20 hover:bg-emerald-500 hover:text-white transition-all">
                                <i data-lucide="file-text" class="w-3 h-3 mr-1 inline"></i> Baixar Laudo
                            </a>
                            <a href="{{ route('patient.access-logs') }}" class="px-4 py-2 bg-zinc-800/50 text-zinc-400 text-[9px] font-black uppercase tracking-widest rounded-xl border border-white/5 hover:bg-zinc-800 hover:text-white transition-all">
                                <i data-lucide="shield-check" class="w-3 h-3 mr-1 inline"></i> Histórico
                            </a>
                        </div>
                </div>
                
                <div class="relative w-28 h-28 flex items-center justify-center">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="56" cy="56" r="48" stroke="rgba(255,255,255,0.04)" stroke-width="12" fill="transparent" />
                        <circle cx="56" cy="56" r="48" 
                                stroke="url(#gradientScore)" 
                                stroke-width="12" 
                                fill="transparent" 
                                stroke-dasharray="301.6" 
                                stroke-dashoffset="{{ 301.6 - (301.6 * ($healthScore / 100)) }}" 
                                stroke-linecap="round"
                                class="progress-circle drop-shadow-[0_0_15px_var(--brand-primary-glow)]" />
                        <defs>
                            <linearGradient id="gradientScore" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="var(--brand-primary)" />
                                <stop offset="100%" stop-color="var(--brand-accent)" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black text-white leading-none">{{ $healthScore }}</span>
                        <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest mt-1">Score</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="glass-card p-5 rounded-[2rem] space-y-2 border-l-4 border-l-[var(--brand-primary)]">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Última Consulta</span>
                <p class="text-white text-xs font-black italic">{{ $summary['last_appointment'] ? $summary['last_appointment']->appointment_at->format('d/m/Y') : '--/--/----' }}</p>
            </div>
            <div class="glass-card p-5 rounded-[2rem] space-y-2 border-l-4 border-l-[var(--brand-accent)]">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Próxima Consulta</span>
                <p class="text-white text-xs font-black italic">{{ $summary['next_appointment'] ? $summary['next_appointment']->appointment_at->format('d/m/Y') : '--/--/----' }}</p>
            </div>
        </div>

        <!-- Check-in de Humor Diário -->
        <section class="glass-card rounded-[2.5rem] p-6 space-y-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Diário</span>
                <i data-lucide="smile" class="w-4 h-4 text-zinc-500"></i>
            </div>
            <h4 class="text-white font-black text-sm tracking-tight text-center">Como você está se sentindo hoje?</h4>
            <div class="flex justify-between items-center px-4 pt-2 pb-1">
                <button onclick="registerMood(2, 'Péssimo')" class="text-4xl grayscale hover:grayscale-0 hover:scale-125 transition-all transform origin-bottom" title="Péssimo">😖</button>
                <button onclick="registerMood(4, 'Ruim')" class="text-4xl grayscale hover:grayscale-0 hover:scale-125 transition-all transform origin-bottom" title="Ruim">🙁</button>
                <button onclick="registerMood(6, 'Normal')" class="text-4xl grayscale hover:grayscale-0 hover:scale-125 transition-all transform origin-bottom" title="Normal">😐</button>
                <button onclick="registerMood(8, 'Bem')" class="text-4xl grayscale hover:grayscale-0 hover:scale-125 transition-all transform origin-bottom" title="Bem">🙂</button>
                <button onclick="registerMood(10, 'Excelente')" class="text-4xl grayscale hover:grayscale-0 hover:scale-125 transition-all transform origin-bottom" title="Excelente">🤩</button>
            </div>
            <p id="mood-feedback" class="text-[10px] font-black text-center text-emerald-400 hidden uppercase tracking-widest transition-opacity">Registro salvo com sucesso!</p>
        </section>

        <!-- Banner Promoção Aluno -->
        @if(!Auth::user()->hasRole('aluno'))
        <section class="glass-card rounded-[2.5rem] p-8 border-l-4 border-l-[var(--brand-primary)] bg-gradient-to-r from-[var(--brand-primary-dim)] to-transparent relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-[var(--brand-primary)] opacity-[0.05] blur-2xl group-hover:scale-150 transition-transform"></div>
            <div class="relative z-10 space-y-4">
                <h4 class="text-white font-black text-lg tracking-tight leading-tight">Quer evoluir ainda mais?</h4>
                <p class="text-zinc-400 text-xs font-medium leading-relaxed">Torne-se aluno e tenha acesso a treinos personalizados e acompanhamento completo.</p>
                <a href="{{ route('patient.plans.index') }}" class="inline-flex items-center gap-2 px-8 py-4 btn-brand-glow text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-[var(--brand-primary-glow)]">
                    <i class="fas fa-crown text-[8px]"></i>
                    Ver Planos
                </a>
            </div>
        </section>
        @endif

        <!-- Navigation Menu Grid -->
        <section class="grid grid-cols-2 gap-6">
            <!-- Plano de Tratamento -->
            <a href="{{ route('patient.treatment-plan') }}" class="glass-card p-6 rounded-[2.5rem] flex flex-col items-center gap-4 hover:scale-105 transition-transform group">
                <div class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-blue-500 shadow-lg shadow-blue-500/10">
                    <i data-lucide="clipboard-list" class="w-7 h-7"></i>
                </div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest text-center">Plano de Tratamento</span>
            </a>

            @if($summary['has_evolution_data'])
            <!-- Evolução -->
            <a href="{{ route('patient.evolution') }}" class="glass-card p-6 rounded-[2.5rem] flex flex-col items-center gap-4 hover:scale-105 transition-transform group">
                <div class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-emerald-500 shadow-lg shadow-emerald-500/10">
                    <i data-lucide="trending-up" class="w-7 h-7"></i>
                </div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest text-center">Minha Evolução</span>
            </a>
            @endif

            <!-- Prescrições -->
            <a href="{{ route('patient.prescriptions') }}" class="glass-card p-6 rounded-[2.5rem] flex flex-col items-center gap-4 hover:scale-105 transition-transform group">
                <div class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-amber-500 shadow-lg shadow-amber-500/10">
                    <i data-lucide="pill" class="w-7 h-7"></i>
                </div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest text-center">Prescrições</span>
            </a>

            <!-- Documentos -->
            <a href="{{ route('patient.documents') }}" class="glass-card p-6 rounded-[2.5rem] flex flex-col items-center gap-4 hover:scale-105 transition-transform group">
                <div class="w-14 h-14 rounded-2xl bg-zinc-900 flex items-center justify-center text-blue-400 shadow-lg shadow-blue-400/10">
                    <i data-lucide="folder" class="w-7 h-7"></i>
                </div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest text-center">Documentos</span>
            </a>
        </section>

        <!-- Professional Alert Block -->
        @if($primaryLink && $primaryLink->professional_notes_for_patient)
        <div class="pt-6">
            <div class="glass-card rounded-[2.5rem] p-6 border-l-4 border-l-amber-500/50 relative overflow-hidden">
                <div class="absolute right-6 top-6 text-amber-500/10 text-4xl">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 class="text-amber-500 text-[10px] font-black uppercase tracking-widest mb-2">Aviso do Profissional</h4>
                <p class="text-zinc-300 text-xs font-bold leading-relaxed italic">
                    "{{ $primaryLink->professional_notes_for_patient }}"
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Premium Tab Bar Navigation -->
    <nav class="fixed bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-md nav-bar-blur border border-white/10 p-3 rounded-[3rem] flex items-center justify-around shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)] z-50">
        <a href="{{ route('patient.portal') }}" class="flex flex-col items-center gap-1 text-[var(--brand-primary)] px-4">
            <i class="fas fa-home-alt text-xl"></i>
            <span class="text-[8px] font-black uppercase">Home</span>
        </a>
        
        @if($summary['has_evolution_data'])
        <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-chart-pie text-xl"></i>
            <span class="text-[8px] font-black uppercase tracking-tighter">Bio</span>
        </a>
        @endif

        <div class="relative">
            <a href="{{ route('patient.agenda') }}" class="w-[4.5rem] h-[4.5rem] btn-brand-glow text-white rounded-full flex items-center justify-center -mt-20 border-[6px] border-[#06080c] shadow-2xl relative group overflow-hidden">
                <i class="fas fa-calendar-alt text-2xl"></i>
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

<script>
    function registerMood(score, label) {
        fetch('{{ route('patient.mood.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mood_score: score,
                notes: 'Sentimento registrado via dashboard: ' + label
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.ok) {
                const feedback = document.getElementById('mood-feedback');
                feedback.classList.remove('hidden');
                setTimeout(() => feedback.classList.add('hidden'), 3000);
            }
        })
        .catch(error => console.error('Erro ao registrar humor:', error));
    }
</script>
@endsection
