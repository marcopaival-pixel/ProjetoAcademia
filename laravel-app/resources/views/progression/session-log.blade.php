@extends('layouts.app')

@section('title', 'Sessão Ativa: ' . $plan->name)

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto" x-data="activeSession()">
    
    <!-- Premium Rest Timer Floating Panel -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4" 
         x-show="timerActive || timerFinished" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-16 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-16 scale-95"
         style="display: none;">
         
         <div class="bg-zinc-900/95 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-6 shadow-2xl overflow-hidden relative"
              :class="{ 'border-emerald-500/50 shadow-[0_0_30px_rgba(16,185,129,0.2)]': timerFinished, 'border-red-500/50 shadow-[0_0_30px_rgba(239,68,68,0.2)]': timerEnding && !timerFinished }">
              
              <!-- Background Pulse for last 3 seconds -->
              <div class="absolute inset-0 bg-red-500/10 animate-pulse" x-show="timerEnding && !timerFinished" style="display: none;"></div>

              <div class="flex items-center gap-6 relative z-10">
                  <!-- Circular Progress -->
                  <div class="relative w-20 h-20 flex-shrink-0">
                      <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                          <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="8" class="text-zinc-800"></circle>
                          <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="8" 
                                  stroke-linecap="round"
                                  :class="{ 'text-red-500': timerEnding, 'text-blue-500': !timerEnding && !timerFinished, 'text-emerald-500': timerFinished }"
                                  :stroke-dasharray="276.5"
                                  :stroke-dashoffset="276.5 - (276.5 * (timerSeconds / totalSeconds))"
                                  class="transition-all duration-1000 ease-linear"></circle>
                      </svg>
                      <div class="absolute inset-0 flex items-center justify-center">
                          <template x-if="!timerFinished">
                              <span class="text-xl font-black tabular-nums tracking-tighter"
                                    :class="{ 'text-red-500 animate-pulse': timerEnding, 'text-white': !timerEnding }" 
                                    x-text="timerSeconds"></span>
                          </template>
                          <template x-if="timerFinished">
                              <i class="fa-solid fa-check text-2xl text-emerald-500 animate-bounce"></i>
                          </template>
                      </div>
                  </div>

                  <!-- Info & Actions -->
                  <div class="flex-1">
                      <template x-if="!timerFinished">
                          <div>
                              <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Descanso Ativo</p>
                              <p class="text-sm font-bold text-zinc-300 mb-3">Recupere suas energias</p>
                              <div class="flex gap-2">
                                  <button @click="stopTimer(true)" type="button" class="flex-1 py-2 rounded-xl bg-zinc-800/80 text-zinc-400 font-black uppercase text-[10px] tracking-widest hover:bg-zinc-700 hover:text-white transition-all">
                                      Pular
                                  </button>
                                  <button @click="addTime(15)" type="button" class="px-3 py-2 rounded-xl bg-zinc-800/80 text-zinc-400 font-black uppercase text-[10px] tracking-widest hover:bg-zinc-700 hover:text-white transition-all">
                                      +15s
                                  </button>
                              </div>
                          </div>
                      </template>

                      <template x-if="timerFinished">
                          <div class="animate-fade-in">
                              <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Concluído</p>
                              <p class="text-sm font-bold text-white mb-3">Pronto para esmagar!</p>
                              <button @click="closeTimerModal()" type="button" class="w-full py-2.5 rounded-xl bg-emerald-500 text-white font-black uppercase text-[10px] tracking-widest shadow-[0_0_15px_rgba(16,185,129,0.3)] hover:bg-emerald-400 transition-all active:scale-95">
                                  Iniciar Série
                              </button>
                          </div>
                      </template>
                  </div>
              </div>
         </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Modo Foco Ativado</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ now()->format('d/m/Y') }}</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic">
                {{ $plan->name }}
            </h1>
            <p class="text-zinc-500 text-sm font-medium">Execute com precisão. O sistema está monitorando sua progressão.</p>
        </div>
        <div class="flex items-center gap-4">
             <button type="submit" form="workoutForm" class="px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl shadow-2xl shadow-blue-600/30 transition-all active:scale-95 uppercase tracking-[0.2em] text-xs">
                Finalizar Treino
             </button>
        </div>
    </div>

    <form id="workoutForm" action="{{ route('progression.log.store') }}" method="POST" class="space-y-10">
        @csrf
        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">

        @foreach($plan->exercises as $index => $exercise)
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-8 lg:p-10 shadow-2xl space-y-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-zinc-950 rounded-[1.75rem] flex items-center justify-center text-blue-500 border border-white/5 text-2xl shadow-inner">
                            <i class="fa-solid fa-dumbbell"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight uppercase italic">{{ $exercise->catalogExercise->name }}</h3>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="px-3 py-0.5 bg-zinc-950 text-zinc-500 text-[10px] font-bold rounded-lg border border-white/5">
                                    {{ $exercise->catalogExercise->muscle_group }}
                                </span>
                                @if($exercise->last_log)
                                    <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest">
                                        <i class="fa-solid fa-clock-rotate-left mr-1"></i>Último: {{ $exercise->last_log->weight_kg }}kg x {{ $exercise->last_log->reps_done }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @php
                        $indicator = $exercise->suggestion['indicator'] ?? '';
                        $badgeClass = match($indicator) {
                            'increase' => 'text-emerald-400 border-emerald-500/20 bg-emerald-500/10',
                            'decrease' => 'text-amber-400 border-amber-500/20 bg-amber-500/10',
                            'locked' => 'text-zinc-500 border-white/5 bg-zinc-950/50',
                            default => 'text-blue-400 border-blue-500/20 bg-blue-500/10'
                        };
                    @endphp
                    <div class="px-6 py-3 rounded-2xl border {{ $badgeClass }} flex items-center gap-3">
                        @if($indicator == 'locked')
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">{{ $exercise->suggestion['message'] }}</span>
                        @else
                            <i class="fa-solid fa-bolt-lightning text-xs animate-pulse"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">{{ $exercise->suggestion['message'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <input type="hidden" name="logs[{{ $index }}][training_plan_exercise_id]" value="{{ $exercise->id }}">
                    <input type="hidden" name="logs[{{ $index }}][exercise_id]" value="{{ $exercise->exercise_id }}">

                    @foreach($exercise->sets as $setIdx => $set)
                        @php
                            $isPremium = auth()->user()->hasPremiumAccess();
                            $suggestedWeight = ($isPremium && isset($exercise->suggestion['suggested_weight'])) 
                                ? $exercise->suggestion['suggested_weight'] 
                                : ($exercise->last_log->weight_kg ?? ($set->weight_target ?? 0));
                            
                            $maxSafeWeight = $exercise->suggestion['max_safe_weight'] ?? ($suggestedWeight > 0 ? $suggestedWeight * 1.5 : 100);

                            $typeLabel = match($set->set_type) {
                                'warmup' => 'WUP',
                                'drop' => 'DROP',
                                'failure' => 'FAIL',
                                default => 'SET ' . ($setIdx + 1)
                            };
                        @endphp
                        
                        <div x-data="setControl({{ $suggestedWeight }}, {{ $maxSafeWeight }})" 
                             class="bg-zinc-950/80 border rounded-3xl p-5 transition-all duration-300 relative overflow-hidden"
                             :class="isOverloaded ? 'border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.15)]' : (isProgressing ? 'border-emerald-500/30' : 'border-white/5')">
                            
                            <!-- Alerta de Sobrecarga Background -->
                            <div class="absolute inset-0 bg-red-500/5 transition-opacity duration-300" x-show="isOverloaded" style="display: none;"></div>

                            <div class="relative z-10 flex flex-col gap-5">
                                <!-- Header da Série -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 bg-zinc-900 border border-white/10 rounded-lg text-[10px] font-black text-zinc-400">
                                            {{ $typeLabel }}
                                        </span>
                                        <p class="text-xs font-black text-blue-400 italic">{{ $suggestedWeight }}kg <span class="text-zinc-600 not-italic">x</span> {{ $set->reps_target }}</p>
                                    </div>
                                    <button type="button" @click="startTimer({{ $set->rest_seconds }})" class="px-3 py-1.5 bg-zinc-900 border border-white/5 text-zinc-400 hover:text-white rounded-xl text-[10px] font-black uppercase transition-all flex items-center gap-2">
                                        {{ $set->rest_seconds }}s <i class="fa-regular fa-clock"></i>
                                    </button>
                                </div>

                                <!-- Controles Principais (Mobile-First) -->
                                <div class="grid grid-cols-2 gap-4">
                                    
                                    <!-- Controle de Carga -->
                                    <div class="space-y-3 col-span-2 md:col-span-1">
                                        <div class="flex items-center justify-between">
                                            <label class="text-[10px] font-black uppercase text-zinc-500 tracking-widest">Carga (kg)</label>
                                            <span x-show="isOverloaded" class="text-[9px] font-black uppercase text-red-500 animate-pulse flex items-center gap-1" style="display: none;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Risco de Sobrecarga
                                            </span>
                                            <span x-show="isProgressing && !isOverloaded" class="text-[9px] font-black uppercase text-emerald-500 flex items-center gap-1" style="display: none;">
                                                <i class="fa-solid fa-arrow-trend-up"></i> Evolução
                                            </span>
                                        </div>
                                        
                                        <!-- Stepper -->
                                        <div class="flex items-center justify-between bg-zinc-900 border border-white/5 rounded-2xl p-1 relative overflow-hidden">
                                            <button type="button" @click="decrementWeight()" class="w-12 h-12 rounded-xl bg-zinc-950/50 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors active:scale-95 z-10">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            
                                            <input type="number" step="0.5" x-model="weight" name="logs[{{ $index }}][sets][{{ $setIdx }}][weight]" 
                                                class="w-20 bg-transparent border-0 text-center text-xl font-black text-white focus:ring-0 p-0 outline-none z-10">
                                            
                                            <button type="button" @click="incrementWeight()" class="w-12 h-12 rounded-xl bg-zinc-950/50 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors active:scale-95 z-10">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>

                                        <!-- Slider Moderno -->
                                        <div class="pt-2">
                                            <input type="range" x-model="weight" min="0" :max="Math.max(150, maxSafeWeight * 1.5)" step="1" 
                                                class="w-full h-1 bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-blue-500 hover:accent-blue-400 transition-all">
                                        </div>
                                    </div>

                                    <!-- Reps, RPE e Falha -->
                                    <div class="grid grid-cols-3 gap-3 col-span-2 md:col-span-1">
                                        <!-- Repetições -->
                                        <div class="space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-zinc-500 tracking-widest text-center">Reps</label>
                                            <input type="number" name="logs[{{ $index }}][sets][{{ $setIdx }}][reps]" 
                                                value="{{ $set->reps_target }}"
                                                class="w-full bg-zinc-900 border border-white/5 rounded-2xl p-4 text-center text-lg font-black text-white focus:border-blue-500/50 outline-none"
                                                placeholder="{{ $set->reps_target }}">
                                        </div>

                                        <!-- RPE -->
                                        <div class="space-y-1">
                                            <label class="block text-[9px] font-black uppercase text-zinc-500 tracking-widest text-center">RPE</label>
                                            <input type="number" min="1" max="10" name="logs[{{ $index }}][sets][{{ $setIdx }}][rpe]" 
                                                class="w-full bg-zinc-900 border border-white/5 rounded-2xl p-4 text-center text-lg font-black text-blue-400 focus:border-blue-500/50 outline-none"
                                                placeholder="-">
                                        </div>

                                        <!-- Falha -->
                                        <div class="space-y-1 flex flex-col items-center justify-center pt-5">
                                            <label class="cursor-pointer relative flex items-center group">
                                                <input type="checkbox" name="logs[{{ $index }}][sets][{{ $setIdx }}][failure]" value="1" class="peer sr-only">
                                                <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-white/5 peer-checked:bg-red-500/20 peer-checked:border-red-500/50 peer-checked:text-red-500 text-zinc-600 flex items-center justify-center transition-all shadow-inner">
                                                    <i class="fa-solid fa-skull"></i>
                                                </div>
                                                
                                                <!-- Tooltip -->
                                                <div class="absolute bottom-full mb-2 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-36 bg-zinc-800 text-white text-[9px] font-bold text-center p-2 rounded-lg shadow-xl border border-white/10 z-20 left-1/2 -translate-x-1/2">
                                                    Série executada até a falha muscular total
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-white/10"></div>
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[3px] border-transparent border-t-zinc-800" style="margin-top: -1px;"></div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="pt-10 pb-32 flex justify-center">
             <button type="submit" class="px-20 py-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black rounded-3xl shadow-3xl shadow-blue-600/40 transition-all active:scale-[0.98] uppercase tracking-[0.3em] text-sm">
                Finalizar Treino
             </button>
        </div>
    </form>
</div>

<script>
function setControl(initialWeight, maxSafeWeight) {
    return {
        weight: parseFloat(initialWeight) || 0,
        maxSafeWeight: parseFloat(maxSafeWeight) || 100,
        initialWeight: parseFloat(initialWeight) || 0,
        
        get isOverloaded() {
            return this.weight > this.maxSafeWeight;
        },
        
        get isProgressing() {
            return this.weight > this.initialWeight && !this.isOverloaded;
        },

        incrementWeight() {
            this.weight = parseFloat((this.weight + 1).toFixed(1));
        },

        decrementWeight() {
            if (this.weight >= 1) {
                this.weight = parseFloat((this.weight - 1).toFixed(1));
            }
        }
    }
}

function activeSession() {
    return {
        timerActive: false,
        timerFinished: false,
        timerEnding: false,
        timerSeconds: 0,
        totalSeconds: 1,
        interval: null,

        startTimer(seconds) {
            if (this.interval) clearInterval(this.interval);
            
            this.timerFinished = false;
            this.timerEnding = false;
            
            this.timerActive = true;
            this.totalSeconds = seconds;
            this.timerSeconds = seconds;
            
            // Feedback inicial
            if (navigator.vibrate) navigator.vibrate(50);

            this.interval = setInterval(() => {
                if (this.timerSeconds > 0) {
                    this.timerSeconds--;
                    
                    // Alerta últimos 3 segundos
                    if (this.timerSeconds <= 3 && this.timerSeconds > 0) {
                        this.timerEnding = true;
                        if (navigator.vibrate) navigator.vibrate(50);
                    }
                } else {
                    // Descanso finalizado
                    clearInterval(this.interval);
                    this.interval = null;
                    
                    this.timerEnding = false;
                    this.timerFinished = true;
                    
                    // Vibração premium e som
                    if (navigator.vibrate) navigator.vibrate([200, 100, 200, 100, 400]);
                    this.playElegantSound();
                }
            }, 1000);
        },

        stopTimer(manual = false) {
            if (this.interval) clearInterval(this.interval);
            this.interval = null;
            this.timerActive = false;
            this.timerFinished = false;
            this.timerEnding = false;
        },

        closeTimerModal() {
            this.timerActive = false;
            this.timerFinished = false;
        },

        addTime(seconds) {
            this.timerSeconds += seconds;
            this.totalSeconds += seconds;
            this.timerEnding = false;
        },

        playElegantSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                const playChime = (freq, startTime, duration) => {
                    const osc = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, audioCtx.currentTime + startTime);
                    
                    gainNode.gain.setValueAtTime(0, audioCtx.currentTime + startTime);
                    gainNode.gain.linearRampToValueAtTime(0.5, audioCtx.currentTime + startTime + 0.05);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + startTime + duration);
                    
                    osc.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    
                    osc.start(audioCtx.currentTime + startTime);
                    osc.stop(audioCtx.currentTime + startTime + duration);
                };

                // Premium success chord (Cmaj7)
                playChime(523.25, 0, 1.0);    // C5
                playChime(659.25, 0.1, 1.0);  // E5
                playChime(783.99, 0.2, 1.5);  // G5
                playChime(987.77, 0.3, 2.0);  // B5
            } catch (e) {
                console.log('Audio not supported', e);
            }
        }
    }
}
</script>

<style>
    body { background-color: #0b0e14 !important; }
    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
