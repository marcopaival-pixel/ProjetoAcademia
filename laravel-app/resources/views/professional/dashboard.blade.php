@extends('layouts.app')

@section('title', 'Portal Pro — NexShape Business')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry mx-auto px-4 md:px-6" x-data="{ showFinance: true }">
    <!-- Quick Actions Bar (New Tool) -->
    <div class="flex flex-wrap items-center gap-4 p-4 bg-zinc-900/60 backdrop-blur-2xl rounded-3xl border border-white/5 shadow-2xl overflow-x-auto no-scrollbar">
        <span class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.3em] px-4 border-r border-white/5 mr-2">Ações Rápidas</span>
        
        <a href="{{ route('professional.profile.edit') }}" class="flex items-center gap-2 px-6 py-3 bg-zinc-800 text-zinc-300 font-black rounded-2xl hover:bg-zinc-700 transition-all text-[10px] shrink-0 border border-white/5">
            <i data-lucide="user-cog" class="w-4 h-4"></i> MEU PERFIL
        </a>
        <a href="{{ route('professional.patients.create') }}" class="flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all text-[10px] shrink-0 border border-emerald-400/20 shadow-lg shadow-emerald-500/10">
            <i data-lucide="user-plus" class="w-4 h-4"></i> NOVO PACIENTE
        </a>
        <a href="{{ route('exercise') }}" class="flex items-center gap-2 px-6 py-3 bg-zinc-800 text-zinc-300 font-black rounded-2xl hover:bg-zinc-700 transition-all text-[10px] shrink-0 border border-white/5">
            <i data-lucide="dumbbell" class="w-4 h-4"></i> PRESCREVER TREINO
        </a>
        <a href="{{ route('nutrition.index') }}" class="flex items-center gap-2 px-6 py-3 bg-zinc-800 text-zinc-300 font-black rounded-2xl hover:bg-zinc-700 transition-all text-[10px] shrink-0 border border-white/5">
            <i data-lucide="utensils" class="w-4 h-4"></i> NOVA DIETA
        </a>
        <a href="{{ route('assessments.index') }}" class="flex items-center gap-2 px-6 py-3 bg-zinc-800 text-zinc-300 font-black rounded-2xl hover:bg-zinc-700 transition-all text-[10px] shrink-0 border border-white/5">
            <i data-lucide="clipboard-check" class="w-4 h-4"></i> AVALIAÇÃO
        </a>
        <a href="{{ route('kb.index') }}" class="flex items-center gap-2 px-6 py-3 bg-zinc-800 text-emerald-400 font-black rounded-2xl hover:bg-zinc-700 transition-all text-[10px] shrink-0 border border-emerald-500/10">
            <i data-lucide="help-circle" class="w-4 h-4"></i> CENTRAL DE AJUDA
        </a>

        <div class="ml-auto flex items-center gap-3 pr-4">
            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest" x-text="showFinance ? 'PRIVACIDADE OFF' : 'PRIVACIDADE ON'"></span>
            <button @click="showFinance = !showFinance" class="w-12 h-6 rounded-full bg-zinc-800 relative transition-all border border-white/5" :class="!showFinance ? 'bg-emerald-600' : 'bg-zinc-800'">
                <div class="absolute top-1 w-4 h-4 rounded-full bg-white transition-all shadow-md" :class="!showFinance ? 'left-7' : 'left-1'"></div>
            </button>
        </div>
    </div>

    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-8 border-b border-white/5">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">Unidade Clínica Ativa</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-6xl font-black tracking-tighter text-white leading-none">
                Gestão Clínica de <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-400 to-teal-500">Alta Performance</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-2xl text-lg">Bem-vindo ao centro de inteligência NexShape. Analisamos seus dados para potencializar a retenção e os resultados dos seus pacientes.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-3 p-2 bg-zinc-900/40 backdrop-blur-2xl rounded-[2rem] border border-white/5 shadow-2xl">
                <a href="{{ route('professional.patients.index') }}" class="group relative px-8 py-4 bg-emerald-600 text-white font-black rounded-2xl overflow-hidden transition-all hover:pr-12 active:scale-95 shadow-lg shadow-emerald-500/20">
                    <span class="relative z-10">PACIENTES</span>
                    <i data-lucide="arrow-right" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-0 group-hover:opacity-100 transition-all"></i>
                </a>
                <a href="{{ route('professional.ai-wizard.index') }}" class="px-8 py-4 bg-zinc-800 text-zinc-300 hover:bg-zinc-700 hover:text-white font-black rounded-2xl transition-all border border-white/5">IA WIZARD</a>
            </div>
        </div>
    </div>

    <!-- Tier & Patient Limit Status -->
    @php
        $currentPlan = auth()->user()->professionalPlan;
        $maxPatients = $currentPlan->max_patients ?? 0;
        $usagePercent = $maxPatients > 0 ? ($stats['total_patients'] / $maxPatients) * 100 : 0;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-gradient-to-r from-emerald-600/5 to-transparent p-8 rounded-[3rem] border border-emerald-500/10">
        <div class="lg:col-span-4">
            <h4 class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Plano Atual</h4>
            <div class="flex items-center gap-4">
                <span class="text-3xl font-black text-white italic uppercase">{{ $currentPlan->name ?? 'Grátis' }}</span>
                @if($maxPatients > 0 && $usagePercent >= 80)
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[9px] font-black rounded-full border border-amber-500/20 animate-pulse">LIMITE PRÓXIMO</span>
                @endif
            </div>
        </div>
        <div class="lg:col-span-6">
            <div class="flex justify-between items-end mb-3">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Capacidade de Pacientes</span>
                <span class="text-sm font-black text-white">{{ $stats['total_patients'] }} <span class="text-zinc-600">/</span> {{ $maxPatients > 0 ? $maxPatients : '∞' }}</span>
            </div>
            <div class="h-3 bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-400 shadow-[0_0_15px_rgba(16,185,129,0.4)] transition-all duration-1000" style="width: {{ $maxPatients > 0 ? min(100, $usagePercent) : 100 }}%"></div>
            </div>
        </div>
        <div class="lg:col-span-2 text-right">
            <button class="px-6 py-3 bg-white text-zinc-900 font-black rounded-2xl hover:bg-emerald-400 hover:text-white transition-all text-xs shadow-xl">UPGRADE</button>
        </div>
    </div>

    <!-- Readiness Alert Banner -->
    @if(!$readiness['is_ready'])
    <div class="bg-gradient-to-r from-amber-600 to-orange-700 p-6 rounded-[2.5rem] border border-amber-500/20 shadow-2xl animate-pulse-slow">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-xl rounded-[1.5rem] flex items-center justify-center border border-white/20 shadow-xl">
                    <i class="fas fa-exclamation-triangle text-3xl text-amber-300"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-white italic tracking-tighter leading-none uppercase">Configuração Incompleta</h3>
                    <p class="text-sm font-medium text-amber-100 mt-2">Sua conta ainda não está totalmente pronta para operar. Conclua os itens abaixo para liberar todas as funcionalidades.</p>
                </div>
            </div>
            <a href="#readiness-checklist" class="px-8 py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-zinc-900 hover:text-white transition-all text-xs shadow-2xl">
                CONCLUIR AGORA
            </a>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8 space-y-10">
            <!-- Readiness Checklist Section -->
            <div id="readiness-checklist">
                @include('professional.partials.readiness_checklist')
            </div>

            <!-- Growth Chart & Financials -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Chart Area -->
                <div class="md:col-span-2 bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3.5rem] p-10 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    <div class="flex justify-between items-start mb-10">
                        <div>
                            <h3 class="text-xl font-black text-white leading-none">Adesão Diária</h3>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-2">Engajamento médio da base (7d)</p>
                        </div>
                        <div class="flex items-center gap-2">
                           <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                           <span class="text-[10px] text-zinc-400 font-bold uppercase">% ENGAGE</span>
                        </div>
                    </div>
                    
                    <div class="h-64">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>

                <!-- Financial Card -->
                <div class="bg-zinc-900/60 border border-white/5 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden flex flex-col justify-between">
                    <div class="relative z-10">
                        <h4 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-4">Estimativa SaaS Business</h4>
                        
                        <div class="space-y-6">
                            <div>
                                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mb-1 italic">FATURAMENTO (MÊS)</p>
                                <span class="text-4xl font-black text-white italic tracking-tighter transition-all" :class="!showFinance && 'blur-md select-none'">
                                    {{ $stats['revenue_month'] }}
                                </span>
                            </div>
                            
                            <div class="pt-6 border-t border-white/5">
                                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mb-1 italic">PROJEÇÃO (CAPACIDADE)</p>
                                <span class="text-2xl font-black text-zinc-400 italic tracking-tighter transition-all" :class="!showFinance && 'blur-md select-none'">
                                    {{ $stats['projected_revenue'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between">
                        <div class="px-4 py-2 bg-emerald-500/10 rounded-xl border border-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase">
                           +{{ $stats['growth'] }}% GROW
                        </div>
                        <i data-lucide="landmark" class="text-zinc-800 w-10 h-10"></i>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $metrics = [
                        ['label' => 'Pacientes', 'val' => $stats['total_patients'], 'sub' => 'Base Cadastrada', 'color' => 'emerald', 'icon' => 'users'],
                        ['label' => 'Ativos (7d)', 'val' => $stats['active_patients'], 'sub' => $stats['inactive_patients'] . ' sem logs', 'color' => 'teal', 'icon' => 'user-check'],
                        ['label' => 'Treinos Ativos', 'val' => $stats['active_workouts'], 'sub' => 'Vigentes hoje', 'color' => 'emerald', 'icon' => 'dumbbell'],
                        ['label' => 'Pendências', 'val' => $stats['pending_assessments'], 'sub' => 'Ações necessárias', 'color' => 'rose', 'icon' => 'alert-triangle'],
                    ];
                @endphp

                @foreach($metrics as $m)
                <div class="group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/5 p-8 rounded-[3rem] overflow-hidden transition-all hover:border-{{ $m['color'] }}-500/30 hover:-translate-y-1 shadow-xl">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-{{ $m['color'] }}-500/5 blur-3xl rounded-full"></div>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">{{ $m['label'] }}</p>
                        <i data-lucide="{{ $m['icon'] }}" class="text-{{ $m['color'] }}-500/40 w-4 h-4"></i>
                    </div>
                    <h3 class="text-5xl font-black text-white mt-1 tracking-tighter">{{ $m['val'] }}</h3>
                    <p class="text-{{ $m['color'] }}-400 text-[10px] font-bold uppercase mt-2 opacity-80 group-hover:opacity-100 transition-opacity">{{ $m['sub'] }}</p>
                </div>
                @endforeach
            </div>

            <!-- Agenda do Dia -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 shadow-2xl">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-white">Agenda do Dia</h3>
                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Sessões e atendimentos agendados</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @forelse($todayAppointments as $app)
                    <div class="flex items-center gap-6 p-6 bg-white/5 rounded-3xl border border-white/5 hover:border-emerald-500/20 transition-all cursor-pointer group">
                        <div class="text-right w-20">
                            <p class="text-xl font-black text-white leading-none">{{ \Carbon\Carbon::parse($app->appointment_at)->format('H:i') }}</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">UTC-3</p>
                        </div>
                        <div class="w-px h-12 bg-zinc-800"></div>
                        <div class="flex-1">
                            <h4 class="text-white font-black text-lg leading-tight group-hover:text-emerald-400 transition-colors">{{ $app->patient?->name ?? 'Paciente Externo' }}</h4>
                            <p class="text-xs text-zinc-500 font-medium">{{ $app->service_type ?? 'Consulta Geral' }}</p>
                        </div>
                        <span class="px-4 py-2 bg-emerald-500/10 text-emerald-400 text-[10px] font-black rounded-xl border border-emerald-500/10">CONFIRMADO</span>
                    </div>
                    @empty
                    <div class="py-12 text-center">
                        <div class="w-20 h-20 bg-zinc-800/50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="calendar-x" class="w-8 h-8 text-zinc-700"></i>
                        </div>
                        <h4 class="text-zinc-500 font-black text-lg">Nenhum atendimento para hoje</h4>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Tabela de Aderência -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between mb-10 px-4">
                    <h3 class="text-2xl font-black text-white leading-none">Aderência Recente</h3>
                    <a href="{{ route('professional.patients.index') }}" class="text-[10px] font-black text-emerald-400 uppercase tracking-widest hover:text-white transition-colors">Ver Todos &rarr;</a>
                </div>
                
                <div class="overflow-x-auto px-4">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-zinc-700 text-[10px] font-black uppercase tracking-[0.3em] border-b border-white/5">
                                <th class="pb-6">PACIENTE</th>
                                <th class="pb-6">BIO-STATUS / EVOLUÇÃO</th>
                                <th class="pb-6">PRONTUÁRIO</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($recentPatients as $p)
                            <tr class="group hover:bg-white/5 transition-all cursor-pointer">
                                <td class="py-10">
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 rounded-[1.75rem] bg-gradient-to-tr {{ $p['color'] }} flex items-center justify-center text-white font-black text-xl shadow-2xl group-hover:scale-110 transition-transform">
                                            {{ $p['initials'] }}
                                        </div>
                                        <div>
                                            <p class="text-white font-black text-xl group-hover:text-emerald-400 transition-colors">{{ $p['name'] }}</p>
                                            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mt-1">{{ $p['bio'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-3 w-48">
                                        <div class="flex justify-between items-end">
                                            <span class="text-[9px] font-black text-zinc-500 uppercase">{{ $p['status'] }}</span>
                                            <span class="text-sm font-black text-white">{{ $p['engage'] }}%</span>
                                        </div>
                                        <div class="h-2 bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                                            <div class="h-full bg-gradient-to-r {{ $p['color'] }} rounded-full" style="width: {{ $p['engage'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right pr-6">
                                    <div class="flex gap-2 justify-end">
                                        <a href="{{ route('professional.patients.show', $p['id']) }}" class="p-4 bg-zinc-800 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all border border-white/5 flex items-center gap-2">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                            <span class="text-[9px] font-black uppercase">Ver Prontuário</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side: Sidebar Widgets -->
        <div class="lg:col-span-4 space-y-10">
            <!-- Professional IQ Card -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-10 rounded-[4rem] shadow-2xl text-white relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
                <div class="relative z-10 text-center">
                    <div class="flex flex-col items-center mb-8">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-xl rounded-[2rem] flex items-center justify-center border border-white/20 mb-4 shadow-3xl">
                            <i data-lucide="brain" class="w-10 h-10"></i>
                        </div>
                        <h3 class="text-2xl font-black italic tracking-tighter">NEXSENSE INTEL</h3>
                        <p class="text-[9px] font-bold uppercase tracking-[0.3em] opacity-60">Insight Automático Ativo</p>
                    </div>

                    <div class="space-y-6">
                        @foreach($tasks as $task)
                        <div class="p-6 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/10 hover:bg-white/20 transition-all cursor-pointer text-left group/task">
                            <div class="flex gap-5">
                                <div class="mt-1.5 w-3 h-3 rounded-full shrink-0 @if($task['priority'] == 'critical') bg-red-400 animate-ping @elseif($task['priority'] == 'high') bg-orange-400 @else bg-cyan-400 @endif shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
                                <div>
                                    <p class="text-sm font-black leading-tight group-hover/task:text-emerald-200 transition-colors">{{ $task['msg'] }}</p>
                                    @if(isset($task['type']))
                                        <p class="text-[9px] font-bold uppercase opacity-50 mt-1 italic tracking-widest">{{ $task['type'] }} alert</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if(empty($tasks))
                        <div class="py-10">
                           <i data-lucide="check-circle" class="w-10 h-10 mx-auto opacity-20 mb-4"></i>
                           <p class="text-sm font-bold opacity-80 italic">Tudo em conformidade.</p>
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('professional.ai-wizard.index') }}" class="mt-10 flex items-center justify-between w-full p-2 pr-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-zinc-900 hover:text-white transition-all group/btn shadow-2xl">
                        <div class="h-12 w-12 bg-zinc-900 text-white rounded-2xl flex items-center justify-center group-hover/btn:bg-white group-hover/btn:text-zinc-900 transition-colors">
                            <i data-lucide="wand-2" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px]">IA WIZARD HUB</span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Social Intelligence -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 shadow-2xl">
                <h3 class="text-xl font-black text-white mb-8">Social Intelligence</h3>
                <div class="space-y-6">
                    @forelse($birthdayPatients as $bp)
                    <div class="flex items-center gap-4 p-5 bg-emerald-500/5 border border-emerald-500/10 rounded-[2.5rem] hover:bg-emerald-500/10 transition-all">
                        <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg">
                            🎂
                        </div>
                        <div class="flex-1">
                            <p class="text-white font-black text-sm">{{ $bp->name }}</p>
                            <p class="text-[9px] text-emerald-400 font-black uppercase tracking-widest">Aniversariante hoje!</p>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $bp->phone ?? '') }}?text=Parabéns pelo seu dia, {{ explode(' ', $bp->name)[0] }}! Muita saúde e bons treinos. 🚀" target="_blank" class="w-12 h-12 bg-emerald-600/20 text-emerald-400 rounded-2xl flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                             <i data-lucide="message-circle" class="w-5 h-5"></i>
                        </a>
                    </div>
                    @empty
                    <div class="py-6 text-center">
                        <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-[0.2em]">Nenhum aniversário hoje</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Central de Ajuda & Suporte -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 shadow-2xl relative overflow-hidden group/kb">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover/kb:bg-emerald-600/20 transition-all"></div>
                <div class="relative z-10">
                    <h3 class="text-xl font-black text-white mb-2 leading-none italic uppercase">Suporte & Tutoriais</h3>
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-[0.2em] mb-8">Base de Conhecimento NexShape</p>
                    
                    <div class="space-y-4">
                        <a href="{{ route('kb.index') }}" class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-emerald-600/10 hover:border-emerald-500/30 transition-all group/item">
                            <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center text-zinc-500 group-hover/item:text-emerald-400">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-white font-black text-xs leading-tight">Guia de Primeiros Passos</p>
                                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-0.5">Aprenda a configurar sua clínica</p>
                            </div>
                        </a>
                        <a href="{{ route('kb.index') }}" class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-emerald-600/10 hover:border-emerald-500/30 transition-all group/item">
                            <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center text-zinc-500 group-hover/item:text-emerald-400">
                                <i data-lucide="video" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-white font-black text-xs leading-tight">Vídeo Aulas & Dicas</p>
                                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-0.5">Tutoriais em vídeo</p>
                            </div>
                        </a>
                    </div>

                    <a href="{{ route('kb.index') }}" class="mt-8 block text-center py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                        Acessar Central Completa
                    </a>
                </div>
            </div>

            <!-- NexLink ID -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 shadow-2xl overflow-hidden relative group/qr">
                <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover/qr:opacity-100 transition-opacity"></div>
                <div class="relative z-10 text-center">
                    <h3 class="text-lg font-black text-white mb-1 leading-none italic uppercase">NexLink ID</h3>
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-[0.2em] mb-8">Vínculo Direto</p>
                    
                    <div class="p-6 bg-zinc-950 rounded-[2.5rem] border border-white/10 inline-block mx-auto mb-6 shadow-3xl group-hover/qr:scale-105 transition-transform">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-32 h-32 rounded-xl">
                    </div>

                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-3 bg-zinc-950 px-6 py-3 rounded-2xl border border-white/10 group/code cursor-pointer" onclick="copyCode('{{ $professionalCode }}')">
                            <span class="text-2xl font-black text-emerald-400 tracking-tighter">{{ $professionalCode }}</span>
                            <i data-lucide="copy" class="w-4 h-4 text-zinc-500 group-hover:text-white transition-colors"></i>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest px-4">Código Profissional</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('engagementChart').getContext('2d');
    
    // Gradient setup
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($engagementLabels) !!},
            datasets: [{
                label: 'Engajamento %',
                data: {!! json_encode($engagementData) !!},
                borderColor: '#3b82f6',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                backgroundColor: gradient,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#18181b',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '% Engajamento';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { color: '#52525b', font: { size: 10, weight: 'bold' }, stepSize: 20 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#52525b', font: { size: 10, weight: 'bold' } }
                }
            }
        }
    });
});

function copyCode(code) {
    navigator.clipboard.writeText(code);
    // Podia adicionar um toast aqui
}
</script>
@endpush

<style>
@keyframes dashboard-entry { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-dashboard-entry { animation: dashboard-entry 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection

