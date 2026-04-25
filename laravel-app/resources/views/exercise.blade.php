@extends('layouts.app')

@section('title', 'Performance HUB — NexShape Pro')

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto" x-data="workoutHUD()">
    
    <!-- Timer Flutuante "SaaS Elite" -->
    <div class="fixed bottom-8 right-8 z-[100] group" x-show="timerActive" x-transition>
        <div class="bg-zinc-900 border-2 border-blue-600/50 p-6 rounded-[2.5rem] shadow-2xl backdrop-blur-3xl flex items-center gap-6">
            <div class="text-center">
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1" x-text="timerPaused ? 'Pausado' : 'Treinando'"></p>
                <h3 class="text-4xl font-black text-white italic tracking-tighter tabular-nums" x-text="formatTimer(elapsedSeconds)">00:00</h3>
            </div>
            <div class="flex flex-col gap-2">
                <button @click="togglePause()" class="w-10 h-10 rounded-xl bg-amber-500 text-zinc-950 flex items-center justify-center hover:bg-amber-400 transition-all shadow-lg shadow-amber-500/20">
                    <i class="fa-solid" :class="timerPaused ? 'fa-play' : 'fa-pause'"></i>
                </button>
                <button @click="stopTraining()" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                    <i class="fa-solid fa-stop text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter">PERFORMANCE <span class="text-blue-500">HUB</span></h1>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest flex items-center gap-2">
                Sincronização Elite
                <span class="w-1 h-1 rounded-full bg-blue-500/50"></span>
                Registro Avançado v2.0
            </p>
        </div>

        <!-- Date Selector -->
        <div class="flex items-center gap-3 bg-zinc-900/40 backdrop-blur-3xl border border-white/10 p-2 rounded-2xl shadow-2xl">
            <a href="{{ route('exercise', ['date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 rounded-xl transition-all active:scale-95">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            
            <label class="relative cursor-pointer px-6 py-2 group">
                <input type="date" value="{{ $date }}" 
                       onchange="window.location.href = '{{ route('exercise') }}?date=' + this.value"
                       class="absolute inset-0 opacity-0 cursor-pointer z-10">
                <div class="flex items-center gap-3 text-sm font-black text-white tracking-widest uppercase group-hover:text-blue-400 transition-colors">
                    <i class="far fa-calendar-alt text-blue-500"></i>
                    {{ date('d/m/Y', strtotime($date)) }}
                    <i class="fas fa-caret-down text-[10px] text-zinc-600 group-hover:text-blue-400 transition-colors"></i>
                </div>
            </label>

            <a href="{{ route('exercise', ['date' => date('Y-m-d', strtotime($date . ' +1 day'))]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 rounded-xl transition-all active:scale-95">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] shadow-2xl transition-all hover:border-blue-500/20">
            <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Tempo Total</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white tracking-tighter tabular-nums">{{ $sumMin }}</span>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">min</span>
            </div>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] shadow-2xl transition-all hover:border-emerald-500/20">
            <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Queima Estimada</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white tracking-tighter tabular-nums">{{ $sumBurn }}</span>
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">kcal</span>
            </div>
        </div>
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] shadow-2xl transition-all hover:border-indigo-500/20">
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
        <div class="bg-blue-600/10 backdrop-blur-3xl border border-blue-500/20 p-6 rounded-[2rem] shadow-2xl flex items-center justify-between group">
            <div x-show="!timerActive">
                <p class="text-[9px] text-blue-400 font-black uppercase tracking-widest mb-1">Pronto para começar?</p>
                <h4 class="text-sm font-black text-white">Novo Treino</h4>
            </div>
            <button @click="startTraining()" x-show="!timerActive" class="px-5 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-500 transition-all text-[10px] uppercase tracking-widest">INICIAR TREINO</button>
            <div x-show="timerActive" class="w-full">
                 <p class="text-[9px] text-blue-400 font-black uppercase tracking-widest mb-1">Treino em curso</p>
                 <div class="h-1.5 w-full bg-zinc-800 rounded-full mt-2 overflow-hidden">
                    <div class="h-full bg-blue-500 transition-all duration-500" :style="'width: ' + progressPercentage + '%'"></div>
                 </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Activity List (Left) -->
        <div class="lg:col-span-12 xl:col-span-8 space-y-8">
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-8 lg:p-12 shadow-2xl relative overflow-hidden">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Registros de Hoje</h3>
                    <div class="flex gap-4">
                        <button @click="repeatLastWorkout()" class="text-[10px] font-black text-zinc-400 hover:text-white uppercase tracking-widest flex items-center gap-2 transition-all">
                            <i class="fas fa-redo text-[8px]"></i>
                            Repetir último treino
                        </button>
                    </div>
                </div>

                @if($rows->isEmpty())
                    <div class="py-20 text-center">
                        <div class="w-24 h-24 bg-zinc-950 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-white/5">
                            <i class="fa-solid fa-dumbbell text-3xl text-zinc-800"></i>
                        </div>
                        <h4 class="text-xl font-black text-zinc-500 tracking-tight">Prepare-se para o primeiro set</h4>
                        <p class="text-xs text-zinc-600 font-bold uppercase mt-2">Nenhuma atividade registrada ainda</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($rows as $row)
                            @php $sets = json_decode($row->sets_data ?? '[]', true); @endphp
                            <div class="group p-8 bg-zinc-950/40 border border-white/5 rounded-[2.5rem] hover:border-blue-500/30 transition-all shadow-inner">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-600/20">
                                            <i class="fa-solid fa-bolt"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-black text-white tracking-tighter italic uppercase">{{ $row->activity_type }}</h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest bg-zinc-900/80 px-2 py-0.5 rounded-md border border-white/5">{{ $row->duration_min }} MIN</span>
                                                <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest bg-zinc-900/80 px-2 py-0.5 rounded-md border border-white/5">{{ $row->calories_burned ?? 0 }} KCAL</span>
                                                @if($row->rpe)
                                                    <span class="text-[9px] text-amber-500 font-black uppercase tracking-widest bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">RPE: {{ $row->rpe }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('exercise', ['date' => $date, 'edit' => $row->id]) }}" class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-blue-500 border border-white/5 transition-all">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                        <form method="POST" onsubmit="return confirm('Excluir registro?')">
                                            @csrf
                                            <input type="hidden" name="action" value="delete_exercise">
                                            <input type="hidden" name="entry_date" value="{{ $date }}">
                                            <input type="hidden" name="exercise_id" value="{{ $row->id }}">
                                            <button type="submit" class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-red-500 border border-white/5 transition-all">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if(!empty($sets))
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 pt-4 border-t border-white/5">
                                        @foreach($sets as $idx => $s)
                                            <div class="p-4 rounded-2xl border border-white/5 transition-all {{ ($s['completed'] ?? false) ? 'bg-blue-600/10 border-blue-500/20' : 'bg-zinc-900/50' }}">
                                                <div class="flex justify-between items-center mb-2">
                                                    <p class="text-[8px] font-black {{ ($s['completed'] ?? false) ? 'text-blue-400' : 'text-zinc-600' }} uppercase">Set {{ $idx + 1 }}</p>
                                                    @if($s['completed'] ?? false)
                                                        <i class="fas fa-check-circle text-[8px] text-blue-500"></i>
                                                    @endif
                                                </div>
                                                <p class="text-sm font-black text-white">{{ $s['weight'] }}kg <span class="text-zinc-500">x</span> {{ $s['reps'] }}</p>
                                                <div class="flex items-center justify-between mt-2">
                                                    @if(isset($s['weight']) && isset($s['reps']) && $s['weight'] > 0)
                                                        <p class="text-[8px] text-zinc-500 font-bold uppercase">1RM: {{ round($s['weight'] * (1 + 0.0333 * $s['reps']), 1) }}kg</p>
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

            <!-- Resumo ao Finalizar (Opcional - aparece quando finalizar treino clicado) -->
            <div x-show="showSummary" x-transition class="bg-gradient-to-br from-zinc-900 to-black border-2 border-blue-600/50 rounded-[3rem] p-10 shadow-2xl">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">RESUMO DO TREINO</h3>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Sessão finalizada com sucesso</p>
                    </div>
                    <button @click="showSummary = false" class="text-zinc-500 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-zinc-950 p-6 rounded-2xl border border-white/5">
                        <p class="text-[8px] text-zinc-600 font-black uppercase mb-1">Exercícios</p>
                        <p class="text-2xl font-black text-white" x-text="rowsCount"></p>
                    </div>
                    <div class="bg-zinc-950 p-6 rounded-2xl border border-white/5">
                        <p class="text-[8px] text-zinc-600 font-black uppercase mb-1">Duração</p>
                        <p class="text-2xl font-black text-white">{{ $sumMin }} <span class="text-xs text-zinc-500">min</span></p>
                    </div>
                    <div class="bg-zinc-950 p-6 rounded-2xl border border-white/5">
                        <p class="text-[8px] text-zinc-600 font-black uppercase mb-1">Volume</p>
                        <p class="text-2xl font-black text-white">{{ number_format($totalVolume, 0, ',', '.') }} <span class="text-xs text-zinc-500">kg</span></p>
                    </div>
                    <div class="bg-zinc-950 p-6 rounded-2xl border border-white/5">
                        <p class="text-[8px] text-zinc-600 font-black uppercase mb-1">Kcal</p>
                        <p class="text-2xl font-black text-white">{{ $sumBurn }} <span class="text-xs text-zinc-500">kcal</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Registry Form (Right) -->
        <div class="lg:col-span-12 xl:col-span-4 lg:sticky lg:top-8 h-fit">
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 lg:p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8">
                    <div x-show="autoSaving" class="flex items-center gap-2 text-[8px] text-emerald-500 font-black uppercase tracking-widest animate-pulse">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Salvo automaticamente
                    </div>
                </div>

                <h3 class="text-2xl font-black text-white tracking-tighter uppercase mb-2">{{ $editRow ? 'EDITAR' : 'NOVO' }} REGISTRO</h3>
                <div class="flex items-center gap-4 mb-10">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Performance Hub Logging</p>
                    <div class="h-1 flex-1 bg-white/5 rounded-full"></div>
                </div>

                <form method="POST" class="space-y-8" @submit.prevent="submitForm($event)">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if($editRow) <input type="hidden" name="exercise_edit_id" value="{{ $editRow->id }}"> @endif
                    <input type="hidden" name="sets_data" :value="JSON.stringify(sets)">

                    <div class="space-y-4 relative" x-on:click.away="showResults = false">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Atividade / Exercício</label>
                        <div class="relative">
                            <div class="flex gap-2">
                                <input type="text" name="activity_type" 
                                    x-model="activity"
                                    @input.debounce.300ms="searchExercises()"
                                    @keydown.enter.prevent="selectExerciseFromKeyboard()"
                                    @keydown.arrow-down.prevent="focusNextResult()"
                                    @keydown.arrow-up.prevent="focusPrevResult()"
                                    autocomplete="off"
                                    required placeholder="Pesquisar exercício..." 
                                    class="flex-1 bg-zinc-950 border border-white/5 p-6 rounded-2xl text-white text-sm font-black outline-none focus:border-blue-500/50 transition-all shadow-inner uppercase tracking-wider">
                                
                                <button type="button" @click="openListModal()" 
                                    class="w-20 bg-zinc-950 border border-white/5 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-blue-500 hover:border-blue-500/30 transition-all shadow-inner">
                                    <i class="fa-solid fa-list-ul"></i>
                                </button>
                            </div>
                            
                            <!-- Dropdown Autocomplete -->
                            <div x-show="showResults && results.length > 0" class="absolute z-50 left-0 right-0 mt-2 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-3xl">
                                <template x-for="(res, i) in results" :key="i">
                                    <div @click="selectExercise(res)" :class="{'bg-blue-600/20 text-white': focusIndex === i, 'text-zinc-400': focusIndex !== i}" 
                                         class="px-6 py-4 cursor-pointer hover:bg-white/5 flex items-center justify-between transition-all border-b border-white/5 last:border-0">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest" x-text="res.name"></p>
                                            <p class="text-[8px] font-bold text-zinc-500 uppercase mt-1" x-text="res.muscle_group"></p>
                                        </div>
                                        <i class="fas fa-plus text-[10px] text-blue-500"></i>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Histórico Rápido -->
                        <div x-show="history.length > 0" class="mt-4 p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                            <p class="text-[8px] text-zinc-600 font-black uppercase mb-3">Últimas Marcas</p>
                            <div class="space-y-2">
                                <template x-for="h in history" :key="h.entry_date">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[9px] text-zinc-500 font-bold" x-text="formatDate(h.entry_date)"></span>
                                        <div class="flex gap-2">
                                            <template x-for="s in JSON.parse(h.sets_data).slice(0, 3)" :key="Math.random()">
                                                <span class="text-[9px] text-white font-black" x-text="s.weight + 'kg x ' + s.reps"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Sugestão de Progressão -->
                            <div x-show="suggestion" class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                                        <i class="fas fa-chart-line text-[10px]"></i>
                                    </div>
                                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">Sugestão: <span x-text="suggestion"></span> kg</p>
                                </div>
                                <button type="button" @click="applySuggestion()" class="text-[8px] bg-emerald-500 text-zinc-950 px-2 py-1 rounded-md font-black uppercase hover:bg-emerald-400 transition-all">Aplicar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Séries Manager (Live Logging) -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center ml-2">
                            <div class="flex items-center gap-3">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Séries / Sets</label>
                                <span class="text-[9px] bg-zinc-950 px-2 py-0.5 rounded-full border border-white/5 text-zinc-400 font-black" x-text="completedSetsCount + '/' + sets.length"></span>
                            </div>
                            <div class="flex gap-4">
                                <button type="button" @click="duplicateLastSet()" class="text-[9px] font-black text-zinc-500 hover:text-white uppercase tracking-widest transition-colors">Duplicar</button>
                                <button type="button" @click="addSet()" class="text-[9px] font-black text-blue-400 hover:text-white uppercase tracking-widest transition-colors">+ Adicionar</button>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(set, index) in sets" :key="index">
                                <div class="grid grid-cols-12 gap-2 group/set">
                                    <button type="button" @click="toggleComplete(index)" 
                                            :class="set.completed ? 'bg-blue-600 text-white border-blue-500' : 'bg-zinc-950 text-zinc-600 border-white/5'"
                                            class="col-span-2 h-12 flex items-center justify-center border rounded-xl text-[10px] font-black transition-all hover:scale-105">
                                        <i class="fa-solid" :class="set.completed ? 'fa-check' : index + 1"></i>
                                    </button>
                                    <input type="number" placeholder="kg" x-model="set.weight" @input="handleDataChange()" 
                                           @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                           class="col-span-3 bg-zinc-950 border border-white/5 rounded-xl text-center text-xs font-black text-white outline-none focus:border-blue-500/50">
                                    <input type="number" placeholder="reps" x-model="set.reps" @input="handleDataChange()" 
                                           @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                           class="col-span-3 bg-zinc-950 border border-white/5 rounded-xl text-center text-xs font-black text-white outline-none focus:border-blue-500/50">
                                    
                                    <div class="col-span-3">
                                        <select x-model="set.rest" @change="handleDataChange()" 
                                                @keydown.enter.prevent="index === sets.length - 1 ? addSet() : focusNextSetInput($event)"
                                                class="w-full h-12 bg-zinc-950 border border-white/5 rounded-xl text-center text-[10px] font-black text-zinc-400 outline-none appearance-none">
                                            <option value="30">30s</option>
                                            <option value="45">45s</option>
                                            <option value="60">60s</option>
                                            <option value="90">90s</option>
                                            <option value="120">120s</option>
                                        </select>
                                    </div>

                                    <button type="button" @click="removeSet(index)" class="col-span-1 h-12 flex items-center justify-center bg-zinc-900 hover:bg-red-500/10 text-zinc-700 hover:text-red-500 border border-white/5 rounded-xl transition-all opacity-0 group-hover/set:opacity-100">
                                        <i class="fa-solid fa-times text-[10px]"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Métricas Avançadas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between ml-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Intensidade (RPE)</label>
                                @if(!$isPremium) <i class="fas fa-lock text-[8px] text-amber-500"></i> @endif
                            </div>
                            <select name="rpe" x-model="rpe" @change="handleDataChange()" {{ !$isPremium ? 'disabled' : '' }}
                                class="w-full bg-zinc-950 border border-white/5 p-6 rounded-2xl text-white text-sm font-black outline-none focus:border-blue-500/50 shadow-inner appearance-none disabled:opacity-50">
                                <option value="0">Selecionar</option>
                                @for($i=1; $i<=10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between ml-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Gasto (kcal)</label>
                                @if(!$isPremium) <i class="fas fa-lock text-[8px] text-amber-500"></i> @endif
                            </div>
                            <input type="number" name="calories_burned" x-model="calories" readonly placeholder="Auto"
                                class="w-full bg-zinc-950/50 border border-white/5 p-6 rounded-2xl text-white text-sm font-black outline-none shadow-inner opacity-80 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Observações</label>
                        <input type="text" name="notes" x-model="notes" placeholder="Ex: Dor no ombro, execução difícil..." 
                               class="w-full bg-zinc-950 border border-white/5 p-6 rounded-2xl text-white text-[10px] font-bold outline-none focus:border-blue-500/50 shadow-inner uppercase tracking-widest">
                    </div>

                    <div class="space-y-4 pt-4">
                        <button type="submit" class="w-full py-6 bg-blue-600 hover:bg-blue-500 text-white font-black text-[11px] uppercase tracking-[0.3em] rounded-3xl transition-all shadow-2xl shadow-blue-500/30 active:scale-[0.98]">
                            {{ $editRow ? 'ATUALIZAR REGISTRO' : 'REGISTRAR PERFORMANCE' }}
                        </button>
                        
                        @if($editRow)
                            <a href="{{ route('exercise', ['date' => $date]) }}" class="block text-center text-[10px] text-zinc-600 font-black uppercase tracking-widest hover:text-white transition-colors">Abortar Edição</a>
                        @endif
                    </div>
                </form>

                @if(!$isPremium)
                <div class="mt-8 p-6 bg-amber-500/5 border border-amber-500/20 rounded-2xl">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-crown text-amber-500 text-xs"></i>
                        <h4 class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Upgrade para o Plano PRO</h4>
                    </div>
                    <p class="text-[9px] text-zinc-500 font-bold leading-relaxed">Libere Cronômetro automático, RPE, Sugestão de carga e Gasto calórico inteligente.</p>
                    <a href="{{ route('plano') }}" class="mt-4 block text-center py-3 bg-amber-500 text-zinc-950 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-400 transition-all">Ver Planos</a>
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
         class="fixed inset-0 z-[200] flex items-center justify-center p-4 lg:p-12 bg-zinc-950/90 backdrop-blur-md"
         @keydown.escape.window="showListModal = false"
         style="display: none;">
        
        <div class="bg-zinc-900 border border-white/10 w-full max-w-4xl h-[80vh] rounded-[3rem] shadow-3xl flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="p-8 lg:p-10 border-b border-white/5 flex items-center justify-between shrink-0">
                <div class="space-y-1">
                    <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">CATÁLOGO <span class="text-blue-500">NEXSHAPE</span></h2>
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Navegue por grupos musculares</p>
                </div>
                <button @click="showListModal = false" class="w-12 h-12 bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <!-- Modal Search -->
            <div class="px-8 lg:px-10 py-6 bg-zinc-950/50 border-b border-white/5 shrink-0">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-6 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                    <input type="text" x-model="listSearch" placeholder="Filtrar por nome ou músculo..." 
                           class="w-full bg-zinc-900 border border-white/5 p-5 pl-14 rounded-2xl text-white text-sm font-bold outline-none focus:border-blue-500/30 transition-all uppercase tracking-widest placeholder:text-zinc-700">
                </div>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-8 lg:p-10 custom-scrollbar">
                <div x-show="loadingCatalog" class="h-full flex flex-col items-center justify-center space-y-4">
                    <div class="w-12 h-12 border-4 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                    <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Sincronizando base de dados...</p>
                </div>

                <div x-show="!loadingCatalog">
                    <template x-for="(exs, group) in groupedCatalog" :key="group">
                        <div class="mb-10">
                            <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] mb-6 flex items-center gap-4">
                                <span x-text="group"></span>
                                <div class="h-px flex-1 bg-white/5"></div>
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="ex in exs" :key="ex.name">
                                    <button @click="selectExercise(ex); showListModal = false" 
                                            class="flex items-center justify-between p-5 bg-zinc-950/50 border border-white/5 rounded-2xl hover:bg-blue-600/10 hover:border-blue-500/30 transition-all text-left group">
                                        <span class="text-xs font-black text-white uppercase tracking-wider group-hover:text-blue-400" x-text="ex.name"></span>
                                        <i class="fa-solid fa-plus text-[10px] text-zinc-700 group-hover:text-blue-500"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div x-show="Object.keys(groupedCatalog).length === 0" class="h-64 flex flex-col items-center justify-center">
                        <i class="fa-solid fa-ghost text-4xl text-zinc-800 mb-4"></i>
                        <p class="text-xs text-zinc-600 font-black uppercase tracking-widest">Nenhum exercício encontrado</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 bg-zinc-950/80 border-t border-white/5 flex items-center justify-between shrink-0">
                <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-widest">Selecione um exercício para iniciar o registro</p>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[8px] text-emerald-500 font-black uppercase">Sistema Online</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function workoutHUD() {
        return {
            // State
            activity: '{{ old('activity_type', $editRow->activity_type ?? '') }}',
            sets: @json(json_decode($editRow->sets_data ?? '[]')),
            duration: {{ old('duration_min', $editRow->duration_min ?? '45') }},
            calories: '{{ old('calories_burned', $editRow->calories_burned ?? '') }}',
            rpe: '{{ old('rpe', $editRow->rpe ?? '0') }}',
            notes: '{{ old('notes', $editRow->notes ?? '') }}',
            
            // UI State
            results: [],
            showResults: false,
            focusIndex: -1,
            history: [],
            suggestion: null,
            autoSaving: false,
            showSummary: false,
            rowsCount: {{ $rows->count() }},
            isPremium: {{ $isPremium ? 'true' : 'false' }},

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

            get progressPercentage() {
                if (this.sets.length === 0) return 0;
                return (this.completedSetsCount / this.sets.length) * 100;
            },

            get completedSetsCount() {
                return this.sets.filter(s => s.completed).length;
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
                this.sets.push({ weight: '', reps: '', rest: '60', completed: false });
            },

            duplicateLastSet() {
                if (this.sets.length === 0) {
                    this.addSet();
                    return;
                }
                const last = this.sets[this.sets.length - 1];
                this.sets.push({ ...last, completed: false });
                this.handleDataChange();
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
                this.showResults = false;
                this.fetchHistory();
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
                    alert('Último treino carregado com sucesso!');
                } else {
                    alert('Nenhum treino anterior encontrado.');
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
                    id: '{{ $editRow->id ?? 0 }}',
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
                    await fetch(`{{ route('api.exercise.sync') }}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(body)
                    });
                    setTimeout(() => { this.autoSaving = false; }, 2000);
                } catch (e) {
                    this.autoSaving = false;
                }
            },

            submitForm(event) {
                // Manually inject the final sets_data JSON before native submission
                const form = event.target;
                const setsInput = form.querySelector('input[name="sets_data"]');
                setsInput.value = JSON.stringify(this.sets);
                form.submit();
            },

            formatDate(d) {
                const date = new Date(d);
                return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            }
        }
    }
</script>

<style>
    body { background-color: #0b0e14 !important; }
    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }

    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.2); }
</style>
@endsection
