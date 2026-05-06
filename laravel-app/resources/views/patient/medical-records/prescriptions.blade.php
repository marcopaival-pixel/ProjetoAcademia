@extends('layouts.app')

@section('title', 'Minhas Receitas — ' . config('app.name'))

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .prescription-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .prescription-card:hover {
        transform: translateY(-8px);
        border-color: rgba(16, 185, 129, 0.3);
        box-shadow: 0 30px 60px -12px rgba(16, 185, 129, 0.15);
        background: rgba(20, 22, 28, 0.8);
    }
    
    .status-badge {
        font-size: 8px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 5px 12px;
        border-radius: 99px;
    }
    
    .search-input:focus {
        border-color: rgba(16, 185, 129, 0.4);
        background: rgba(20, 22, 28, 0.9);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.05);
    }

    .rx-glow {
        background: radial-gradient(circle at center, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
    }

    @keyframes fade-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fade-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-32" x-data="{ 
    search: '',
    items: @js($prescriptions->items()),
    get filteredItems() {
        if (!this.search) return this.items;
        return this.items.filter(item => 
            item.medicine.toLowerCase().includes(this.search.toLowerCase()) ||
            (item.observations && item.observations.toLowerCase().includes(this.search.toLowerCase()))
        );
    }
}">
    <div class="py-10 px-6 max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-16 animate-fade-up">
            <div>
                <x-patient.page-header 
                    title="Minhas Receitas" 
                    subtitle="Gestão Digital de Prescrições e Protocolos" 
                    backUrl="{{ route('patient.medical-records.index') }}"
                    icon="fas fa-file-prescription"
                />
            </div>
            
            <!-- Search Bar -->
            <div class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-zinc-600 group-focus-within:text-emerald-500 transition-colors">
                    <i class="fas fa-search"></i>
                </div>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Buscar por medicamento ou orientação..." 
                    class="search-input w-full bg-zinc-900/40 border border-zinc-800 rounded-3xl py-5 pl-14 pr-8 text-sm font-bold text-white transition-all outline-none placeholder:text-zinc-700"
                >
                <div class="absolute right-5 top-1/2 -translate-y-1/2" x-show="search" x-cloak>
                    <button @click="search = ''" class="w-6 h-6 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] text-zinc-400 hover:bg-zinc-700 hover:text-white transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Prescriptions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <template x-for="(prescription, index) in filteredItems" :key="prescription.id">
                <div class="glass-card prescription-card rounded-[3.5rem] p-10 transition-all group relative overflow-hidden flex flex-col h-full animate-fade-up" :style="'animation-delay: ' + (index * 100) + 'ms'">
                    <!-- Ambient Glow -->
                    <div class="absolute -right-20 -top-20 w-64 h-64 rx-glow rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
                    
                    <!-- Card Header -->
                    <div class="flex items-start justify-between mb-10 relative z-10">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all duration-500 shadow-2xl border border-emerald-500/10">
                                <i class="fas fa-capsules text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-white leading-none tracking-tighter uppercase italic mb-2" x-text="prescription.medicine"></h3>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-[10px] text-zinc-600"></i>
                                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em]" x-text="'Emitido em ' + new Date(prescription.date).toLocaleDateString('pt-BR')"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Specs Grid -->
                    <div class="grid grid-cols-1 gap-4 mb-10 relative z-10">
                        <div class="flex items-center justify-between p-6 rounded-[2rem] bg-white/[0.02] border border-white/5 group-hover:bg-white/[0.05] transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-600">
                                    <i class="fas fa-vial text-xs"></i>
                                </div>
                                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Dosagem</span>
                            </div>
                            <span class="text-white font-black text-sm tracking-tight" x-text="prescription.dosage || 'N/A'"></span>
                        </div>
                        
                        <div class="flex items-center justify-between p-6 rounded-[2rem] bg-white/[0.02] border border-white/5 group-hover:bg-white/[0.05] transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-600">
                                    <i class="fas fa-clock text-xs"></i>
                                </div>
                                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Frequência</span>
                            </div>
                            <span class="text-white font-black text-sm tracking-tight" x-text="prescription.frequency || 'N/A'"></span>
                        </div>

                        <div class="flex items-center justify-between p-6 rounded-[2rem] bg-white/[0.02] border border-white/5 group-hover:bg-white/[0.05] transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-600">
                                    <i class="fas fa-hourglass-half text-xs"></i>
                                </div>
                                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Duração</span>
                            </div>
                            <span class="text-white font-black text-sm tracking-tight" x-text="prescription.duration || 'N/A'"></span>
                        </div>
                    </div>

                    <!-- Observations -->
                    <template x-if="prescription.observations">
                        <div class="mb-12 relative z-10 flex-grow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Orientações do Especialista</p>
                            </div>
                            <div class="p-6 rounded-3xl bg-zinc-950/50 border-l-4 border-emerald-500/30">
                                <p class="text-zinc-400 text-xs leading-relaxed italic font-medium" x-text="'&quot;' + prescription.observations + '&quot;'"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Action Button -->
                    <div class="mt-auto relative z-10 pt-4">
                        <a :href="'{{ route('patient.medical-records.prescriptions.download', ':id') }}'.replace(':id', prescription.id)" 
                           class="w-full py-6 bg-zinc-950 border border-zinc-800 hover:bg-emerald-500 hover:text-zinc-950 text-white font-black rounded-[2.5rem] text-[11px] uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-4 group/btn shadow-xl active:scale-95">
                            <i class="fas fa-file-pdf text-emerald-500 group-hover/btn:text-zinc-950 transition-colors text-lg"></i> 
                            BAIXAR RECEITA DIGITAL
                        </a>
                    </div>
                </div>
            </template>
            
            <!-- Empty Search State -->
            <div x-show="filteredItems.length === 0" class="col-span-full py-32 text-center animate-fade-up" x-cloak>
                <div class="w-24 h-24 bg-zinc-900 rounded-[2.5rem] flex items-center justify-center text-zinc-800 mx-auto mb-8 border border-zinc-800">
                    <i class="fas fa-search-minus text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter">Nenhum protocolo encontrado</h3>
                <p class="text-zinc-500 text-sm mt-4 font-medium">Não encontramos registros para o termo "<span x-text="search" class="text-emerald-500 font-black"></span>"</p>
                <button @click="search = ''" class="mt-10 px-8 py-4 bg-zinc-900 text-[10px] font-black text-emerald-500 uppercase tracking-widest rounded-2xl hover:bg-zinc-800 transition-all border border-zinc-800">Limpar Filtros</button>
            </div>
        </div>

        <!-- Real Empty State (From Backend) -->
        @if($prescriptions->isEmpty())
            <div class="py-20 animate-fade-up">
                <x-patient.empty-state 
                    icon="fas fa-prescription-bottle-alt" 
                    title="Sem Receitas" 
                    description="Seu histórico de receitas médicas e prescrições de medicamentos aparecerá aqui assim que seu profissional realizar a emissão."
                />
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-20 flex justify-center" x-show="!search && @js($prescriptions->hasPages())">
            <div class="p-2 bg-zinc-900/50 rounded-3xl border border-zinc-800 backdrop-blur-xl">
                {{ $prescriptions->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Pagination Overrides for Soft Premium */
    .pagination { @apply flex gap-2; }
    .page-item { @apply rounded-xl overflow-hidden; }
    .page-link { @apply bg-zinc-900 border-zinc-800 text-zinc-500 font-black uppercase text-[10px] px-4 py-2 hover:bg-emerald-500 hover:text-zinc-950 transition-all; }
    .active .page-link { @apply bg-emerald-500 text-zinc-950; }
</style>
@endsection

