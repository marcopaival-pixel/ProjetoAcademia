@extends('layouts.app')

@section('title', 'Sessão Ativa: ' . $plan->name)

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto" x-data="activeSession()">
    
    <!-- Timer de Descanso SaaS Elite -->
    <div class="fixed bottom-8 right-8 z-[100] group" x-show="timerActive" x-transition>
        <div class="bg-zinc-900 border-2 border-emerald-500/50 p-6 rounded-[2.5rem] shadow-2xl backdrop-blur-3xl flex items-center gap-6">
            <div class="text-center">
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Recuperação / Descanso</p>
                <h3 class="text-4xl font-black text-white italic tracking-tighter tabular-nums" x-text="formatTime()">00s</h3>
            </div>
            <button @click="stopTimer()" class="w-12 h-12 rounded-2xl bg-zinc-800 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-lg">
                <i class="fa-solid fa-stop text-sm"></i>
            </button>
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

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] border-b border-white/5">
                                <th class="pb-6 w-16">Tipo</th>
                                <th class="pb-6">Meta Sugerida</th>
                                <th class="pb-6 text-center">Carga (kg)</th>
                                <th class="pb-6 text-center">Reps</th>
                                <th class="pb-6 text-center">RPE</th>
                                <th class="pb-6 text-center"><i class="fa-solid fa-skull text-zinc-800"></i></th>
                                <th class="pb-6 text-right">Descanso</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <input type="hidden" name="logs[{{ $index }}][training_plan_exercise_id]" value="{{ $exercise->id }}">
                            <input type="hidden" name="logs[{{ $index }}][exercise_id]" value="{{ $exercise->exercise_id }}">

                            @foreach($exercise->sets as $setIdx => $set)
                                @php
                                    $isPremium = auth()->user()->hasPremiumAccess();
                                    $suggestedWeight = ($isPremium && isset($exercise->suggestion['suggested_weight'])) 
                                        ? $exercise->suggestion['suggested_weight'] 
                                        : ($exercise->last_log->weight_kg ?? ($set->weight_target ?? 0));
                                    
                                    $typeLabel = match($set->set_type) {
                                        'warmup' => 'WUP',
                                        'drop' => 'DROP',
                                        'failure' => 'FAIL',
                                        default => 'SET ' . ($setIdx + 1)
                                    };
                                @endphp
                                <tr class="group hover:bg-white/[0.02] transition-colors">
                                    <td class="py-6">
                                        <span class="px-2 py-1 bg-zinc-950 border border-white/10 rounded-lg text-[9px] font-black text-zinc-400 group-hover:text-white transition-colors">
                                            {{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td class="py-6">
                                        <p class="text-xs font-black text-blue-400 italic">{{ $suggestedWeight }}kg <span class="text-zinc-600 not-italic">x</span> {{ $set->reps_target }}</p>
                                        @if($set->cadence)
                                            <p class="text-[9px] text-zinc-600 font-bold uppercase mt-1">{{ $set->cadence }}</p>
                                        @endif
                                    </td>
                                    <td class="py-6 px-2">
                                        <input type="number" step="0.5" name="logs[{{ $index }}][sets][{{ $setIdx }}][weight]" 
                                            class="w-20 mx-auto bg-zinc-950 border border-white/5 rounded-xl p-3 text-center text-sm font-black text-white focus:border-blue-500/50 outline-none"
                                            value="{{ $suggestedWeight }}">
                                    </td>
                                    <td class="py-6 px-2">
                                        <input type="number" name="logs[{{ $index }}][sets][{{ $setIdx }}][reps]" 
                                            class="w-20 mx-auto bg-zinc-950 border border-white/5 rounded-xl p-3 text-center text-sm font-black text-white focus:border-blue-500/50 outline-none"
                                            placeholder="{{ $set->reps_target }}">
                                    </td>
                                    <td class="py-6 px-2">
                                        <input type="number" min="1" max="10" name="logs[{{ $index }}][sets][{{ $setIdx }}][rpe]" 
                                            class="w-16 mx-auto bg-zinc-950 border border-white/5 rounded-xl p-3 text-center text-[10px] font-black text-zinc-400 focus:border-blue-500/50 outline-none"
                                            placeholder="RPE">
                                    </td>
                                    <td class="py-6 text-center">
                                        <input type="checkbox" name="logs[{{ $index }}][sets][{{ $setIdx }}][failure]" value="1" class="w-4 h-4 bg-zinc-950 border-white/5 rounded text-red-600 focus:ring-0">
                                    </td>
                                    <td class="py-6 text-right">
                                        <button type="button" @click="startTimer({{ $set->rest_seconds }})" 
                                            class="px-4 py-2 bg-zinc-950 border border-white/5 text-zinc-500 hover:text-white hover:bg-zinc-800 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                            {{ $set->rest_seconds }}s <i class="fa-regular fa-clock ml-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="pt-10 flex justify-center">
             <button type="submit" class="px-20 py-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black rounded-3xl shadow-3xl shadow-blue-600/40 transition-all active:scale-[0.98] uppercase tracking-[0.3em] text-sm">
                Finalizar Treino
             </button>
        </div>
    </form>
</div>

<script>
function activeSession() {
    return {
        timerActive: false,
        timerSeconds: 0,
        interval: null,

        startTimer(seconds) {
            if (this.interval) clearInterval(this.interval);
            this.timerActive = true;
            this.timerSeconds = seconds;
            
            // Notificação tátil
            if (navigator.vibrate) navigator.vibrate(100);

            this.interval = setInterval(() => {
                if (this.timerSeconds > 0) {
                    this.timerSeconds--;
                } else {
                    this.stopTimer();
                    if (navigator.vibrate) navigator.vibrate([200, 100, 200]);
                    alert('Descanso finalizado! Próxima série.');
                }
            }, 1000);
        },

        stopTimer() {
            clearInterval(this.interval);
            this.interval = null;
            this.timerActive = false;
        },

        formatTime() {
            return this.timerSeconds + 's';
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
