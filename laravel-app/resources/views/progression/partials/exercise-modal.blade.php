<!-- Custom Glass Modal (Vanilla JS + Alpine-friendly) -->
<div id="exerciseModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <!-- Backdrop overlay -->
    <div onclick="closeExerciseModal()" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity opacity-0" id="exerciseModalBackdrop"></div>
    
    <!-- Modal Dialog -->
    <div class="relative bg-zinc-950 border border-white/10 rounded-[2.5rem] shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col transform scale-95 opacity-0 transition-all duration-300 pointer-events-auto" id="exerciseModalDialog">
        <div class="p-6 md:p-8 flex items-center justify-between border-b border-white/5 shrink-0">
            <div>
                <h3 class="text-xl font-black text-white">Adicionar Exercício Inteligente</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Busca avançada e filtros por biotipo</p>
            </div>
            <button type="button" onclick="closeExerciseModal()" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-900 text-zinc-500 hover:text-white hover:bg-zinc-800 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Filtros Avançados (Improvement 6) --}}
        <div class="p-6 border-b border-white/5 bg-zinc-900/20 shrink-0 space-y-4">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
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

                <div class="flex items-center gap-2 px-4 py-2 bg-blue-600/10 border border-blue-500/20 rounded-xl">
                    <i class="fas fa-magic text-blue-500 text-[10px]"></i>
                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">Sugestão IA</span>
                </div>
            </div>
        </div>

        <div class="overflow-y-auto p-6 space-y-8 custom-scrollbar flex-1">
            {{-- Seção Recentes (Placeholder) --}}
            <div class="space-y-4">
                <h4 class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.3em] flex items-center gap-2">
                    <i class="fas fa-history text-[8px]"></i>
                    Movimentos Frequentes
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="recentExercises">
                    {{-- Populado via JS ou Blade se houver histórico --}}
                </div>
            </div>

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
                                    onclick="selectExercise(this)"
                                    class="item-exercise text-left p-4 rounded-2xl bg-zinc-900/40 border border-white/5 hover:border-blue-500/40 hover:bg-zinc-900 transition-all flex justify-between items-center group/btn" 
                                    data-id="{{ $exercise->id }}" 
                                    data-name="{{ $exercise->name }}"
                                    data-group="{{ $group }}"
                                    data-equipment="{{ $exercise->equipment }}"
                                    data-difficulty="{{ $exercise->difficulty }}"
                                    data-muscles="{{ $exercise->muscles->pluck('name')->implode(', ') }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-600 group-hover/btn:text-blue-500 transition-colors">
                                        <i class="fas fa-dumbbell text-sm"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-white group-hover/btn:text-blue-400 transition-colors">{{ $exercise->name }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[8px] text-zinc-600 uppercase font-black tracking-tighter">{{ $exercise->equipment ?? 'Sem eq.' }}</span>
                                            <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                                            <span class="text-[8px] text-zinc-600 uppercase font-black tracking-tighter">{{ $exercise->difficulty ?? 'Geral' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-950 border border-white/5 group-hover/btn:bg-blue-600 group-hover/btn:border-blue-500 group-hover/btn:text-white text-zinc-600 transition-all shadow-lg">
                                    <i class="fas fa-plus text-[10px]"></i>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    // Modal Functions
    const modalEl = document.getElementById('exerciseModal');
    const modalBackdrop = document.getElementById('exerciseModalBackdrop');
    const modalDialog = document.getElementById('exerciseModalDialog');

    function openExerciseModal() {
        modalEl.classList.remove('hidden');
        void modalEl.offsetWidth;
        modalBackdrop.classList.remove('opacity-0');
        modalBackdrop.classList.add('opacity-100');
        modalDialog.classList.remove('opacity-0', 'scale-95');
        modalDialog.classList.add('opacity-100', 'scale-100');
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

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const muscleTerm = muscleFilter.value;
        const equipmentTerm = equipmentFilter.value;
        const difficultyTerm = difficultyFilter.value;

        document.querySelectorAll('.item-exercise').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const group = item.dataset.group;
            const equipment = item.dataset.equipment;
            const difficulty = item.dataset.difficulty;

            const matchesSearch = name.includes(searchTerm);
            const matchesMuscle = !muscleTerm || group === muscleTerm;
            const matchesEquipment = !equipmentTerm || equipment === equipmentTerm;
            const matchesDifficulty = !difficultyTerm || difficulty === difficultyTerm;

            if (matchesSearch && matchesMuscle && matchesEquipment && matchesDifficulty) {
                item.classList.remove('hidden');
                item.classList.add('flex');
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
    }

    [searchInput, muscleFilter, equipmentFilter, difficultyFilter].forEach(el => {
        el?.addEventListener('input', applyFilters);
    });

    function selectExercise(btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const muscles = btn.dataset.muscles;
        
        // Dispatch event to Alpine
        window.dispatchEvent(new CustomEvent('add-exercise', {
            detail: { id, name, muscles }
        }));
        
        closeExerciseModal();
    }
</script>
