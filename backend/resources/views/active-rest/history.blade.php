@extends('layouts.app')

@section('title', 'Meu Histórico de Recuperação — NexShape')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
    }
    .btn-brand-glow {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        box-shadow: 0 10px 30px -5px rgba(99, 102, 241, 0.4);
    }
    .animate-entry {
        animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] relative overflow-hidden pb-40 animate-entry">
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-[#6366f1] opacity-[0.08] blur-[150px] rounded-full"></div>
    
    <div class="relative z-10 py-10 px-6 max-w-4xl mx-auto space-y-12">
        <header class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('active-rest.index') }}" class="w-12 h-12 rounded-2xl glass-card flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tighter leading-none mb-2">Histórico de Recuperação</h1>
                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Acompanhe sua evolução e consistência no biohacking</p>
                </div>
            </div>
        </header>

        <!-- Stats Overview -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-8 rounded-[2.5rem] flex items-center gap-6">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 text-xl shadow-lg shadow-blue-500/10">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <span class="block text-white font-black text-2xl tracking-tight">{{ $stats['total_sessions'] }}</span>
                    <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Sessões Totais</span>
                </div>
            </div>
            
            <div class="glass-card p-8 rounded-[2.5rem] flex items-center gap-6">
                <div class="w-14 h-14 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500 text-xl shadow-lg shadow-purple-500/10">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <span class="block text-white font-black text-2xl tracking-tight">{{ $stats['total_minutes'] }}m</span>
                    <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Tempo Acumulado</span>
                </div>
            </div>

            <div class="glass-card p-8 rounded-[2.5rem] flex items-center gap-6">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xl shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-smile-beam"></i>
                </div>
                <div>
                    <span class="block text-white font-black text-2xl tracking-tight">{{ $stats['avg_score'] }}/5</span>
                    <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Bem-Estar Médio</span>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            @forelse($logs as $log)
                <div class="glass-card rounded-[2.5rem] p-6 flex flex-col md:flex-row items-center gap-6 border-l-4 {{ $log->feedback_score >= 4 ? 'border-l-emerald-500/50' : 'border-l-purple-500/50' }}">
                    <div class="w-20 h-20 rounded-2xl bg-zinc-800 overflow-hidden border border-white/5 shrink-0">
                        <img src="{{ $log->routine->thumbnail ?? '/images/tutorials/hip_mobility.png' }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-3">
                            <h3 class="text-white font-black text-lg tracking-tight">{{ $log->routine->title }}</h3>
                            <span class="px-2 py-0.5 rounded-md bg-white/5 text-zinc-500 text-[8px] font-black uppercase tracking-widest">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                            <span class="flex items-center gap-1.5"><i class="far fa-clock text-blue-400"></i> {{ floor($log->duration_spent / 60) }}m {{ $log->duration_spent % 60 }}s</span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-star {{ $log->feedback_score >= 4 ? 'text-amber-400' : 'text-zinc-600' }}"></i> 
                                Feedback: {{ $log->feedback_score }}/5
                            </span>
                        </div>

                        @if($log->notes)
                            <p class="text-zinc-400 text-xs italic bg-white/5 p-3 rounded-2xl">
                                "{{ $log->notes }}"
                            </p>
                        @endif
                    </div>

                    <a href="{{ route('active-rest.show', $log->active_rest_routine_id) }}" class="px-6 py-3 rounded-xl border border-white/10 text-white text-[10px] font-black uppercase tracking-widest hover:bg-white/5 transition-all">
                        Repetir
                    </a>
                </div>
            @empty
                <div class="glass-card p-12 rounded-[3rem] text-center space-y-4">
                    <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto text-zinc-600 text-3xl">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black text-lg">Sem registros ainda</h4>
                        <p class="text-zinc-500 text-xs font-medium">Inicie o seu primeiro protocolo de recuperação para ver o seu histórico aqui.</p>
                    </div>
                    <a href="{{ route('active-rest.index') }}" class="inline-flex px-8 py-4 rounded-2xl btn-brand-glow text-white text-[10px] font-black uppercase tracking-widest">
                        Começar Agora
                    </a>
                </div>
            @endforelse

            <div class="py-6">
                {{ $logs->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
