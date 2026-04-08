@extends('layouts.app')

@section('title', 'Criar Novo Plano de Treino')

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto">
    
    <header class="pb-6 border-b border-white/5">
        <h1 class="text-3xl font-black text-white tracking-tight">Criar Plano de Treino</h1>
        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-2">Monte sua rotina selecionando exercícios e definindo metas estratégicas.</p>
    </header>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl relative">
        <form action="{{ route('progression.plans.store') }}" method="POST" id="planForm" class="space-y-10 relative z-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Select -->
                <div class="space-y-2">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Rótulo / Identificador</label>
                    <div class="relative">
                        <select name="plan_label" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none">
                            <option value="" class="bg-zinc-900 text-zinc-400">Sem rótulo...</option>
                            <option value="Treino A" class="bg-zinc-900 text-white">Treino A</option>
                            <option value="Treino B" class="bg-zinc-900 text-white">Treino B</option>
                            <option value="Treino C" class="bg-zinc-900 text-white">Treino C</option>
                            <option value="Treino D" class="bg-zinc-900 text-white">Treino D</option>
                            <option value="Treino E" class="bg-zinc-900 text-white">Treino E</option>
                        </select>
                        <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Name -->
                <div class="space-y-2 lg:col-span-1">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do Plano</label>
                    <input type="text" name="name" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Ex: Peito e Tríceps">
                </div>

                <!-- Goal -->
                <div class="space-y-2 lg:col-span-1">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Objetivo Foco</label>
                    <input type="text" name="goal" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Ex: Hipertrofia Absoluta">
                </div>

                <!-- Description -->
                <div class="space-y-2 md:col-span-2 lg:col-span-3">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Instruções ou Descrição</label>
                    <textarea name="description" rows="3" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all resize-none placeholder:text-zinc-700" placeholder="Anotações para seguir neste treino..."></textarea>
                </div>
            </div>

            <!-- Exercises Builder -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-px bg-white/10"></span>
                    <h2 class="text-sm font-black text-white uppercase tracking-[0.2em]">Montagem de Exercícios</h2>
                    <span class="flex-1 h-px bg-gradient-to-r from-white/10 to-transparent"></span>
                </div>

                <div id="exerciseList" class="space-y-4">
                    <!-- Dynamic Blocks Input -->
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
                            Consolidar Plano
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Custom Glass Modal (Vanilla JS) -->
<div id="exerciseModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <!-- Backdrop overlay -->
    <div onclick="closeExerciseModal()" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity opacity-0" id="exerciseModalBackdrop"></div>
    
    <!-- Modal Dialog -->
    <div class="relative bg-zinc-950 border border-white/10 rounded-[2.5rem] shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col transform scale-95 opacity-0 transition-all duration-300 pointer-events-auto" id="exerciseModalDialog">
        <div class="p-6 md:p-8 flex items-center justify-between border-b border-white/5 shrink-0">
            <div>
                <h3 class="text-xl font-black text-white">Catálogo de Movimentos</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Selecione para incluir na rotina</p>
            </div>
            <button type="button" onclick="closeExerciseModal()" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-900 text-zinc-500 hover:text-white hover:bg-zinc-800 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4 border-b border-white/5 shrink-0">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="exerciseSearch" class="w-full bg-zinc-900 border border-white/5 rounded-xl pl-10 pr-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-blue-500 transition-colors placeholder:text-zinc-600" placeholder="Procurar por nome...">
            </div>
        </div>

        <div class="overflow-y-auto p-4 md:p-8 space-y-3 custom-scrollbar flex-1">
            @foreach($catalog as $group => $exercises)
                <div class="border border-white/5 rounded-2xl overflow-hidden bg-zinc-900/30">
                    <button type="button" onclick="toggleAccordion('group-{{ Str::slug($group) }}', this)" class="w-full flex items-center justify-between p-4 bg-zinc-900/50 hover:bg-zinc-900 transition-colors text-left group">
                        <span class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-blue-500/50"></span>
                            {{ $group }}
                        </span>
                        <svg class="w-4 h-4 text-zinc-500 group-hover:text-white transition-transform duration-300 transform accordion-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <!-- Expandable content -->
                    <div id="group-{{ Str::slug($group) }}" class="hidden">
                        <div class="p-2 space-y-1 border-t border-white/5 bg-zinc-950/20">
                            @foreach($exercises as $exercise)
                                <button type="button" class="item-exercise w-full text-left p-3 rounded-xl hover:bg-blue-500/10 text-zinc-400 hover:text-white transition-all flex justify-between items-center group/btn" 
                                    data-id="{{ $exercise->id }}" data-name="{{ $exercise->name }}">
                                    <span class="font-bold text-sm">{{ $exercise->name }}</span>
                                    <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-zinc-900 border border-white/5 group-hover/btn:bg-blue-600 group-hover/btn:border-blue-500 group-hover/btn:text-white text-zinc-600 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

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
                Remover
            </button>
        </div>
        
        <input type="hidden" name="exercises[{IDX}][id]" value="{ID}">
        
        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3" id="setsContainer-{IDX}">
            <!-- Sets injected here -->
        </div>
        
        <div class="mt-4 pt-4 border-t border-white/5">
            <button type="button" class="btn-add-set text-xs text-blue-500 font-bold hover:text-blue-400 transition-colors flex items-center gap-2" data-ex-idx="{IDX}">
                <div class="w-5 h-5 rounded bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                </div>
                Adicionar Série Extra
            </button>
        </div>
    </div>
