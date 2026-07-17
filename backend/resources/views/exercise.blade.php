@extends('layouts.app')

@section('title', 'NexShape Performance')

@section('content')
<div class="space-y-8 animate-fade-in py-8 pb-32 px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto" x-data="workoutHUD()">
    
    <!-- Include html2canvas for sharing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Timer Flutuante "SaaS Elite" -->
    <div class="fixed bottom-6 md:bottom-8 left-1/2 -translate-x-1/2 z-[100] group" x-show="timerActive" x-transition>
        <div class="bg-zinc-900 border border-emerald-500/30 p-4 md:p-6 rounded-[2.5rem] shadow-2xl backdrop-blur-3xl flex items-center gap-4 md:gap-6 shadow-emerald-500/10">
            <div class="text-center">
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1" x-text="timerPaused ? 'Pausado' : 'Treinando'"></p>
                <h3 class="text-4xl font-black text-white italic tracking-tighter tabular-nums" x-text="formatTimer(elapsedSeconds)">00:00</h3>
            </div>
            <div class="flex flex-col gap-2">
                <button @click="togglePause()" class="w-10 h-10 rounded-xl bg-emerald-500 text-zinc-950 flex items-center justify-center hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                    <i data-lucide="play" x-show="timerPaused" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="pause" x-show="!timerPaused" class="w-4 h-4 fill-current"></i>
                </button>
                <button @click="stopTraining()" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all">
                    <i data-lucide="square" class="w-3 h-3 fill-current"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Descanso guiado -->
    <div x-show="restActive" x-cloak class="fixed inset-0 z-[220] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm" x-transition.opacity>
        <div class="w-full max-w-md bg-zinc-900 border border-emerald-500/20 rounded-[2rem] p-8 shadow-2xl text-center">
            <p class="text-xs text-emerald-400 font-black uppercase tracking-[0.25em] mb-2">Descanso em andamento</p>
            <h2 class="text-6xl font-black text-white tabular-nums tracking-tighter" x-text="formatRest(restRemaining)">01:00</h2>
            <p class="text-sm text-zinc-400 font-bold mt-3">Respire, ajuste a carga se precisar e prepare a próxima série.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-8">
                <button type="button" @click="startNextSet()" class="py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                    Próxima série
                </button>
                <button type="button" @click="extendRest(30)" class="py-4 bg-zinc-950 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                    +30s
                </button>
                <button type="button" @click="finishExercise()" class="py-4 bg-zinc-950 hover:bg-rose-500/10 text-zinc-300 hover:text-rose-300 border border-zinc-800 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                    Finalizar
                </button>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-zinc-900">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase">NEXSHAPE <span class="text-emerald-500">PERFORMANCE</span></h1>
            <p class="text-xs text-zinc-400 font-black uppercase tracking-widest flex items-center gap-2">
                Registro de treino
                <span class="w-1 h-1 rounded-full bg-emerald-500/50"></span>
                Fluxo por série
            </p>
        </div>

        <!-- High-Performance Weekly Navigator -->
        <div class="flex items-center gap-3 bg-zinc-900/30 p-1 rounded-[2.5rem] border border-zinc-800/50 shadow-inner">
            <a href="{{ route('exercise', ['date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:bg-emerald-500 hover:text-zinc-950 rounded-full transition-all">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>

            <div class="flex items-center gap-2 overflow-hidden px-2">
                @php
                    $pivot = \Carbon\Carbon::parse($date);
                    $start = $pivot->copy()->subDays(2);
                @endphp
                @for($i = 0; $i < 5; $i++)
                    @php
                        $day = $start->copy()->addDays($i);
                        $isCurrent = $day->isSameDay($pivot);
                        $isToday = $day->isToday();
                        $calendarStatus = ($calendarStatuses ?? [])[$day->format('Y-m-d')] ?? null;
                        $statusClass = match ($calendarStatus) {
                            'completed' => $isCurrent ? 'bg-zinc-950' : 'bg-emerald-500',
                            'planned' => $isCurrent ? 'bg-zinc-950' : 'bg-sky-400',
                            'missed' => $isCurrent ? 'bg-zinc-950' : 'bg-rose-500',
                            default => '',
                        };
                    @endphp
                    <a href="{{ route('exercise', ['date' => $day->format('Y-m-d')]) }}" 
                       class="relative flex flex-col items-center justify-center min-w-[58px] h-16 rounded-2xl transition-all {{ $isCurrent ? 'bg-emerald-500 text-zinc-950 shadow-lg scale-105' : 'hover:bg-white/5 text-zinc-400' }}">
                        @if($isToday)
                            <span class="absolute -top-2 px-2 py-0.5 rounded-full text-[7px] font-black uppercase tracking-widest {{ $isCurrent ? 'bg-zinc-950 text-emerald-400' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">Hoje</span>
                        @endif
                        <span class="text-[8px] font-black uppercase tracking-widest">{{ $day->translatedFormat('D') }}</span>
                        <span class="text-sm font-black">{{ $day->format('d') }}</span>
                        @if($calendarStatus)
                            <span title="{{ ['completed' => 'Treino concluído', 'planned' => 'Treino planejado', 'missed' => 'Treino perdido'][$calendarStatus] ?? '' }}"
                                  class="absolute bottom-1.5 w-1.5 h-1.5 rounded-full {{ $statusClass }}"></span>
                        @endif
                    </a>
                @endfor
            </div>

            <div class="relative group">
                <input type="date" value="{{ $date }}" 
                       onchange="window.location.href = '{{ route('exercise') }}?date=' + this.value"
                       class="absolute inset-0 opacity-0 cursor-pointer z-10">
                <div class="w-10 h-10 flex items-center justify-center bg-zinc-950 border border-zinc-800 rounded-full text-emerald-500 group-hover:border-emerald-500/50 transition-all shadow-xl">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                </div>
            </div>

            <a href="{{ route('exercise', ['date' => date('Y-m-d', strtotime($date . ' + 1 day'))]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:bg-emerald-500 hover:text-zinc-950 rounded-full transition-all">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>

            <a href="{{ route('exercise', ['date' => now()->format('Y-m-d')]) }}"
               class="hidden sm:flex h-10 px-4 items-center justify-center text-xs font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 hover:bg-emerald-500 rounded-full transition-all">
                Hoje
            </a>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 -mt-4 text-[10px] text-zinc-400 font-black uppercase tracking-widest">
        <div class="flex flex-wrap items-center gap-4">
            <span class="flex items-center gap-2"><i class="w-2 h-2 rounded-full bg-emerald-500"></i> Concluído</span>
            <span class="flex items-center gap-2"><i class="w-2 h-2 rounded-full bg-sky-400"></i> Planejado</span>
            <span class="flex items-center gap-2"><i class="w-2 h-2 rounded-full bg-rose-500"></i> Perdido</span>
        </div>
        <button type="button" @click="showScheduleModal = true" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-xl text-zinc-300 hover:text-white transition-all">
            Planejar semana
        </button>
    </div>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] shadow-xl transition-all hover:border-emerald-500/20">
            <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Tempo Total</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white tracking-tighter tabular-nums">{{ $sumMin }}</span>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">min</span>
            </div>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] shadow-xl transition-all hover:border-emerald-500/20">
            <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Queima Estimada</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white tracking-tighter tabular-nums">{{ $sumBurn }}</span>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">kcal</span>
            </div>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] shadow-xl transition-all hover:border-emerald-500/20">
            <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Volume de Treino</p>
            <div class="flex items-baseline gap-2">
                @php
                    $totalVolume = 0;
                    foreach($rows as $r) {
                        $sets = json_decode($r->sets_data ?? '[]', true);
                        foreach($sets as $s) {
                            $totalVolume += ($s['weight'] ?? 0) * ($s['reps'] ?? 0);
                        }
                    }
                @endphp
                <span class="text-4xl font-black text-white tracking-tighter tabular-nums">{{ number_format($totalVolume, 0, ',', '.') }}</span>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">kg</span>
            </div>
        </div>
        <div class="bg-emerald-500/5 border border-emerald-500/20 p-6 rounded-[2rem] shadow-2xl flex items-center justify-between group">
            <div x-show="!timerActive">
                <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest mb-1 italic">Pronto para começar?</p>
                <h4 class="text-sm font-black text-white uppercase tracking-tighter">Novo Treino</h4>
            </div>
            <button @click="startTraining()" x-show="!timerActive" class="px-5 py-3 bg-emerald-500 text-zinc-950 font-black rounded-xl hover:bg-emerald-400 transition-all text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/10">INICIAR TREINO</button>
            <div x-show="timerActive" class="w-full">
                 <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest mb-1">Treino em curso</p>
                 <div class="h-1.5 w-full bg-zinc-800 rounded-full mt-2 overflow-hidden">
                    <div class="h-full bg-emerald-500 transition-all duration-500" :style="'width: ' + progressPercentage + '%'"></div>
                 </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Activity List (Left) -->
        <div class="lg:col-span-12 xl:col-span-8 space-y-8">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[3rem] p-8 lg:p-12 shadow-2xl relative overflow-hidden">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xs font-black text-zinc-500 uppercase tracking-[0.3em] italic">Registros de Hoje</h3>
                    <div class="flex gap-4">
                        <button @click="repeatLastWorkout()" class="text-[9px] font-black text-zinc-400 hover:text-emerald-500 uppercase tracking-widest flex items-center gap-2 transition-all">
                            <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                            Repetir último treino
                        </button>
                    </div>
                </div>

                <div x-show="plannedExercises.length > 0" x-cloak class="mb-8 bg-sky-500/5 border border-sky-500/20 rounded-[2rem] p-5">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <p class="text-xs text-sky-300 font-black uppercase tracking-widest">Treino planejado de hoje</p>
                            <p class="text-sm text-zinc-400 font-bold mt-1">Carregue um exercício da ficha para registrar com as séries previstas.</p>
                        </div>
                        <span class="text-xs text-sky-300 font-black tabular-nums" x-text="plannedExercises.length + ' exercícios'"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="exercise in plannedExercises" :key="exercise.plan_name + exercise.name">
                            <button type="button" @click="loadPlannedExercise(exercise)"
                                class="p-4 bg-zinc-950/70 hover:bg-zinc-950 border border-zinc-800 hover:border-sky-400/40 rounded-2xl text-left transition-all">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm text-white font-black uppercase tracking-tight" x-text="exercise.name"></p>
                                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1" x-text="(exercise.plan_label || exercise.plan_name || 'Plano') + ' • ' + (exercise.muscle_group || 'Grupo muscular')"></p>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-sky-300"></i>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                @if($rows->isEmpty())
                    <div x-show="!activity" class="py-20 text-center">
                        <div class="w-24 h-24 bg-zinc-950 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-zinc-800 shadow-inner">
                            <i data-lucide="dumbbell" class="w-10 h-10 text-zinc-800"></i>
                        </div>
                        <h4 class="text-2xl font-black text-white tracking-tight uppercase">Selecione um exercício para começar</h4>
                        <p class="text-sm text-zinc-400 font-bold mt-3">Escolha um exercício do treino atual ou repita o último treino.</p>
                        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <button type="button" @click="openListModal()" class="w-full sm:w-auto px-6 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                                Escolher exercício
                            </button>
                            <button type="button" @click="repeatLastWorkout()" class="w-full sm:w-auto px-6 py-4 bg-zinc-950 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                                Repetir último treino
                            </button>
                        </div>
                    </div>

                    <div x-show="activity" x-cloak class="space-y-8">
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <div class="lg:col-span-2 bg-zinc-950/50 border border-zinc-800 rounded-[2rem] p-6">
                                <p class="text-xs text-emerald-400 font-black uppercase tracking-widest mb-2">Exercício atual</p>
                                <h4 class="text-3xl font-black text-white uppercase tracking-tight" x-text="activity"></h4>
                                <p class="text-sm text-zinc-400 font-bold mt-3" x-text="exerciseInstructions"></p>
                                <div class="flex flex-wrap gap-2 mt-5">
                                    <span class="px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-black uppercase tracking-widest" x-text="exerciseMuscle"></span>
                                    <span class="px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-black uppercase tracking-widest" x-text="exerciseEquipment"></span>
                                    <span class="px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-black uppercase tracking-widest" x-text="exerciseDifficulty"></span>
                                </div>
                            </div>
                            <div class="bg-zinc-950/50 border border-zinc-800 rounded-[2rem] p-6 flex flex-col justify-between">
                                <div class="w-full aspect-video rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center overflow-hidden">
                                    <template x-if="exerciseVideoUrl">
                                        <a :href="exerciseVideoUrl" target="_blank" rel="noopener" class="w-full h-full flex flex-col items-center justify-center gap-3 text-emerald-300 hover:text-emerald-200 transition-colors">
                                            <i data-lucide="play-circle" class="w-10 h-10"></i>
                                            <span class="text-xs font-black uppercase tracking-widest">Ver vídeo</span>
                                        </a>
                                    </template>
                                    <template x-if="!exerciseVideoUrl">
                                        <div class="text-center px-4">
                                            <i data-lucide="activity" class="w-10 h-10 text-zinc-700 mx-auto mb-3"></i>
                                            <p class="text-xs text-zinc-500 font-black uppercase tracking-widest">Guia visual indisponível</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="bg-zinc-950/50 border border-zinc-800 rounded-[2rem] p-6">
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-2">Progresso</p>
                                <div class="flex items-end gap-2">
                                    <span class="text-4xl font-black text-white tabular-nums" x-text="completedSetsCount"></span>
                                    <span class="pb-1 text-sm text-zinc-400 font-black uppercase">de <span x-text="sets.length"></span> séries</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="discomfort" x-cloak class="bg-amber-500/10 border border-amber-500/20 rounded-[2rem] p-5 flex items-start gap-4">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-300 mt-0.5 shrink-0"></i>
                            <div>
                                <p class="text-xs text-amber-300 font-black uppercase tracking-widest">Desconforto registrado</p>
                                <p class="text-sm text-zinc-300 font-bold mt-1" x-text="discomfort + '. Reduza carga, ajuste execução ou finalize o exercício se necessário.'"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-zinc-950/40 border border-zinc-800 rounded-[2rem] p-6">
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-4">Dicas rápidas</p>
                                <template x-for="tip in exerciseTips" :key="tip">
                                    <div class="flex items-start gap-3 mb-3 last:mb-0">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0"></i>
                                        <p class="text-sm text-zinc-300 font-bold leading-relaxed" x-text="tip"></p>
                                    </div>
                                </template>
                            </div>
                            <div class="bg-zinc-950/40 border border-zinc-800 rounded-[2rem] p-6">
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-4">Atenção na execução</p>
                                <template x-for="mistake in exerciseMistakes" :key="mistake">
                                    <div class="flex items-start gap-3 mb-3 last:mb-0">
                                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-300 mt-0.5 shrink-0"></i>
                                        <p class="text-sm text-zinc-300 font-bold leading-relaxed" x-text="mistake"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="overflow-hidden border border-zinc-800 rounded-[2rem] bg-zinc-950/40">
                            <div class="grid grid-cols-4 gap-3 px-5 py-4 border-b border-zinc-800 text-xs text-zinc-400 font-black uppercase tracking-widest">
                                <span>Série</span>
                                <span>Último treino</span>
                                <span>Hoje</span>
                                <span>Status</span>
                            </div>
                            <template x-for="(set, index) in sets" :key="index">
                                <div class="grid grid-cols-4 gap-3 px-5 py-4 border-b border-zinc-900 last:border-0 items-center">
                                    <span class="text-sm text-zinc-300 font-black" x-text="index + 1"></span>
                                    <span class="text-sm text-zinc-500 font-bold tabular-nums" x-text="lastSetLabel(index)"></span>
                                    <span class="text-sm text-white font-black tabular-nums" x-text="todaySetLabel(set)"></span>
                                    <span class="text-xs font-black uppercase" :class="set.completed ? 'text-emerald-400' : (index === currentSetIndex ? 'text-amber-300' : 'text-zinc-600')" x-text="set.completed ? 'Concluída' : (index === currentSetIndex ? 'Atual' : 'Pendente')"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($rows as $row)
                            @php $sets = json_decode($row->sets_data ?? '[]', true); @endphp
                            <div class="group p-8 bg-zinc-950/40 border border-zinc-800 rounded-[2.5rem] hover:border-emerald-500/30 transition-all shadow-inner">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/20">
                                            <i data-lucide="zap" class="w-7 h-7 fill-current"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-black text-white tracking-tighter italic uppercase">{{ $row->activity_type }}</h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest bg-zinc-900 px-2 py-0.5 rounded-md border border-zinc-800">{{ $row->duration_min }} MIN</span>
                                                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest bg-zinc-900 px-2 py-0.5 rounded-md border border-zinc-800">{{ $row->calories_burned ?? 0 }} KCAL</span>
                                                @if($row->rpe)
                                                    <span class="text-[9px] text-amber-500 font-black uppercase tracking-widest bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">RPE: {{ $row->rpe }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('exercise', ['date' => $date, 'edit' => $row->id]) }}" class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-emerald-500 border border-zinc-800 transition-all">
                                            <i data-lucide="edit-3" class="w-5 h-5"></i>
                                        </a>
                                        <form method="POST" onsubmit="return confirm('Excluir registro?')">
                                            @csrf
                                            <input type="hidden" name="action" value="delete_exercise">
                                            <input type="hidden" name="entry_date" value="{{ $date }}">
                                            <input type="hidden" name="exercise_id" value="{{ $row->id }}">
                                            <button type="submit" class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-rose-500 border border-zinc-800 transition-all">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if(!empty($sets))
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 pt-4 border-t border-zinc-900">
                                        @foreach($sets as $idx => $s)
                                            <div class="p-4 rounded-2xl border border-zinc-800 transition-all {{ ($s['completed'] ?? false) ? 'bg-emerald-500/5 border-emerald-500/20' : 'bg-zinc-950 shadow-inner' }}">
                                                <div class="flex justify-between items-center mb-2">
                                                    <p class="text-[8px] font-black {{ ($s['completed'] ?? false) ? 'text-emerald-500' : 'text-zinc-600' }} uppercase">Set {{ $idx + 1 }}</p>
                                                    @if($s['completed'] ?? false)
                                                        <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald-500"></i>
                                                    @endif
                                                </div>
                                                <p class="text-sm font-black text-white tabular-nums">{{ $s['weight'] }}kg <span class="text-zinc-600">x</span> {{ $s['reps'] }}</p>
                                                <div class="flex items-center justify-between mt-2">
                                                    @if(isset($s['weight']) && isset($s['reps']) && $s['weight'] > 0)
                                                        <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-tighter">1RM: {{ round($s['weight'] * (1 + 0.0333 * $s['reps']), 1) }}kg</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Resumo ao Finalizar -->
            <!-- Resumo ao Finalizar (Modal Premium) -->
            <div x-show="showSummary" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-zinc-950/80 backdrop-blur-sm"
                 style="display: none;">
                
                <div class="bg-zinc-900 border border-emerald-500/30 rounded-[3rem] p-10 max-w-xl w-full shadow-3xl shadow-emerald-500/10 relative overflow-hidden">
                    <!-- Glass effect decoration -->
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-10">
                            <div>
                                <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">SESSÃO FINALIZADA</h3>
                                <p class="text-[10px] text-emerald-500 font-black uppercase tracking-[0.3em] mt-1">Alta Performance NexShape</p>
                            </div>
                            <button @click="showSummary = false" class="w-10 h-10 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-10">
                            <div class="bg-zinc-950 p-6 rounded-3xl border border-zinc-800/50 shadow-inner group transition-all hover:border-emerald-500/20">
                                <p class="text-[9px] text-zinc-600 font-black uppercase mb-1 tracking-widest">Exercícios</p>
                                <p class="text-3xl font-black text-white tabular-nums italic" x-text="rowsCount"></p>
                            </div>
                            <div class="bg-zinc-950 p-6 rounded-3xl border border-zinc-800/50 shadow-inner group transition-all hover:border-emerald-500/20">
                                <p class="text-[9px] text-zinc-600 font-black uppercase mb-1 tracking-widest">Duração</p>
                                <p class="text-3xl font-black text-white tabular-nums italic">{{ $sumMin }}<span class="text-xs text-zinc-600 ml-1">m</span></p>
                            </div>
                            <div class="bg-zinc-950 p-6 rounded-3xl border border-zinc-800/50 shadow-inner group transition-all hover:border-emerald-500/20">
                                <p class="text-[9px] text-zinc-600 font-black uppercase mb-1 tracking-widest">Volume Total</p>
                                <p class="text-3xl font-black text-white tabular-nums italic">{{ number_format($totalVolume, 0, ',', '.') }}<span class="text-xs text-zinc-600 ml-1">kg</span></p>
                            </div>
                            <div class="bg-zinc-950 p-6 rounded-3xl border border-zinc-800/50 shadow-inner group transition-all hover:border-emerald-500/20">
                                <p class="text-[9px] text-zinc-600 font-black uppercase mb-1 tracking-widest">Energia</p>
                                <p class="text-3xl font-black text-white tabular-nums italic">{{ $sumBurn }}<span class="text-xs text-zinc-600 ml-1">kcal</span></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-10 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Streak: <span class="text-emerald-500">{{ $streak }} DIAS 🔥</span></p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            <button @click="generateShareCard()" class="w-full py-5 bg-emerald-500 text-zinc-950 font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 flex items-center justify-center gap-4">
                                <i data-lucide="instagram" class="w-5 h-5"></i>
                                Gerar Card de Treino
                            </button>
                            <button @click="showSummary = false" class="w-full py-5 bg-zinc-800/50 text-zinc-500 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-800 hover:text-white transition-all">
                                Voltar ao Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Shareable Card Template (Refinado) -->
            <div id="share-card-template" class="fixed -top-[3000px] left-0 w-[400px] bg-[#06080c] p-12 font-sans -z-50" style="border-radius: 48px; border: 1px solid rgba(16,185,129,0.3);">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-12">
                        <div>
                            <h2 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none">NEX<span class="text-emerald-500">SHAPE</span></h2>
                            <p class="text-[10px] text-emerald-500/60 font-black uppercase tracking-[0.4em] mt-2">Elite Performance</p>
                        </div>
                        <div class="w-16 h-16 bg-emerald-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-emerald-500/30">
                            <i data-lucide="zap" class="w-8 h-8 text-zinc-950 fill-current"></i>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <div class="bg-zinc-900/80 p-10 rounded-[3rem] border border-white/5 shadow-2xl">
                            <h3 class="text-[11px] text-emerald-500 font-black uppercase tracking-[0.3em] mb-8 italic">Métricas da Sessão</h3>
                            <div class="grid grid-cols-2 gap-y-12 gap-x-8">
                                <div>
                                    <p class="text-[9px] text-zinc-600 font-black uppercase mb-2 tracking-widest">Exercícios</p>
                                    <p class="text-5xl font-black text-white italic tracking-tighter tabular-nums" x-text="rowsCount"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-zinc-600 font-black uppercase mb-2 tracking-widest">Duração</p>
                                    <p class="text-5xl font-black text-white italic tracking-tighter tabular-nums">{{ $sumMin }}<span class="text-[12px] text-emerald-500 ml-1 font-bold">m</span></p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-zinc-600 font-black uppercase mb-2 tracking-widest">Volume (t)</p>
                                    <p class="text-4xl font-black text-white italic tracking-tighter tabular-nums">{{ number_format($totalVolume / 1000, 1) }}<span class="text-[12px] text-emerald-500 ml-1 font-bold">t</span></p>
                                </div>
                                <div>
                                    <p class="text-[9px] text-zinc-600 font-black uppercase mb-2 tracking-widest">Calorias</p>
                                    <p class="text-4xl font-black text-white italic tracking-tighter tabular-nums">{{ $sumBurn }}<span class="text-[12px] text-emerald-500 ml-1 font-bold">kcal</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between px-6 pt-4">
                            <div class="space-y-1">
                                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Data</p>
                                <p class="text-sm font-black text-white uppercase tracking-tighter">{{ date('d M Y', strtotime($date)) }}</p>
                            </div>
                            <div class="text-right space-y-1">
                                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Streak</p>
                                <p class="text-sm font-black text-emerald-500 uppercase tracking-tighter">{{ $streak }} DIAS 🔥</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-16 text-center border-t border-zinc-900 pt-8">
                        <p class="text-[8px] text-zinc-700 font-black uppercase tracking-[0.6em]">NexShape Performance — Biohacking & Elite Fitness</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Registry Form (Right) -->
        <div class="lg:col-span-12 xl:col-span-4 lg:sticky lg:top-8 h-fit">
            <div class="bg-zinc-900 border border-zinc-800 p-8 lg:p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8">
                    <div x-show="autoSaving" class="flex items-center gap-2 text-[8px] text-emerald-500 font-black uppercase tracking-widest animate-pulse">
                        <i data-lucide="cloud-upload" class="w-3 h-3"></i>
                        Salvo
                    </div>
                </div>

                <h3 class="text-2xl font-black text-white tracking-tighter uppercase mb-2 italic">{{ $editRow ? 'EDITAR' : 'REGISTRAR' }} SÉRIE</h3>
                <div class="flex items-center gap-4 mb-10">
                    <p class="text-xs text-zinc-400 font-black uppercase tracking-widest">NexShape Performance</p>
                    <div class="h-[1px] flex-1 bg-zinc-800 rounded-full"></div>
                </div>

                <form method="POST" class="space-y-8" @submit.prevent="submitForm($event)">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if($editRow) <input type="hidden" name="exercise_edit_id" value="{{ $editRow->id }}"> @endif
                    <input type="hidden" name="sets_data" :value="JSON.stringify(sets)">
                    <input type="hidden" name="duration_min" :value="duration">
                    <input type="hidden" name="rest_default" :value="sets[0]?.rest || 60">

                    <div class="space-y-4 relative" x-on:click.away="showResults = false">
                        <label class="text-xs text-zinc-400 font-black uppercase tracking-widest ml-2">Exercício selecionado</label>
                        <div class="relative">
                            <div class="flex gap-2">
                                <input type="text" name="activity_type" 
                                    x-model="activity"
                                    @input.debounce.300ms="searchExercises()"
                                    @keydown.enter.prevent="selectExerciseFromKeyboard()"
                                    @keydown.arrow-down.prevent="focusNextResult()"
                                    @keydown.arrow-up.prevent="focusPrevResult()"
                                    autocomplete="off"
                                    required placeholder="PESQUISAR EXERCÍCIO..." 
                                    class="flex-1 bg-zinc-950 border border-zinc-800 p-6 rounded-2xl text-white text-base font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner uppercase tracking-widest">
                                
                                <button type="button" @click="openListModal()" 
                                    class="w-20 bg-zinc-950 border border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-600 hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-inner">
                                    <i data-lucide="layout-list" class="w-6 h-6"></i>
                                </button>
                            </div>
                            
                            <!-- Dropdown Autocomplete -->
                            <div x-show="showResults && results.length > 0" class="absolute z-50 left-0 right-0 mt-2 bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-3xl">
                                <template x-for="(res, i) in results" :key="i">
                                    <div @click="selectExercise(res)" :class="{'bg-emerald-500 text-zinc-950': focusIndex === i, 'text-zinc-400': focusIndex !== i}" 
                                         class="px-6 py-4 cursor-pointer hover:bg-emerald-500/10 flex items-center justify-between transition-all border-b border-zinc-800 last:border-0">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest" x-text="res.name"></p>
                                            <p class="text-[8px] font-bold uppercase mt-1 opacity-60" x-text="res.muscle_group"></p>
                                        </div>
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Histórico Rápido -->
                        <div x-show="history.length > 0" class="mt-4 p-4 bg-zinc-950/50 rounded-2xl border border-zinc-800 shadow-inner">
                            <p class="text-xs text-zinc-400 font-black uppercase mb-3 italic">Último desempenho</p>
                            <div class="space-y-2">
                                <template x-for="h in history" :key="h.entry_date">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[9px] text-zinc-600 font-black uppercase" x-text="formatDate(h.entry_date)"></span>
                                        <div class="flex gap-2">
                                            <template x-for="s in JSON.parse(h.sets_data).slice(0, 3)" :key="Math.random()">
                                                <span class="text-[9px] text-white font-black tabular-nums" x-text="s.weight + 'kg x ' + s.reps"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Sugestão de Progressão -->
                            <div x-show="suggestion" class="mt-4 pt-3 border-t border-zinc-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                                    </div>
                                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">Sugestão: <span x-text="suggestion"></span> kg</p>
                                </div>
                                <button type="button" @click="applySuggestion()" class="text-[8px] bg-emerald-500 text-zinc-950 px-3 py-1 rounded-md font-black uppercase hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/10">Aplicar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Séries Manager -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center ml-2">
                            <div class="flex items-center gap-3">
                                <label class="text-xs text-zinc-400 font-black uppercase tracking-widest">Série atual</label>
                                <span class="text-xs bg-zinc-950 px-3 py-1 rounded-full border border-zinc-800 text-zinc-300 font-black tabular-nums" x-text="currentSetNumber + ' de ' + sets.length"></span>
                            </div>
                            <div class="flex gap-4">
                                <button type="button" @click="duplicateLastSet()" class="text-[9px] font-black text-zinc-600 hover:text-white uppercase tracking-widest transition-colors">Duplicar</button>
                                <button type="button" @click="addSet()" class="text-[9px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-widest transition-colors">+ Adicionar</button>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(set, index) in sets" :key="index">
                                <div class="bg-zinc-950/80 border rounded-3xl p-5 transition-all duration-300 relative overflow-hidden group/set mb-4"
                                     :class="(set.weight > (suggestion * 1.25) && suggestion > 0) ? 'border-rose-500/50 shadow-[0_0_15px_rgba(244,63,94,0.15)]' : (set.weight > suggestion ? 'border-emerald-500/30' : 'border-zinc-800')">
                                    
                                    <!-- Alerta de Sobrecarga Background -->
                                    <div class="absolute inset-0 bg-rose-500/5 transition-opacity duration-300" x-show="(set.weight > (suggestion * 1.25) && suggestion > 0)" style="display: none;"></div>

                                    <div class="relative z-10 flex flex-col gap-5">
                                        <!-- Header da Série -->
                                        <div class="flex items-center justify-between">
                                            <button type="button" @click="toggleComplete(index)" 
                                                    :class="set.completed ? 'bg-emerald-500 text-zinc-950 border-emerald-500' : 'bg-zinc-900 text-zinc-400 border-zinc-800'"
                                                    class="flex items-center justify-center border w-8 h-8 rounded-xl text-[10px] font-black transition-all hover:scale-105 shadow-inner">
                                                <i x-show="set.completed" data-lucide="check" class="w-4 h-4"></i>
                                                <span x-show="!set.completed" x-text="index + 1"></span>
                                            </button>
                                            
                                            <button type="button" @click="removeSet(index)" class="w-8 h-8 flex items-center justify-center bg-zinc-900 hover:bg-rose-500/10 text-zinc-700 hover:text-rose-500 border border-zinc-800 rounded-xl transition-all shadow-inner">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                            </button>
                                        </div>

                                        <!-- Controles Principais (Mobile-First) -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            
                                            <!-- Controle de Carga -->
                                            <div class="space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <label class="text-xs font-black uppercase text-zinc-400 tracking-widest">Carga (kg)</label>
                                                    <span x-show="(set.weight > (suggestion * 1.25) && suggestion > 0)" class="text-[9px] font-black uppercase text-rose-500 animate-pulse flex items-center gap-1" style="display: none;">
                                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i> Sobrecarga
                                                    </span>
                                                    <span x-show="set.weight > suggestion && !(set.weight > (suggestion * 1.25)) && suggestion > 0" class="text-[9px] font-black uppercase text-emerald-500 flex items-center gap-1" style="display: none;">
                                                        <i data-lucide="trending-up" class="w-3 h-3"></i> Evolução
                                                    </span>
                                                </div>
                                                
                                                <!-- Stepper -->
                                                <div class="flex items-center justify-between bg-zinc-900 border border-zinc-800 rounded-2xl p-1 relative overflow-hidden">
                                                    <button type="button" @click="set.weight = Math.max(0, (parseFloat(set.weight) || 0) - 1); handleDataChange()" class="w-12 h-12 rounded-xl bg-zinc-950/50 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors active:scale-95 z-10">
                                                        <i data-lucide="minus" class="w-4 h-4"></i>
                                                    </button>
                                                    
                                                    <input type="number" step="0.5" x-model="set.weight" @input="handleDataChange()" @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                                        class="w-24 bg-transparent border-0 text-center text-2xl font-black text-white focus:ring-0 p-0 outline-none z-10 tabular-nums">
                                                    
                                                    <button type="button" @click="set.weight = (parseFloat(set.weight) || 0) + 1; handleDataChange()" class="w-12 h-12 rounded-xl bg-zinc-950/50 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors active:scale-95 z-10">
                                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                                    </button>
                                                </div>

                                                <!-- Slider Moderno -->
                                                <div class="pt-2">
                                                    <input type="range" x-model="set.weight" @input="handleDataChange()" min="0" max="150" step="1" 
                                                        class="w-full h-1 bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-emerald-500 hover:accent-emerald-400 transition-all">
                                                </div>
                                            </div>

                                            <!-- Reps e Descanso -->
                                            <div class="grid grid-cols-2 gap-3">
                                                <!-- Repetições -->
                                                <div class="space-y-1">
                                                    <label class="block text-xs font-black uppercase text-zinc-400 tracking-widest text-center">Reps</label>
                                                    <input type="number" x-model="set.reps" @input="handleDataChange()" @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                                        class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center text-2xl font-black text-white focus:border-emerald-500/50 outline-none shadow-inner tabular-nums"
                                                        placeholder="0">
                                                </div>

                                                <!-- Descanso -->
                                                <div class="space-y-1">
                                                    <label class="block text-xs font-black uppercase text-zinc-400 tracking-widest text-center">Descanso</label>
                                                    <select x-model="set.rest" @change="handleDataChange()" @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                                        class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center text-base font-black text-emerald-400 outline-none appearance-none shadow-inner uppercase tracking-tighter">
                                                        <option value="30">30s</option>
                                                        <option value="45">45s</option>
                                                        <option value="60">60s</option>
                                                        <option value="90">90s</option>
                                                        <option value="120">120s</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Esforço e Velocidade -->
                                            <div x-show="advancedOpen" class="grid grid-cols-2 gap-3 mt-3">
                                                <!-- RPE por Série -->
                                                <div class="space-y-1 relative group/rpe">
                                                    <div class="flex items-center justify-center gap-1 mb-1">
                                                        <label class="block text-[9px] font-black uppercase text-zinc-500 tracking-widest text-center">Esforço (1-10)</label>
                                                        <template x-if="!isPremium">
                                                            <i data-lucide="lock" class="w-3 h-3 text-amber-500"></i>
                                                        </template>
                                                    </div>
                                                    <input type="number" min="1" max="10" x-model="set.rpe" @input="handleDataChange()" @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                                        class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center text-sm font-black focus:border-emerald-500/50 outline-none shadow-inner tabular-nums"
                                                        :class="isPremium ? 'text-amber-500' : 'text-zinc-600 opacity-50 cursor-not-allowed'"
                                                        placeholder="Ex: 8" :disabled="!isPremium">
                                                </div>

                                                <!-- Cadência -->
                                                <div class="space-y-1 relative group/cadence">
                                                    <div class="flex items-center justify-center gap-1 mb-1">
                                                        <label class="block text-[9px] font-black uppercase text-zinc-500 tracking-widest text-center">Ritmo</label>
                                                        <template x-if="!isPremium">
                                                            <i data-lucide="lock" class="w-3 h-3 text-amber-500"></i>
                                                        </template>
                                                    </div>
                                                    <input type="text" x-model="set.cadence" @input="handleDataChange()" @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                                        class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center text-sm font-black focus:border-emerald-500/50 outline-none shadow-inner"
                                                        :class="isPremium ? 'text-white' : 'text-zinc-600 opacity-50 cursor-not-allowed'"
                                                        placeholder="Ex: 3010" :disabled="!isPremium">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-zinc-900/50">
                            <button type="button" @click="advancedOpen = !advancedOpen; setTimeout(() => lucide.createIcons(), 50)" class="w-full flex items-center justify-center gap-2 text-xs text-zinc-300 hover:text-emerald-400 font-black uppercase tracking-widest transition-colors">
                                <i :data-lucide="advancedOpen ? 'chevron-up' : 'chevron-down'" class="w-4 h-4"></i>
                                Mais detalhes da série
                            </button>
                        </div>
                    </div>

                    <!-- Métricas Avançadas -->
                    <div x-show="advancedOpen" class="grid grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between ml-2">
                                <label class="text-xs text-zinc-400 font-black uppercase tracking-widest">Intensidade (RPE)</label>
                                @if(!$isPremium) <i data-lucide="lock" class="w-3 h-3 text-amber-500"></i> @endif
                            </div>
                            <select name="rpe" x-model="rpe" @change="handleDataChange()" {{ !$isPremium ? 'disabled' : '' }}
                                class="w-full bg-zinc-950 border border-zinc-800 p-6 rounded-2xl text-white text-sm font-black outline-none focus:border-emerald-500/50 shadow-inner appearance-none disabled:opacity-50 uppercase tracking-widest">
                                <option value="0">SELECIONAR</option>
                                @for($i=1; $i<=10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between ml-2">
                                <label class="text-xs text-zinc-400 font-black uppercase tracking-widest">Gasto (kcal)</label>
                                @if(!$isPremium) <i data-lucide="lock" class="w-3 h-3 text-amber-500"></i> @endif
                            </div>
                            <input type="number" name="calories_burned" x-model="calories" readonly placeholder="AUTO"
                                class="w-full bg-zinc-950 border border-zinc-800 p-6 rounded-2xl text-white text-sm font-black outline-none shadow-inner opacity-60 cursor-not-allowed tabular-nums">
                        </div>
                    </div>

                    <div x-show="advancedOpen" class="space-y-4">
                        <label class="text-xs text-zinc-400 font-black uppercase tracking-widest ml-2">Observações</label>
                        <select x-model="discomfort"
                            class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xs font-black outline-none focus:border-amber-500/50 shadow-inner uppercase tracking-widest">
                            <option value="">SEM DOR OU DESCONFORTO</option>
                            <option value="Dor leve">DOR LEVE</option>
                            <option value="Dor moderada">DOR MODERADA</option>
                            <option value="Dor forte">DOR FORTE</option>
                            <option value="Desconforto técnico">DESCONFORTO TÉCNICO</option>
                        </select>
                        <input type="text" name="notes" x-model="notes" placeholder="EX: DOR NO OMBRO, EXECUÇÃO DIFÍCIL..." 
                               class="w-full bg-zinc-950 border border-zinc-800 p-6 rounded-2xl text-white text-[10px] font-black outline-none focus:border-emerald-500/50 shadow-inner uppercase tracking-widest">
                    </div>

                    <div class="space-y-4 pt-4">
                        <button type="button" @click="completeCurrentSet()" class="w-full py-5 bg-zinc-950 hover:bg-zinc-800 text-white border border-zinc-800 font-black text-xs uppercase tracking-[0.25em] rounded-3xl transition-all active:scale-[0.98]">
                            Concluir série atual
                        </button>
                        <button type="submit" class="w-full py-6 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-[0.3em] rounded-3xl transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98]">
                            {{ $editRow ? 'ATUALIZAR REGISTRO' : 'SALVAR TREINO' }}
                        </button>
                        
                        @if($editRow)
                            <a href="{{ route('exercise', ['date' => $date]) }}" class="block text-center text-[10px] text-zinc-600 font-black uppercase tracking-widest hover:text-white transition-colors">Abortar Edição</a>
                        @endif
                    </div>
                </form>

                @if(!$isPremium)
                <div class="mt-8 p-6 bg-amber-500/5 border border-amber-500/20 rounded-2xl">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="crown" class="w-4 h-4 text-amber-500"></i>
                        <h4 class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Upgrade para o Plano PRO</h4>
                    </div>
                    <p class="text-[9px] text-zinc-500 font-bold leading-relaxed">Libere Cronômetro automático, RPE, Sugestão de carga e Gasto calórico inteligente.</p>
                    <a href="{{ route('plano') }}" class="mt-4 block text-center py-3 bg-amber-500 text-zinc-950 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/10">Ver Planos</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Listagem de Exercícios -->
    <div x-show="showListModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[200] flex items-center justify-center p-4 lg:p-12 bg-zinc-950/95 backdrop-blur-md"
         @keydown.escape.window="showListModal = false"
         style="display: none;">
        
        <div class="bg-zinc-900 border border-zinc-800 w-full max-w-4xl h-[80vh] rounded-[3rem] shadow-3xl flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="p-8 lg:p-10 border-b border-zinc-800 flex items-center justify-between shrink-0">
                <div class="space-y-1">
                    <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">CATÁLOGO <span class="text-emerald-500">NEXSHAPE</span></h2>
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Navegue por grupos musculares</p>
                </div>
                <button @click="showListModal = false" class="w-12 h-12 bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-rose-500 transition-all border border-zinc-700 shadow-xl">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Modal Search -->
            <div class="px-8 lg:px-10 py-6 bg-zinc-950/50 border-b border-zinc-800 shrink-0">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-6 top-1/2 -translate-y-1/2 text-zinc-700 w-5 h-5"></i>
                    <input type="text" x-model="listSearch" placeholder="FILTRAR POR NOME OU MÚSCULO..." 
                           class="w-full bg-zinc-900 border border-zinc-800 p-6 pl-14 rounded-2xl text-white text-sm font-black outline-none focus:border-emerald-500/30 transition-all uppercase tracking-widest placeholder:text-zinc-800 shadow-inner">
                </div>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-8 lg:p-10 custom-scrollbar">
                <div x-show="loadingCatalog" class="h-full flex flex-col items-center justify-center space-y-4">
                    <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                    <p class="text-[10px] text-zinc-700 font-black uppercase tracking-widest">Sincronizando base de dados...</p>
                </div>

                <div x-show="!loadingCatalog">
                    <template x-for="(exs, group) in groupedCatalog" :key="group">
                        <div class="mb-10">
                            <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] mb-6 flex items-center gap-4 italic">
                                <span x-text="group"></span>
                                <div class="h-[1px] flex-1 bg-zinc-800"></div>
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="ex in exs" :key="ex.name">
                                    <button @click="selectExercise(ex); showListModal = false" 
                                            class="flex items-center justify-between p-5 bg-zinc-950 border border-zinc-800 rounded-2xl hover:bg-emerald-500/5 hover:border-emerald-500/30 transition-all text-left group shadow-inner">
                                        <span class="text-xs font-black text-zinc-400 uppercase tracking-widest group-hover:text-emerald-500 transition-colors" x-text="ex.name"></span>
                                        <i data-lucide="plus" class="w-4 h-4 text-zinc-800 group-hover:text-emerald-500 transition-all"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div x-show="Object.keys(groupedCatalog).length === 0" class="h-64 flex flex-col items-center justify-center">
                        <i data-lucide="ghost" class="w-16 h-16 text-zinc-900 mb-4"></i>
                        <p class="text-xs text-zinc-800 font-black uppercase tracking-widest">Nenhum exercício encontrado</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 bg-zinc-950 border-t border-zinc-800 flex items-center justify-between shrink-0 shadow-2xl">
                <p class="text-[9px] text-zinc-700 font-black uppercase tracking-widest">Selecione para iniciar o registro</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">Sistema Online</span>
                </div>
            </div>
        </div>
    </div>

    <div x-show="notice.show" x-cloak class="fixed inset-0 z-[160] flex items-center justify-center px-4" x-transition.opacity>
        <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm" @click="notice.show = false"></div>
        <div class="relative w-full max-w-md bg-zinc-900 border border-white/10 rounded-[2rem] shadow-2xl overflow-hidden" x-transition.scale.origin.center>
            <div class="p-6 space-y-5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center border"
                         :class="notice.type === 'success' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-amber-500/10 border-amber-500/20 text-amber-300'">
                        <i :data-lucide="notice.type === 'success' ? 'check-circle-2' : 'info'" class="w-5 h-5"></i>
                    </div>
                    <div class="space-y-1 flex-1">
                        <p class="text-[10px] font-black uppercase tracking-[0.25em]" :class="notice.type === 'success' ? 'text-emerald-400' : 'text-amber-300'" x-text="notice.eyebrow"></p>
                        <h3 class="text-lg font-black text-white uppercase tracking-tight" x-text="notice.title"></h3>
                        <p class="text-sm text-zinc-400 font-medium leading-relaxed" x-text="notice.message"></p>
                    </div>
                </div>
                <button type="button"
                        @click="notice.show = false"
                        class="w-full px-5 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                    Entendi
                </button>
            </div>
        </div>
    </div>

    <div x-show="showScheduleModal" x-cloak class="fixed inset-0 z-[210] flex items-center justify-center p-4 bg-zinc-950/85 backdrop-blur-sm" x-transition.opacity>
        <div class="w-full max-w-xl bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-2xl">
            <div class="flex items-start justify-between gap-6 mb-8">
                <div>
                    <p class="text-xs text-sky-300 font-black uppercase tracking-widest">Planejamento semanal</p>
                    <h3 class="text-2xl text-white font-black uppercase tracking-tight mt-2">Dias previstos de treino</h3>
                </div>
                <button type="button" @click="showScheduleModal = false" class="w-10 h-10 bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 rounded-xl flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="action" value="update_training_schedule">
                <input type="hidden" name="entry_date" value="{{ $date }}">

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([0 => 'Dom', 1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'] as $dayNumber => $dayLabel)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="days_of_week[]" value="{{ $dayNumber }}" class="hidden peer" {{ in_array($dayNumber, $quickScheduleDays ?? [], true) ? 'checked' : '' }}>
                            <span class="block text-center px-4 py-4 rounded-2xl border border-zinc-800 bg-zinc-950 text-zinc-400 peer-checked:bg-sky-400 peer-checked:text-zinc-950 peer-checked:border-sky-300 text-xs font-black uppercase tracking-widest transition-all">
                                {{ $dayLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 py-4 bg-sky-400 hover:bg-sky-300 text-zinc-950 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                        Salvar planejamento
                    </button>
                    <button type="button" @click="showScheduleModal = false" class="flex-1 py-4 bg-zinc-950 hover:bg-zinc-800 text-zinc-300 border border-zinc-800 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="timerActive" x-cloak class="fixed bottom-0 left-0 right-0 z-[140] border-t border-zinc-800 bg-zinc-950/95 backdrop-blur-xl px-4 py-3">
        <div class="max-w-[1600px] mx-auto grid grid-cols-3 gap-3">
            <button type="button" @click="togglePause()" class="py-4 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <span x-text="timerPaused ? 'Retomar' : 'Pausar'"></span>
            </button>
            <button type="button" @click="completeCurrentSet()" class="py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                Concluir série
            </button>
            <button type="button" @click="stopTraining()" class="py-4 bg-zinc-900 hover:bg-rose-500/10 text-zinc-200 hover:text-rose-300 border border-zinc-800 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                Finalizar treino
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush

<script>
    function workoutHUD() {
        return {
            // State
            activity: '{{ old('activity_type', $editRow->activity_type ?? '') }}',
            selectedExercise: null,
            plannedExercises: @json($plannedExercises ?? []),
            scheduleDays: @json($quickScheduleDays ?? []),
            sets: @json(json_decode($editRow->sets_data ?? '[]')),
            duration: {{ old('duration_min', $editRow->duration_min ?? '45') }},
            calories: '{{ old('calories_burned', $editRow->calories_burned ?? '') }}',
            rpe: '{{ old('rpe', $editRow->rpe ?? '0') }}',
            notes: '{{ old('notes', $editRow->notes ?? '') }}',
            discomfort: '',
            
            // UI State
            results: [],
            showResults: false,
            focusIndex: -1,
            history: [],
            suggestion: null,
            autoSaving: false,
            showSummary: false,
            advancedOpen: false,
            showScheduleModal: false,
            rowsCount: {{ $rows->count() }},
            isPremium: {{ $isPremium ? 'true' : 'false' }},
            editMode: {{ $editRow ? 'true' : 'false' }},
            currentEntryId: {{ $editRow->id ?? 0 }},
            notice: {
                show: false,
                type: 'info',
                eyebrow: 'Aviso',
                title: 'Atenção',
                message: '',
            },

            // Exercise List Modal
            showListModal: false,
            catalogExercises: [],
            listSearch: '',
            loadingCatalog: false,

            // Timer
            timerActive: false,
            timerPaused: false,
            elapsedSeconds: 0,
            timerInterval: null,
            startTime: null,
            restActive: false,
            restRemaining: 0,
            restInterval: null,

            init() {
                if (this.sets.length === 0) {
                    this.addSet();
                }
                
                // Keyboard shortcuts for the whole page
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') this.showResults = false;
                });

                if (this.activity.length > 2) {
                    this.fetchHistory();
                }

                // Auto-save interval (every 10s if training active)
                setInterval(() => {
                    if (this.timerActive && !this.timerPaused) {
                        this.autoSave();
                    }
                }, 10000);

                this.$watch('showListModal', value => {
                    if (value) setTimeout(() => lucide.createIcons(), 50);
                });
                
                this.$watch('showResults', value => {
                    if (value) setTimeout(() => lucide.createIcons(), 50);
                });
            },

            async openListModal() {
                this.showListModal = true;
                if (this.catalogExercises.length === 0) {
                    this.loadingCatalog = true;
                    try {
                        const r = await fetch('{{ route('api.exercise.list-all') }}');
                        const data = await r.json();
                        this.catalogExercises = data.exercises;
                    } catch (e) {
                        console.error('Error fetching catalog:', e);
                    } finally {
                        this.loadingCatalog = false;
                    }
                }
            },

            get filteredCatalog() {
                if (!this.listSearch) return this.catalogExercises;
                const s = this.listSearch.toLowerCase();
                return this.catalogExercises.filter(ex => 
                    ex.name.toLowerCase().includes(s) || 
                    ex.muscle_group.toLowerCase().includes(s)
                );
            },

            get groupedCatalog() {
                const groups = {};
                this.filteredCatalog.forEach(ex => {
                    if (!groups[ex.muscle_group]) groups[ex.muscle_group] = [];
                    groups[ex.muscle_group].push(ex);
                });
                return groups;
            },

            // --- Training Management ---
            startTraining() {
                if (!this.isPremium) return; // Feature lock
                this.timerActive = true;
                this.timerPaused = false;
                this.startTime = new Date();
                this.timerInterval = setInterval(() => {
                    if (!this.timerPaused) {
                        this.elapsedSeconds++;
                        this.duration = Math.ceil(this.elapsedSeconds / 60);
                    }
                }, 1000);
            },

            togglePause() {
                this.timerPaused = !this.timerPaused;
            },

            stopTraining() {
                clearInterval(this.timerInterval);
                this.stopRest();
                this.timerActive = false;
                this.showSummary = true;
                // Final calculation
                this.calculateCalories();
            },

            formatTimer(seconds) {
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return [h, m, s]
                    .map(v => v < 10 ? "0" + v : v)
                    .filter((v, i) => v !== "00" || i > 0)
                    .join(":");
            },

            formatRest(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m}:${s < 10 ? '0' + s : s}`;
            },

            get progressPercentage() {
                if (this.sets.length === 0) return 0;
                return (this.completedSetsCount / this.sets.length) * 100;
            },

            get completedSetsCount() {
                return this.sets.filter(s => s.completed).length;
            },

            get exerciseMuscle() {
                return this.selectedExercise?.muscle_group || 'Grupo muscular';
            },

            get exerciseEquipment() {
                return this.selectedExercise?.equipment || 'Equipamento livre';
            },

            get exerciseDifficulty() {
                return this.selectedExercise?.difficulty || 'Execução guiada';
            },

            get exerciseInstructions() {
                return this.selectedExercise?.instructions || 'Execute com controle, registre carga e repetições, conclua a série e descanse.';
            },

            get exerciseVideoUrl() {
                return this.selectedExercise?.video_url || '';
            },

            get exerciseTips() {
                return this.normalizeList(this.selectedExercise?.tips, [
                    'Mantenha controle durante toda a repetição.',
                    'Use amplitude confortável e postura estável.',
                ]);
            },

            get exerciseMistakes() {
                return this.normalizeList(this.selectedExercise?.common_mistakes, [
                    'Evite compensar com impulso.',
                    'Pare e registre desconforto se sentir dor incomum.',
                ]);
            },

            get currentSetIndex() {
                const index = this.sets.findIndex(s => !s.completed);
                return index === -1 ? Math.max(this.sets.length - 1, 0) : index;
            },

            get currentSetNumber() {
                return this.currentSetIndex + 1;
            },

            focusNextSetInput(event) {
                const inputs = Array.from(document.querySelectorAll('input[type="number"], select'));
                const index = inputs.indexOf(event.target);
                if (index > -1 && inputs[index + 1]) {
                    inputs[index + 1].focus();
                }
            },

            // --- Set Management ---
            addSet() {
                this.sets.push({ weight: '', reps: '', rest: '60', rpe: '', cadence: '', completed: false });
            },

            normalizeList(value, fallback) {
                if (!value) return fallback;
                if (Array.isArray(value)) return value.length ? value : fallback;
                try {
                    const parsed = JSON.parse(value);
                    return Array.isArray(parsed) && parsed.length ? parsed : fallback;
                } catch (e) {
                    return String(value).split('\n').map(v => v.trim()).filter(Boolean).slice(0, 3) || fallback;
                }
            },

            duplicateLastSet() {
                if (this.sets.length === 0) {
                    this.addSet();
                    return;
                }
                const last = this.sets[this.sets.length - 1];
                this.sets.push({ ...last, completed: false });
                this.handleDataChange();
                setTimeout(() => lucide.createIcons(), 50);
            },

            removeSet(index) {
                if (this.sets.length > 1) {
                    this.sets.splice(index, 1);
                    this.handleDataChange();
                }
            },

            toggleComplete(index) {
                this.sets[index].completed = !this.sets[index].completed;
                if (this.sets[index].completed) {
                    this.sets[index].completed_at = new Date().toLocaleTimeString();
                }
                this.handleDataChange();
                setTimeout(() => lucide.createIcons(), 50);
            },

            completeCurrentSet() {
                if (!this.timerActive && !this.editMode) {
                    this.showNotice({
                        type: 'info',
                        eyebrow: 'Inicie o treino',
                        title: 'Registro bloqueado',
                        message: 'Toque em Iniciar treino antes de concluir séries.',
                    });
                    return;
                }

                const index = this.currentSetIndex;
                if (!this.sets[index]) {
                    this.addSet();
                    return;
                }

                this.sets[index].completed = true;
                this.sets[index].completed_at = new Date().toLocaleTimeString();
                this.handleDataChange();

                this.showNotice({
                    type: 'success',
                    eyebrow: 'Série concluída',
                    title: `Descanso: ${this.sets[index].rest || 60}s`,
                    message: 'Quando estiver pronto, inicie a próxima série ou ajuste o descanso.',
                });

                this.startRest(parseInt(this.sets[index].rest || 60));

                if (index === this.sets.length - 1) {
                    this.addSet();
                }

                setTimeout(() => lucide.createIcons(), 50);
            },

            startRest(seconds = 60) {
                this.stopRest();
                this.restRemaining = Math.max(0, seconds);
                this.restActive = this.restRemaining > 0;
                if (!this.restActive) return;

                this.restInterval = setInterval(() => {
                    this.restRemaining--;
                    if (this.restRemaining <= 0) {
                        this.stopRest();
                    }
                }, 1000);
            },

            stopRest() {
                if (this.restInterval) clearInterval(this.restInterval);
                this.restInterval = null;
                this.restActive = false;
            },

            extendRest(seconds) {
                this.restRemaining += seconds;
                if (!this.restActive) this.startRest(this.restRemaining);
            },

            startNextSet() {
                this.stopRest();
                this.showNotice({
                    type: 'info',
                    eyebrow: 'Próxima série',
                    title: `Série ${this.currentSetNumber} de ${this.sets.length}`,
                    message: 'Ajuste carga e repetições se necessário, depois conclua a série.',
                });
            },

            finishExercise() {
                this.stopRest();
                this.finishAndLoadNextExercise();
            },

            get currentPlannedIndex() {
                if (!this.activity) return -1;
                return this.plannedExercises.findIndex(exercise => exercise.name === this.activity);
            },

            get nextPlannedExercise() {
                const index = this.currentPlannedIndex;
                if (index < 0) return null;
                return this.plannedExercises[index + 1] || null;
            },

            async finishAndLoadNextExercise() {
                if (!this.activity) return;
                await this.autoSave();

                if (this.nextPlannedExercise) {
                    this.loadPlannedExercise(this.nextPlannedExercise);
                    this.showNotice({
                        type: 'success',
                        eyebrow: 'Exercício salvo',
                        title: 'Próximo exercício carregado',
                        message: 'A ficha avançou para o próximo exercício planejado.',
                    });
                    return;
                }

                this.showNotice({
                    type: 'success',
                    eyebrow: 'Exercício salvo',
                    title: 'Ficha concluída',
                    message: 'Não há próximo exercício planejado para hoje.',
                });
            },

            // --- Autocomplete & Search ---
            async searchExercises() {
                if (this.activity.length < 2) {
                    this.results = [];
                    this.showResults = false;
                    return;
                }
                const res = await fetch(`{{ route('api.exercise.search') }}?q=${this.activity}`);
                const data = await res.json();
                this.results = data.results;
                this.showResults = true;
                this.focusIndex = -1;
            },

            selectExercise(res) {
                this.activity = res.name;
                this.selectedExercise = res;
                this.showResults = false;
                this.fetchHistory();
                setTimeout(() => lucide.createIcons(), 50);
            },

            loadPlannedExercise(exercise) {
                this.activity = exercise.name;
                this.selectedExercise = exercise;
                this.sets = (exercise.sets || []).map(set => ({
                    weight: set.weight ?? '',
                    reps: set.reps ?? '',
                    rest: String(set.rest ?? 60),
                    rpe: set.rpe ?? '',
                    cadence: set.cadence ?? '',
                    completed: false,
                }));
                if (this.sets.length === 0) this.addSet();
                this.currentEntryId = 0;
                this.fetchHistory();
                this.showNotice({
                    type: 'success',
                    eyebrow: 'Ficha carregada',
                    title: exercise.name,
                    message: 'As séries previstas foram aplicadas ao registro atual.',
                });
                setTimeout(() => lucide.createIcons(), 50);
            },

            focusNextResult() {
                if (this.focusIndex < this.results.length - 1) this.focusIndex++;
            },

            focusPrevResult() {
                if (this.focusIndex > 0) this.focusIndex--;
            },

            selectExerciseFromKeyboard() {
                if (this.focusIndex >= 0 && this.results[this.focusIndex]) {
                    this.selectExercise(this.results[this.focusIndex]);
                }
            },

            showNotice({ type = 'info', eyebrow = 'Aviso', title = 'Atenção', message }) {
                this.notice = { show: true, type, eyebrow, title, message };
                this.$nextTick(() => {
                    if (window.lucide) lucide.createIcons();
                });
            },

            // --- History & Progression ---
            async fetchHistory() {
                if (!this.isPremium) return;
                const res = await fetch(`{{ route('api.exercise.history') }}?exercise=${encodeURIComponent(this.activity)}`);
                const data = await res.json();
                this.history = data.history;
                
                // Calculate Suggestion (Simplified)
                if (this.history.length > 0) {
                    const lastSets = JSON.parse(this.history[0].sets_data);
                    const maxWeight = Math.max(...lastSets.map(s => parseFloat(s.weight) || 0));
                    this.suggestion = maxWeight > 0 ? (maxWeight + 2).toString() : null;
                } else {
                    this.suggestion = null;
                }
            },

            applySuggestion() {
                if (this.suggestion) {
                    this.sets.forEach(s => {
                        if (!s.weight) s.weight = this.suggestion;
                    });
                    this.handleDataChange();
                }
            },

            async repeatLastWorkout() {
                const res = await fetch(`{{ route('api.exercise.last') }}`);
                if (res.ok) {
                    const data = await res.json();
                    const last = data.last;
                    this.activity = last.activity_type;
                    this.sets = JSON.parse(last.sets_data).map(s => ({...s, completed: false}));
                    this.rpe = last.rpe || '0';
                    this.notes = last.notes || '';
                    this.fetchHistory();
                    setTimeout(() => lucide.createIcons(), 50);
                    this.showNotice({
                        type: 'success',
                        eyebrow: 'Treino recuperado',
                        title: 'Ultimo treino carregado',
                        message: 'Os exercicios, series e observacoes do treino anterior foram aplicados ao registro atual.',
                    });
                } else {
                    this.showNotice({
                        type: 'info',
                        eyebrow: 'Historico vazio',
                        title: 'Nenhum treino anterior',
                        message: 'Assim que voce registrar um treino, podera repetir a ultima sessao automaticamente por aqui.',
                    });
                }
            },

            async generateShareCard() {
                const element = document.getElementById('share-card-template');
                // Não precisamos mais mover o elemento para o centro, ele pode ser capturado fora do viewport
                
                try {
                    // Feedback visual no botão se possível, ou apenas proceder
                    const canvas = await html2canvas(element, {
                        backgroundColor: '#06080c',
                        scale: 2,
                        logging: false,
                        useCORS: true
                    });
                    
                    const image = canvas.toDataURL("image/png");
                    
                    if (navigator.share && /Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                        const blob = await (await fetch(image)).blob();
                        const file = new File([blob], 'meu-treino.png', { type: blob.type });
                        await navigator.share({
                            files: [file],
                            title: 'Meu Treino NexShape',
                            text: 'Sessão finalizada! 💪 #NexShape',
                        });
                    } else {
                        const link = document.createElement('a');
                        link.download = `meu-treino-${new Date().getTime()}.png`;
                        link.href = image;
                        link.click();
                    }
                } catch (e) {
                    console.error('Error sharing card:', e);
                    this.showNotice({
                        type: 'info',
                        eyebrow: 'Compartilhamento',
                        title: 'Nao foi possivel gerar o card',
                        message: 'Tente novamente em alguns instantes. Se persistir, atualize a pagina e gere o card novamente.',
                    });
                }
            },

            // --- Logic & Sync ---
            handleDataChange() {
                this.calculateCalories();
            },

            async calculateCalories() {
                if (!this.isPremium || !this.activity) return;
                const res = await fetch(`{{ route('api.exercise.calculate-calories') }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        duration: this.duration,
                        exercise: this.activity,
                        rpe: this.rpe
                    })
                });
                const data = await res.json();
                this.calories = data.calories;
            },

            async autoSave() {
                if (!this.activity) return;
                this.autoSaving = true;
                const body = {
                    id: this.currentEntryId,
                    activity_type: this.activity,
                    duration_min: this.duration,
                    calories_burned: this.calories,
                    rpe: this.rpe,
                    rest_default: this.sets[0]?.rest || 60,
                    sets_data: JSON.stringify(this.sets),
                    notes: this.notes,
                    entry_date: '{{ $date }}'
                };
                
                try {
                    const response = await fetch(`{{ route('api.exercise.sync') }}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(body)
                    });
                    const data = await response.json();
                    if (data.id) this.currentEntryId = data.id;
                    setTimeout(() => { this.autoSaving = false; }, 2000);
                } catch (e) {
                    this.autoSaving = false;
                }
            },

            submitForm(event) {
                if (!this.timerActive && !this.editMode) {
                    this.showNotice({
                        type: 'info',
                        eyebrow: 'Inicie o treino',
                        title: 'Salvamento bloqueado',
                        message: 'Toque em Iniciar treino antes de salvar o registro.',
                    });
                    return;
                }
                // Manually inject the final sets_data JSON before native submission
                const form = event.target;
                const setsInput = form.querySelector('input[name="sets_data"]');
                const notesInput = form.querySelector('input[name="notes"]');
                setsInput.value = JSON.stringify(this.sets);
                if (notesInput) {
                    const cleanNotes = (this.notes || '').replace(/^Desconforto: .*?\.\s*/i, '').trim();
                    notesInput.value = this.discomfort
                        ? `Desconforto: ${this.discomfort}. ${cleanNotes}`.trim()
                        : cleanNotes;
                }
                form.submit();
            },

            submitVisibleForm() {
                const form = document.querySelector('form[method="POST"]');
                if (form) this.submitForm({ target: form });
            },

            lastSetLabel(index) {
                if (!this.history.length) return '—';
                try {
                    const sets = JSON.parse(this.history[0].sets_data || '[]');
                    const set = sets[index];
                    if (!set) return '—';
                    return `${set.weight || 0} kg × ${set.reps || 0}`;
                } catch (e) {
                    return '—';
                }
            },

            todaySetLabel(set) {
                const weight = set.weight || 0;
                const reps = set.reps || 0;
                return `${weight} kg × ${reps}`;
            },

            formatDate(d) {
                const date = new Date(d);
                return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            }
        }
    }
</script>

<style>
    body {
        background-color: #080a0f;
        background-image:
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(16, 185, 129, 0.1);
        border-radius: 20px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(16, 185, 129, 0.2);
    }
    
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endsection
