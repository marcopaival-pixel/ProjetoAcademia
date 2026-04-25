@extends('layouts.app')

@section('title', 'Minha Agenda — ' . $branding['clinic_name'])

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
            <div class="flex-1">
                <h1 class="text-xl font-black tracking-tighter uppercase italic">Minha Agenda</h1>
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Consultas e Agendamentos</p>
            </div>
            <a href="{{ route('patient.professionals.search') }}" class="px-4 py-2 bg-indigo-600 rounded-xl text-[9px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-600/20">
                <i class="fas fa-plus mr-1"></i> Novo
            </a>
        </header>

        <!-- Timeline Section -->
        <div class="space-y-10 relative">
            <div class="absolute left-[26px] top-4 bottom-4 w-0.5 bg-zinc-800/50"></div>

            @forelse($appointments as $app)
            @php $isFuture = \Carbon\Carbon::parse($app->appointment_at)->isFuture(); @endphp
            <div class="flex gap-6 relative">
                <!-- Date Circle -->
                <div class="w-14 h-14 rounded-2xl {{ $isFuture ? 'bg-[var(--brand-primary)]' : 'bg-zinc-800' }} flex flex-col items-center justify-center shadow-lg z-10">
                    <span class="text-sm font-black leading-none {{ $isFuture ? 'text-white' : 'text-zinc-500' }}">
                        {{ \Carbon\Carbon::parse($app->appointment_at)->format('d') }}
                    </span>
                    <span class="text-[8px] font-black uppercase {{ $isFuture ? 'text-white/70' : 'text-zinc-600' }}">
                        {{ \Carbon\Carbon::parse($app->appointment_at)->translatedFormat('M') }}
                    </span>
                </div>

                <!-- Info Card -->
                <div class="flex-1 glass-card p-5 rounded-[2rem] space-y-2 border-l-4 {{ $isFuture ? 'border-l-[var(--brand-primary)]' : 'border-l-zinc-700' }}">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black text-white uppercase tracking-wider italic">
                            {{ $app->service_type ?? 'Consulta' }}
                        </h4>
                        <span class="text-[8px] font-black px-2 py-0.5 rounded uppercase
                            {{ $app->status === 'scheduled' || $app->status === 'confirmed' ? 'bg-emerald-500/10 text-emerald-500' : 
                               ($app->status === 'cancelled' || $app->status === 'no_show' ? 'bg-rose-500/10 text-rose-500' : 'bg-zinc-800 text-zinc-500') }}">
                            {{ $app->status_label }}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-3 text-zinc-500">
                        <span class="text-[10px] font-bold"><i class="far fa-clock mr-1 text-[var(--brand-primary)]"></i> {{ \Carbon\Carbon::parse($app->appointment_at)->format('H:i') }}</span>
                        <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                        <span class="text-[10px] font-bold"><i class="far fa-user mr-1"></i> {{ $app->professional->name ?? 'Pro' }}</span>
                    </div>

                    @if($app->notes)
                    <p class="text-[9px] text-zinc-600 font-medium italic mt-2 border-t border-white/5 pt-2">
                        "{{ $app->notes }}"
                    </p>
                    @endif
                </div>
            </div>
            @empty
            <div class="glass-card p-12 rounded-[3.5rem] text-center border-dashed border-white/5 bg-transparent">
                <div class="w-16 h-16 bg-zinc-900/50 rounded-full mx-auto flex items-center justify-center text-zinc-700 mb-6 font-bold">
                    <i class="fas fa-calendar-times text-2xl"></i>
                </div>
                <h5 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Sem horários</h5>
                <p class="text-zinc-700 text-[9px] font-bold px-10 leading-relaxed uppercase tracking-widest">Nenhum agendamento futuro ou histórico encontrado.</p>
            </div>
            @endforelse
        </div>

        <!-- Notification Note -->
        <div class="glass-card p-6 rounded-3xl border-dashed border-white/10">
            <p class="text-[9px] text-zinc-500 font-bold text-center leading-relaxed uppercase tracking-widest">
                Para reagendar ou cancelar, entre em contato diretamente com o profissional via Chat.
            </p>
        </div>
    </div>
</div>
@endsection
