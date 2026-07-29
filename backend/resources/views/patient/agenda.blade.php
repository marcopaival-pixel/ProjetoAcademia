@extends('layouts.app')

@section('title', 'Minha Agenda - ' . $branding['clinic_name'])

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
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
            <i class="fas fa-calendar-alt text-8xl text-indigo-500"></i>
        </div>
        
        <div class="relative z-10 flex items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('patient.portal') }}" class="w-12 h-12 bg-zinc-800 hover:bg-zinc-700 rounded-2xl flex items-center justify-center text-white transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-black text-white tracking-tight mb-2">Minha <span class="text-indigo-500">Agenda</span></h1>
                    <p class="text-zinc-400 font-medium max-w-2xl">Rotina de evolução, consultas e retornos com seus profissionais.</p>
                </div>
            </div>
            
            <a href="{{ route('patient.professionals.search') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-600/20 transition-colors">
                <i class="fas fa-plus mr-2"></i> Novo Agendamento
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Overview Column -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] border-l-4 border-l-indigo-500">
                <p class="text-xs font-black text-zinc-500 uppercase tracking-[0.25em] mb-2">Hoje na sua evolução</p>
                <h2 class="text-2xl font-black text-white tracking-tighter italic">
                    {{ $agendaFocus['today_appointments_count'] > 0 ? 'Você tem compromisso hoje' : 'Nenhum compromisso hoje' }}
                </h2>
                <p class="text-sm text-zinc-400 font-medium leading-relaxed mt-4">
                    Acompanhe consultas, retornos, treinos ativos e próximos passos importantes em um só lugar.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('patient.prescriptions') }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl flex flex-col justify-between hover:border-[var(--brand-primary)] transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--brand-primary)]/10 flex items-center justify-center text-[var(--brand-primary)] mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-dumbbell text-xl"></i>
                    </div>
                    <div>
                        <p class="text-3xl font-black text-white leading-none mb-1">{{ $agendaFocus['active_training_count'] }}</p>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Treinos ativos</p>
                    </div>
                </a>

                <a href="{{ route('body-analysis.index') }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl flex flex-col justify-between hover:border-[var(--brand-accent)] transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--brand-accent)]/10 flex items-center justify-center text-[var(--brand-accent)] mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-camera text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-black mb-1 {{ $agendaFocus['has_recent_body_analysis'] ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $agendaFocus['has_recent_body_analysis'] ? 'Atualizada' : 'Pendente' }}
                        </p>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Foto corporal</p>
                    </div>
                </a>
            </div>

            @if($agendaFocus['next_appointment'])
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2.5rem] flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Próximo atendimento</p>
                    <p class="text-base text-white font-black">
                        {{ \Carbon\Carbon::parse($agendaFocus['next_appointment']->appointment_at)->format('d/m H:i') }}
                        <span class="text-zinc-400 font-medium">com {{ $agendaFocus['next_appointment']->professional->name ?? 'profissional' }}</span>
                    </p>
                </div>
            </div>
            @endif
            
            <div class="bg-blue-500/5 border border-blue-500/10 p-6 rounded-3xl">
                <p class="text-[10px] text-zinc-400 font-bold text-center leading-relaxed uppercase tracking-widest">
                    Para reagendar ou cancelar, entre em contato diretamente com o profissional via chat ou suporte.
                </p>
            </div>
        </div>

        <!-- Timeline Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-zinc-400 uppercase tracking-widest">Linha do tempo</h3>
                <span class="text-[10px] font-black px-3 py-1 bg-zinc-800 text-zinc-300 rounded-lg">{{ $appointments->count() }} Registros</span>
            </div>

            <div class="space-y-6 relative pl-4">
                <div class="absolute left-[38px] top-6 bottom-6 w-1 bg-zinc-800/50 rounded-full"></div>

                @forelse($appointments as $app)
                @php $isFuture = \Carbon\Carbon::parse($app->appointment_at)->isFuture(); @endphp
                <div class="flex gap-6 relative items-start group">
                    <div class="w-16 h-16 rounded-2xl {{ $isFuture ? 'bg-indigo-500' : 'bg-zinc-800' }} flex flex-col items-center justify-center shadow-xl z-10 shrink-0 group-hover:scale-110 transition-transform">
                        <span class="text-xl font-black leading-none {{ $isFuture ? 'text-white' : 'text-zinc-400' }}">
                            {{ \Carbon\Carbon::parse($app->appointment_at)->format('d') }}
                        </span>
                        <span class="text-[10px] font-black uppercase mt-1 {{ $isFuture ? 'text-white/80' : 'text-zinc-500' }}">
                            {{ \Carbon\Carbon::parse($app->appointment_at)->translatedFormat('M') }}
                        </span>
                    </div>

                    <div class="flex-1 bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] border-l-4 {{ $isFuture ? 'border-l-indigo-500' : 'border-l-zinc-700' }} hover:border-indigo-500/30 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-black text-white uppercase tracking-wider italic">
                                {{ $app->service_type ?? 'Consulta' }}
                            </h4>
                            <span class="text-[10px] font-black px-3 py-1 rounded-lg uppercase
                                {{ $app->status === 'scheduled' || $app->status === 'confirmed' ? 'bg-emerald-500/10 text-emerald-500' :
                                   ($app->status === 'cancelled' || $app->status === 'no_show' ? 'bg-rose-500/10 text-rose-500' : 'bg-zinc-800 text-zinc-500') }}">
                                {{ $app->status_label }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 text-zinc-400">
                            <span class="text-sm font-bold flex items-center"><i class="far fa-clock mr-2 text-indigo-400"></i> {{ \Carbon\Carbon::parse($app->appointment_at)->format('H:i') }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                            <span class="text-sm font-bold flex items-center"><i class="far fa-user mr-2 text-zinc-500"></i> {{ $app->professional->name ?? 'Profissional' }}</span>
                        </div>

                        @if($app->notes)
                        <div class="mt-4 pt-4 border-t border-zinc-800">
                            <p class="text-sm text-zinc-500 font-medium italic">
                                <i class="fas fa-quote-left text-zinc-700 mr-2"></i>{{ $app->notes }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-zinc-900 border border-zinc-800 border-dashed p-16 rounded-[3.5rem] text-center">
                    <div class="w-20 h-20 bg-zinc-800 rounded-full mx-auto flex items-center justify-center text-zinc-600 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-times text-3xl"></i>
                    </div>
                    <h5 class="text-white text-lg font-black uppercase tracking-wider mb-2">Sem Horários</h5>
                    <p class="text-zinc-500 text-sm font-medium max-w-sm mx-auto">Nenhum agendamento futuro ou histórico encontrado na sua linha do tempo.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
