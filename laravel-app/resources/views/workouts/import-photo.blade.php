@extends('layouts.app')

@section('title', 'Importar Treino por Foto')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="workoutImporter">
    <!-- Header -->
    <div class="mb-12 space-y-4">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-emerald-500 rounded-3xl flex items-center justify-center text-zinc-950 shadow-2xl shadow-emerald-500/20 transform -rotate-6">
                <i data-lucide="camera" class="w-8 h-8"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic leading-none">Importar Treino</h1>
                <p class="text-zinc-500 text-sm font-medium mt-2 uppercase tracking-widest">Digitalize sua ficha usando Inteligência Artificial</p>
            </div>
        </div>
    </div>

    @monetizationGate('workout_import_photo')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            <!-- Left: Controls & History (4 cols) -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Main Controls -->
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-[3rem] p-10 space-y-10 relative overflow-hidden group">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-emerald-500/5 rounded-full blur-[80px] pointer-events-none group-hover:bg-emerald-500/10 transition-all duration-700"></div>
                    
                    <div class="space-y-6 relative">
                        <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] italic">Capturar Ficha</h3>
                        
                        <div class="flex flex-col gap-4">
                            <!-- Mobile Camera -->
                            <label class="relative group/btn cursor-pointer overflow-hidden rounded-2xl active:scale-[0.98] transition-all">
                                <div class="absolute inset-0 bg-emerald-500 group-hover/btn:bg-emerald-400 transition-colors"></div>
                                <div class="relative py-5 px-6 flex items-center justify-center gap-4 text-zinc-950 font-black">
                                    <i data-lucide="camera" class="w-5 h-5"></i>
                                    <span class="text-[10px] uppercase tracking-[0.2em]">Tirar Foto agora</span>
                                    <input type="file" class="hidden" accept="image/*" capture="environment" @change="onFileSelected">
                                </div>
                            </label>

                            <!-- File Selection -->
                            <label class="relative group/btn cursor-pointer overflow-hidden rounded-2xl border border-white/10 active:scale-[0.98] transition-all">
                                <div class="absolute inset-0 bg-zinc-900 group-hover/btn:bg-zinc-800 transition-colors"></div>
                                <div class="relative py-5 px-6 flex items-center justify-center gap-4 text-white font-black">
                                    <i data-lucide="image" class="w-5 h-5"></i>
                                    <span class="text-[10px] uppercase tracking-[0.2em]">Selecionar Galeria</span>
                                    <input type="file" class="hidden" accept="image/*" @change="onFileSelected">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Status / Actions -->
                    <div x-show="previewUrl" class="space-y-6 pt-10 border-t border-white/5 animate-fade-in relative">
                        <button 
                            @click="processPhoto()" 
                            :disabled="isProcessing"
                            class="w-full relative group/process py-6 bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-black rounded-2xl overflow-hidden transition-all shadow-2xl shadow-blue-500/20 active:scale-[0.98] disabled:opacity-50"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-500 opacity-0 group-hover/process:opacity-100 transition-opacity"></div>
                            
                            <template x-if="!isProcessing">
                                <div class="relative flex items-center justify-center gap-4 text-[10px] uppercase tracking-[0.2em]">
                                    <i data-lucide="sparkles" class="w-5 h-5 fill-current animate-pulse"></i>
                                    Iniciar Escaneamento IA
                                </div>
                            </template>
                            
                            <template x-if="isProcessing">
                                <div class="relative flex items-center justify-center gap-4 text-[10px] uppercase tracking-[0.2em]">
                                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="processingStep"></span>
                                </div>
                            </template>
                        </button>

                        <button @click="clear()" class="w-full text-zinc-700 hover:text-white text-[9px] font-black uppercase tracking-[0.4em] transition-all">
                            Descartar Imagem
                        </button>
                    </div>
                </div>

                <!-- Tips & History Cards -->
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-[2.5rem] p-8 space-y-4">
                        <div class="flex items-center gap-3 text-emerald-500">
                            <i data-lucide="zap" class="w-5 h-5 fill-current"></i>
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] italic">Dicas de Precisão</span>
                        </div>
                        <ul class="space-y-3 text-[11px] text-zinc-500 font-bold italic leading-relaxed">
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-500 mt-1">•</span>
                                Use luz natural sempre que possível
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-500 mt-1">•</span>
                                Mantenha o celular paralelo à ficha
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-500 mt-1">•</span>
                                Fichas impressas têm maior taxa de acerto
                            </li>
                        </ul>
                    </div>

                    @if($history->count() > 0)
                        <div class="bg-zinc-900/30 border border-white/5 rounded-[2.5rem] p-8 space-y-6">
                            <h3 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] italic">Últimas Importações</h3>
                            <div class="space-y-4">
                                @foreach($history as $log)
                                    <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-900 flex items-center justify-center">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-zinc-500"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold text-white uppercase tracking-wider">
                                                    {{ $log->created_at->format('d/m/Y') }}
                                                </p>
                                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">
                                                    {{ ucfirst($log->status) }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($log->status === 'completed')
                                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500/50"></i>
                                        @else
                                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500/50"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Preview and Results (8 cols) -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Preview Area -->
                <div x-show="!exercises.length" class="relative bg-zinc-950 border border-white/5 rounded-[4rem] aspect-square lg:aspect-video flex items-center justify-center overflow-hidden shadow-inner">
                    <template x-if="!previewUrl">
                        <div class="flex flex-col items-center gap-6 text-zinc-800 animate-pulse">
                            <div class="w-24 h-24 rounded-full border-4 border-dashed border-zinc-900 flex items-center justify-center">
                                <i data-lucide="scan" class="w-10 h-10"></i>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-[0.5em] italic">Aguardando Ficha de Treino</p>
                        </div>
                    </template>
                    <template x-if="previewUrl">
                        <img :src="previewUrl" class="w-full h-full object-contain p-12 animate-fade-in shadow-2xl">
                    </template>
                    
                    <!-- Neural Scanning Overlay -->
                    <div x-show="isProcessing" class="absolute inset-0 bg-emerald-500/5 pointer-events-none overflow-hidden backdrop-blur-[2px]">
                        <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_30px_rgba(16,185,129,0.8)] animate-neural-scan"></div>
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-emerald-500/10 via-transparent to-transparent animate-pulse"></div>
                    </div>
                </div>

                <!-- Neural Result Dashboard -->
                <div x-show="exercises.length > 0" class="bg-zinc-900 border border-white/10 rounded-[4rem] p-12 space-y-12 animate-fade-in-up shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] pointer-events-none"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative">
                        <div class="space-y-2">
                            <h3 class="text-3xl font-black text-white tracking-tighter uppercase italic leading-none">Dados Extraídos</h3>
                            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em]">Refine os parâmetros detectados pela rede neural</p>
                        </div>
                        <div class="flex items-center gap-3 px-6 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">Análise Concluída</span>
                        </div>
                    </div>

                    <div class="space-y-8 relative">
                        <!-- Global Config -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 bg-zinc-950/50 rounded-3xl border border-white/5">
                            <label class="space-y-3">
                                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] ml-2 italic">Identificação da Rotina</span>
                                <input type="text" x-model="workoutName" placeholder="Ex: Protocolo Alpha - Pernas" class="w-full bg-zinc-900 border border-white/10 rounded-2xl px-6 py-5 text-white text-sm font-bold focus:border-emerald-500/50 focus:ring-0 transition-all outline-none">
                            </label>
                            
                            <div class="flex items-end pb-1">
                                <div class="px-6 py-5 bg-zinc-900/50 border border-dashed border-white/10 rounded-2xl w-full flex items-center justify-between">
                                    <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest italic">Total de Exercícios</span>
                                    <span class="text-xl font-black text-emerald-500 italic" x-text="exercises.length"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Grid de Exercícios -->
                        <div class="space-y-4">
                            <template x-for="(ex, index) in exercises" :key="index">
                                <div class="group bg-zinc-950/40 hover:bg-zinc-950 border border-white/5 hover:border-emerald-500/30 rounded-3xl p-6 transition-all duration-300">
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        <!-- Header Exercício -->
                                        <div class="flex-1 space-y-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-zinc-700 font-black text-xs italic" x-text="index + 1"></div>
                                                <input type="text" x-model="ex.nome_exercicio" class="flex-1 bg-transparent border-none p-0 text-lg font-black text-white italic focus:ring-0 placeholder-zinc-800" placeholder="Nome do exercício">
                                            </div>
                                            <textarea x-model="ex.observacoes" placeholder="Adicionar observações..." class="w-full bg-transparent border-none p-0 text-[11px] text-zinc-600 font-bold italic focus:ring-0 resize-none" rows="1"></textarea>
                                        </div>

                                        <!-- Params -->
                                        <div class="flex items-center gap-4">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div class="space-y-2">
                                                    <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Séries</span>
                                                    <input type="text" x-model="ex.series" class="w-16 bg-zinc-900 border border-white/5 rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0">
                                                </div>
                                                <div class="space-y-2">
                                                    <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Reps</span>
                                                    <input type="text" x-model="ex.repeticoes" class="w-16 bg-zinc-900 border border-white/5 rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0">
                                                </div>
                                                <div class="space-y-2">
                                                    <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Carga</span>
                                                    <input type="text" x-model="ex.carga" class="w-16 bg-zinc-900 border border-white/5 rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0">
                                                </div>
                                            </div>

                                            <button @click="removeExercise(index)" class="w-12 h-12 rounded-2xl bg-zinc-900 border border-white/5 text-zinc-700 hover:text-red-500 hover:bg-red-500/10 transition-all flex items-center justify-center">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Finalizer Area -->
                    <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8 relative">
                        <div class="text-center md:text-left">
                            <p class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] italic">Conferência Humana</p>
                            <p class="text-[11px] text-zinc-500 font-bold italic">Ao salvar, este treino será integrado ao seu calendário evolutivo.</p>
                        </div>

                        <button 
                            @click="saveImport()" 
                            :disabled="isSaving || !workoutName"
                            class="w-full md:w-auto px-12 py-6 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-3xl transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-4 group/save"
                        >
                            <template x-if="!isSaving">
                                <div class="flex items-center gap-4">
                                    <span class="text-xs uppercase tracking-[0.2em]">Incorporar ao Sistema</span>
                                    <i data-lucide="arrow-right" class="w-5 h-5 group-hover/save:translate-x-1 transition-transform"></i>
                                </div>
                            </template>
                            <template x-if="isSaving">
                                <div class="flex items-center gap-4">
                                    <svg class="animate-spin h-5 w-5 text-zinc-950" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-xs uppercase tracking-[0.2em]">Salvando Dados...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endMonetizationGate
