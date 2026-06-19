<div x-data="globalPatientSelector()" 
     @keydown.escape.window="close" 
     @open-global-patient-selector.window="open"
     class="relative z-[1000]">
    
    {{-- Acionador (Substitui o antigo) --}}
    <button @click="open" class="hidden md:flex items-center gap-3 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 hover:border-emerald-500/50 rounded-2xl p-1.5 pr-4 shadow-inner transition-all group">
        @php
            $activePatient = \App\Models\User::find(session('active_patient_id'));
        @endphp
        
        @if($activePatient)
            <img src="{{ $activePatient->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($activePatient->name).'&color=10b981&background=09090b&bold=true' }}" alt="Avatar" class="w-7 h-7 rounded-lg object-cover border border-emerald-500/30">
            <div class="flex flex-col items-start leading-none text-left">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">{{ \Illuminate\Support\Str::limit($activePatient->name, 15) }}</span>
                <span class="text-[7px] font-bold text-emerald-500 uppercase tracking-widest mt-0.5">Trocar Aluno</span>
            </div>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-zinc-500 group-hover:text-emerald-500 ml-1"></i>
        @else
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-500 font-black text-[9px] uppercase tracking-widest shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                <i data-lucide="search" class="w-3.5 h-3.5"></i> 
                <span>Buscar Aluno...</span>
            </div>
        @endif
    </button>

    <template x-teleport="body">
        <div class="relative z-[1000]">
            {{-- Backdrop --}}
            <div x-show="isOpen" 
                 x-transition.opacity 
                 class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm" 
                 @click="close"
                 x-cloak></div>

            {{-- Slide-over Panel --}}
            <div x-show="isOpen" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transition ease-in duration-200" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full" 
                 class="fixed inset-y-0 right-0 w-full md:w-[450px] lg:w-[500px] bg-zinc-950 border-l border-zinc-800 shadow-2xl flex flex-col"
                 x-cloak>

        
        {{-- Header & Search --}}
        <div class="p-6 border-b border-zinc-900">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-emerald-500"></i> Seletor Global
                </h2>
                <button @click="close" class="text-zinc-500 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="relative group">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600 group-focus-within:text-emerald-500 transition-colors"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.300ms="fetchData"
                       placeholder="Buscar por nome, CPF ou e-mail..." 
                       class="w-full bg-zinc-900 border border-zinc-800 rounded-xl py-3 pl-11 pr-4 text-xs font-bold text-white placeholder:text-zinc-600 focus:outline-none focus:border-emerald-500/50 focus:shadow-[0_0_15px_rgba(16,185,129,0.1)] transition-all">
            </div>

            {{-- Quick Filters --}}
            <div class="flex gap-2 overflow-x-auto pb-2 mt-4 scrollbar-hide">
                <template x-for="filter in availableFilters" :key="filter.value">
                    <button @click="setFilter(filter.value)" 
                            :class="currentFilter === filter.value ? 'bg-emerald-500 text-zinc-950' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'"
                            class="whitespace-nowrap px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                        <span x-text="filter.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Lista de Pacientes --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-6 relative">
            
            {{-- Loading State --}}
            <div x-show="isLoading" class="absolute inset-0 bg-zinc-950/50 backdrop-blur-sm z-10 flex flex-col items-center justify-center">
                <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-4">Carregando...</p>
            </div>

            {{-- Resultados vazios --}}
            <div x-show="!isLoading && Object.keys(data.favorites).length === 0 && Object.keys(data.recent).length === 0 && Object.keys(data.alphabetical).length === 0" class="text-center py-10">
                <i data-lucide="users-round" class="w-12 h-12 text-zinc-800 mx-auto mb-3"></i>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Nenhum aluno encontrado</p>
            </div>

            {{-- Favoritos --}}
            <div x-show="data.favorites && data.favorites.length > 0">
                <h3 class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                    <i data-lucide="star" class="w-3 h-3 text-amber-500 fill-amber-500"></i> Favoritos
                </h3>
                <div class="space-y-2">
                    <template x-for="patient in data.favorites" :key="'fav-'+patient.id">
                        <div @click="selectPatient(patient.id)" class="group cursor-pointer bg-zinc-900 border border-zinc-800 hover:border-emerald-500/50 rounded-xl p-3 transition-all relative overflow-hidden">
                            <div class="flex items-center gap-3 relative z-10">
                                <img :src="patient.photo_url" class="w-10 h-10 rounded-lg object-cover border border-zinc-700 group-hover:border-emerald-500/50 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-black text-white uppercase tracking-wider truncate" x-text="patient.name"></h4>
                                        <button @click.stop="toggleFavorite(patient.id, $event)" class="w-6 h-6 flex items-center justify-center rounded hover:bg-zinc-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="patient.is_favorite ? 'text-amber-500 fill-amber-500' : 'text-zinc-600 group-hover:text-amber-500'"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[8px] font-bold uppercase tracking-widest px-1.5 py-0.5 rounded border" 
                                              :class="patient.status === 'Ativo' ? 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20' : (patient.status === 'Pendente' ? 'text-amber-500 bg-amber-500/10 border-amber-500/20' : 'text-zinc-500 bg-zinc-800/50 border-zinc-700/50')"
                                              x-text="patient.status">
                                        </span>
                                        <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                            <span x-text="patient.last_portal_access === '--' ? 'Nunca acessou' : patient.last_portal_access"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Recentes --}}
            <div x-show="data.recent && data.recent.length > 0 && currentFilter === 'todos' && searchQuery === ''">
                <h3 class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                    <i data-lucide="clock" class="w-3 h-3"></i> Últimos Acessados
                </h3>
                <div class="space-y-2">
                    <template x-for="patient in data.recent" :key="'rec-'+patient.id">
                        <div @click="selectPatient(patient.id)" class="group cursor-pointer bg-zinc-900 border border-zinc-800 hover:border-emerald-500/50 rounded-xl p-3 transition-all relative overflow-hidden">
                            <div class="flex items-center gap-3 relative z-10">
                                <img :src="patient.photo_url" class="w-10 h-10 rounded-lg object-cover border border-zinc-700 group-hover:border-emerald-500/50 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-black text-white uppercase tracking-wider truncate" x-text="patient.name"></h4>
                                        <button @click.stop="toggleFavorite(patient.id, $event)" class="w-6 h-6 flex items-center justify-center rounded hover:bg-zinc-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="patient.is_favorite ? 'text-amber-500 fill-amber-500' : 'text-zinc-600 group-hover:text-amber-500'"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[8px] font-bold uppercase tracking-widest px-1.5 py-0.5 rounded border" 
                                              :class="patient.status === 'Ativo' ? 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20' : (patient.status === 'Pendente' ? 'text-amber-500 bg-amber-500/10 border-amber-500/20' : 'text-zinc-500 bg-zinc-800/50 border-zinc-700/50')"
                                              x-text="patient.status">
                                        </span>
                                        <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                            <span x-text="patient.last_portal_access === '--' ? 'Nunca acessou' : patient.last_portal_access"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Alfabético --}}
            <div x-show="Object.keys(data.alphabetical).length > 0">
                <template x-for="[letter, group] in Object.entries(data.alphabetical)" :key="letter">
                    <div class="mb-6">
                        <h3 class="text-[9px] font-black text-zinc-700 uppercase tracking-[0.2em] mb-3 border-b border-zinc-900 pb-1" x-text="letter"></h3>
                        <div class="space-y-2">
                            <template x-for="patient in group" :key="'alpha-'+patient.id">
                                <div @click="selectPatient(patient.id)" class="group cursor-pointer bg-zinc-900 border border-zinc-800 hover:border-emerald-500/50 rounded-xl p-3 transition-all relative overflow-hidden">
                                    <div class="flex items-center gap-3 relative z-10">
                                        <img :src="patient.photo_url" class="w-10 h-10 rounded-lg object-cover border border-zinc-700 group-hover:border-emerald-500/50 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-black text-white uppercase tracking-wider truncate" x-text="patient.name"></h4>
                                                <button @click.stop="toggleFavorite(patient.id, $event)" class="w-6 h-6 flex items-center justify-center rounded hover:bg-zinc-800 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="patient.is_favorite ? 'text-amber-500 fill-amber-500' : 'text-zinc-600 group-hover:text-amber-500'"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                </button>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[8px] font-bold uppercase tracking-widest px-1.5 py-0.5 rounded border" 
                                                      :class="patient.status === 'Ativo' ? 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20' : (patient.status === 'Pendente' ? 'text-amber-500 bg-amber-500/10 border-amber-500/20' : 'text-zinc-500 bg-zinc-800/50 border-zinc-700/50')"
                                                      x-text="patient.status">
                                                </span>
                                                <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                                    <span x-text="patient.last_portal_access === '--' ? 'Nunca acessou' : patient.last_portal_access"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('globalPatientSelector', () => ({
        isOpen: false,
        isLoading: false,
        searchQuery: '',
        currentFilter: 'todos',
        data: {
            favorites: [],
            recent: [],
            alphabetical: {}
        },
        availableFilters: [
            { label: 'Todos', value: 'todos' },
            { label: 'Ativos', value: 'ativos' },
            { label: 'Inativos', value: 'inativos' },
            { label: 'Arquivados', value: 'arquivados' },
            { label: 'Aniversariantes', value: 'aniversariantes' }
        ],

        open() {
            this.isOpen = true;
            this.fetchData();
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        },

        setFilter(filter) {
            this.currentFilter = filter;
            this.fetchData();
        },

        async fetchData() {
            this.isLoading = true;
            try {
                const queryParams = new URLSearchParams({
                    q: this.searchQuery,
                    filter: this.currentFilter
                });
                
                const response = await fetch(`{{ route('professional.global-selector.data') }}?${queryParams}`);
                const result = await response.json();
                
                this.data = result;
                this.$nextTick(() => { lucide.createIcons(); });
            } catch (error) {
                console.error('Error fetching patients:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async toggleFavorite(patientId, event) {
            event.stopPropagation();
            try {
                const response = await fetch(`{{ url('/professional/global-selector/favorite') }}/${patientId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.fetchData(); // Recarrega para reorganizar a lista
                }
            } catch (error) {
                console.error('Error toggling favorite:', error);
            }
        },

        async selectPatient(patientId) {
            try {
                const response = await fetch(`{{ route('professional.active-patient.set') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ patient_id: patientId })
                });
                
                if (response.ok) {
                    // Recarrega a página atual para aplicar o contexto
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error setting active patient:', error);
            }
        }
    }));
});
</script>
