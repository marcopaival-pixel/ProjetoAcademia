@extends('layouts.app')

@section('title', 'Editar Plano de Treino')

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto">
    
    <header class="pb-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Editar: {{ $plan->name }}</h1>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-2">Atualize sua rotina e metas estratégicas.</p>
        </div>
        @if(!Auth::user()->hasPremiumAccess())
            <div class="px-4 py-2 bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/30 rounded-2xl flex items-center gap-3 shadow-lg backdrop-blur-md animate-pulse">
                <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.15 14.1H3.85L12 5.45zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Funcionalidades Pro bloqueadas</p>
                    <p class="text-[9px] text-zinc-400 font-medium">Assine o plano Pro para liberar RPE e Cadência.</p>
                </div>
            </div>
        @endif
    </header>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl relative">
        <form action="{{ route('progression.plans.update', $plan) }}" method="POST" id="planForm" class="space-y-10 relative z-10">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Select Label -->
                <div class="space-y-2">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Rótulo / Identificador</label>
                    <div class="relative">
                        <select name="plan_label" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none">
                            <option value="" class="bg-zinc-900 text-zinc-400">Sem rótulo...</option>
                            @foreach(['Treino A', 'Treino B', 'Treino C', 'Treino D', 'Treino E'] as $label)
                                <option value="{{ $label }}" class="bg-zinc-900 text-white" {{ $plan->plan_label == $label ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Name -->
                <div class="space-y-2 lg:col-span-1">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do Plano</label>
                    <input type="text" name="name" value="{{ $plan->name }}" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Ex: Peito e Tríceps">
                </div>

                <!-- Goal -->
                <div class="space-y-2">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Objetivo Foco</label>
                    <input type="text" name="goal" value="{{ $plan->goal }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Ex: Hipertrofia Absoluta">
                </div>

                <!-- Frequency -->
                <div class="space-y-2">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Frequência Semanal</label>
                    <div class="relative">
                        <select name="frequency" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none">
                            @for($i=1; $i<=7; $i++)
                                <option value="{{ $i }}" {{ $plan->frequency == $i ? 'selected' : '' }}>{{ $i }}x na semana</option>
                            @endfor
                        </select>
                        <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Difficulty -->
                <div class="space-y-2">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nível de Dificuldade</label>
                    <div class="relative">
                        <select name="difficulty" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none">
                            @foreach(['Iniciante', 'Intermediário', 'Avançado', 'Elite'] as $diff)
                                <option value="{{ $diff }}" {{ $plan->difficulty == $diff ? 'selected' : '' }}>{{ $diff }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Duration -->
                <div class="space-y-2">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Duração Est. (min)</label>
                    <input type="number" name="estimated_duration" value="{{ $plan->estimated_duration }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Ex: 60">
                </div>

                <!-- Description -->
                <div class="space-y-2 md:col-span-2 lg:col-span-3">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Instruções ou Descrição</label>
                    <textarea name="description" rows="3" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all resize-none placeholder:text-zinc-700" placeholder="Anotações para seguir neste treino...">{{ $plan->description }}</textarea>
                </div>
            </div>

            <!-- Exercises Builder -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-px bg-white/10"></span>
                    <h2 class="text-sm font-black text-white uppercase tracking-[0.2em]">Sua Estrutura de Treino</h2>
                    <span class="flex-1 h-px bg-gradient-to-r from-white/10 to-transparent"></span>
                </div>

                <div id="exerciseList" class="space-y-4">
                    <!-- Dynamic Blocks populated by JS -->
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-6 mt-6 border-t border-white/5">
                    <button type="button" onclick="openExerciseModal()" class="w-full sm:w-auto px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-2xl border border-white/5 transition-all text-xs flex items-center justify-center gap-3 shadow-xl group">
                        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-zinc-950 border border-white/10 group-hover:scale-110 transition-transform">
                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        Adicionar Exercício
                    </button>
                    
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <a href="{{ route('progression.plans.index') }}" class="px-6 py-4 text-xs font-bold text-zinc-500 hover:text-white uppercase tracking-widest transition-colors">Cancelar</a>
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-[0.98] text-[10px] uppercase tracking-[0.2em]">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal logic (same as create) -->
@include('progression.partials.exercise-modal')

<!-- TEMPLATES -->
<template id="exerciseRowTemplate">
    <div class="bg-zinc-900/50 border border-white/5 rounded-3xl p-6 shadow-xl relative animate-fade-left mb-4 group/box transition-colors hover:border-white/10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 text-sm font-black shadow-inner header-num-icon">
                    {IDX_PLUS}
                </div>
                <h6 class="text-white font-black text-lg ex-name-display">{NAME}</h6>
            </div>
            <button type="button" class="btn-remove-ex flex items-center gap-2 px-3 py-2 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-colors text-[10px] uppercase font-black tracking-widest shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Remover Exercício
            </button>
        </div>
        
        <input type="hidden" name="exercises[{IDX}][id]" value="{ID}">
        
        <!-- Column Headers for Beginners -->
        <div class="grid grid-cols-12 gap-1 mb-2 px-1">
            <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-tighter">Série</div>
            <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-tighter">Repetições</div>
            <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-tighter">Carga (kg)</div>
            <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-tighter">Descanso</div>
            <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-tighter">Esforço (1-10)</div>
            <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-tighter">Velocidade</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-1 gap-1" id="setsContainer-{IDX}">
            <!-- Sets injected here -->
        </div>
        
        <div class="mt-4 pt-4 border-t border-white/5 flex justify-between items-center">
            <button type="button" class="btn-add-set text-xs text-blue-500 font-bold hover:text-blue-400 transition-colors flex items-center gap-2" data-ex-idx="{IDX}">
                <div class="w-5 h-5 rounded bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                </div>
                Adicionar mais uma série
            </button>
            <p class="text-[9px] text-zinc-600 font-bold uppercase italic">* Velocidade: ex 3010 (3s descida, 0s pausa, 1s subida)</p>
        </div>
    </div>
</template>

<template id="setRowTemplate">
    <div class="set-row grid grid-cols-12 gap-1 bg-zinc-950/80 border border-white/5 rounded-xl overflow-hidden shadow-inner focus-within:border-white/20 transition-colors mb-2">
        <div class="col-span-2 bg-zinc-900 border-r border-white/5 flex items-center justify-center relative">
            <select name="exercises[{IDX}][sets][{SET_IDX}][type]" class="bg-transparent border-0 text-[10px] text-zinc-400 font-black uppercase w-full h-full outline-none appearance-none text-center cursor-pointer hover:text-white transition-colors"
                    {{ !Auth::user()->hasPremiumAccess() ? 'data-premium-only' : '' }}>
                <option value="work">{SET}ª Série</option>
                @if(Auth::user()->hasPremiumAccess())
                    <option value="warmup">Aquecim.</option>
                    <option value="drop">Drop Set</option>
                    <option value="failure">Até Falha</option>
                    <option value="rest-pause">Rest-Pause</option>
                @else
                    <option disabled>💎 PRO</option>
                @endif
            </select>
        </div>
        <div class="col-span-2 flex items-center border-r border-white/5">
            <input type="number" min="0" name="exercises[{IDX}][sets][{SET_IDX}][reps]" class="w-full bg-transparent border-0 text-white text-[11px] font-bold px-1 py-2.5 outline-none placeholder:text-zinc-700 text-center input-reps" placeholder="Vezes">
        </div>
        <div class="col-span-2 flex items-center border-r border-white/5">
            <input type="number" min="0" step="0.5" name="exercises[{IDX}][sets][{SET_IDX}][weight]" class="w-full bg-transparent border-0 text-white text-[11px] font-bold px-1 py-2.5 outline-none placeholder:text-zinc-700 text-center input-weight" placeholder="Kg">
        </div>
        <div class="col-span-2 flex items-center border-r border-white/5">
            <input type="number" min="0" name="exercises[{IDX}][sets][{SET_IDX}][rest]" class="w-full bg-transparent border-0 text-zinc-500 text-[11px] font-bold px-1 py-2.5 outline-none placeholder:text-zinc-700 text-center input-rest" placeholder="Seg" value="60">
        </div>
        <div class="col-span-2 flex items-center border-r border-white/5 relative">
            <input type="number" min="1" max="10" name="exercises[{IDX}][sets][{SET_IDX}][rpe]" 
                   class="w-full bg-transparent border-0 text-blue-400 text-[11px] font-bold px-1 py-2.5 outline-none placeholder:text-zinc-800 text-center input-rpe {{ !Auth::user()->hasPremiumAccess() ? 'opacity-20 cursor-not-allowed' : '' }}" 
                   placeholder="1-10" 
                   {{ !Auth::user()->hasPremiumAccess() ? 'disabled' : '' }}>
        </div>
        <div class="col-span-2 flex items-center relative text-center">
            <input type="text" name="exercises[{IDX}][sets][{SET_IDX}][cadence]" 
                   class="w-full bg-transparent border-0 text-zinc-500 font-bold px-1 py-2.5 outline-none placeholder:text-zinc-800 text-[9px] text-center input-cadence {{ !Auth::user()->hasPremiumAccess() ? 'opacity-20 cursor-not-allowed' : '' }}" 
                   placeholder="Ritmo"
                   {{ !Auth::user()->hasPremiumAccess() ? 'disabled' : '' }}>
        </div>
    </div>
</template>

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    .animate-fade-left { animation: fadeLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
    body { background-color: #0b0e14; }
</style>

<script>
    let exerciseCounter = 0;
    const exerciseList = document.getElementById('exerciseList');
    
    document.addEventListener('DOMContentLoaded', function() {
        // Hydrate from existing data
        const initialData = @json($plan->exercises->map(function($ex) {
            return [
                'id' => $ex->exercise_id,
                'name' => $ex->catalogExercise->name,
                'sets' => $ex->sets
            ];
        }));

        initialData.forEach(exData => {
            const idx = addExercise(exData.id, exData.name);
            exData.sets.forEach((setData, sidx) => {
                if (sidx >= 3) { // addExercise adds 3 sets by default in create, but let's adjust it
                     // We'll clear the default sets if we want precise hydration
                }
            });
            // Better: clear default sets or make addExercise not add defaults if we pass data
        });

        // Binding modal selection (Event-driven)
        window.addEventListener('add-exercise', (e) => {
            addExercise(e.detail.id, e.detail.name);
        });

        function addExercise(id, name, setsData = null) {
            const idx = exerciseCounter++;
            let html = document.getElementById('exerciseRowTemplate').innerHTML;
            html = html.replace(/{ID}/g, id).replace(/{NAME}/g, name)
                       .replace(/{IDX}/g, idx).replace(/{IDX_PLUS}/g, idx + 1);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const node = tempDiv.firstElementChild;
            exerciseList.appendChild(node);
            
            node.querySelector('.btn-add-set').addEventListener('click', () => addSet(idx));
            node.querySelector('.btn-remove-ex').addEventListener('click', () => {
                node.style.opacity = '0';
                setTimeout(() => node.remove(), 300);
            });

            return idx;
        }

        function addSet(exIdx, data = null) {
            const container = document.getElementById('setsContainer-' + exIdx);
            const setIdx = container.querySelectorAll('.set-row').length;
            let html = document.getElementById('setRowTemplate').innerHTML;
            html = html.replace(/{IDX}/g, exIdx).replace(/{SET_IDX}/g, setIdx).replace(/{SET}/g, setIdx + 1);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newNode = tempDiv.firstElementChild;
            container.appendChild(newNode);

            if (data) {
                newNode.querySelector('.input-reps').value = data.reps_target;
                newNode.querySelector('.input-weight').value = data.weight_target;
                newNode.querySelector('.input-rest').value = data.rest_seconds;
                if(newNode.querySelector('.input-rpe')) newNode.querySelector('.input-rpe').value = data.rpe_target;
                if(newNode.querySelector('.input-cadence')) newNode.querySelector('.input-cadence').value = data.cadence;
                newNode.querySelector('select').value = data.set_type;
            }
        }

        // Precise Hydration
        exerciseList.innerHTML = '';
        initialData.forEach(exData => {
            const exIdx = addExercise(exData.id, exData.name);
            // Clear default sets if added by addExercise (not added above for simplicity)
            const container = document.getElementById('setsContainer-' + exIdx);
            container.innerHTML = ''; 
            exData.sets.forEach(setData => addSet(exIdx, setData));
        });
    });
</script>
@endsection