</div>

<style>
    @keyframes neural-scan {
        0% { top: 0%; opacity: 0; }
        15% { opacity: 1; }
        85% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
    .animate-neural-scan {
        animation: neural-scan 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('workoutImporter', () => ({
            previewUrl: null,
            file: null,
            isProcessing: false,
            isSaving: false,
            processingStep: 'Inicializando Rede...',
            exercises: [],
            workoutName: '',
            
            onFileSelected(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                // Validação básica
                if (!file.type.startsWith('image/')) {
                    window.toast.error('Por favor, selecione um arquivo de imagem.');
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    window.toast.error('A imagem deve ter no máximo 10MB.');
                    return;
                }
                
                this.file = file;
                this.previewUrl = URL.createObjectURL(file);
                this.exercises = [];
                this.workoutName = '';
            },
            
            async processPhoto() {
                if (!this.file) return;
                
                this.isProcessing = true;
                this.processingStep = 'Extraindo Visão...';
                
                const formData = new FormData();
                formData.append('photo', this.file);
                
                try {
                    const response = await fetch("{{ route('progression.plans.process-photo') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    this.processingStep = 'Interpretando Treino...';
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.exercises = data.exercises;
                        this.workoutName = 'Treino IA - ' + new Date().toLocaleDateString('pt-BR');
                        
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                        
                        window.toast.success('Treino extraído com sucesso pela IA!');
                    } else {
                        window.toast.error(data.error || 'Falha ao processar imagem.');
                    }
                } catch (err) {
                    console.error(err);
                    window.toast.error('Falha na comunicação com o servidor de IA.');
                } finally {
                    this.isProcessing = false;
                }
            },
            
            removeExercise(index) {
                this.exercises.splice(index, 1);
            },
            
            async saveImport() {
                if (!this.workoutName) {
                    window.toast.error('Informe um nome para o treino.');
                    return;
                }

                this.isSaving = true;
                
                try {
                    const response = await fetch("{{ route('progression.plans.save-import') }}", {
                        method: 'POST',
                        body: JSON.stringify({
                            workout_name: this.workoutName,
                            exercises: this.exercises
                        }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        window.toast.success(data.message);
                        setTimeout(() => window.location.href = data.redirect, 1500);
                    } else {
                        window.toast.error(data.error || 'Falha ao salvar treino.');
                    }
                } catch (err) {
                    window.toast.error('Ocorreu um erro ao salvar o treino.');
                } finally {
                    this.isSaving = false;
                }
            },
            
            clear() {
                this.file = null;
                this.previewUrl = null;
                this.exercises = [];
                this.workoutName = '';
            }
        }));
    });
</script>
@endpush
@endsection
