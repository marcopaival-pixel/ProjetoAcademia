@extends('layouts.app')

@section('title', 'Dashboard — ' . ($activeProfessional ? $activeProfessional->name : 'Minha Saúde'))

@section('style')
<style>
    :root {
        --bg-dark: #07090c;
        --card-bg: #0c0e12;
        --card-border: rgba(255, 255, 255, 0.05);
        --brand-blue: #0077ff;
    }
    
    body {
        background-color: var(--bg-dark);
        color: #ffffff;
    }

    .glass-btn {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.2s ease;
    }
    .glass-btn:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .score-ring {
        background: conic-gradient(
            rgba(255,255,255,0.05) calc(var(--score) * 1%), 
            transparent 0
        );
    }
    
    .nav-bar-shape {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-bottom: none;
        border-radius: 40px 40px 0 0;
    }

    /* Esconde barra de rolagem */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen pb-40 px-5 pt-12 font-sans max-w-3xl mx-auto flex flex-col relative z-0">
    <!-- Efeitos de Fundo -->
    <div class="fixed inset-0 pointer-events-none z-[-1]">
        <div class="absolute top-0 right-0 w-[60%] h-[40%] bg-blue-500/5 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-[50%] h-[30%] bg-emerald-500/5 blur-[100px] rounded-full"></div>
    </div>

    <!-- Cabeçalho Principal -->
    <header class="flex items-center justify-between mb-12">
        <div class="flex items-center gap-5">
            <!-- Logo Icon -->
            <div class="w-16 h-16 rounded-2xl bg-[#0ea5e9] flex items-center justify-center shadow-lg shadow-blue-500/20 text-white shrink-0">
                <i class="fas fa-stethoscope text-3xl"></i>
            </div>
            <!-- Textos -->
            <div>
                <h1 class="text-white font-black text-2xl leading-none tracking-tight uppercase max-w-[280px]">
                    {{ $activeProfessional ? $activeProfessional->name : $patient->name }}
                </h1>
                <div class="flex items-center gap-2 mt-2.5">
                    <span class="text-[10px] text-zinc-500 font-bold tracking-[0.15em] uppercase">Portal Oficial</span>
                    <span class="w-1 h-1 rounded-full bg-zinc-700"></span>
                    <span class="text-[10px] text-white font-bold tracking-[0.15em] uppercase">Atendimento Ativo</span>
                </div>
            </div>
        </div>
        <!-- Botões -->
        <div class="flex gap-3 shrink-0">
            <a href="#" class="w-14 h-14 rounded-2xl glass-btn flex items-center justify-center text-zinc-400">
                <i class="far fa-envelope text-xl"></i>
            </a>
            <a href="#" class="w-14 h-14 rounded-2xl glass-btn flex items-center justify-center text-zinc-400">
                <i class="far fa-bell text-xl"></i>
            </a>
        </div>
    </header>

    <!-- Card Principal de Dashboard -->
    <div class="bg-[var(--card-bg)] rounded-[3rem] border border-[var(--card-border)] p-10 mb-8 relative overflow-hidden shadow-2xl">
        <!-- Badge -->
        <div class="inline-block px-5 py-2 rounded-full border border-white/20 text-[10px] font-bold tracking-[0.2em] text-white uppercase mb-10">
            Dashboard
        </div>

        <div class="flex justify-between items-start mb-12">
            <div class="flex-1">
                @php $firstName = explode(' ', $patient->name)[0]; @endphp
                <h2 class="text-6xl font-black text-white tracking-tighter mb-10 leading-none">Olá, {{ $firstName }}!</h2>
                
                <h3 class="text-[10px] font-bold text-zinc-500 tracking-[0.15em] uppercase mb-4">Profissionais Vinculados</h3>
                
                @if($activeProfessional)
                    <div class="inline-flex items-center gap-3 bg-black/40 border border-white/10 rounded-full py-2.5 px-2.5 pr-6">
                        <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-bold text-white shrink-0">
                            {{ substr($activeProfessional->name, 0, 1) }}
                        </div>
                        <span class="text-xs font-bold text-white tracking-wide truncate max-w-[150px]">{{ $activeProfessional->name }}</span>
                    </div>
                @else
                    @foreach($professionals->take(2) as $prof)
                        <div class="inline-flex items-center gap-3 bg-black/40 border border-white/10 rounded-full py-2.5 px-2.5 pr-6 mb-2">
                            <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-bold text-white shrink-0">
                                {{ substr($prof->name, 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-white tracking-wide truncate max-w-[150px]">{{ $prof->name }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <!-- Score Gauge -->
            <div class="relative w-40 h-40 shrink-0 ml-4">
                <div class="absolute inset-0 rounded-full bg-[#06070a] shadow-inner border border-white/5"></div>
                <div class="absolute inset-[8px] rounded-full score-ring" style="--score: {{ $summary['health_score'] ?? 100 }};"></div>
                <div class="absolute inset-[14px] rounded-full bg-[#0c0e12] flex flex-col items-center justify-center z-10 border border-black/50">
                    <span class="text-5xl font-black text-white leading-none tracking-tighter">{{ $summary['health_score'] ?? 100 }}</span>
                    <span class="text-[10px] font-bold text-zinc-500 tracking-[0.2em] uppercase mt-1">Score</span>
                </div>
            </div>
        </div>

        <div class="flex gap-5">
            <a href="{{ route('patient.medical-records.index') }}" class="flex-1 py-5 px-6 rounded-2xl border border-[#0ea5e9]/30 bg-[#0ea5e9]/10 flex items-center justify-center gap-3 text-[#0ea5e9] hover:bg-[#0ea5e9]/20 transition-colors">
                <i class="far fa-file-alt text-lg"></i>
                <span class="text-xs font-black uppercase tracking-[0.15em]">Baixar Laudo</span>
            </a>
            <a href="{{ route('patient.medical-records.index') }}" class="flex-1 py-5 px-6 rounded-2xl border border-white/10 bg-white/5 flex items-center justify-center gap-3 text-zinc-300 hover:text-white transition-colors">
                <i class="far fa-check-circle text-lg"></i>
                <span class="text-xs font-black uppercase tracking-[0.15em]">Histórico</span>
            </a>
        </div>
    </div>

    <!-- Cards de Consulta -->
    <div class="grid grid-cols-2 gap-5 mb-8">
        @php
            $lastApp = $appointments->where('appointment_at', '<', now())->last();
            $nextApp = $appointments->where('appointment_at', '>=', now())->first();
        @endphp
        
        <div class="bg-[var(--card-bg)] rounded-[2.5rem] border border-[var(--card-border)] p-8 relative overflow-hidden flex flex-col justify-center min-h-[140px]">
            <!-- Detalhe Curvo Esquerdo -->
            <div class="absolute -left-[2px] top-[15%] bottom-[15%] w-2 bg-white rounded-r-full shadow-[0_0_12px_rgba(255,255,255,0.5)]"></div>
            
            <div class="pl-4">
                <h3 class="text-[10px] font-bold text-zinc-500 tracking-[0.15em] uppercase mb-4">Última Consulta</h3>
                <p class="text-white font-black text-xl tracking-tighter">
                    {{ $lastApp ? $lastApp->appointment_at->format('d/m/Y') : '--/--/----' }}
                </p>
            </div>
        </div>
        
        <div class="bg-[var(--card-bg)] rounded-[2.5rem] border border-[var(--card-border)] p-8 relative overflow-hidden flex flex-col justify-center min-h-[140px]">
            <!-- Detalhe Curvo Esquerdo -->
            <div class="absolute -left-[2px] top-[15%] bottom-[15%] w-2 bg-white rounded-r-full shadow-[0_0_12px_rgba(255,255,255,0.5)]"></div>
            
            <div class="pl-4">
                <h3 class="text-[10px] font-bold text-zinc-500 tracking-[0.15em] uppercase mb-4">Próxima Consulta</h3>
                <p class="text-white font-black text-xl tracking-tighter">
                    {{ $nextApp ? $nextApp->appointment_at->format('d/m/Y') : '--/--/----' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Banner Promocional -->
    <div class="bg-[var(--card-bg)] rounded-[3rem] border border-[var(--card-border)] p-8 flex items-center gap-6 relative overflow-hidden mb-12">
        <!-- Detalhe Curvo Esquerdo -->
        <div class="absolute -left-[2px] top-[20%] bottom-[20%] w-2 bg-white rounded-r-full shadow-[0_0_12px_rgba(255,255,255,0.5)]"></div>
        
        <div class="pl-5 flex-1">
            <h3 class="text-white font-black text-2xl tracking-tight mb-3">Quer evoluir ainda mais?</h3>
            <p class="text-zinc-500 text-sm leading-relaxed pr-12">
                Torne-se aluno e tenha acesso a treinos personalizados e acompanhamento completo.
            </p>
        </div>
        
        <!-- Ícone / Botão para ação (se houver no futuro) -->
        <div class="absolute right-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-white">
            <i class="fas fa-chevron-right text-sm"></i>
        </div>
    </div>

</div>

<!-- Navegação Flutuante Inferior -->
<div class="fixed bottom-0 left-0 w-full z-50 pointer-events-none">
    <div class="max-w-3xl mx-auto relative pointer-events-auto">
        <!-- Curvas Decorativas (Linhas Finas) -->
        <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-full h-12 pointer-events-none flex justify-center overflow-hidden z-[-1]">
            <div class="w-[100px] h-[100px] border border-[var(--card-border)] rounded-full mt-2"></div>
        </div>
        
        <nav class="nav-bar-shape px-8 pb-8 pt-6 flex items-center justify-between">
            <a href="{{ route('patient.unified.dashboard') }}" class="flex flex-col items-center gap-1.5 text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-home text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Home</span>
            </a>
            
            <!-- Botão Flutuante Central -->
            <div class="relative flex-1 flex justify-center -mt-20">
                <a href="{{ route('patient.agenda') }}" class="w-20 h-20 rounded-full bg-[#0a0c10] border-2 border-[var(--card-border)] flex items-center justify-center text-white shadow-2xl relative z-10 transition-transform hover:scale-105">
                    <i class="far fa-calendar-alt text-3xl"></i>
                </a>
            </div>

            <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1.5 text-zinc-500 hover:text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-dumbbell text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Treino</span>
            </a>

            <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1.5 text-zinc-500 hover:text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-user-circle text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Perfil</span>
            </a>
        </nav>
    </div>
</div>
@endsection

