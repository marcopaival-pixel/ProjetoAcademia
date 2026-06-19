@extends('layouts.app')

@section('title', 'Histórico de Atendimentos')

@section('style')
    :root {
        --brand-primary: #3b82f6; /* Default fallback */
        --brand-accent: #10b981;
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

    .animate-entry {
        animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .nav-bar-blur {
        background: rgba(10, 12, 18, 0.85);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-32 animate-entry relative z-0">
    <!-- Background Effects -->
    <div class="fixed inset-0 pointer-events-none z-[-1]">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-blue-500/10 blur-[150px] rounded-full"></div>
        <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] bg-emerald-500/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="py-10 px-6 max-w-2xl mx-auto relative z-10">
        <x-patient.page-header 
            title="Meus Atendimentos" 
            subtitle="Registros Clínicos e Evoluções" 
            backUrl="{{ route('patient.medical-records.index') }}"
            icon="fas fa-notes-medical"
        />

        <div class="space-y-6 mt-8">
            @forelse($evolutions as $evolution)
                <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 blur-3xl rounded-full"></div>
                    
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-1.5 bg-blue-500/10 text-blue-400 rounded-lg text-xs font-black uppercase tracking-widest border border-blue-500/20">
                                {{ $evolution->type ?: 'Consulta' }}
                            </span>
                            <span class="text-zinc-500 text-xs font-black uppercase tracking-widest">{{ $evolution->date->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-xs font-black text-zinc-600 uppercase tracking-widest italic">ID #{{ $evolution->id }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                        <div class="space-y-3">
                            <p class="text-xs font-black text-zinc-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Avaliação Clínica
                            </p>
                            <p class="text-zinc-300 text-sm leading-relaxed font-medium italic">
                                "{{ $evolution->assessment ?: 'Sem detalhes registrados.' }}"
                            </p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs font-black text-zinc-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Conduta & Plano
                            </p>
                            <p class="text-zinc-300 text-sm leading-relaxed font-medium italic">
                                "{{ $evolution->conduct ?: 'Sem orientações registradas.' }}"
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center text-zinc-500 shadow-inner">
                                <i class="fas fa-user-md text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Profissional Responsável</p>
                                <p class="text-xs font-black text-white uppercase tracking-wider">{{ $evolution->professional->name }}</p>
                            </div>
                        </div>
                        
                        @if($evolution->attachments)
                            <button class="px-5 py-3 bg-white/5 text-zinc-400 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all flex items-center gap-2 border border-white/5">
                                <i class="fas fa-paperclip"></i>
                                Anexos ({{ count(json_decode($evolution->attachments, true)) }})
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <x-patient.empty-state 
                    icon="fas fa-notes-medical" 
                    title="Nenhum Registro" 
                    description="Suas evoluções e registros de atendimentos clínicos aparecerão aqui após cada consulta."
                />
            @endforelse

            <div class="mt-10">
                {{ $evolutions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Premium Tab Bar Navigation -->
<nav class="fixed bottom-10 left-1/2 -translate-x-1/2 w-[90%] max-w-md nav-bar-blur border border-white/10 p-3 rounded-[3rem] flex items-center justify-around shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)] z-50">
    <a href="{{ route('patient.portal') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors px-4">
        <i class="fas fa-home-alt text-2xl"></i>
        <span class="text-[10px] font-black uppercase mt-1">Home</span>
    </a>
    
    <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
        <i class="fas fa-chart-pie text-2xl"></i>
        <span class="text-[10px] font-black uppercase tracking-tighter mt-1">Bio</span>
    </a>

    <div class="relative">
        <a href="{{ route('patient.agenda') }}" class="w-[5rem] h-[5rem] bg-blue-500 text-white rounded-full flex items-center justify-center -mt-20 border-[6px] border-[#06080c] shadow-[0_10px_30px_-5px_rgba(59,130,246,0.5)] relative group overflow-hidden">
            <i class="fas fa-calendar-alt text-3xl"></i>
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </a>
    </div>

    <a href="{{ route('patient.prescriptions') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
        <i class="fas fa-running text-2xl"></i>
        <span class="text-[10px] font-black uppercase tracking-tighter mt-1">Treino</span>
    </a>

    <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1 text-zinc-500 hover:text-white transition-colors">
        <i class="fas fa-user-circle text-2xl"></i>
        <span class="text-[10px] font-black uppercase tracking-tighter mt-1">Perfil</span>
    </a>
</nav>
@endsection