</template>

<template id="setRowTemplate">
    <div class="set-row flex items-center bg-zinc-950/80 border border-white/5 rounded-xl overflow-hidden shadow-inner focus-within:border-white/20 transition-colors">
        <span class="bg-zinc-900 border-r border-white/5 w-8 h-full flex flex-col justify-center items-center py-2 text-zinc-500 text-[9px] font-black uppercase shrink-0">
            S{SET}
        </span>
        <div class="flex-1 px-1">
            <input type="number" min="0" name="exercises[{IDX}][sets][{SET_IDX}][reps]" class="w-full bg-transparent border-0 text-white text-xs font-bold px-2 py-2.5 outline-none placeholder:text-zinc-700 text-center" placeholder="Reps">
        </div>
        <div class="w-px h-6 bg-white/5 shrink-0"></div>
        <div class="flex-1 px-1">
            <input type="number" min="0" name="exercises[{IDX}][sets][{SET_IDX}][weight]" class="w-full bg-transparent border-0 text-white text-xs font-bold px-2 py-2.5 outline-none placeholder:text-zinc-700 text-center" placeholder="Kg">
        </div>
    </div>
</template>

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    .animate-fade-left { animation: fadeLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes fadeLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
    }
    body { background-color: #0b0e14; }
</style>

<script>
    let exerciseCounter = 0;
    const exerciseList = document.getElementById('exerciseList');
    
    // Modal Functions
    const modalEl = document.getElementById('exerciseModal');
    const modalBackdrop = document.getElementById('exerciseModalBackdrop');
    const modalDialog = document.getElementById('exerciseModalDialog');

    function openExerciseModal() {
        modalEl.classList.remove('hidden');
        // Trigger reflow
        void modalEl.offsetWidth;
        modalBackdrop.classList.remove('opacity-0');
        modalBackdrop.classList.add('opacity-100');
        modalDialog.classList.remove('opacity-0', 'scale-95');
        modalDialog.classList.add('opacity-100', 'scale-100');
    }

    function closeExerciseModal() {
        modalBackdrop.classList.remove('opacity-100');
        modalBackdrop.classList.add('opacity-0');
        modalDialog.classList.remove('opacity-100', 'scale-100');
        modalDialog.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modalEl.classList.add('hidden');
        }, 300);
    }

    // Accordion UI Logic
    function toggleAccordion(contentId, btn) {
        const content = document.getElementById(contentId);
        const chevron = btn.querySelector('.accordion-chevron');
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Exercise Selection binding
        document.querySelectorAll('.item-exercise').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                addExercise(id, name);
                closeExerciseModal();
            });
        });

        function addExercise(id, name) {
            const idx = exerciseCounter++;
            let html = document.getElementById('exerciseRowTemplate').innerHTML;
            html = html.replace(/{ID}/g, id).replace(/{NAME}/g, name)
                       .replace(/{IDX}/g, idx).replace(/{IDX_PLUS}/g, idx + 1);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const node = tempDiv.firstElementChild;
            
            // Text replacement alternative as fallback if string replace missed
            const nameEl = node.querySelector('.ex-name-display');
            if(nameEl) nameEl.textContent = name;
            
            exerciseList.appendChild(node);
            
            // Add 1 default sets
            addSet(idx);
            addSet(idx);
            addSet(idx);

            node.querySelector('.btn-add-set').addEventListener('click', () => addSet(idx));
            node.querySelector('.btn-remove-ex').addEventListener('click', () => {
                node.style.opacity = '0';
                node.style.transform = 'translateY(10px) scale(0.98)';
                node.style.transition = 'all 0.3s ease';
                setTimeout(() => node.remove(), 300);
            });
        }

        function addSet(exIdx) {
            const container = document.getElementById('setsContainer-' + exIdx);
            const setIdx = container.querySelectorAll('.set-row').length;
            const setNum = setIdx + 1;
            
            let html = document.getElementById('setRowTemplate').innerHTML;
            html = html.replace(/{IDX}/g, exIdx).replace(/{SET_IDX}/g, setIdx).replace(/{SET}/g, setNum);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Small animate
            const newNode = tempDiv.firstElementChild;
            newNode.style.opacity = '0';
            newNode.style.transform = 'translateY(-5px)';
            newNode.style.transition = 'all 0.3s ease';
            
            container.appendChild(newNode);
            
            requestAnimationFrame(() => {
                newNode.style.opacity = '1';
                newNode.style.transform = 'translateY(0)';
            });
        }

        // Live Simple Search
        document.getElementById('exerciseSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.item-exercise').forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(term)) {
                    item.classList.remove('hidden');
                    item.classList.add('flex');
                } else {
                    item.classList.remove('flex');
                    item.classList.add('hidden');
                }
            });
            // Auto open accordions if filtering
            if(term.length > 2) {
                document.querySelectorAll('[id^="group-"]').forEach(grp => grp.classList.remove('hidden'));
                document.querySelectorAll('.accordion-chevron').forEach(icon => icon.classList.add('rotate-180'));
            }
        });
    });
</script>
@endsection
