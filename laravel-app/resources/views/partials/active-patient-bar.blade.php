<div x-data="activePatientManager()" class="w-full bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-900/80 sticky top-0 z-[50] shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto py-2.5 flex items-center justify-between">
        
        <!-- Estado: Paciente Selecionado -->
        <template x-if="activePatient">
            <div class="flex items-center gap-4 animate-fade-in">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-500 overflow-hidden shadow-[0_0_15px_-3px_rgba(16,185,129,0.2)]">
                    <template x-if="activePatient.photo_url">
                        <img :src="activePatient.photo_url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!activePatient.photo_url">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </template>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Paciente Ativo</p>
                    <p class="text-sm font-bold text-white truncate max-w-[200px] sm:max-w-sm" x-text="activePatient.name"></p>
                </div>
                <div class="h-4 w-px bg-zinc-800 mx-2 hidden sm:block"></div>
                <button @click="clearPatient()" class="hidden sm:flex text-[10px] font-bold text-zinc-500 hover:text-red-400 uppercase tracking-widest transition-colors flex items-center gap-1 group">
                    <i data-lucide="x-circle" class="w-3 h-3 group-hover:scale-110 transition-transform"></i>
                    Desvincular
                </button>
            </div>
        </template>

        <!-- Estado: Nenhum Paciente -->
        <template x-if="!activePatient">
            <div class="flex items-center gap-3 animate-fade-in">
                <div class="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-600">
                    <i data-lucide="user-minus" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-zinc-400">Nenhum paciente selecionado</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-600 hidden sm:block">Ações clínicas restritas</p>
                </div>
            </div>
        </template>

        <!-- Ações -->
        <div class="flex items-center gap-2 sm:gap-3">
            <template x-if="activePatient">
                <a :href="`/professional/patients/${activePatient.id}`" class="px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 transition-colors flex items-center gap-2 text-[10px] font-black text-white uppercase tracking-widest">
                    <i data-lucide="file-text" class="w-3 h-3 text-emerald-500"></i>
                    <span class="hidden sm:inline">Prontuário</span>
                </a>
            </template>
            
            <button @click="$dispatch('open-global-patient-selector')" class="px-4 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/10 flex items-center gap-2 text-[10px] font-black text-zinc-950 uppercase tracking-widest active:scale-95">
                <i data-lucide="search" class="w-3 h-3"></i>
                <span x-text="activePatient ? 'Trocar Paciente' : 'Selecionar Paciente'" class="hidden sm:inline"></span>
                <span x-text="activePatient ? 'Trocar' : 'Selecionar'" class="sm:hidden"></span>
            </button>
        </div>
    </div>

    <!-- Modal Antigo Removido em favor do GlobalPatientSelector -->
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('activePatientManager', () => ({
        activePatient: @json($activePatient ?? null),

        async clearPatient() {
            try {
                const response = await fetch(`{{ route('professional.active-patient.clear') }}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                if (result.success) {
                    this.activePatient = null;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Paciente desvinculado.', type: 'success' } }));
                    window.location.reload();
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Falha na comunicação com o servidor.', type: 'error' } }));
            }
        }
    }));
});
</script>
