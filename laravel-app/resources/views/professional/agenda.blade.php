@extends('layouts.app')

@section('title', 'Agenda Profissional')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .status-badge {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .status-scheduled { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .status-confirmed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-in_progress { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-finished { background: rgba(107, 114, 128, 0.1); color: #9ca3af; }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .status-no_show { background: rgba(156, 163, 175, 0.1); color: #4b5563; }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black tracking-tighter italic uppercase">Agenda</h1>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Gestão de horários e atendimentos</p>
            </div>
            <button onclick="document.getElementById('modalSettings').classList.remove('hidden')" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 rounded-2xl text-xs font-black uppercase tracking-widest transition-all border border-white/5 flex items-center gap-2">
                <i class="fas fa-cog"></i> Configurar Agenda
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Appointments List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-blue-500"></i> Próximos Atendimentos
                    </h3>

                    <div class="space-y-4">
                        @forelse($appointments as $app)
                        <div class="flex items-center gap-6 p-4 rounded-3xl hover:bg-white/5 transition-all border border-transparent hover:border-white/5 group">
                            <div class="w-16 h-16 rounded-2xl bg-zinc-900 flex flex-col items-center justify-center text-center">
                                <span class="text-xs font-black text-zinc-500">{{ $app->appointment_at->format('H:i') }}</span>
                                <span class="text-[9px] font-bold text-zinc-600 uppercase">{{ $app->appointment_at->format('d/m') }}</span>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold">{{ $app->patient->name }}</h4>
                                    <span class="status-badge status-{{ $app->status }}">{{ $app->status_label }}</span>
                                </div>
                                <p class="text-[10px] text-zinc-500 uppercase font-black tracking-tighter">{{ $app->service_type }}</p>
                            </div>

                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <select onchange="updateStatus({{ $app->id }}, this.value)" class="bg-zinc-900 border-none text-[10px] font-black uppercase rounded-xl py-2 px-3 focus:ring-1 ring-blue-500">
                                    @foreach(\App\Models\ProfessionalAppointment::getStatuses() as $key => $label)
                                    <option value="{{ $key }}" {{ $app->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-20 opacity-30">
                            <i class="fas fa-calendar-times text-4xl mb-4"></i>
                            <p class="text-xs font-black uppercase tracking-widest">Nenhum atendimento agendado</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Stats & Quick Actions -->
            <div class="space-y-6">
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-6">Resumo Semanal</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-zinc-900/50 p-5 rounded-3xl border border-white/5">
                            <span class="text-[9px] font-black text-zinc-500 uppercase block mb-1">Total</span>
                            <span class="text-2xl font-black italic">{{ $appointments->count() }}</span>
                        </div>
                        <div class="bg-emerald-500/10 p-5 rounded-3xl border border-emerald-500/10">
                            <span class="text-[9px] font-black text-emerald-500 uppercase block mb-1">Concluídos</span>
                            <span class="text-2xl font-black italic text-emerald-500">{{ $appointments->where('status', 'finished')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] p-8 border-dashed border-zinc-800 bg-transparent">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-4">Dica NexShape</h3>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-bold uppercase tracking-wider">
                        Mantenha sua agenda sempre atualizada para que seus {{ mb_strtolower($patientsLabel) }} possam encontrar os melhores horários para atendimento.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Settings -->
<div id="modalSettings" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden">
    <div class="glass-card w-full max-w-2xl rounded-[3rem] p-8 overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-black uppercase italic tracking-tighter">Configurações da Agenda</h2>
            <button onclick="document.getElementById('modalSettings').classList.add('hidden')" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('professional.agenda.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Duração da Consulta (min)</label>
                    <input type="number" name="appointment_duration" value="{{ $profile->appointment_duration ?? 60 }}" class="w-full bg-zinc-900 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 ring-blue-500">
                </div>
                <div>
                    <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Intervalo entre Consultas (min)</label>
                    <input type="number" name="appointment_interval" value="{{ $profile->appointment_interval ?? 15 }}" class="w-full bg-zinc-900 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 ring-blue-500">
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest block">Horários de Atendimento</label>
                
                @php
                    $days = [
                        1 => 'Segunda-feira',
                        2 => 'Terça-feira',
                        3 => 'Quarta-feira',
                        4 => 'Quinta-feira',
                        5 => 'Sexta-feira',
                        6 => 'Sábado',
                        0 => 'Domingo'
                    ];
                @endphp

                @foreach($days as $dayNum => $dayName)
                @php $avail = $availabilities->where('day_of_week', $dayNum)->first(); @endphp
                <div class="flex items-center gap-4 bg-zinc-900/30 p-4 rounded-3xl border border-white/5">
                    <div class="flex-1">
                        <span class="text-[10px] font-black uppercase tracking-wider">{{ $dayName }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="availabilities[{{ $dayNum }}][enabled]" {{ $avail ? 'checked' : '' }} class="rounded border-zinc-800 bg-zinc-900 text-blue-500 focus:ring-blue-500">
                        <input type="hidden" name="availabilities[{{ $dayNum }}][day_of_week]" value="{{ $dayNum }}">
                        <input type="time" name="availabilities[{{ $dayNum }}][start_time]" value="{{ $avail->start_time ?? '08:00' }}" class="bg-zinc-900 border-none text-[10px] font-bold rounded-xl p-2">
                        <span class="text-zinc-600 text-[10px]">às</span>
                        <input type="time" name="availabilities[{{ $dayNum }}][end_time]" value="{{ $avail->end_time ?? '18:00' }}" class="bg-zinc-900 border-none text-[10px] font-bold rounded-xl p-2">
                    </div>
                </div>
                @endforeach
            </div>

            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-3xl text-xs font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-blue-500/20 mt-6">
                Salvar Configurações
            </button>
        </form>
    </div>
</div>

<script>
function updateStatus(id, status) {
    fetch(`{{ url('professional/agenda/appointment') }}/${id}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Erro ao atualizar status: ' + data.message);
        }
    });
}
</script>
@endsection



