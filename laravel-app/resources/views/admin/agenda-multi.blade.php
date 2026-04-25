@extends('layouts.app')

@section('title', 'Agenda Clínica Multi-Profissional')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .agenda-grid {
        display: grid;
        grid-template-columns: 100px repeat({{ $professionals->count() }}, minmax(200px, 1fr));
        overflow-x: auto;
    }
    .time-slot {
        height: 60px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        border-right: 1px solid rgba(255, 255, 255, 0.03);
    }
    .appointment-box {
        background: rgba(59, 130, 246, 0.1);
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        margin: 2px;
        padding: 4px 8px;
        font-size: 0.7rem;
        overflow: hidden;
    }
    .status-badge {
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 1px 4px;
        border-radius: 4px;
    }
    .status-scheduled { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .status-confirmed { background: rgba(16, 185, 129, 0.2); color: #10b981; }
    .status-in_progress { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .status-finished { background: rgba(107, 114, 128, 0.2); color: #9ca3af; }
    .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-20">
    <div class="max-w-[95%] mx-auto py-10">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black tracking-tighter italic uppercase">Agenda da Clínica</h1>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Visão Geral Multi-Profissional — {{ \Carbon\Carbon::parse($date)->translatedFormat('d \d\e F, Y') }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <form action="{{ route('agenda.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" 
                        class="bg-zinc-900 border-none rounded-xl text-xs font-black uppercase p-3 focus:ring-1 ring-blue-500">
                </form>
                
                <button class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20">
                    Novo Agendamento
                </button>
            </div>
        </div>

        @if($professionals->isEmpty())
            <div class="glass-card rounded-[3rem] p-20 text-center">
                <div class="w-20 h-20 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-md text-zinc-700 text-3xl"></i>
                </div>
                <h2 class="text-xl font-black uppercase italic mb-2">Nenhum profissional encontrado</h2>
                <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest">Certifique-se de que os profissionais estão vinculados à sua clínica.</p>
            </div>
        @else
            <!-- Grid Agenda -->
            <div class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5">
                
                <!-- Professionals Header -->
                <div class="agenda-grid bg-zinc-900/50 border-bottom border-white/10">
                    <div class="p-6 text-center border-right border-white/5 flex items-center justify-center">
                        <i class="fas fa-clock text-zinc-600"></i>
                    </div>
                    @foreach($professionals as $pro)
                    <div class="p-6 text-center border-right border-white/5">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-800 mx-auto mb-3 flex items-center justify-center overflow-hidden">
                            @if($pro->avatar)
                                <img src="{{ $pro->avatar }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs font-black">{{ substr($pro->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-tighter">{{ $pro->name }}</h4>
                        <p class="text-[8px] text-zinc-500 uppercase font-bold">{{ $pro->professionalProfile->specialty ?? 'Geral' }}</p>
                    </div>
                    @endforeach
                </div>

                <!-- Time Slots -->
                <div class="agenda-grid bg-black/20">
                    @php
                        $startHour = 7;
                        $endHour = 21;
                    @endphp

                    @for($hour = $startHour; $hour <= $endHour; $hour++)
                        <!-- Time Column -->
                        <div class="time-slot flex items-center justify-center bg-zinc-950/40">
                            <span class="text-[10px] font-black text-zinc-600">{{ sprintf('%02d:00', $hour) }}</span>
                        </div>

                        <!-- Professional Columns -->
                        @foreach($professionals as $pro)
                            <div class="time-slot relative group hover:bg-white/[0.02] transition-colors">
                                @php
                                    $apps = $appointments->filter(function($a) use ($pro, $hour) {
                                        return $a->professional_id == $pro->id && $a->appointment_at->hour == $hour;
                                    });
                                @endphp

                                @foreach($apps as $app)
                                    <div class="appointment-box absolute inset-x-0 z-10 cursor-pointer hover:scale-[1.02] transition-transform" 
                                         style="top: {{ ($app->appointment_at->minute / 60) * 100 }}%;">
                                        <div class="flex justify-between items-start gap-1">
                                            <span class="font-black truncate">{{ $app->patient->name }}</span>
                                            <span class="status-badge status-{{ $app->status }}">{{ substr($app->status, 0, 3) }}</span>
                                        </div>
                                        <div class="text-[8px] opacity-70 flex justify-between">
                                            <span>{{ $app->appointment_at->format('H:i') }}</span>
                                            <span class="truncate">{{ $app->service_type }}</span>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Quick Add Button (Visible on Hover) -->
                                <button class="absolute inset-0 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <i class="fas fa-plus-circle text-blue-500/30 text-lg"></i>
                                </button>
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>
        @endif

    </div>
</div>

@section('scripts')
<script>
    // Interatividade futura: drag and drop, abrir modal de agendamento, etc.
</script>
@endsection
@endsection
