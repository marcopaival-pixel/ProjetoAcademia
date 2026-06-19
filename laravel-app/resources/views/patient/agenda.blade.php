@extends('layouts.app')

@section('title', 'Minha Agenda')

@section('style')
<style>
    :root {
        --bg-dark: #07090c;
        --card-bg: #0c0e12;
        --card-border: rgba(255, 255, 255, 0.05);
        --brand-accent: #0ea5e9;
    }
    body {
        background-color: var(--bg-dark);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen pb-32 px-5 pt-8 font-sans max-w-3xl mx-auto flex flex-col relative z-0">

    <!-- Efeito de Luz de Fundo (Glow Superior) -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-[#0ea5e9]/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="flex justify-between items-start mb-12 animate-fade-in-up">
        <div class="flex items-center gap-6">
            <a href="{{ route('patient.unified.dashboard') }}" class="w-16 h-16 rounded-[2rem] bg-[var(--card-bg)] border border-[var(--card-border)] flex items-center justify-center text-zinc-500 hover:text-white transition-colors shadow-2xl">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
            <div>
                <h1 class="text-white font-black text-3xl leading-none tracking-tight uppercase max-w-[280px]">
                    Minha Agenda
                </h1>
                <div class="flex items-center gap-2 mt-2.5">
                    <span class="text-xs text-zinc-500 font-bold tracking-[0.15em] uppercase">Consultas e Agendamentos</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                </div>
            </div>
        </div>
    </div>

        <!-- Timeline Section -->
        <div class="space-y-10 relative">
            <div class="absolute left-[36px] top-6 bottom-6 w-0.5 bg-zinc-800/50"></div>

            @forelse($appointments as $app)
            @php $isFuture = \Carbon\Carbon::parse($app->appointment_at)->isFuture(); @endphp
            <div class="flex gap-8 relative group">
                <!-- Date Circle -->
                <div class="w-20 h-20 rounded-[2rem] {{ $isFuture ? 'bg-[#0ea5e9]/10 border border-[#0ea5e9]/30 text-[#0ea5e9]' : 'bg-[#0a0c10] border border-[var(--card-border)] text-zinc-500' }} flex flex-col items-center justify-center shadow-2xl z-10 group-hover:scale-110 transition-transform duration-500">
                    <span class="text-2xl font-black leading-none">
                        {{ \Carbon\Carbon::parse($app->appointment_at)->format('d') }}
                    </span>
                    <span class="text-[10px] font-black uppercase mt-1 tracking-widest">
                        {{ \Carbon\Carbon::parse($app->appointment_at)->translatedFormat('M') }}
                    </span>
                </div>

                <!-- Info Card -->
                <div class="flex-1 bg-[var(--card-bg)] p-8 rounded-[3rem] border border-[var(--card-border)] relative overflow-hidden shadow-2xl transition-all duration-300 {{ $isFuture ? 'group-hover:border-[#0ea5e9]/40' : 'group-hover:border-zinc-700' }}">
                    <div class="absolute -left-[2px] top-[20%] bottom-[20%] w-2 rounded-r-full {{ $isFuture ? 'bg-[#0ea5e9] shadow-[0_0_12px_rgba(14,165,233,0.5)]' : 'bg-zinc-700' }}"></div>
                    
                    <div class="pl-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-black text-white uppercase tracking-tight">
                                {{ $app->service_type ?? 'Consulta' }}
                            </h4>
                            <span class="text-[10px] font-black px-3 py-1.5 rounded-xl uppercase tracking-widest
                                {{ $app->status === 'scheduled' || $app->status === 'confirmed' ? 'bg-emerald-500/10 text-emerald-500' : 
                                   ($app->status === 'cancelled' || $app->status === 'no_show' ? 'bg-rose-500/10 text-rose-500' : 'bg-zinc-800 text-zinc-500') }}">
                                {{ $app->status_label }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4 text-zinc-500 mb-2">
                            <span class="text-xs font-bold flex items-center gap-2"><i class="far fa-clock text-[#0ea5e9]"></i> {{ \Carbon\Carbon::parse($app->appointment_at)->format('H:i') }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-800"></span>
                            <span class="text-xs font-bold flex items-center gap-2"><i class="far fa-user text-zinc-600"></i> {{ $app->professional->name ?? 'Pro' }}</span>
                        </div>

                        @if($app->notes)
                        <div class="mt-5 border-t border-white/5 pt-5">
                            <p class="text-xs text-zinc-500 font-medium italic leading-relaxed">
                                "{{ $app->notes }}"
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-[var(--card-bg)] p-12 rounded-[3rem] text-center border border-[var(--card-border)] shadow-2xl">
                <div class="w-24 h-24 rounded-full bg-zinc-900 border-4 border-zinc-800 flex items-center justify-center text-zinc-700 mb-6 mx-auto">
                    <i class="fas fa-calendar-times text-4xl"></i>
                </div>
                <h5 class="text-white text-lg font-black uppercase tracking-tight mb-3">Sem horários</h5>
                <p class="text-zinc-500 text-sm font-bold leading-relaxed max-w-sm mx-auto">Nenhum agendamento futuro ou histórico encontrado no momento.</p>
            </div>
            @endforelse
        </div>

        <!-- Notification Note -->
        <div class="mt-12 p-8 bg-[#0ea5e9]/5 border border-[#0ea5e9]/10 rounded-[2.5rem] flex items-center gap-6 shadow-xl">
            <div class="w-14 h-14 bg-[#0ea5e9]/10 rounded-[1.5rem] flex items-center justify-center text-[#0ea5e9] shrink-0">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-zinc-400 font-bold leading-relaxed">
                    Para reagendar ou cancelar, entre em contato diretamente com o profissional via Chat.
                </p>
            </div>
        </div>

    </div>
</div>

<!-- Navegação Flutuante Inferior (mesma do dashboard) -->
<div class="fixed bottom-0 left-0 w-full z-50 pointer-events-none">
    <div class="max-w-3xl mx-auto relative pointer-events-auto">
        <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-full h-12 pointer-events-none flex justify-center overflow-hidden z-[-1]">
            <div class="w-[100px] h-[100px] border border-[var(--card-border)] rounded-full mt-2"></div>
        </div>
        
        <nav class="nav-bar-shape px-8 pb-8 pt-6 flex items-center justify-between" style="background: linear-gradient(to top, #07090c 40%, rgba(7,9,12,0.9)); -webkit-mask-image: radial-gradient(circle at center -40px, transparent 50px, black 51px);">
            <a href="{{ route('patient.unified.dashboard') }}" class="flex flex-col items-center gap-1.5 text-zinc-500 hover:text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-home text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Home</span>
            </a>
            
            <div class="relative flex-1 flex justify-center -mt-20">
                <a href="{{ route('patient.agenda') }}" class="w-20 h-20 rounded-full bg-[#0a0c10] border-2 border-[#0ea5e9] flex items-center justify-center text-[#0ea5e9] shadow-[0_0_20px_rgba(14,165,233,0.3)] relative z-10 transition-transform hover:scale-105">
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
