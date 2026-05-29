<!-- Custom Glass Modal (Vanilla JS + Alpine-friendly) -->
<div id="exerciseModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <!-- Backdrop overlay -->
    <div onclick="closeExerciseModal()" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity opacity-0" id="exerciseModalBackdrop"></div>
    
    <!-- Modal Dialog -->
    <div class="relative bg-zinc-950 border border-white/10 rounded-[2.5rem] shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col transform scale-95 opacity-0 transition-all duration-300 pointer-events-auto" id="exerciseModalDialog">
        <div class="p-6 md:p-8 flex items-center justify-between border-b border-white/5 shrink-0">
            <div>
                <h3 class="text-xl font-black text-white">Biblioteca de Exercícios</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Busque e selecione os movimentos para seu treino</p>
            </div>
            <button type="button" onclick="closeExerciseModal()" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-900 text-zinc-500 hover:text-white hover:bg-zinc-800 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Filtros Avançados --}}
        <div class="p-6 border-b border-white/5 bg-zinc-900/20 shrink-0 space-y-4">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500"></i>
                <input type="text" id="exerciseSearch" class="w-full bg-zinc-950 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-zinc-700" placeholder="Buscar exercício por nome...">
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <select id="filterMuscle" class="bg-zinc-950 border border-white/5 rounded-xl px-4 py-2 text-[10px] font-black text-zinc-500 uppercase tracking-widest outline-none focus:border-blue-500 transition-colors cursor-pointer">
                    <option value="">Grupo Muscular</option>
                    @foreach($catalog->keys() as $group)
                        <option value="{{ $group }}">{{ $group }}</option>
                    @endforeach
                </select>

                <select id="filterEquipment" class="bg-zinc-950 border border-white/5 rounded-xl px-4 py-2 text-[10px] font-black text-zinc-500 uppercase tracking-widest outline-none focus:border-blue-500 transition-colors cursor-pointer">
                    <option value="">Equipamento</option>
                    <option value="Halter">Halter</option>
                    <option value="Barra">Barra</option>
                    <option value="Máquina">Máquina</option>
                    <option value="Polia">Polia</option>
                    <option value="Peso Corporal">Peso Corporal</option>
                </select>

                <select id="filterDifficulty" class="bg-zinc-950 border border-white/5 rounded-xl px-4 py-2 text-[10px] font-black text-zinc-500 uppercase tracking-widest outline-none focus:border-blue-500 transition-colors cursor-pointer">
                    <option value="">Nível</option>
                    <option value="Iniciante">Iniciante</option>
                    <option value="Intermediário">Intermediário</option>
                    <option value="Avançado">Avançado</option>
                </select>

                <div class="flex items-center gap-2 px-4 py-2 bg-blue-600/10 border border-blue-500/20 rounded-xl cursor-not-allowed opacity-50" title="Recurso IA em breve">
                    <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">Sugestão IA</span>
                </div>
            </div>
        </div>

        <div class="overflow-y-auto p-6 space-y-8 custom-scrollbar flex-1 relative">
            
            @if(count($catalog) === 0)
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center text-red-500 mb-4">
                        <i class="fas fa-database text-2xl"></i>
                    </div>
                    <h4 class="text-white font-bold mb-1">Biblioteca Vazia</h4>
                    <p class="text-xs text-zinc-500 max-w-xs">Não há exercícios cadastrados no sistema. O administrador precisa cadastrar os exercícios primeiro.</p>
                </div>
            @else
                <div id="emptyState" class="hidden flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-600 mb-4">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h4 class="text-white font-bold mb-1">Nenhum exercício encontrado</h4>
                    <p class="text-xs text-zinc-500 max-w-xs">Não encontramos resultados para os filtros selecionados.</p>
                </div>

                @php
                    $targetNames = [];
                    // Mapeamento dos nomes do corpo (frontend) para os do banco de dados
                    $aliasMap = [
                        'Peito' => ['Peitoral', 'Peitoral Maior', 'Peitoral Superior'],
                        'Deltoides' => ['Ombros', 'Deltoide Anterior', 'Deltoide Lateral', 'Deltoide Posterior'],
                        'Bíceps' => ['Braços', 'Bíceps Braquial', 'Braquial'],
                        'Tríceps' => ['Braços', 'Tríceps Braquial'],
                        'Antebraço' => ['Antebraços'],
                        'Abdômen' => ['Abdômen', 'Core', 'Reto Abdominal'],
                        'Oblíquos' => ['Abdômen', 'Core', 'Oblíquos'],
                        'Quadríceps' => ['Quadríceps'],
                        'Posterior' => ['Posterior de Coxa', 'Isquiotibiais'],
                        'Panturrilha' => ['Panturrilhas'],
                        'Latíssimo' => ['Costas', 'Latíssimo do Dorso'],
                        'Trapézio' => ['Costas', 'Trapézio', 'Costas Superior'],
                        'Lombar' => ['Lombar', 'Costas Inferior'],
                        'Glúteos' => ['Glúteos']
                    ];

                    if (!empty($selectedTargets)) {
                        foreach ($selectedTargets as $t) {
                            $name = is_array($t) ? ($t['name'] ?? '') : $t;
                            $targetNames[] = $name;
                            if (isset($aliasMap[$name])) {
                                $targetNames = array_merge($targetNames, $aliasMap[$name]);
                            }
                        }
                    }
                    
                    $recommendedExercises = collect();
                    if (!empty($targetNames)) {
                        foreach ($catalog as $group => $exercises) {
                            foreach ($exercises as $ex) {
                                $exMuscles = $ex->muscles->pluck('name')->toArray();
                                if (count(array_intersect($exMuscles, $targetNames)) > 0 || in_array($group, $targetNames)) {
                                    $recommendedExercises->push($ex);
                                }
                            }
                        }
                        $recommendedExercises = $recommendedExercises->unique('id');
                    }
                @endphp

                @if($recommendedExercises->isNotEmpty())
                    <div class="space-y-4 accordion-group" data-group="Recomendados">
                        <h4 class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] flex items-center gap-3">
                            <i class="fas fa-fire text-orange-500"></i>
                            Recomendados para o Foco de Hoje
                            <span class="text-[8px] text-orange-500/70">({{ count($recommendedExercises) }} movimentos)</span>
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($recommendedExercises as $exercise)
                                <button type="button" 
                                        onclick="toggleExerciseSelection(this)"
                                        class="item-exercise text-left p-4 rounded-2xl bg-orange-500/10 border border-orange-500/20 hover:border-orange-500/40 hover:bg-orange-500/20 transition-all flex justify-between items-center group/btn" 
                                        data-id="{{ $exercise->id }}" 
                                        data-name="{{ $exercise->name }}"
                                        data-group="Recomendados"
                                        data-equipment="{{ $exercise->equipment }}"
                                        data-difficulty="{{ $exercise->difficulty }}"
                                        data-muscles="{{ $exercise->muscles->pluck('name')->implode(', ') }}">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center text-orange-500 group-hover/btn:text-orange-400 transition-colors icon-wrapper">
                                            <i class="fas fa-dumbbell text-sm"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm text-orange-100 group-hover/btn:text-white transition-colors exercise-name">{{ $exercise->name }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[8px] text-orange-500/80 uppercase font-black tracking-tighter">{{ $exercise->equipment ?? 'Sem eq.' }}</span>
                                                <span class="w-1 h-1 rounded-full bg-orange-500/30"></span>
                                                <span class="text-[8px] text-orange-500/80 uppercase font-black tracking-tighter">{{ $exercise->difficulty ?? 'Geral' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="action-btn w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-950 border border-white/5 group-hover/btn:bg-blue-600 group-hover/btn:border-blue-500 group-hover/btn:text-white text-zinc-600 transition-all shadow-lg">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="w-full h-px bg-white/5 my-6 accordion-group" data-group="Recomendados"></div>
                @endif

                @foreach($catalog as $group => $exercises)
                    <div class="space-y-4 accordion-group" data-group="{{ $group }}">
                        <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(37,99,235,0.5)]"></span>
                            {{ $group }}
                            <span class="text-[8px] text-zinc-700">({{ count($exercises) }} movimentos)</span>
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($exercises as $exercise)
                                <button type="button" 
                                        onclick="toggleExerciseSelection(this)"
                                        class="item-exercise text-left p-4 rounded-2xl bg-zinc-900/40 border border-white/5 hover:border-blue-500/40 hover:bg-zinc-900 transition-all flex justify-between items-center group/btn" 
                                        data-id="{{ $exercise->id }}" 
                                        data-name="{{ $exercise->name }}"
                                        data-group="{{ $group }}"
                                        data-equipment="{{ $exercise->equipment }}"
                                        data-difficulty="{{ $exercise->difficulty }}"
                                        data-muscles="{{ $exercise->muscles->pluck('name')->implode(', ') }}">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-600 group-hover/btn:text-blue-500 transition-colors icon-wrapper">
                                            <i class="fas fa-dumbbell text-sm"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm text-white group-hover/btn:text-blue-400 transition-colors exercise-name">{{ $exercise->name }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[8px] text-zinc-600 uppercase font-black tracking-tighter">{{ $exercise->equipment ?? 'Sem eq.' }}</span>
                                                <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                                                <span class="text-[8px] text-zinc-600 uppercase font-black tracking-tighter">{{ $exercise->difficulty ?? 'Geral' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="action-btn w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-950 border border-white/5 group-hover/btn:bg-blue-600 group-hover/btn:border-blue-500 group-hover/btn:text-white text-zinc-600 transition-all shadow-lg">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Rodapé de Ação --}}
        <div class="p-6 border-t border-white/5 bg-zinc-950 rounded-b-[2.5rem] flex items-center justify-between shrink-0">
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">
                <span id="selectedCount" class="text-white text-sm">0</span> selecionados
            </p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeExerciseModal()" class="px-6 py-3 text-zinc-500 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors">Cancelar</button>
                <button type="button" onclick="addSelectedExercises()" id="addBtn" disabled class="px-8 py-3 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20">
                    Adicionar ao Plano
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Functions
    const modalEl = document.getElementById('exerciseModal');
    const modalBackdrop = document.getElementById('exerciseModalBackdrop');
    const modalDialog = document.getElementById('exerciseModalDialog');
    let selectedExercisesData = new Map(); // Store exercise objects

    function openExerciseModal() {
        modalEl.classList.remove('hidden');
        void modalEl.offsetWidth;
        modalBackdrop.classList.remove('opacity-0');
        modalBackdrop.classList.add('opacity-100');
        modalDialog.classList.remove('opacity-0', 'scale-95');
        modalDialog.classList.add('opacity-100', 'scale-100');
        
        // Reset selections upon open
        selectedExercisesData.clear();
        updateSelectedUI();
        
        // Clear visually
        document.querySelectorAll('.item-exercise').forEach(el => {
            el.classList.remove('border-blue-500', 'bg-blue-900/20');
            el.classList.add('border-white/5', 'bg-zinc-900/40');
            
            const btn = el.querySelector('.action-btn');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-plus text-[10px]"></i>';
                btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                btn.classList.add('bg-zinc-950', 'text-zinc-600');
            }
            
            const iconWrap = el.querySelector('.icon-wrapper');
            if (iconWrap) {
                iconWrap.classList.remove('text-blue-400');
                iconWrap.classList.add('text-zinc-600');
            }
            
            const nameEl = el.querySelector('.exercise-name');
            if (nameEl) {
                nameEl.classList.remove('text-blue-400');
                nameEl.classList.add('text-white');
            }
        });
    }

    function closeExerciseModal() {
        if(!modalBackdrop) return;
        modalBackdrop.classList.remove('opacity-100');
        modalBackdrop.classList.add('opacity-0');
        modalDialog.classList.remove('opacity-100', 'scale-100');
        modalDialog.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modalEl.classList.add('hidden');
        }, 300);
    }

    // Filter Logic
    const searchInput = document.getElementById('exerciseSearch');
    const muscleFilter = document.getElementById('filterMuscle');
    const equipmentFilter = document.getElementById('filterEquipment');
    const difficultyFilter = document.getElementById('filterDifficulty');
    const emptyState = document.getElementById('emptyState');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const muscleTerm = muscleFilter.value;
        const equipmentTerm = equipmentFilter.value;
        const difficultyTerm = difficultyFilter.value;

        let totalVisible = 0;

        document.querySelectorAll('.item-exercise').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const group = item.dataset.group;
            const equipment = item.dataset.equipment || '';
            const difficulty = item.dataset.difficulty || '';

            const matchesSearch = name.includes(searchTerm);
            const matchesMuscle = !muscleTerm || group === muscleTerm;
            const matchesEquipment = !equipmentTerm || equipment === equipmentTerm;
            const matchesDifficulty = !difficultyTerm || difficulty === difficultyTerm;

            if (matchesSearch && matchesMuscle && matchesEquipment && matchesDifficulty) {
                item.classList.remove('hidden');
                item.classList.add('flex');
                totalVisible++;
            } else {
                item.classList.remove('flex');
                item.classList.add('hidden');
            }
        });

        // Hide empty groups
        document.querySelectorAll('.accordion-group').forEach(groupEl => {
            const hasVisible = groupEl.querySelectorAll('.item-exercise:not(.hidden)').length > 0;
            groupEl.style.display = hasVisible ? 'block' : 'none';
        });

        if (emptyState) {
            if (totalVisible === 0 && document.querySelectorAll('.item-exercise').length > 0) {
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
            } else {
                emptyState.classList.add('hidden');
                emptyState.classList.remove('flex');
            }
        }
    }

    [searchInput, muscleFilter, equipmentFilter, difficultyFilter].forEach(el => {
        el?.addEventListener('input', applyFilters);
    });

    function toggleExerciseSelection(btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const muscles = btn.dataset.muscles;
        
        if (selectedExercisesData.has(id)) {
            // Deselect
            selectedExercisesData.delete(id);
            btn.classList.remove('border-blue-500', 'bg-blue-900/20');
            btn.classList.add('border-white/5', 'bg-zinc-900/40');
            
            const actionBtn = btn.querySelector('.action-btn');
            actionBtn.innerHTML = '<i class="fas fa-plus text-[10px]"></i>';
            actionBtn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
            actionBtn.classList.add('bg-zinc-950', 'text-zinc-600');
            
            btn.querySelector('.icon-wrapper').classList.remove('text-blue-400');
            btn.querySelector('.icon-wrapper').classList.add('text-zinc-600');
            
            btn.querySelector('.exercise-name').classList.remove('text-blue-400');
            btn.querySelector('.exercise-name').classList.add('text-white');
        } else {
            // Select
            selectedExercisesData.set(id, { id, name, muscles });
            btn.classList.remove('border-white/5', 'bg-zinc-900/40');
            btn.classList.add('border-blue-500', 'bg-blue-900/20');
            
            const actionBtn = btn.querySelector('.action-btn');
            actionBtn.innerHTML = '<i class="fas fa-check text-[10px]"></i>';
            actionBtn.classList.remove('bg-zinc-950', 'text-zinc-600');
            actionBtn.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
            
            btn.querySelector('.icon-wrapper').classList.remove('text-zinc-600');
            btn.querySelector('.icon-wrapper').classList.add('text-blue-400');
            
            btn.querySelector('.exercise-name').classList.remove('text-white');
            btn.querySelector('.exercise-name').classList.add('text-blue-400');
        }
        
        updateSelectedUI();
    }
    
    function updateSelectedUI() {
        const count = selectedExercisesData.size;
        document.getElementById('selectedCount').innerText = count;
        
        const addBtn = document.getElementById('addBtn');
        if (count > 0) {
            addBtn.removeAttribute('disabled');
            addBtn.innerHTML = count === 1 ? 'Adicionar 1 Exercício' : `Adicionar ${count} Exercícios`;
        } else {
            addBtn.setAttribute('disabled', 'true');
            addBtn.innerHTML = 'Adicionar ao Plano';
        }
    }

    function addSelectedExercises() {
        if (selectedExercisesData.size === 0) return;

        const addBtn = document.getElementById('addBtn');
        addBtn.setAttribute('disabled', 'true');
        addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adicionando...';
        
        selectedExercisesData.forEach((data, id) => {
            window.dispatchEvent(new CustomEvent('add-exercise', {
                detail: { id: data.id, name: data.name, muscles: data.muscles }
            }));
        });
        
        selectedExercisesData.clear();
        closeExerciseModal();
    }
</script>
