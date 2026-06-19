@extends('layouts.app')

@section('title', 'Dashboard Executivo — NEX SHAPE PRO')

@section('content')
<div class="py-10 space-y-8 animate-dashboard-entry mx-auto px-4 lg:px-8 max-w-[1600px]">
    
    <!-- SEÇÃO DE BOAS-VINDAS GERAL -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-8 bg-zinc-900/60 backdrop-blur-2xl rounded-[2.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700 pointer-events-none"></div>
        <div class="relative z-10 space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter">
                Seja bem-vindo ao NexShape, <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400">{{ explode(' ', $professional->name)[0] }}</span>
            </h1>
            <p class="text-zinc-400 font-medium text-lg">
                <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1 -mt-1 text-zinc-500"></i> {{ now()->translatedFormat('d \d\e F \d\e Y') }} 
                <span class="mx-2 text-zinc-700">•</span> 
                <i data-lucide="clock" class="w-4 h-4 inline-block mr-1 -mt-1 text-zinc-500"></i> {{ now()->format('H:i') }}
            </p>
            <p class="text-emerald-400/80 text-sm font-bold uppercase tracking-widest mt-2">
                Resumo de Hoje: {{ count($todayAppointments) }} consultas agendadas, {{ $pendingAssessmentsCount }} avaliações pendentes, {{ $unreadMessagesCount }} mensagens não lidas.
            </p>
        </div>
        <div class="relative z-10 flex gap-4">
            <a href="{{ route('professional.profile.edit') }}" class="w-14 h-14 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded-2xl flex items-center justify-center transition-all border border-white/5 shadow-lg">
                <i data-lucide="user" class="w-6 h-6"></i>
            </a>
            <div class="text-right flex flex-col justify-center">
                <span class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">NEXLINK ID</span>
                <span class="text-xl font-black text-white">{{ $professional->professional_code }}</span>
            </div>
        </div>
    </div>

    @if(!$activePatient)
        <!-- ESTADO: NENHUM PACIENTE SELECIONADO -->
        <div class="flex flex-col items-center justify-center p-12 bg-zinc-900/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-zinc-950/50 pointer-events-none"></div>
            <div class="w-20 h-20 mb-6 rounded-full bg-zinc-800/50 flex items-center justify-center border border-zinc-700/50 relative z-10">
                <i data-lucide="users" class="w-10 h-10 text-zinc-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tighter mb-2 relative z-10 text-center">Nenhum {{ strtolower($patientLabel) }} selecionado</h2>
            <p class="text-zinc-400 font-medium text-center max-w-lg mb-8 relative z-10">Para realizar ações clínicas como prescrever treinos ou avaliações, você precisa selecionar um {{ strtolower($patientLabel) }} primeiro.</p>
            
            <div class="flex items-center gap-4 relative z-10" x-data>
                <button @click="$dispatch('open-global-patient-selector')" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Selecionar {{ $patientLabel }}
                </button>
                <a href="{{ route('professional.patients.create') }}" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl transition-all border border-zinc-700 flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4 text-emerald-500"></i>
                    Cadastrar
                </a>
            </div>
        </div>
    @else
        <!-- ESTADO: PACIENTE SELECIONADO -->
        <div class="bg-emerald-950/20 backdrop-blur-2xl p-8 rounded-[3rem] border border-emerald-500/20 shadow-[0_0_50px_-12px_rgba(16,185,129,0.1)] relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row gap-8 items-start relative z-10">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">{{ mb_strtoupper($patientLabel) }} ATIVO NO CONTEXTO</span>
                        @if($activePatientStats['status'] === 'Ativo')
                            <span class="flex items-center gap-1 text-[9px] font-black text-emerald-500 uppercase tracking-widest"><i data-lucide="check-circle" class="w-3 h-3"></i> Ativo</span>
                        @else
                            <span class="flex items-center gap-1 text-[9px] font-black text-zinc-500 uppercase tracking-widest"><i data-lucide="clock" class="w-3 h-3"></i> Inativo</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-emerald-500/30 shrink-0">
                            <img src="{{ $activePatient->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($activePatient->name).'&color=10b981&background=09090b&bold=true' }}" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-white tracking-tighter">{{ $activePatient->name }}</h2>
                            <p class="text-sm font-bold text-emerald-400/80">{{ $activePatientStats['active_plan'] }}</p>
                            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mt-1">Último Acesso: {{ $activePatient->last_activity_at ? \Carbon\Carbon::parse($activePatient->last_activity_at)->diffForHumans() : 'Nunca' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Cards Rapidas do Paciente -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full md:w-auto">
                    <div class="p-4 bg-zinc-900/50 rounded-2xl border border-white/5">
                        <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Última Avaliação</p>
                        <p class="text-sm font-bold text-white">{{ $activePatientStats['last_assessment'] }}</p>
                    </div>
                    <div class="p-4 bg-zinc-900/50 rounded-2xl border border-white/5">
                        <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Próxima Consulta</p>
                        <p class="text-sm font-bold text-white">{{ $activePatientStats['next_appointment'] }}</p>
                    </div>
                    <div class="p-4 bg-zinc-900/50 rounded-2xl border border-white/5">
                        <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Último Treino</p>
                        <p class="text-sm font-bold text-white">{{ $activePatientStats['last_training'] }}</p>
                    </div>
                    <div class="p-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 flex flex-col justify-center items-center group-hover:bg-emerald-500/20 transition-all cursor-pointer">
                        <a href="{{ route('professional.patients.show', $activePatient->id) }}" class="text-emerald-500 text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                            Abrir Prontuário <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ações Clínicas Liberadas -->
            <div class="mt-8 border-t border-white/5 pt-6">
                <h3 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-4">Ações Clínicas para {{ explode(' ', $activePatient->name)[0] }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                    <a href="{{ route('progression.plans.index') }}" class="flex flex-col items-center justify-center p-3 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-emerald-500/50 transition-all text-center">
                        <i data-lucide="dumbbell" class="w-5 h-5 text-emerald-400 mb-2"></i>
                        <span class="text-[9px] font-black text-zinc-300 uppercase leading-tight">Criar Treino</span>
                    </a>
                    <a href="{{ route('assessments.index') }}" class="flex flex-col items-center justify-center p-3 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-emerald-500/50 transition-all text-center">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-emerald-400 mb-2"></i>
                        <span class="text-[9px] font-black text-zinc-300 uppercase leading-tight">Avaliações</span>
                    </a>
                    <a href="{{ route('professional.patients.index') }}" class="flex flex-col items-center justify-center p-3 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-emerald-500/50 transition-all text-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-400 mb-2"></i>
                        <span class="text-[9px] font-black text-zinc-300 uppercase leading-tight">Evolução</span>
                    </a>
                    <a href="{{ route('professional.patients.index') }}" class="flex flex-col items-center justify-center p-3 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-emerald-500/50 transition-all text-center">
                        <i data-lucide="folder" class="w-5 h-5 text-emerald-400 mb-2"></i>
                        <span class="text-[9px] font-black text-zinc-300 uppercase leading-tight">Arquivos</span>
                    </a>
                    <a href="{{ route('messages.index') }}" class="flex flex-col items-center justify-center p-3 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-emerald-500/50 transition-all text-center">
                        <i data-lucide="message-square" class="w-5 h-5 text-emerald-400 mb-2"></i>
                        <span class="text-[9px] font-black text-zinc-300 uppercase leading-tight">Mensagens</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- ATALHOS RÁPIDOS GLOBAIS (Produtividade) -->
    <div>
        <h3 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-4 ml-2">Atalhos de Produtividade</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
            @foreach($quickShortcuts as $shortcut)
            <a href="{{ $shortcut['route'] }}" class="flex flex-col items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-md rounded-3xl border border-white/5 hover:border-{{ $shortcut['color'] }}-500/50 hover:bg-{{ $shortcut['color'] }}-500/10 transition-all group shadow-lg aspect-square text-center">
                <div class="w-10 h-10 mb-3 rounded-full bg-{{ $shortcut['color'] }}-500/10 text-{{ $shortcut['color'] }}-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="{{ $shortcut['icon'] }}" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-black text-zinc-300 uppercase leading-tight group-hover:text-white transition-colors">{{ $shortcut['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- MAIN GRID (2 COLUMNS) - DASHBOARD OPERACIONAL -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        <!-- LEFT COLUMN: Alunos & Indicadores -->
        <div class="xl:col-span-8 space-y-8">
            
            <!-- MEUS PACIENTES / ALUNOS -->
            <div class="bg-zinc-900/40 backdrop-blur-2xl p-8 rounded-[3rem] border border-white/5 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-white tracking-tighter">{{ mb_strtoupper($patientsLabel) }}</h2>
                    <a href="{{ route('professional.patients.index') }}" class="text-[10px] font-black text-emerald-400 uppercase tracking-widest hover:text-white transition-colors">Gerenciar Base &rarr;</a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="p-6 bg-emerald-500/5 rounded-3xl border border-emerald-500/10 hover:border-emerald-500/30 transition-colors">
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mb-2">Ativos</p>
                        <p class="text-4xl font-black text-emerald-400 tracking-tighter">{{ $activePatientsCount }}</p>
                    </div>
                    <div class="p-6 bg-rose-500/5 rounded-3xl border border-rose-500/10 hover:border-rose-500/30 transition-colors">
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mb-2">Inativos</p>
                        <p class="text-4xl font-black text-rose-400 tracking-tighter">{{ $inactivePatientsCount }}</p>
                    </div>
                    <div class="p-6 bg-blue-500/5 rounded-3xl border border-blue-500/10 hover:border-blue-500/30 transition-colors">
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mb-2">Novos (Mês)</p>
                        <p class="text-4xl font-black text-blue-400 tracking-tighter">+{{ $newPatientsMonth }}</p>
                    </div>
                    <div class="p-6 bg-amber-500/5 rounded-3xl border border-amber-500/10 hover:border-amber-500/30 transition-colors">
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mb-2">Aniversariantes</p>
                        <p class="text-4xl font-black text-amber-400 tracking-tighter">{{ $birthdayPatientsCount }}</p>
                    </div>
                </div>
            </div>

            <!-- ATENDIMENTOS E INDICADORES -->
            <div class="bg-zinc-900/40 backdrop-blur-2xl p-8 rounded-[3rem] border border-white/5 shadow-2xl">
                <h2 class="text-2xl font-black text-white tracking-tighter mb-8">Atendimentos & Indicadores</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="p-5 bg-white/5 rounded-3xl border border-white/5 text-center">
                        <i data-lucide="dumbbell" class="w-6 h-6 text-emerald-400 mx-auto mb-2"></i>
                        <p class="text-3xl font-black text-white tracking-tighter">{{ $activeWorkoutsCount }}</p>
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mt-1">Treinos Ativos</p>
                    </div>
                    <div class="p-5 bg-white/5 rounded-3xl border border-white/5 text-center">
                        <i data-lucide="clipboard-list" class="w-6 h-6 text-purple-400 mx-auto mb-2"></i>
                        <p class="text-3xl font-black text-white tracking-tighter">{{ $assessmentsMonthCount }}</p>
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mt-1">Av. no Mês</p>
                    </div>
                    <div class="p-5 bg-white/5 rounded-3xl border border-white/5 text-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-blue-400 mx-auto mb-2"></i>
                        <p class="text-3xl font-black text-white tracking-tighter">{{ $appointmentsCompletedMonth }}</p>
                        <p class="text-zinc-500 text-[9px] font-black uppercase tracking-widest mt-1">Consultas Concluídas</p>
                    </div>
                    <div class="p-5 bg-emerald-500/10 rounded-3xl border border-emerald-500/20 text-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-emerald-400 mx-auto mb-2"></i>
                        <p class="text-xl font-black text-emerald-400 tracking-tighter mt-1">R$ {{ number_format($revenueMonth, 2, ',', '.') }}</p>
                        <p class="text-emerald-500/70 text-[9px] font-black uppercase tracking-widest mt-1">Receita Mensal</p>
                    </div>
                </div>
            </div>

            <!-- ATIVIDADE RECENTE -->
            <div class="bg-zinc-900/40 backdrop-blur-2xl p-8 rounded-[3rem] border border-white/5 shadow-2xl">
                <h2 class="text-2xl font-black text-white tracking-tighter mb-8">Atividade Recente</h2>
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-zinc-800 before:to-transparent">
                    @foreach($recentActivities as $act)
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-zinc-950 bg-{{ $act['color'] }}-500 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                            <i data-lucide="{{ $act['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-2xl bg-white/5 border border-white/5 shadow-lg group-hover:bg-white/10 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-black text-zinc-500 uppercase tracking-widest">{{ $act['time'] }}</p>
                            </div>
                            <p class="text-sm font-bold text-white">{{ $act['text'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
        </div>

        <!-- RIGHT COLUMN: Agenda, Pendências, Atenção, Dicas -->
        <div class="xl:col-span-4 space-y-8">
            
            <!-- AGENDA DE HOJE -->
            <div class="bg-zinc-900/60 backdrop-blur-2xl p-8 rounded-[3rem] border border-white/5 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-white tracking-tighter">Agenda de Hoje</h2>
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-500/20">{{ count($todayAppointments) }} Sessões</span>
                </div>
                
                <div class="space-y-4 mb-6">
                    @forelse($todayAppointments as $app)
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl border border-white/5">
                        <div class="text-center w-14">
                            <p class="text-lg font-black text-white leading-none">{{ \Carbon\Carbon::parse($app->appointment_at)->format('H:i') }}</p>
                        </div>
                        <div class="w-px h-8 bg-zinc-800"></div>
                        <div class="flex-1">
                            <p class="text-sm font-black text-white truncate">{{ $app->patient?->name ?? 'Externo' }}</p>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-0.5">{{ $app->service_type ?? 'Consulta Geral' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i data-lucide="calendar-x" class="w-10 h-10 text-zinc-700 mx-auto mb-3"></i>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Nenhuma consulta hoje</p>
                    </div>
                    @endforelse
                </div>

                <a href="{{ route('agenda.index') }}" class="block w-full text-center py-4 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all text-[10px] uppercase tracking-[0.2em] shadow-lg">
                    Ver Agenda Completa
                </a>
            </div>

            <!-- COMUNICAÇÃO E PENDÊNCIAS -->
            <div class="bg-zinc-900/60 backdrop-blur-2xl p-8 rounded-[3rem] border border-rose-500/10 shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/5 rounded-full blur-3xl group-hover:bg-rose-500/10 transition-all pointer-events-none"></div>
                <h2 class="text-2xl font-black text-white tracking-tighter mb-8 relative z-10">Comunicação & Tarefas</h2>
                
                <div class="space-y-3 relative z-10">
                    <div class="flex items-center justify-between p-4 bg-rose-500/5 rounded-2xl border border-rose-500/10">
                        <div class="flex items-center gap-3">
                            <i data-lucide="clipboard-x" class="w-4 h-4 text-rose-400"></i>
                            <span class="text-sm font-bold text-white">Avaliações Pendentes</span>
                        </div>
                        <span class="text-rose-400 font-black">{{ $pendingAssessmentsCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-amber-500/5 rounded-2xl border border-amber-500/10">
                        <div class="flex items-center gap-3">
                            <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
                            <span class="text-sm font-bold text-white">Consultas Pendentes</span>
                        </div>
                        <span class="text-amber-400 font-black">{{ $pendingAppointmentsCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-500/5 rounded-2xl border border-blue-500/10">
                        <div class="flex items-center gap-3">
                            <i data-lucide="message-circle" class="w-4 h-4 text-blue-400"></i>
                            <span class="text-sm font-bold text-white">Mensagens Não Lidas</span>
                        </div>
                        <span class="text-blue-400 font-black">{{ $unreadMessagesCount }}</span>
                    </div>
                </div>
            </div>

            <!-- DICAS INTELIGENTES (NexSense) -->
            <div class="bg-gradient-to-br from-indigo-900/40 to-purple-900/40 backdrop-blur-2xl p-8 rounded-[3rem] border border-indigo-500/20 shadow-2xl relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center border border-indigo-500/30">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <h2 class="text-xl font-black text-white tracking-tighter">Dicas Inteligentes</h2>
                </div>
                
                <div class="space-y-4">
                    @foreach($smartTips as $tip)
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-sm font-medium text-indigo-100">{{ $tip }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes dashboard-entry { 
    from { opacity: 0; transform: translateY(20px) scale(0.98); filter: blur(10px); } 
    to { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); } 
}
.animate-dashboard-entry { 
    animation: dashboard-entry 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; 
}
</style>
@endsection
