@props(['selectedMuscles' => []])

<div class="space-y-4" x-data="muscleSelector(@js($selectedMuscles))">
    <div class="flex items-center justify-between">
        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Músculos Atuantes</label>
        <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 rounded-lg text-[10px] font-bold" x-text="selectedMuscles.length + ' selecionados'">0 selecionados</span>
    </div>

    <!-- Área de Tags -->
    <div class="min-h-[80px] p-4 bg-zinc-950 border border-white/5 rounded-2xl flex flex-wrap gap-2 items-start transition-all"
         :class="selectedMuscles.length === 0 ? 'border-dashed' : ''">
        
        <template x-if="selectedMuscles.length === 0">
            <p class="text-zinc-700 italic text-[11px] p-2">Nenhum músculo selecionado...</p>
        </template>

        <template x-for="muscle in selectedMuscles" :key="muscle.id">
            <div class="flex items-center bg-blue-500/10 border border-blue-500/20 text-blue-400 px-3 py-1.5 rounded-xl text-[10px] font-bold animate-fade-in">
                <span x-text="muscle.name"></span>
                <button type="button" @click="removeMuscle(muscle.id)" class="ml-2 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Input Autocomplete -->
    <div class="relative">
        <div class="flex items-center bg-zinc-950 border border-white/5 rounded-2xl focus-within:ring-2 focus-within:ring-blue-600 transition-all">
            <i class="fas fa-search ml-4 text-zinc-700 text-xs"></i>
            <input type="text" 
                   x-model="search" 
                   @input.debounce.300ms="fetchMuscles()"
                   @keydown.enter.prevent="selectFirstResult()"
                   placeholder="Buscar músculo..."
                   class="w-full bg-transparent border-none text-white py-4 px-3 focus:ring-0 placeholder:text-zinc-700 text-xs font-medium">
            
            <div x-show="loading" class="mr-4">
                <i class="fas fa-circle-notch fa-spin text-blue-500 text-xs"></i>
            </div>
        </div>

        <!-- Lista de Resultados -->
        <div x-show="results.length > 0 && search.length > 0" 
             class="absolute z-50 w-full mt-2 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto"
             @click.away="results = []">
            <template x-for="result in results" :key="result.id">
                <button type="button" 
                        @click="addMuscle(result)" 
                        class="w-full text-left px-4 py-3 hover:bg-white/5 flex items-center justify-between group transition-colors">
                    <div>
                        <span class="text-white text-xs font-bold group-hover:text-blue-400 transition-colors" x-text="result.name"></span>
                        <span class="ml-2 text-[9px] text-zinc-500" x-text="result.group"></span>
                    </div>
                    <span class="text-[8px] uppercase font-black tracking-widest text-zinc-600 bg-zinc-800 px-1.5 py-0.5 rounded" x-text="result.type"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Hidden input to save data -->
    <input type="hidden" name="selected_muscles" :value="JSON.stringify(selectedMuscles.map(m => m.id))">
</div>

@once
<script>
    function muscleSelector(initialMuscles = []) {
        return {
            search: '',
            loading: false,
            results: [],
            selectedMuscles: initialMuscles,

            async fetchMuscles() {
                if (this.search.length < 2) {
                    this.results = [];
                    return;
                }

                this.loading = true;
                try {
                    const response = await fetch(`{{ route('muscles.search') }}?q=${this.search}`);
                    this.results = await response.json();
                } catch (e) {
                    console.error('Error fetching muscles:', e);
                } finally {
                    this.loading = false;
                }
            },

            addMuscle(muscle) {
                if (!this.selectedMuscles.find(m => m.id === muscle.id)) {
                    this.selectedMuscles.push(muscle);
                }
                this.search = '';
                this.results = [];
            },

            removeMuscle(id) {
                this.selectedMuscles = this.selectedMuscles.filter(m => m.id !== id);
            },

            selectFirstResult() {
                if (this.results.length > 0) {
                    this.addMuscle(this.results[0]);
                }
            }
        }
    }
</script>
@endonce
