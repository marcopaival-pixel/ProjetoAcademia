@extends('layouts.app')

@section('title', 'Importar Treino por Foto')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="workoutImporter">
    <!-- Header -->
    <div class="mb-12 space-y-4">
        <div class="flex items-center justify-between gap-6 flex-wrap">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl flex items-center justify-center text-zinc-950 shadow-2xl shadow-emerald-500/20 transform -rotate-6">
                    <i data-lucide="sparkles" class="w-8 h-8"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic leading-none">Importador Inteligente</h1>
                    <p class="text-zinc-500 text-sm font-medium mt-2 uppercase tracking-widest">Digitalização inteligente de treinos com Orquestrador de Agentes IA</p>
                </div>
            </div>

            {{-- Badge compacto de Créditos IA + Popover --}}
            @auth
            @if($access['allowed'] ?? false)
            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.stop>
                {{-- Badge --}}
                <button id="ia-credits-badge" @click="open = !open"
                    class="flex items-center gap-3 px-5 py-3 rounded-2xl border transition-all duration-300 cursor-pointer
                           {{ $remainingImports <= 2 ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-purple-500/10 border-purple-500/25 text-purple-300 hover:bg-purple-500/20' }}">
                    <span class="text-lg">🧠</span>
                    <div class="flex flex-col items-start leading-none">
                        <span class="text-[11px] font-black uppercase tracking-widest {{ $remainingImports <= 2 ? 'text-orange-400' : 'text-purple-300' }}">IA Escaneamentos</span>
                        <span class="text-base font-black tabular-nums {{ $remainingImports <= 2 ? 'text-orange-400' : 'text-white' }} mt-0.5">
                            {{ $remainingImports }} <span class="text-zinc-500 text-xs font-bold">/ {{ $planMonthlyImports }}</span>
                        </span>
                        @if($renewsInDays !== null && $renewsInDays > 0)
                            <span class="text-[9px] font-bold text-zinc-600 uppercase tracking-wider mt-0.5">Renova em {{ $renewsInDays }} dias</span>
                        @endif
                    </div>
                    <i data-lucide="info" class="w-3.5 h-3.5 opacity-40"></i>
                </button>

                {{-- Popover --}}
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute top-full right-0 mt-3 w-72 bg-zinc-950 border border-purple-500/20 rounded-3xl shadow-2xl shadow-purple-500/10 p-6 space-y-5 z-50">

                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black text-white uppercase tracking-[0.2em]">Créditos IA</h4>
                        <span class="text-[9px] font-black text-purple-400 uppercase tracking-widest bg-purple-500/10 px-2 py-1 rounded-lg border border-purple-500/20">
                            {{ auth()->user()->plan->name ?? 'Premium' }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Disponíveis</span>
                            <span class="text-sm font-black text-white tabular-nums">{{ $remainingImports }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Utilizados</span>
                            <span class="text-sm font-black text-zinc-400 tabular-nums">{{ $usedImports }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Total mensal</span>
                            <span class="text-sm font-black text-zinc-400 tabular-nums">{{ $planMonthlyImports }}</span>
                        </div>

                        {{-- Progress bar --}}
                        <div class="h-1.5 bg-zinc-900 rounded-full overflow-hidden mt-2">
                            @php $pct = $planMonthlyImports > 0 ? min(100, round(($remainingImports / $planMonthlyImports) * 100)) : 100; @endphp
                            <div class="h-full rounded-full transition-all duration-500 {{ $pct <= 20 ? 'bg-orange-500' : 'bg-purple-500' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="border-t border-white/5 pt-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-wider">Renovação</span>
                            <span class="text-[10px] font-black text-zinc-400">{{ $renewalDate }}</span>
                        </div>
                        <p class="text-[9px] text-zinc-700 font-medium leading-relaxed">Cada importação permite até 7 imagens por treino semanal.</p>
                        <a href="{{ route('credits.buy') }}" class="block w-full text-center py-2.5 bg-purple-500 hover:bg-purple-400 text-zinc-950 text-[9px] font-black uppercase tracking-[0.2em] rounded-xl transition-all mt-3">
                            Comprar mais créditos
                        </a>
                    </div>
                </div>
            </div>
            @endif
            @endauth
        </div>
    </div>


    <!-- Wizard Steps Indicator -->
    <div class="mb-12 bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></div>
                <span class="text-xs font-black text-white uppercase tracking-widest italic">Progresso da Importação</span>
            </div>
            <div class="flex items-center justify-between w-full md:w-auto md:gap-8 lg:gap-12 overflow-x-auto pb-2 md:pb-0">
                <!-- Step 1 -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-all duration-300"
                         :class="step === 1 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20' : (step > 1 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-zinc-850 text-zinc-500')">
                        1
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider" :class="step === 1 ? 'text-white' : 'text-zinc-500'">Fotos</span>
                </div>
                <div class="h-[1px] w-8 bg-zinc-800 hidden md:block"></div>

                <!-- Step 2 -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-all duration-300"
                         :class="step === 2 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20' : (step > 2 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-zinc-850 text-zinc-500')">
                        2
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider" :class="step === 2 ? 'text-white' : 'text-zinc-500'">Dias</span>
                </div>
                <div class="h-[1px] w-8 bg-zinc-800 hidden md:block"></div>

                <!-- Step 3 -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-all duration-300"
                         :class="step === 3 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20' : (step > 3 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-zinc-850 text-zinc-500')">
                        3
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider" :class="step === 3 ? 'text-white' : 'text-zinc-500'">Orquestrador IA</span>
                </div>
                <div class="h-[1px] w-8 bg-zinc-800 hidden md:block"></div>

                <!-- Step 4 -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-all duration-300"
                         :class="step === 4 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20' : (step > 4 ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-zinc-850 text-zinc-500')">
                        4
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider" :class="step === 4 ? 'text-white' : 'text-zinc-500'">Revisão</span>
                </div>
                <div class="h-[1px] w-8 bg-zinc-800 hidden md:block"></div>

                <!-- Step 5 -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-all duration-300"
                         :class="step === 5 ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20' : 'bg-zinc-850 text-zinc-500'">
                        5
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider" :class="step === 5 ? 'text-white' : 'text-zinc-500'">Sucesso</span>
                </div>
            </div>
        </div>
    </div>

    @if($access['allowed'])
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            <!-- Left Panel (Summary & History) - 4 cols -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Premium Credits Card -->
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-purple-500/20 rounded-[2.5rem] p-8 space-y-6 relative overflow-hidden group shadow-lg shadow-purple-500/5">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-purple-500/10 rounded-full blur-[80px] pointer-events-none animate-pulse"></div>
                    
                    <div class="relative space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/25 flex items-center justify-center text-purple-400">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-white uppercase tracking-wider">Escaneamentos IA</h3>
                                <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mt-0.5">Recurso NexShape Premium</p>
                            </div>
                        </div>

                        <div class="flex items-baseline justify-between pt-2">
                            <div>
                                <span class="text-3xl font-black text-white tracking-tight tabular-nums">
                                    {{ floor((auth()->user()->ai_credits ?? 0) / 50) }}
                                </span>
                                <span class="text-zinc-500 font-bold text-xs uppercase tracking-wider ml-1">restantes</span>
                            </div>
                            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-950 px-3 py-1.5 rounded-full border border-white/5">
                                {{ auth()->user()->ai_credits ?? 0 }} créditos
                            </span>
                        </div>

                        <div class="pt-2">
                            <a href="{{ route('credits.buy') }}" class="w-full py-3 bg-purple-500 hover:bg-purple-400 text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2 group-hover:scale-[1.02] transform duration-300">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i> Comprar Mais
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-white/5 rounded-[3rem] p-10 space-y-8 relative overflow-hidden group">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-emerald-500/5 rounded-full blur-[80px] pointer-events-none"></div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] italic">Resumo do Treino</h3>
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 rounded-full text-[9px] font-black text-zinc-400 uppercase tracking-widest" x-text="selectedImages.length + ' / 7 Fotos'"></span>
                        </div>

                        <!-- Statistics Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                                <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest block">Imagens</span>
                                <span class="text-xl font-black text-white block mt-1" x-text="selectedImages.length">0</span>
                            </div>
                            <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                                <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest block">Créditos IA</span>
                                <span class="text-xl font-black text-emerald-500 block mt-1" x-text="selectedImages.length > 0 ? '1 IA' : '0'">0</span>
                            </div>
                            <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                                <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest block">Exercícios</span>
                                <span class="text-xl font-black text-blue-400 block mt-1" x-text="exercises.length > 0 ? exercises.length : '-'">-</span>
                            </div>
                            <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                                <span class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest block">Confiança</span>
                                <span class="text-xl font-black text-indigo-400 block mt-1" x-text="exercises.length > 0 ? '98%' : '-'">-</span>
                            </div>
                        </div>

                        <!-- Credit Estimation & Estimated Time Alert -->
                        <div x-show="selectedImages.length > 0" class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl space-y-2">
                            <div class="flex items-center gap-2 text-emerald-400">
                                <i data-lucide="zap" class="w-4 h-4"></i>
                                <span class="text-[9px] font-black uppercase tracking-wider">Estimativa da Transação</span>
                            </div>
                            <p class="text-[10px] text-zinc-400 font-medium italic">
                                ✔ <span x-text="selectedImages.length"></span> imagens • ✔ 1 treino semanal • ✔ 1 crédito IA
                            </p>
                            <p class="text-[9px] text-zinc-500 font-bold">
                                Tempo estimado de análise: <span x-text="selectedImages.length * 7 + 10"></span> segundos
                            </p>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div class="bg-zinc-900/30 border border-white/5 rounded-[2.5rem] p-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] italic">Histórico de Capturas</h3>
                        @if($history->count() > 0)
                            <button 
                                type="button"
                                onclick="window.openClearHistoryModal && window.openClearHistoryModal()"
                                class="text-[9px] font-black text-red-500/70 hover:text-red-500 uppercase tracking-widest transition-all"
                            >
                                Limpar
                            </button>
                        @endif
                    </div>
                    <div class="space-y-4">
                        @forelse($history as $log)
                            @php
                                $mappedStatus = 'Processado';
                                $statusClass = 'text-emerald-500';
                                $statusIcon = 'check-circle';
                                if ($log->status === 'failed') {
                                    $mappedStatus = 'Foto inválida';
                                    $statusClass = 'text-red-500';
                                    $statusIcon = 'x-circle';
                                } elseif ($log->status === 'waiting_review') {
                                    $mappedStatus = 'Revisão necessária';
                                    $statusClass = 'text-amber-500';
                                    $statusIcon = 'alert-triangle';
                                }
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-white/10 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-900 flex items-center justify-center">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-zinc-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-white uppercase tracking-wider">
                                            {{ $log->created_at->format('d/m') }} - Treino {{ chr(65 + ($loop->index % 3)) }}
                                        </p>
                                        <p class="text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                            {{ $mappedStatus }}
                                        </p>
                                    </div>
                                </div>
                                <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 {{ $statusClass }}/50"></i>
                            </div>
                        @empty
                            <p class="text-[10px] text-zinc-600 font-bold italic text-center py-4">Nenhuma importação anterior encontrada.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Panel (Content area matches the Wizard step) - 8 cols -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- STEP 1: Selecionar Fotos -->
                <div x-show="step === 1" class="bg-zinc-900/50 border border-white/5 rounded-[3rem] p-10 space-y-8 animate-fade-in">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-black text-white uppercase tracking-wider italic">Etapa 1: Selecionar Fichas</h3>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Até 7 imagens</span>
                    </div>

                    <!-- Dropzone -->
                    <div class="border-2 border-dashed border-white/10 hover:border-emerald-500/30 rounded-[2.5rem] p-12 text-center transition-all bg-zinc-950/20 group relative">
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/png, image/jpeg, image/jpg, image/webp" multiple @change="onFilesSelected">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-400 group-hover:text-emerald-400 transition-colors">
                                <i data-lucide="upload-cloud" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Arraste as imagens do treino aqui ou clique para selecionar</p>
                                <p class="text-xs text-zinc-500 mt-1">Suporta PNG, JPG, JPEG e WEBP (Máx. 10MB por arquivo)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Render Selected Images List -->
                    <div x-show="selectedImages.length > 0" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(img, idx) in selectedImages" :key="idx">
                                <div class="bg-zinc-950 p-4 rounded-2xl border border-white/5 flex gap-4 items-center relative group">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-zinc-900 border border-white/10 cursor-pointer" @click="openPreview(img, idx)">
                                        <img :src="img.previewUrl" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest block">Imagem <span x-text="idx + 1"></span></span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded"
                                                  :class="img.qualityScore >= 95 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400'">
                                                Qualidade <span x-text="img.qualityScore + '%'"></span>
                                            </span>
                                            <span class="text-[9px] text-zinc-400 font-bold" x-text="img.qualityStatus"></span>
                                        </div>
                                    </div>
                                    <button @click="removeSelectedImage(idx)" class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-zinc-900 border border-white/5 text-zinc-500 hover:text-red-500 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Action Button to Step 2 -->
                        <div class="flex justify-end pt-4">
                            <button @click="step = 2" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all flex items-center gap-3 uppercase tracking-wider text-xs">
                                Organizar Dias <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Organizar os Dias -->
                <div x-show="step === 2" class="bg-zinc-900/50 border border-white/5 rounded-[3rem] p-10 space-y-8 animate-fade-in">
                    <div class="flex justify-between items-center">
                        <div class="space-y-1">
                            <h3 class="text-lg font-black text-white uppercase tracking-wider italic">Etapa 2: Organizar Dias</h3>
                            <p class="text-xs text-zinc-500">Selecione ou confirme o dia da semana correspondente a cada ficha.</p>
                        </div>
                        <button @click="step = 1" class="text-xs font-black text-zinc-500 hover:text-white uppercase tracking-wider">Voltar</button>
                    </div>

                    <div x-show="validationMessage" x-cloak class="p-5 bg-red-500/10 border border-red-500/25 rounded-3xl flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center shrink-0">
                            <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-xs font-black text-red-300 uppercase tracking-widest" x-text="validationAlertTitle()">Fotos invalidas detectadas</h4>
                            <p class="text-xs text-red-100/80 font-bold leading-relaxed" x-text="validationMessage"></p>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider" x-text="validationAlertHint()">Troque as imagens marcadas em vermelho e inicie o escaneamento novamente.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <template x-for="(img, idx) in selectedImages" :key="idx">
                            <div class="bg-zinc-950 p-5 rounded-[2rem] border flex flex-col gap-4 relative group"
                                 :class="img.status === 'error' ? 'border-red-500/40 shadow-lg shadow-red-500/5' : 'border-white/5'">
                                <div class="flex gap-4 items-center">
                                    <div class="w-20 h-20 rounded-2xl overflow-hidden shrink-0 bg-zinc-900 border border-white/10 cursor-pointer" onclick="window.openNativeImagePreview && window.openNativeImagePreview(this.querySelector('img')?.src)">
                                        <img :src="img.previewUrl" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest block">Imagem <span x-text="idx + 1"></span></span>
                                        <p class="text-xs font-bold text-white mt-0.5 truncate" x-text="img.file.name"></p>
                                        
                                        <!-- Detected status or auto detected message -->
                                        <div class="mt-2 flex items-center gap-1.5 text-[9px] font-bold text-zinc-500">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <span x-text="img.detected_day ? '✓ ' + img.detected_day + ' detectado' : 'Ajuste o dia abaixo'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="img.status === 'error'" x-cloak class="rounded-2xl bg-red-500/10 border border-red-500/20 px-4 py-3">
                                    <p class="text-[10px] font-black text-red-300 uppercase tracking-wider">Imagem nao reconhecida como ficha de treino</p>
                                    <p class="text-[10px] text-red-100/70 mt-1 leading-relaxed" x-text="sanitizeImageReason(img.reason) || 'Substitua por uma foto legivel da ficha de treino.'"></p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[9px] font-black text-zinc-500 uppercase tracking-wider">Dia do Treino</label>
                                    <select x-model="img.day" class="block w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-xs font-bold focus:border-emerald-500/50 focus:ring-0 outline-none">
                                        <option value="segunda-feira">Segunda-feira</option>
                                        <option value="terça-feira">Terça-feira</option>
                                        <option value="quarta-feira">Quarta-feira</option>
                                        <option value="quinta-feira">Quinta-feira</option>
                                        <option value="sexta-feira">Sexta-feira</option>
                                        <option value="sábado">Sábado</option>
                                        <option value="domingo">Domingo</option>
                                    </select>
                                </div>

                                <div class="flex gap-2 justify-end mt-1">
                                    <button type="button" onclick="window.openNativeImagePreview && window.openNativeImagePreview(this.closest('.group')?.querySelector('img')?.src)" class="px-3 py-1.5 bg-zinc-900 border border-white/5 hover:bg-zinc-800 text-zinc-400 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-wider">
                                        Visualizar
                                    </button>
                                    <label class="cursor-pointer px-3 py-1.5 bg-zinc-900 border border-white/5 hover:bg-zinc-800 text-zinc-400 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-wider relative">
                                        Trocar
                                        <input type="file" class="hidden" accept="image/png, image/jpeg, image/jpg, image/webp" @change="replacePreviewImage($event, idx)">
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Action Button to Step 3 — triggers confirmation modal -->
                    <div class="flex justify-end pt-4">
                        <button @click="showScanConfirm = true" class="px-10 py-5 bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-500/10 flex items-center gap-3 uppercase tracking-wider text-xs">
                            <i data-lucide="sparkles" class="w-4 h-4"></i> Iniciar Escaneamento IA
                        </button>
                    </div>
                </div>

                {{-- ✅ Modal de Confirmação de Consumo de Crédito --}}
                <div x-show="showScanConfirm" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="fixed inset-0 z-[200] flex items-center justify-center p-4"
                     @keydown.escape.window="showScanConfirm = false">

                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-md" @click="showScanConfirm = false"></div>

                    <div class="relative bg-zinc-900 border border-purple-500/25 rounded-[2.5rem] p-10 max-w-sm w-full shadow-2xl shadow-purple-500/10 space-y-6">
                        <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-20 h-20 bg-purple-500/10 border border-purple-500/30 rounded-3xl flex items-center justify-center text-3xl shadow-xl">
                            🧠
                        </div>

                        <div class="pt-6 text-center space-y-2">
                            <h3 class="text-lg font-black text-white uppercase tracking-wider">Confirmar Escaneamento</h3>
                            <p class="text-xs text-zinc-500 font-medium">Esta operação utilizará créditos de IA.</p>
                        </div>

                        <div class="bg-zinc-950 border border-white/5 rounded-2xl p-5 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Esta importação:</span>
                                <span class="text-sm font-black text-purple-400">1 crédito IA</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-white/5 pt-3">
                                <span class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Após a conclusão:</span>
                                <span class="text-sm font-black text-white">{{ max(0, $remainingImports - 1) }} de {{ $planMonthlyImports }} importações</span>
                            </div>
                        </div>

                        <p class="text-center text-[10px] text-zinc-600 font-medium">
                            ✅ Você ainda pode importar mais <strong class="text-zinc-400">{{ max(0, $remainingImports - 1) }} treinos</strong> este mês após esta operação.
                        </p>

                        <div class="flex gap-3">
                            <button @click="showScanConfirm = false"
                                class="flex-1 py-3 border border-white/10 text-zinc-400 hover:text-white hover:border-white/20 text-xs font-black uppercase tracking-wider rounded-xl transition-all">
                                Cancelar
                            </button>
                            <button @click="showScanConfirm = false; startAIScan()"
                                class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-blue-500/20">
                                Continuar
                            </button>
                        </div>
                    </div>
                </div>



                <!-- STEP 3: Escaneamento IA (Acompanhamento em tempo real) -->
                <div x-show="step === 3" class="bg-zinc-900/50 border border-white/5 rounded-[3rem] p-10 space-y-10 animate-fade-in relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_30px_rgba(16,185,129,0.8)] animate-neural-scan"></div>
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-white/5 pb-6">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-wider italic flex items-center gap-2">
                                🧠 Orquestrador NexShape AI
                            </h3>
                            <p class="text-xs text-zinc-500 mt-1">Nossos agentes inteligentes estão processando e validando suas imagens em paralelo.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Indicador discreto de consumo --}}
                            <div class="px-3 py-1.5 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-xl text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5">
                                🧠 <span>1 crédito IA</span>
                            </div>
                            <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Ativo
                            </div>
                        </div>
                    </div>

                    <!-- Real-time Queue Bar Visualizer -->
                    <div class="bg-zinc-950/60 p-6 rounded-[2rem] border border-white/5 space-y-4">
                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em] italic">Fila do Orquestrador</span>
                        <div class="grid grid-cols-5 gap-3">
                            <div class="space-y-2">
                                <div class="h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                                <span class="text-[8px] font-black text-white uppercase block text-center">Recebidas</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 rounded-full transition-all duration-500" :class="agentValidation.status === 'success' ? 'bg-emerald-500' : (agentValidation.status === 'running' ? 'bg-blue-500 animate-pulse' : 'bg-zinc-880')"></div>
                                <span class="text-[8px] font-black uppercase block text-center" :class="agentValidation.status !== 'idle' ? 'text-white' : 'text-zinc-600'">Validação</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 rounded-full transition-all duration-500" :class="agentOCR.status === 'success' ? 'bg-emerald-500' : (agentOCR.status === 'running' ? 'bg-blue-500 animate-pulse' : 'bg-zinc-880')"></div>
                                <span class="text-[8px] font-black uppercase block text-center" :class="agentOCR.status !== 'idle' ? 'text-white' : 'text-zinc-600'">OCR</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 rounded-full transition-all duration-500" :class="agentSpecialist.status === 'success' ? 'bg-emerald-500' : (agentSpecialist.status === 'running' ? 'bg-blue-500 animate-pulse' : 'bg-zinc-880')"></div>
                                <span class="text-[8px] font-black uppercase block text-center" :class="agentSpecialist.status !== 'idle' ? 'text-white' : 'text-zinc-600'">Especialista</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 rounded-full transition-all duration-500" :class="agentAuditor.status === 'success' ? 'bg-emerald-500' : (agentAuditor.status === 'running' ? 'bg-blue-500 animate-pulse' : 'bg-zinc-880')"></div>
                                <span class="text-[8px] font-black uppercase block text-center" :class="agentAuditor.status !== 'idle' ? 'text-white' : 'text-zinc-600'">Auditoria</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agents Status Panel -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Validation Agent -->
                        <div class="p-5 rounded-2xl border transition-all duration-300 flex items-start gap-4"
                             :class="agentValidation.status === 'running' ? 'bg-blue-500/5 border-blue-500/20' : (agentValidation.status === 'success' ? 'bg-emerald-500/5 border-emerald-500/10' : 'bg-zinc-950/30 border-white/5')">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 :class="agentValidation.status === 'success' ? 'bg-emerald-500/10 text-emerald-400' : (agentValidation.status === 'running' ? 'bg-blue-500/10 text-blue-400 animate-pulse' : 'bg-zinc-900 text-zinc-600')">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider" :class="agentValidation.status !== 'idle' ? 'text-white' : 'text-zinc-500'" x-text="agentValidation.label"></h4>
                                <p class="text-[10px] text-zinc-500 mt-1" x-text="agentValidation.desc"></p>
                                <span class="text-[9px] font-bold uppercase tracking-widest mt-2 block"
                                      :class="agentValidation.status === 'success' ? 'text-emerald-400' : (agentValidation.status === 'running' ? 'text-blue-400' : 'text-zinc-600')"
                                      x-text="agentValidation.status === 'success' ? '✔ Concluído' : (agentValidation.status === 'running' ? '⏳ Analisando...' : 'Aguardando...')"></span>
                            </div>
                        </div>

                        <!-- OCR Agent -->
                        <div class="p-5 rounded-2xl border transition-all duration-300 flex items-start gap-4"
                             :class="agentOCR.status === 'running' ? 'bg-blue-500/5 border-blue-500/20' : (agentOCR.status === 'success' ? 'bg-emerald-500/5 border-emerald-500/10' : 'bg-zinc-950/30 border-white/5')">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 :class="agentOCR.status === 'success' ? 'bg-emerald-500/10 text-emerald-400' : (agentOCR.status === 'running' ? 'bg-blue-500/10 text-blue-400 animate-pulse' : 'bg-zinc-900 text-zinc-600')">
                                <i data-lucide="scan" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider" :class="agentOCR.status !== 'idle' ? 'text-white' : 'text-zinc-500'" x-text="agentOCR.label"></h4>
                                <p class="text-[10px] text-zinc-500 mt-1" x-text="agentOCR.desc"></p>
                                <span class="text-[9px] font-bold uppercase tracking-widest mt-2 block"
                                      :class="agentOCR.status === 'success' ? 'text-emerald-400' : (agentOCR.status === 'running' ? 'text-blue-400' : 'text-zinc-600')"
                                      x-text="agentOCR.status === 'success' ? '✔ Concluído' : (agentOCR.status === 'running' ? '⏳ Analisando...' : 'Aguardando...')"></span>
                            </div>
                        </div>

                        <!-- Specialist Agent -->
                        <div class="p-5 rounded-2xl border transition-all duration-300 flex items-start gap-4"
                             :class="agentSpecialist.status === 'running' ? 'bg-blue-500/5 border-blue-500/20' : (agentSpecialist.status === 'success' ? 'bg-emerald-500/5 border-emerald-500/10' : 'bg-zinc-950/30 border-white/5')">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 :class="agentSpecialist.status === 'success' ? 'bg-emerald-500/10 text-emerald-400' : (agentSpecialist.status === 'running' ? 'bg-blue-500/10 text-blue-400 animate-pulse' : 'bg-zinc-900 text-zinc-600')">
                                <i data-lucide="dumbbell" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider" :class="agentSpecialist.status !== 'idle' ? 'text-white' : 'text-zinc-500'" x-text="agentSpecialist.label"></h4>
                                <p class="text-[10px] text-zinc-500 mt-1" x-text="agentSpecialist.desc"></p>
                                <span class="text-[9px] font-bold uppercase tracking-widest mt-2 block"
                                      :class="agentSpecialist.status === 'success' ? 'text-emerald-400' : (agentSpecialist.status === 'running' ? 'text-blue-400' : 'text-zinc-600')"
                                      x-text="agentSpecialist.status === 'success' ? '✔ Concluído' : (agentSpecialist.status === 'running' ? '⏳ Analisando...' : 'Aguardando...')"></span>
                            </div>
                        </div>

                        <!-- Auditor Agent -->
                        <div class="p-5 rounded-2xl border transition-all duration-300 flex items-start gap-4"
                             :class="agentAuditor.status === 'running' ? 'bg-blue-500/5 border-blue-500/20' : (agentAuditor.status === 'success' ? 'bg-emerald-500/5 border-emerald-500/10' : 'bg-zinc-950/30 border-white/5')">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                 :class="agentAuditor.status === 'success' ? 'bg-emerald-500/10 text-emerald-400' : (agentAuditor.status === 'running' ? 'bg-blue-500/10 text-blue-400 animate-pulse' : 'bg-zinc-900 text-zinc-600')">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider" :class="agentAuditor.status !== 'idle' ? 'text-white' : 'text-zinc-500'" x-text="agentAuditor.label"></h4>
                                <p class="text-[10px] text-zinc-500 mt-1" x-text="agentAuditor.desc"></p>
                                <span class="text-[9px] font-bold uppercase tracking-widest mt-2 block"
                                      :class="agentAuditor.status === 'success' ? 'text-emerald-400' : (agentAuditor.status === 'running' ? 'text-blue-400' : 'text-zinc-600')"
                                      x-text="agentAuditor.status === 'success' ? '✔ Concluído' : (agentAuditor.status === 'running' ? '⏳ Analisando...' : 'Aguardando...')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Images Progress List -->
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Status Individual das Imagens</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(img, idx) in selectedImages" :key="idx">
                                <div class="bg-zinc-950/80 border border-white/5 p-4 rounded-2xl flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-white/10">
                                            <img :src="img.previewUrl" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white" x-text="'Imagem ' + (idx+1)"></p>
                                            <p class="text-[10px] text-zinc-500 font-medium">
                                                <span x-show="img.status === 'queued'">🟡 Na fila</span>
                                                <span x-show="img.status === 'validating'">🔵 Validando...</span>
                                                <span x-show="img.status === 'ocr'">🟣 Extraindo texto (OCR)...</span>
                                                <span x-show="img.status === 'specialist'">🟢 Agente Especialista...</span>
                                                <span x-show="img.status === 'reviewed'">🟢 Finalizado</span>
                                                <span x-show="img.status === 'error'" class="text-red-500">🔴 Falha: Foto inválida</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <template x-if="img.status === 'reviewed'">
                                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                                        </template>
                                        <template x-if="img.status === 'error'">
                                            <i data-lucide="x" class="w-4 h-4 text-red-500"></i>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- AI Messages Console Area -->
                    <div class="bg-zinc-950 border border-white/5 p-5 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between text-zinc-500">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] italic">Mensagens da IA</span>
                            <span class="text-[8px] font-bold uppercase font-mono">Terminal Live</span>
                        </div>
                        <div class="h-32 overflow-y-auto font-mono text-[10px] text-emerald-400 space-y-2 pr-2 custom-scrollbar">
                            <template x-for="(msg, mIdx) in aiMessages" :key="mIdx">
                                <div class="flex items-start gap-2">
                                    <span class="text-emerald-655 shrink-0">&gt;</span>
                                    <span x-text="msg"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Resumo da Importacao -->
                <div x-show="step === 4" class="bg-zinc-900 border border-white/10 rounded-[3rem] p-8 md:p-10 space-y-8 animate-fade-in shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] pointer-events-none"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <p class="text-emerald-400 text-[10px] font-black uppercase tracking-[0.3em]">NexShape AI</p>
                            <h3 class="text-2xl md:text-3xl font-black text-white tracking-tighter uppercase italic leading-none">Importacao concluida</h3>
                            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.25em]">Revise o resumo antes de salvar o treino</p>
                        </div>
                        <div class="flex items-center gap-3 px-5 py-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">Analise concluida</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 relative">
                        <div class="xl:col-span-5 space-y-6">
                            <div class="bg-zinc-950/50 border border-white/5 rounded-3xl p-6 space-y-5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.25em]">Confianca da IA</span>
                                    <span class="text-3xl font-black text-emerald-400 tabular-nums" x-text="averageConfidence() + '%'"></span>
                                </div>
                                <div class="h-2 bg-zinc-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all" :style="'width: ' + averageConfidence() + '%'"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <template x-for="stat in importStats()" :key="stat.label">
                                    <div class="bg-zinc-950/50 border border-white/5 rounded-2xl p-5">
                                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest" x-text="stat.label"></span>
                                        <p class="text-2xl font-black mt-1" :class="stat.color" x-text="stat.value"></p>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-zinc-950/50 border border-white/5 rounded-3xl p-6 space-y-4">
                                <h4 class="text-[10px] font-black text-white uppercase tracking-[0.25em]">Dias encontrados</h4>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="day in daysFound()" :key="day">
                                        <span class="px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-black text-emerald-300 uppercase tracking-wider" x-text="'OK ' + formatDay(day)"></span>
                                    </template>
                                </div>
                            </div>

                            <template x-if="reviewIssues().length > 0">
                                <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 space-y-4">
                                    <div class="flex items-center gap-3 text-amber-400">
                                        <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                                        <span class="text-xs font-black uppercase tracking-widest">Exercicios que precisam de revisao</span>
                                    </div>
                                    <template x-for="issue in reviewIssues()" :key="issue.key">
                                        <div class="flex items-center justify-between gap-4 bg-zinc-950/50 rounded-2xl border border-white/5 p-4">
                                            <div>
                                                <p class="text-xs font-black text-white" x-text="issue.exercise"></p>
                                                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider" x-text="issue.context"></p>
                                            </div>
                                            <span class="text-xs font-black text-amber-300 tabular-nums" x-text="issue.confidence + '%'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <div class="bg-zinc-950/50 border border-white/5 rounded-3xl p-6 space-y-3">
                                <h4 class="text-[10px] font-black text-white uppercase tracking-[0.25em]">Analise da IA</h4>
                                <p class="text-sm text-zinc-400 leading-relaxed font-medium" x-text="aiOpinion()"></p>
                            </div>

                            <template x-if="importObservations().length > 0">
                                <div class="bg-zinc-950/50 border border-white/5 rounded-3xl p-6 space-y-3">
                                    <h4 class="text-[10px] font-black text-white uppercase tracking-[0.25em]">Observacoes</h4>
                                    <template x-for="obs in importObservations()" :key="obs">
                                        <p class="text-xs text-zinc-400 font-bold flex gap-2"><span class="text-amber-400">!</span><span x-text="obs"></span></p>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div class="xl:col-span-7 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6 bg-zinc-950/50 rounded-3xl border border-white/5">
                                <label class="space-y-3">
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] ml-2 italic">Identificacao da Rotina</span>
                                    <input type="text" x-model="workoutName" placeholder="Ex: Treino A - Peito e Triceps" class="w-full bg-zinc-900 border border-white/10 rounded-2xl px-6 py-5 text-white text-sm font-bold focus:border-emerald-500/50 focus:ring-0 transition-all outline-none">
                                </label>
                                <div class="flex items-end pb-1">
                                    <div class="px-6 py-5 bg-zinc-900/50 border border-dashed border-white/10 rounded-2xl w-full flex items-center justify-between">
                                        <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest italic">Total de Exercicios</span>
                                        <span class="text-xl font-black text-emerald-500 italic" x-text="exercises.length"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 overflow-x-auto pb-1">
                                <template x-for="day in daysFound()" :key="day">
                                    <button @click="activeReviewDay = day" class="shrink-0 px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-wider transition-all" :class="activeReviewDay === day ? 'bg-emerald-500 text-zinc-950 border-emerald-500' : 'bg-zinc-950/50 text-zinc-500 border-white/5 hover:text-white'" x-text="formatDay(day)"></button>
                                </template>
                            </div>

                            <div class="space-y-4">
                                <template x-for="ex in filteredExercises()" :key="exerciseOriginalIndex(ex)">
                                    <div class="group bg-zinc-950/40 hover:bg-zinc-950 border border-white/5 rounded-3xl p-6 transition-all duration-300" :class="exerciseNeedsReview(ex) ? 'border-amber-500/30 bg-amber-500/5' : 'hover:border-emerald-500/30'">
                                        <div class="flex flex-col lg:flex-row gap-6">
                                            <div class="flex-1 space-y-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-zinc-700 font-black text-xs italic" x-text="exerciseOriginalIndex(ex) + 1"></div>
                                                    <input type="text" x-model="ex.nome_exercicio" class="flex-1 bg-transparent border-none p-0 text-lg font-black text-white italic focus:ring-0 placeholder-zinc-800" placeholder="Nome do exercicio" :class="exerciseNeedsReview(ex) ? 'border-b-2 border-amber-500/60' : ''">
                                                    <select x-model="ex.day" class="bg-zinc-900 border border-white/10 rounded-xl px-3 py-1.5 text-white text-[10px] font-black uppercase tracking-wider focus:border-emerald-500/50 focus:ring-0 outline-none">
                                                        <option value="">Sem dia</option>
                                                        <option value="segunda-feira">Segunda</option>
                                                        <option value="terça-feira">Terca</option>
                                                        <option value="quarta-feira">Quarta</option>
                                                        <option value="quinta-feira">Quinta</option>
                                                        <option value="sexta-feira">Sexta</option>
                                                        <option value="sábado">Sabado</option>
                                                        <option value="domingo">Domingo</option>
                                                    </select>
                                                </div>
                                                <textarea x-model="ex.observacoes" placeholder="Adicionar observacoes..." class="w-full bg-transparent border-none p-0 text-[11px] text-zinc-600 font-bold italic focus:ring-0 resize-none" rows="1"></textarea>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <div class="grid grid-cols-3 gap-4">
                                                    <div class="space-y-2">
                                                        <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Series</span>
                                                        <input type="text" x-model="ex.series" class="w-16 bg-zinc-900 border rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0" :class="!ex.series || ex.confidence_scores?.series < 0.80 ? 'border-amber-500/50' : 'border-white/5'">
                                                    </div>
                                                    <div class="space-y-2">
                                                        <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Reps</span>
                                                        <input type="text" x-model="ex.repeticoes" class="w-16 bg-zinc-900 border rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0" :class="!ex.repeticoes || ex.confidence_scores?.repeticoes < 0.80 ? 'border-amber-500/50' : 'border-white/5'">
                                                    </div>
                                                    <div class="space-y-2">
                                                        <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Carga</span>
                                                        <input type="text" x-model="ex.carga" class="w-16 bg-zinc-900 border rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0" :class="!ex.carga || ex.confidence_scores?.carga < 0.80 ? 'border-amber-500/50' : 'border-white/5'">
                                                    </div>
                                                </div>
                                                <button @click="removeExercise(exerciseOriginalIndex(ex))" class="w-12 h-12 rounded-2xl bg-zinc-900 border border-white/5 text-zinc-700 hover:text-red-500 hover:bg-red-500/10 transition-all flex items-center justify-center">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="filteredExercises().length === 0" class="p-10 rounded-3xl border border-dashed border-white/10 text-center">
                                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Nenhum exercicio neste dia.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="text-center md:text-left">
                            <p class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] italic">Conferencia Humana</p>
                            <p class="text-[11px] text-zinc-500 font-bold italic">Ao salvar, este treino sera integrado ao seu calendario evolutivo.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <button @click="clear()" class="px-8 py-5 bg-zinc-950 border border-white/10 hover:border-red-500/30 text-zinc-400 hover:text-white font-black rounded-2xl uppercase tracking-wider text-xs transition-all">
                                Cancelar
                            </button>
                            <button @click="$el.closest('[x-data]').scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-8 py-5 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-2xl uppercase tracking-wider text-xs transition-all">
                                Editar Importacao
                            </button>
                            <button 
                                @click="saveImport()" 
                                :disabled="isSaving || !workoutName"
                                class="px-10 py-5 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-4 group/save"
                            >
                                <template x-if="!isSaving">
                                    <div class="flex items-center gap-4">
                                        <span class="text-xs uppercase tracking-[0.2em]">Aceitar e Salvar Treino</span>
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

                <!-- STEP 4: Revisão antiga mantida como fallback visual oculto -->
                <div x-show="false" class="bg-zinc-900 border border-white/10 rounded-[3rem] p-10 space-y-10 animate-fade-in shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] pointer-events-none"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic leading-none">Dados Extraídos</h3>
                            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em]">Refine os parâmetros detectados pela rede neural</p>
                        </div>
                        <div class="flex items-center gap-3 px-5 py-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">Análise Concluída</span>
                        </div>
                    </div>

                    <!-- Encontrado Counter Details -->
                    <div class="bg-zinc-950/50 p-5 rounded-2xl border border-white/5 flex flex-wrap gap-6 items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span class="text-xs font-black text-white uppercase tracking-wider">Detecções:</span>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-bold text-zinc-400">
                            <span>✔ <span x-text="exercises.length"></span> exercícios</span>
                            <span>✔ <span x-text="exercises.reduce((acc, curr) => acc + (parseInt(curr.series) || 3), 0)"></span> séries</span>
                            <span>✔ <span x-text="exercises.reduce((acc, curr) => acc + (parseInt(curr.repeticoes) || 12), 0)"></span> reps</span>
                            <span class="text-emerald-400">✔ 98% confiança</span>
                        </div>
                    </div>

                    <!-- Audit Warnings -->
                    <template x-if="auditWarnings && auditWarnings.length > 0">
                        <div class="p-6 bg-amber-500/10 border border-amber-500/20 rounded-3xl space-y-3 animate-fade-in">
                            <div class="flex items-center gap-3 text-amber-500">
                                <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                                <span class="text-xs font-black uppercase tracking-widest">Alertas da Auditoria de IA</span>
                            </div>
                            <ul class="space-y-2 text-xs text-zinc-400 font-medium">
                                <template x-for="(warn, wIdx) in auditWarnings" :key="wIdx">
                                    <li class="flex items-start gap-2">
                                        <span class="text-amber-500 mt-0.5">•</span>
                                        <div>
                                            <span class="font-black text-white uppercase tracking-wider text-[10px]" x-show="warn.day" x-text="'[' + warn.day + '] '"></span>
                                            <span class="font-black text-white" x-show="warn.exercise_name" x-text="warn.exercise_name + ': '"></span>
                                            <span x-text="warn.message"></span>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

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

                        <!-- Exercises Grid -->
                        <div class="space-y-4">
                            <template x-for="(ex, index) in exercises" :key="index">
                                <div class="group bg-zinc-950/40 hover:bg-zinc-950 border border-white/5 hover:border-emerald-500/30 rounded-3xl p-6 transition-all duration-300">
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        <!-- Exercise Header -->
                                        <div class="flex-1 space-y-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-zinc-700 font-black text-xs italic" x-text="index + 1"></div>
                                                <input type="text" x-model="ex.nome_exercicio" class="flex-1 bg-transparent border-none p-0 text-lg font-black text-white italic focus:ring-0 placeholder-zinc-800" placeholder="Nome do exercício" :class="!ex.nome_exercicio ? 'border-b-2 border-amber-500/50' : (ex.confidence_scores?.nome_exercicio < 0.80 ? 'border-b-2 border-orange-500' : '')">
                                                <select x-model="ex.day" class="bg-zinc-900 border border-white/10 rounded-xl px-3 py-1.5 text-white text-[10px] font-black uppercase tracking-wider focus:border-emerald-500/50 focus:ring-0 outline-none">
                                                    <option value="">Sem dia</option>
                                                    <option value="segunda-feira">Segunda</option>
                                                    <option value="terça-feira">Terça</option>
                                                    <option value="quarta-feira">Quarta</option>
                                                    <option value="quinta-feira">Quinta</option>
                                                    <option value="sexta-feira">Sexta</option>
                                                    <option value="sábado">Sábado</option>
                                                    <option value="domingo">Domingo</option>
                                                </select>
                                            </div>
                                            <textarea x-model="ex.observacoes" placeholder="Adicionar observações..." class="w-full bg-transparent border-none p-0 text-[11px] text-zinc-600 font-bold italic focus:ring-0 resize-none" rows="1"></textarea>
                                        </div>

                                        <!-- Params -->
                                        <div class="flex items-center gap-4">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div class="space-y-2">
                                                    <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Séries</span>
                                                    <input type="text" x-model="ex.series" class="w-16 bg-zinc-900 border rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0" :class="!ex.series ? 'border-amber-500/50' : (ex.confidence_scores?.series < 0.80 ? 'border-orange-500' : 'border-white/5')">
                                                </div>
                                                <div class="space-y-2">
                                                    <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Reps</span>
                                                    <input type="text" x-model="ex.repeticoes" class="w-16 bg-zinc-900 border rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0" :class="!ex.repeticoes ? 'border-amber-500/50' : (ex.confidence_scores?.repeticoes < 0.80 ? 'border-orange-500' : 'border-white/5')">
                                                </div>
                                                <div class="space-y-2">
                                                    <span class="block text-[8px] font-black text-zinc-700 uppercase tracking-widest text-center">Carga</span>
                                                    <input type="text" x-model="ex.carga" class="w-16 bg-zinc-900 border rounded-xl py-3 text-center text-white font-black italic focus:border-emerald-500/30 focus:ring-0" :class="!ex.carga ? 'border-amber-500/50' : (ex.confidence_scores?.carga < 0.80 ? 'border-orange-500' : 'border-white/5')">
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

                    <!-- Finalizer area -->
                    <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8">
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
                                    <span class="text-xs uppercase tracking-[0.2em]">Salvar Importação</span>
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

                <!-- STEP 5: Success Screen -->
                <div x-show="step === 5" class="bg-zinc-900/50 border border-white/5 rounded-[3rem] p-12 text-center space-y-8 animate-fade-in relative overflow-hidden">
                    <div class="absolute -top-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none"></div>
                    
                    <div class="flex flex-col items-center gap-6">
                        <div class="w-24 h-24 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <i data-lucide="party-popper" class="w-12 h-12"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-3xl font-black text-white tracking-tighter uppercase italic">Treino Importado com Sucesso!</h3>
                            <p class="text-zinc-400 text-xs max-w-md mx-auto">Treino disponivel para edicao. A importacao foi salva no sistema evolutivo.</p>
                        </div>
                    </div>

                    <!-- Final Stats Details -->
                    <div class="bg-zinc-950/60 max-w-md mx-auto p-6 rounded-2xl border border-white/5 space-y-4 text-left">
                        <div class="flex items-center justify-between border-b border-white/5 pb-2 text-xs font-bold text-zinc-500">
                            <span>Métrica</span>
                            <span>Valor Importado</span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-zinc-400">Dias de Treino</span>
                            <span class="text-white" x-text="daysFound().length + ' dias'"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-zinc-400">Exercícios Reconhecidos</span>
                            <span class="text-white" x-text="exercises.length"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-zinc-400">Taxa de Confiança</span>
                            <span class="text-emerald-400" x-text="averageConfidence() + '% (Excelente)'"></span>
                        </div>
                    </div>

                    {{-- Mensagem positiva de créditos restantes --}}
                    <div class="max-w-md mx-auto px-5 py-4 rounded-2xl border border-purple-500/20 bg-purple-500/5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🧠</span>
                            <div>
                                <p class="text-xs font-black text-white">Crédito utilizado: <span class="text-purple-400">1 importação</span></p>
                                <p class="text-[10px] text-zinc-500 mt-0.5">
                                    Você ainda pode importar mais
                                    <strong class="text-zinc-300">{{ max(0, $remainingImports - 1) }} treinos</strong>
                                    este mês.
                                </p>
                            </div>
                        </div>
                        <span class="text-xl font-black text-purple-300 tabular-nums shrink-0">{{ max(0, $remainingImports - 1) }}/{{ $planMonthlyImports }}</span>
                    </div>


                        <button @click="step = 4" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-xl uppercase tracking-wider text-xs">
                            Revisar Dados
                        </button>
                        <a :href="successRedirectUrl" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-xl uppercase tracking-wider text-xs shadow-lg shadow-emerald-500/20">
                            Acessar Treino
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            <div class="lg:col-span-5 space-y-8">
                <x-plan-lock>
                    {{ $access['message'] ?? 'Importar treino por foto e IA faz parte do plano Premium.' }}
                </x-plan-lock>
            </div>
        </div>
    @endif

    <!-- Photo Zoom & Rotation Modal (Preview Maior) -->
    <div 
        x-show="previewModal.show" 
        class="fixed inset-0 z-[300] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-zinc-950/90 backdrop-blur-md" @click="previewModal.show = false"></div>
        
        <!-- Content Card -->
        <div 
            class="relative w-full max-w-4xl bg-zinc-900 border border-white/10 rounded-[2.5rem] p-6 shadow-2xl overflow-hidden transform transition-all flex flex-col h-[85vh]"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="scale-95 translate-y-4"
            x-transition:enter-end="scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="scale-100 translate-y-0"
            x-transition:leave-end="scale-95 translate-y-4"
        >
            <div class="flex items-center justify-between pb-4 border-b border-white/5 shrink-0">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Visualizador de Ficha</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5" x-text="'Imagem ' + (previewModal.index + 1)"></p>
                </div>
                <button @click="previewModal.show = false" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Image Canvas -->
            <div class="flex-1 min-h-0 bg-zinc-950 rounded-2xl my-4 overflow-hidden relative flex items-center justify-center p-4">
                <img :src="previewModal.url" 
                     class="max-w-full max-h-full object-contain transition-all duration-300"
                     :style="'transform: rotate(' + previewModal.rotation + 'deg) scale(' + (previewModal.zoom / 100) + ')'">
            </div>

            <!-- Controls and tools -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-white/5 pt-4 shrink-0">
                <div class="flex items-center gap-2">
                    <button @click="zoomOut()" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center" title="Zoom Out">
                        <i data-lucide="zoom-out" class="w-4 h-4"></i>
                    </button>
                    <span class="text-[10px] font-mono font-bold text-zinc-400 px-2" x-text="previewModal.zoom + '%'">100%</span>
                    <button @click="zoomIn()" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center" title="Zoom In">
                        <i data-lucide="zoom-in" class="w-4 h-4"></i>
                    </button>
                    <button @click="rotatePreview()" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center ml-2" title="Rotacionar 90°">
                        <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <label class="cursor-pointer px-5 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-black uppercase tracking-wider relative flex items-center gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i> Trocar imagem
                        <input type="file" class="hidden" accept="image/png, image/jpeg, image/jpg, image/webp" @change="replacePreviewImage">
                    </label>
                    <button @click="previewModal.show = false" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-xl text-xs font-black uppercase tracking-wider">
                        Concluído
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Native Image Preview Modal -->
    <div id="native-image-preview-modal" class="fixed inset-0 z-[380] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-zinc-950/90 backdrop-blur-md" onclick="window.closeNativeImagePreview && window.closeNativeImagePreview()"></div>
        <div class="relative w-full max-w-4xl bg-zinc-900 border border-white/10 rounded-[2.5rem] p-6 shadow-2xl overflow-hidden flex flex-col h-[85vh]">
            <div class="flex items-center justify-between pb-4 border-b border-white/5 shrink-0">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Visualizador de Ficha</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5">Pre-visualizacao da imagem</p>
                </div>
                <button type="button" onclick="window.closeNativeImagePreview && window.closeNativeImagePreview()" class="w-10 h-10 rounded-xl bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="flex-1 min-h-0 bg-zinc-950 rounded-2xl my-4 overflow-hidden relative flex items-center justify-center p-4">
                <img id="native-image-preview-img" src="" alt="Preview da imagem" class="max-w-full max-h-full object-contain">
            </div>
            <div class="flex items-center justify-end border-t border-white/5 pt-4 shrink-0">
                <button type="button" onclick="window.closeNativeImagePreview && window.closeNativeImagePreview()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-xl text-xs font-black uppercase tracking-wider">
                    Concluido
                </button>
            </div>
        </div>
    </div>

    <!-- Clear History Premium Modal -->
    <div
        id="clear-history-modal"
        x-show="clearHistoryModal.show"
        class="fixed inset-0 z-[360] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div class="absolute inset-0 bg-zinc-950/85 backdrop-blur-md" onclick="window.closeClearHistoryModal && window.closeClearHistoryModal()"></div>

        <div class="relative w-full max-w-md bg-zinc-900 border border-red-500/20 rounded-[2.5rem] p-8 shadow-2xl shadow-red-500/10 overflow-hidden">
            <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full blur-[80px] pointer-events-none bg-red-500/10"></div>
            <div class="relative space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 bg-red-500/10 text-red-400 border border-red-500/20">
                        <i data-lucide="trash-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Limpar Historico</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Acao irreversivel</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-zinc-400 font-bold leading-relaxed">
                    Deseja realmente limpar todo o historico de importacoes por foto? Esta acao removera seus registros de importacao evolutivos.
                </p>

                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="button"
                        onclick="window.closeClearHistoryModal && window.closeClearHistoryModal()"
                        class="flex-1 py-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white text-xs font-black uppercase tracking-wider rounded-2xl transition-all border border-white/5 disabled:opacity-50"
                    >
                        Cancelar
                    </button>
                    <form method="POST" action="{{ route('progression.plans.clear-history') }}" class="flex-1">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-4 bg-red-500 hover:bg-red-400 text-white text-xs font-black uppercase tracking-wider rounded-2xl transition-all shadow-lg shadow-red-500/10"
                        >
                            Limpar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm / Error Modals -->
    <div 
        x-show="confirmModal.show || errorModal.show" 
        class="fixed inset-0 z-[350] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-md" @click="if(confirmModal.show) { confirmModal.resolve(false); confirmModal.show = false; } else { errorModal.show = false; }"></div>
        
        <div 
            class="relative w-full max-w-md bg-zinc-900 border border-emerald-500/20 rounded-[2.5rem] p-8 shadow-2xl overflow-hidden transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="scale-95 translate-y-4"
            x-transition:enter-end="scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="scale-100 translate-y-0"
            x-transition:leave-end="scale-95 translate-y-4"
        >
            <!-- Premium Glow Background -->
            <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full blur-[80px] pointer-events-none animate-pulse" :class="errorModal.show ? 'bg-red-500/10' : 'bg-emerald-500/10'"></div>

            <div class="relative space-y-6">
                <!-- Icon & Title -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-lg" 
                         :class="errorModal.show ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'">
                        <template x-if="errorModal.show">
                            <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                        </template>
                        <template x-if="confirmModal.show">
                            <i data-lucide="crown" class="w-6 h-6"></i>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest" x-text="confirmModal.show ? confirmModal.title : errorModal.title"></h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest" x-text="errorModal.show ? 'Aviso do Sistema' : 'Recurso NexShape Premium'"></p>
                        </div>
                    </div>
                </div>

                <!-- Message -->
                <p class="text-xs text-zinc-400 font-bold leading-relaxed whitespace-pre-line" x-text="confirmModal.show ? confirmModal.message : errorModal.message"></p>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <template x-if="confirmModal.show">
                        <div class="flex items-center gap-3 w-full">
                            <button 
                                @click="confirmModal.resolve(false); confirmModal.show = false;" 
                                class="flex-1 py-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white text-xs font-black uppercase tracking-wider rounded-2xl transition-all border border-white/5"
                            >
                                Cancelar
                            </button>
                            <button 
                                @click="confirmModal.resolve(true); confirmModal.show = false;" 
                                class="flex-1 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-zinc-950 text-xs font-black uppercase tracking-wider rounded-2xl transition-all shadow-lg shadow-emerald-500/25"
                            >
                                Confirmar
                            </button>
                        </div>
                    </template>
                    <template x-if="errorModal.show">
                        <button 
                            @click="errorModal.show = false" 
                            class="w-full py-4 bg-red-500 hover:bg-red-400 text-white text-xs font-black uppercase tracking-wider rounded-2xl transition-all shadow-lg shadow-red-500/10"
                        >
                            Entendido
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
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
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
    }
</style>

@push('scripts')
<script>
    window.openNativeImagePreview = function (src) {
        if (!src) {
            return;
        }

        const modal = document.getElementById('native-image-preview-modal');
        const image = document.getElementById('native-image-preview-img');
        if (!modal || !image) {
            return;
        }

        image.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (window.lucide) {
            window.lucide.createIcons();
        }
    };

    window.closeNativeImagePreview = function () {
        const modal = document.getElementById('native-image-preview-modal');
        const image = document.getElementById('native-image-preview-img');
        if (!modal || !image) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        image.src = '';
    };

    window.openClearHistoryModal = function () {
        const modal = document.getElementById('clear-history-modal');
        if (modal) {
            modal.style.display = 'flex';
            modal.removeAttribute('x-cloak');
        }
    };

    window.closeClearHistoryModal = function () {
        const modal = document.getElementById('clear-history-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('workoutImporter', () => ({
            step: 1,
            state: 'IDLE', 
            uuid: null,
            selectedImages: [], // { file, previewUrl, day, is_workout, confidence, reason, image_path, image_id, status, qualityScore, qualityStatus }
            isProcessing: false,
            isSaving: false,
            isClearingHistory: false,
            processingStep: 'Inicializando Rede...',
            exercises: [],
            workoutName: '',
            auditWarnings: [],
            successRedirectUrl: '',
            activeReviewDay: '',
            processingStartedAt: null,
            processingSeconds: 0,
            showScanConfirm: false, // Controla modal de confirmação de consumo de crédito
            validationMessage: '',
            
            // Preview Modal
            previewModal: {
                show: false,
                url: '',
                rotation: 0,
                zoom: 100,
                index: null
            },
            clearHistoryModal: {
                show: false,
            },

            // Live Agents State
            agentValidation: { status: 'idle', label: 'Agente de Validação', desc: 'Conferindo se as imagens são fichas de treino.' },
            agentOCR: { status: 'idle', label: 'Agente OCR', desc: 'Extraindo textos e tabelas.' },
            agentSpecialist: { status: 'idle', label: 'Agente Especialista', desc: 'Interpretando exercícios, séries e repetições.' },
            agentAuditor: { status: 'idle', label: 'Agente Auditor', desc: 'Verificando inconsistências e campos com baixa confiança.' },

            // Terminal Logs
            aiMessages: [],
            
            confirmModal: {
                show: false,
                title: '',
                message: '',
                resolve: null
            },
            
            errorModal: {
                show: false,
                title: '',
                message: ''
            },
            
            showConfirm(title, message) {
                this.confirmModal.title = title;
                this.confirmModal.message = message;
                this.confirmModal.show = true;
                return new Promise((resolve) => {
                    this.confirmModal.resolve = resolve;
                });
            },
            
            showError(msg) {
                this.showScanConfirm = false;
                this.confirmModal.show = false;

                this.errorModal.title = 'Ops! Algo deu errado';
                this.errorModal.message = msg;
                this.errorModal.show = true;

                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: msg, type: 'error' }
                }));
            },

            showSuccess(msg) {
                if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success(msg);
                } else {
                    console.log(msg);
                }
            },

            daysFound() {
                const days = this.exercises
                    .map(ex => ex.day)
                    .filter(day => day && String(day).trim() !== '');
                return [...new Set(days)];
            },

            formatDay(day) {
                const labels = {
                    'segunda-feira': 'Segunda-feira',
                    'terça-feira': 'Terca-feira',
                    'quarta-feira': 'Quarta-feira',
                    'quinta-feira': 'Quinta-feira',
                    'sexta-feira': 'Sexta-feira',
                    'sábado': 'Sabado',
                    'domingo': 'Domingo'
                };
                return labels[day] || day || 'Sem dia';
            },

            numericValue(value, fallback = 0) {
                const match = String(value ?? '').match(/\d+/);
                return match ? parseInt(match[0], 10) : fallback;
            },

            totalSets() {
                return this.exercises.reduce((acc, ex) => acc + this.numericValue(ex.series, 3), 0);
            },

            totalReps() {
                return this.exercises.reduce((acc, ex) => {
                    return acc + (this.numericValue(ex.series, 3) * this.numericValue(ex.repeticoes, 12));
                }, 0);
            },

            fieldConfidence(ex, field) {
                const value = ex.confidence_scores?.[field];
                if (value === undefined || value === null || value === '') {
                    return null;
                }
                const numeric = Number(value);
                return numeric <= 1 ? Math.round(numeric * 100) : Math.round(numeric);
            },

            averageConfidence() {
                const values = [];
                this.exercises.forEach(ex => {
                    ['nome_exercicio', 'series', 'repeticoes', 'carga'].forEach(field => {
                        const confidence = this.fieldConfidence(ex, field);
                        if (confidence !== null) values.push(confidence);
                    });
                });

                if (values.length === 0) {
                    const imageValues = this.selectedImages
                        .map(img => Number(img.confidence))
                        .filter(value => !Number.isNaN(value) && value > 0)
                        .map(value => value <= 1 ? value * 100 : value);

                    if (imageValues.length === 0) return 96;
                    return Math.round(imageValues.reduce((sum, value) => sum + value, 0) / imageValues.length);
                }

                return Math.round(values.reduce((sum, value) => sum + value, 0) / values.length);
            },

            exerciseNeedsReview(ex) {
                if (!ex.nome_exercicio || !ex.series || !ex.repeticoes || !ex.carga) return true;
                return ['nome_exercicio', 'series', 'repeticoes', 'carga'].some(field => {
                    const confidence = this.fieldConfidence(ex, field);
                    return confidence !== null && confidence < 80;
                });
            },

            reviewIssues() {
                const issues = [];
                this.exercises.forEach((ex, index) => {
                    ['nome_exercicio', 'series', 'repeticoes', 'carga'].forEach(field => {
                        const missing = !ex[field];
                        const confidence = this.fieldConfidence(ex, field);
                        if (missing || (confidence !== null && confidence < 80)) {
                            issues.push({
                                key: `${index}-${field}`,
                                exercise: ex.nome_exercicio || `Exercicio ${index + 1}`,
                                context: `${this.formatDay(ex.day)} - ${field.replace('_', ' ')}`,
                                confidence: missing ? 0 : confidence
                            });
                        }
                    });
                });
                return issues;
            },

            importStats() {
                return [
                    { label: 'Dias importados', value: this.daysFound().length, color: 'text-white' },
                    { label: 'Exercicios', value: this.exercises.length, color: 'text-white' },
                    { label: 'Series', value: this.totalSets(), color: 'text-white' },
                    { label: 'Repeticoes', value: this.totalReps(), color: 'text-white' },
                    { label: 'Campos com duvida', value: this.reviewIssues().length, color: this.reviewIssues().length ? 'text-amber-400' : 'text-emerald-400' },
                    { label: 'Processamento', value: `${this.processingSeconds}s`, color: 'text-white' }
                ];
            },

            importObservations() {
                const observations = [];
                const missingLoad = this.exercises.filter(ex => !ex.carga || String(ex.carga).trim() === '').length;
                const missingRest = this.exercises.filter(ex => !ex.intervalo || String(ex.intervalo).trim() === '').length;

                if (missingLoad > 0) observations.push(`Nao foi encontrada carga em ${missingLoad} exercicios.`);
                if (missingRest > 0) observations.push('Descanso nao informado em parte dos exercicios.');
                this.auditWarnings.forEach(warn => observations.push(warn.message || String(warn)));

                return [...new Set(observations)];
            },

            aiOpinion() {
                const days = this.daysFound().length;
                const exercises = this.exercises.length;
                const sets = this.totalSets();
                const confidence = this.averageConfidence();
                const issues = this.reviewIssues().length;

                if (issues > 0) {
                    return `Foram identificados ${days} dias de treino, totalizando ${exercises} exercicios e ${sets} series. A leitura apresentou ${confidence}% de confianca. ${issues} campos exigem revisao manual por estarem incompletos ou com baixa confianca.`;
                }

                return `Foram identificados ${days} dias de treino, totalizando ${exercises} exercicios e ${sets} series. A leitura apresentou ${confidence}% de confianca e o treino esta pronto para ser salvo.`;
            },

            filteredExercises() {
                if (!this.activeReviewDay) {
                    this.activeReviewDay = this.daysFound()[0] || '';
                }
                return this.exercises.filter(ex => ex.day === this.activeReviewDay);
            },

            exerciseOriginalIndex(exercise) {
                return this.exercises.indexOf(exercise);
            },
            
            // Preview Controls
            openPreview(img, index) {
                if (!img || !img.previewUrl) {
                    this.showError('Nao foi possivel abrir a pre-visualizacao desta imagem.');
                    return;
                }

                this.previewModal = {
                    show: true,
                    url: img.previewUrl,
                    rotation: 0,
                    zoom: 100,
                    index: index,
                };
                
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },

            sanitizeImageReason(reason) {
                if (!reason) {
                    return '';
                }

                const text = String(reason);
                const lowered = text.toLowerCase();
                const sensitiveTerms = ['api key', 'apikey', 'api_key', 'openai', 'sk-', 'token', 'billing', 'quota'];

                if (sensitiveTerms.some(term => lowered.includes(term))) {
                    return 'Erro ao validar a imagem no servico de IA. Verifique a configuracao da chave da OpenAI no ambiente e tente novamente.';
                }

                return text;
            },

            validationAlertTitle() {
                if (this.state === 'AI_SERVICE_ERROR') {
                    return 'Erro no servico de IA';
                }

                if (this.state === 'EXTRACTION_FAILED') {
                    return 'Falha na leitura da ficha';
                }

                return 'Fotos invalidas detectadas';
            },

            validationAlertHint() {
                if (this.state === 'AI_SERVICE_ERROR') {
                    return 'As imagens nao foram rejeitadas. Corrija a configuracao e tente novamente.';
                }

                if (this.state === 'EXTRACTION_FAILED') {
                    return 'A foto parece valida. Reprocesse ou envie uma imagem mais aproximada da tabela se o erro persistir.';
                }

                return 'Troque as imagens marcadas em vermelho e inicie o escaneamento novamente.';
            },

            rotatePreview() {
                this.previewModal.rotation = (this.previewModal.rotation + 90) % 360;
            },

            zoomIn() {
                this.previewModal.zoom = Math.min(200, this.previewModal.zoom + 25);
            },

            zoomOut() {
                this.previewModal.zoom = Math.max(50, this.previewModal.zoom - 25);
            },

            async replacePreviewImage(e, explicitIndex = null) {
                const index = explicitIndex !== null ? explicitIndex : this.previewModal.index;
                if (index === null || !e.target.files[0]) return;
                
                const file = e.target.files[0];
                const img = this.selectedImages[index];
                
                // If we already initialized the session, call substitution endpoint
                if (this.uuid && img.image_id) {
                    await this.substituteImage(e, img.image_id);
                    // Update preview URL in modal
                    if (this.previewModal.index === index) {
                        this.previewModal.url = this.selectedImages[index].previewUrl;
                    }
                } else {
                    // Update locally
                    const qualityScore = file.size > 2000000 ? 98 : (file.size > 500000 ? 94 : 88);
                    const qualityStatus = qualityScore >= 95 ? 'Excelente' : (qualityScore >= 90 ? 'Boa' : 'Regular');

                    img.file = file;
                    if (img.previewUrl && img.previewUrl.startsWith('blob:')) {
                        URL.revokeObjectURL(img.previewUrl);
                    }
                    img.previewUrl = URL.createObjectURL(file);
                    img.qualityScore = qualityScore;
                    img.qualityStatus = qualityStatus;
                    this.previewModal.url = img.previewUrl;
                    this.showSuccess('Imagem atualizada com sucesso.');
                }
            },

            async onFilesSelected(e) {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;

                if (this.selectedImages.length + files.length > 7) {
                    this.showError('Você pode selecionar no máximo 7 imagens.');
                    e.target.value = '';
                    return;
                }

                const daysList = ['segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado', 'domingo'];

                for (let file of files) {
                    const ext = file.name.split('.').pop().toLowerCase();
                    const validExts = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
                    if (!file.type.startsWith('image/') && !validExts.includes(ext)) {
                        this.showError(`O arquivo "${file.name}" não é uma imagem válida.`);
                        continue;
                    }

                    const assignedDay = daysList[this.selectedImages.length % daysList.length];
                    const qualityScore = file.size > 2000000 ? 98 : (file.size > 500000 ? 94 : 88);
                    const qualityStatus = qualityScore >= 95 ? 'Excelente' : (qualityScore >= 90 ? 'Boa' : 'Regular');

                    this.selectedImages.push({
                        file: file,
                        previewUrl: URL.createObjectURL(file),
                        day: assignedDay,
                        detected_day: null,
                        is_workout: null,
                        confidence: null,
                        reason: null,
                        image_path: null,
                        image_id: null,
                        status: 'queued',
                        qualityScore: qualityScore,
                        qualityStatus: qualityStatus
                    });
                }

                this.exercises = [];
                this.workoutName = '';
                this.uuid = null;
                this.state = 'IDLE';

                e.target.value = '';

                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },

            removeSelectedImage(index) {
                this.selectedImages.splice(index, 1);
                if (this.selectedImages.length === 0) {
                    this.clear();
                }
            },

            compressImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const MAX_WIDTH = 1600;
                            const MAX_HEIGHT = 1600;
                            let width = img.width;
                            let height = img.height;

                            if (width > height) {
                                if (width > MAX_WIDTH) {
                                    height *= MAX_WIDTH / width;
                                    width = MAX_WIDTH;
                                }
                            } else {
                                if (height > MAX_HEIGHT) {
                                    width *= MAX_HEIGHT / height;
                                    height = MAX_HEIGHT;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            }, 'image/jpeg', 0.75);
                        };
                    };
                });
            },

            // Triggered from Step 2 to start AI processing
            async startAIScan() {
                this.step = 3;
                this.validationMessage = '';
                this.aiMessages = [];
                this.processingStartedAt = Date.now();
                this.processingSeconds = 0;
                this.addLog('Iniciando Orquestrador NexShape AI.');
                this.addLog(`Preparando ${this.selectedImages.length} imagens para validação.`);
                
                await this.processPhoto();
            },

            addLog(text) {
                const time = new Date().toLocaleTimeString('pt-BR', { hour12: false });
                this.aiMessages.push(`[${time}] ${text}`);
                // Auto scroll console
                this.$nextTick(() => {
                    const container = document.querySelector('.h-32');
                    if (container) container.scrollTop = container.scrollHeight;
                });
            },
            
            async processPhoto() {
                if (this.selectedImages.length === 0 || this.isProcessing) return;
                
                this.isProcessing = true;

                try {
                    if (!this.uuid) {
                        this.state = 'RECEIVED';
                        this.addLog('Comprimindo e enviando imagens para o servidor...');
                        
                        const compressedFiles = [];
                        for (let img of this.selectedImages) {
                            const compressed = await this.compressImage(img.file);
                            compressedFiles.push(compressed);
                        }

                        const initData = new FormData();
                        compressedFiles.forEach(file => {
                            initData.append('photos[]', file);
                        });

                        const initResponse = await fetch("{{ route('progression.plans.orch-initialize') }}", {
                            method: 'POST',
                            body: initData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (!initResponse.ok) {
                            throw new Error('Falha ao inicializar a sessão de importação.');
                        }

                        const initRes = await initResponse.json();
                        this.uuid = initRes.uuid;
                        this.selectedImages.forEach((img, idx) => {
                            if (initRes.session.images[idx]) {
                                img.image_id = initRes.session.images[idx].image_id;
                                img.image_path = initRes.session.images[idx].image_path;
                            }
                        });
                        this.addLog('Sessão inicializada no servidor com sucesso.');
                    }

                    // STEP A: VALIDATION AGENT
                    this.state = 'VALIDATING';
                    this.agentValidation.status = 'running';
                    this.selectedImages.forEach(img => {
                        if (img.status === 'queued') img.status = 'validating';
                    });
                    this.addLog('Agente de Validação iniciado: analisando se as imagens são fichas de treino.');

                    const valResponse = await fetch(`/progression/plans/orchestrated/validate/${this.uuid}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const valData = await valResponse.json().catch(() => ({}));

                    if (!valResponse.ok) {
                        this.agentValidation.status = 'failed';
                        const message = valData.error || valData.error_message || 'Falha na validação das imagens.';
                        throw new Error(message);
                    }

                    if (valData.status === 'AI_SERVICE_ERROR') {
                        this.state = 'AI_SERVICE_ERROR';
                        this.agentValidation.status = 'failed';
                        this.addLog('Erro técnico no serviço de IA durante a validação.');
                        this.isProcessing = false;
                        this.step = 2;
                        this.validationMessage = valData.error_message || 'Não foi possível validar as imagens porque o serviço de IA não respondeu corretamente. Verifique a configuração da chave da OpenAI e tente novamente. As imagens não foram rejeitadas.';
                        this.showError(this.validationMessage);
                        return;
                    }

                    if (!valData.session || !Array.isArray(valData.session.images)) {
                        throw new Error(valData.error || 'A validação não retornou os dados esperados. Tente novamente.');
                    }
                    
                    // Update local image states
                    valData.session.images.forEach((imgRes) => {
                        const localImg = this.selectedImages.find(img => img.image_id === imgRes.image_id);
                        if (localImg) {
                            localImg.is_workout = imgRes.is_workout;
                            localImg.confidence = imgRes.confidence;
                            localImg.reason = imgRes.reason;
                            localImg.image_path = imgRes.image_path;
                            localImg.detected_day = imgRes.detected_day;
                            if (imgRes.detected_day) {
                                localImg.day = imgRes.detected_day;
                            }
                            localImg.status = imgRes.is_workout ? 'ocr' : 'error';
                        }
                    });

                    if (valData.status === 'INVALID_IMAGE') {
                        this.state = 'INVALID_IMAGE';
                        this.agentValidation.status = 'failed';
                        const invalidImages = this.selectedImages.filter(img => img.status === 'error');
                        const message = invalidImages.length === 1
                            ? 'A imagem marcada em vermelho nao parece ser uma ficha de treino.'
                            : (invalidImages.length > 1
                                ? `${invalidImages.length} imagens marcadas em vermelho nao parecem ser fichas de treino.`
                                : 'Uma ou mais imagens nao parecem ser fichas de treino.');
                        this.validationMessage = `${message} Substitua por fotos legiveis de fichas de treino para continuar.`;
                        this.addLog('Erro: Uma ou mais imagens não foram reconhecidas como ficha de treino.');
                        this.isProcessing = false;
                        this.step = 2; // Return to stage 2 to fix/replace invalid images
                        this.showError(this.validationMessage);
                        return;
                    }

                    this.agentValidation.status = 'success';
                    this.addLog('Agente de Validação concluído: todas as fotos são válidas.');

                    // STEP B: OCR & EXTRACTION AGENT
                    this.state = 'EXTRACTING';
                    this.agentOCR.status = 'running';
                    this.addLog('Agente OCR iniciado: extraindo textos brutos e tabelas das fichas.');

                    // Simula progresso visual por lote
                    setTimeout(() => {
                        this.agentOCR.status = 'success';
                        this.agentSpecialist.status = 'running';
                        this.addLog('Agente OCR finalizado.');
                        this.addLog('Agente Especialista iniciado: interpretando exercícios, séries e cargas.');
                    }, 1500);

                    const processResponse = await fetch(`/progression/plans/orchestrated/process/${this.uuid}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const processData = await processResponse.json().catch(() => ({}));

                    if (!processResponse.ok) {
                        this.agentOCR.status = 'failed';
                        this.agentSpecialist.status = 'failed';
                        this.state = processData.status || (processData.error_type === 'ai_service' ? 'AI_SERVICE_ERROR' : 'EXTRACTION_FAILED');
                        throw new Error(processData.error_message || processData.error || 'Falha no processamento das imagens de treino.');
                    }

                    if (processData.status === 'WAITING_REVIEW') {
                        this.agentSpecialist.status = 'success';
                        this.agentAuditor.status = 'running';
                        this.addLog('Agente Especialista concluído.');
                        this.addLog('Agente Auditor iniciado: executando regras de validação física e multi-tenant.');

                        const consolidatedPayload = processData.session?.consolidated_workout || {};
                        const consolidated = this.normalizeConsolidatedWorkout(consolidatedPayload);
                        if (Object.keys(consolidated).length === 0) {
                            throw new Error('A IA processou as imagens, mas nao retornou exercicios para revisao.');
                        }
                        const localExList = [];
                        
                        Object.keys(consolidated).forEach(day => {
                            if (!Array.isArray(consolidated[day])) {
                                return;
                            }

                            consolidated[day].forEach(ex => {
                                localExList.push({
                                    nome_exercicio: ex.nome_exercicio,
                                    series: ex.series,
                                    repeticoes: ex.repeticoes,
                                    carga: ex.carga,
                                    intervalo: ex.intervalo,
                                    observacoes: ex.observacoes,
                                    day: day,
                                    confidence_scores: ex.confidence_scores || {}
                                });
                            });
                        });

                        if (localExList.length === 0) {
                            throw new Error('A IA processou as imagens, mas nao retornou exercicios para revisao.');
                        }

                        this.exercises = localExList;
                        this.workoutName = consolidatedPayload.workout_name || consolidatedPayload.name || ('Treino Semanal Importado - ' + new Date().toLocaleDateString('pt-BR'));
                        this.activeReviewDay = this.daysFound()[0] || '';
                        this.processingSeconds = this.processingStartedAt ? Math.max(1, Math.round((Date.now() - this.processingStartedAt) / 1000)) : 0;
                        
                        const auditorData = processData.session.audit_results || {};
                        this.auditWarnings = auditorData.warnings || [];

                        // Finalize images state
                        this.selectedImages.forEach(img => {
                            if (img.status !== 'error') img.status = 'reviewed';
                        });

                        setTimeout(() => {
                            this.agentAuditor.status = 'success';
                            this.addLog('Agente Auditor concluído. Análise consolidada com sucesso.');
                            this.showSuccess('Treino semanal extraído e auditado com sucesso!');
                            
                            setTimeout(() => {
                                this.step = 4; // Advance to Review stage
                                this.state = 'REVIEWING';
                            }, 1000);
                        }, 1200);

                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });

                    } else {
                        if (['FAILED', 'EXTRACTION_FAILED'].includes(processData.status)) {
                            this.state = 'EXTRACTION_FAILED';
                            this.agentOCR.status = 'failed';
                            this.agentSpecialist.status = 'failed';
                            this.selectedImages.forEach(img => {
                                if (img.is_workout === true && img.status !== 'error') {
                                    img.status = 'ocr';
                                }
                            });
                            throw new Error(processData.error_message || 'A ficha foi reconhecida, mas a IA nao conseguiu extrair exercicios. Tente novamente ou recorte a imagem focando somente na tabela do treino.');
                        }
                        throw new Error(processData.error || 'Falha no processamento orquestrado.');
                    }

                } catch (err) {
                    console.error(err);
                    if (!['AI_SERVICE_ERROR', 'EXTRACTION_FAILED', 'INVALID_IMAGE'].includes(this.state)) {
                        this.state = 'IDLE';
                    }
                    this.step = 2; // Return back to organize step
                    this.validationMessage = err.message || 'Falha na comunicação com o servidor de IA.';
                    this.showError(this.validationMessage);
                } finally {
                    this.isProcessing = false;
                }
            },

            normalizeConsolidatedWorkout(payload) {
                if (!payload || typeof payload !== 'object') {
                    return {};
                }

                if (payload.consolidated_workout && typeof payload.consolidated_workout === 'object') {
                    return payload.consolidated_workout;
                }

                if (payload.weekly_plan && typeof payload.weekly_plan === 'object') {
                    return payload.weekly_plan;
                }

                if (payload.days && typeof payload.days === 'object' && !Array.isArray(payload.days)) {
                    return payload.days;
                }

                if (Array.isArray(payload.days)) {
                    return payload.days.reduce((days, dayData) => {
                        const day = dayData.day || dayData.dia || dayData.name || 'Treino';
                        const exercises = dayData.exercises || dayData.exercicios || [];
                        days[day] = exercises;
                        return days;
                    }, {});
                }

                if (Array.isArray(payload.exercises) || Array.isArray(payload.exercicios)) {
                    const exercises = payload.exercises || payload.exercicios;
                    return exercises.reduce((days, exercise) => {
                        const day = exercise.day || exercise.dia || exercise.training_day || 'Treino';
                        if (!days[day]) {
                            days[day] = [];
                        }
                        days[day].push(exercise);
                        return days;
                    }, {});
                }

                return Object.keys(payload).reduce((days, key) => {
                    if (Array.isArray(payload[key])) {
                        days[key] = payload[key];
                    }
                    return days;
                }, {});
            },

            async substituteImage(e, imageId) {
                const file = e.target.files[0];
                if (!file || !this.uuid) return;

                this.isProcessing = true;
                this.state = 'VALIDATING';
                this.processingStep = 'Substituindo imagem...';

                try {
                    const compressed = await this.compressImage(file);
                    
                    const formData = new FormData();
                    formData.append('image_id', imageId);
                    formData.append('photo', compressed);

                    const response = await fetch(`/progression/plans/orchestrated/substitute/${this.uuid}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Falha ao substituir a imagem.');
                    }

                    const data = await response.json();
                    
                    // Update locally
                    const localImg = this.selectedImages.find(img => img.image_id === imageId);
                    if (localImg) {
                        if (localImg.previewUrl && localImg.previewUrl.startsWith('blob:')) {
                            URL.revokeObjectURL(localImg.previewUrl);
                        }
                        localImg.file = file;
                        localImg.previewUrl = URL.createObjectURL(file);
                        const serverImg = data.session?.images?.find(img => Number(img.image_id) === Number(imageId));
                        if (serverImg) {
                            localImg.image_path = serverImg.image_path;
                            localImg.detected_day = serverImg.day || null;
                        }
                        localImg.is_workout = null;
                        localImg.confidence = null;
                        localImg.reason = null;
                        localImg.detected_elements = [];
                        localImg.status = 'queued';
                        this.validationMessage = '';
                    }

                    this.state = 'IDLE';
                    this.showSuccess('Imagem substituída com sucesso. Clique em iniciar para reprocessar.');

                } catch (err) {
                    this.showError(err.message || 'Erro ao substituir imagem.');
                    this.state = 'INVALID_IMAGE';
                } finally {
                    this.isProcessing = false;
                    e.target.value = '';
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            },
            
            removeExercise(index) {
                this.exercises.splice(index, 1);
            },
            
            async saveImport() {
                if (!this.workoutName) {
                    this.showError('Informe um nome para o treino.');
                    return;
                }
                if (this.isSaving) return;
 
                this.isSaving = true;
                this.state = 'SAVING';
                
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
                        this.state = 'SUCCESS';
                        this.successRedirectUrl = data.redirect;
                        this.showSuccess(data.message);
                        // Go to Step 5 (Success Screen)
                        this.step = 5;
                    } else {
                        this.state = 'REVIEWING';
                        this.showError(data.error || 'Falha ao salvar treino.');
                    }
                } catch (err) {
                    this.state = 'REVIEWING';
                    this.showError('Ocorreu um erro ao salvar o treino.');
                } finally {
                    this.isSaving = false;
                }
            },
            
            async confirmClearHistory() {
                if (this.isClearingHistory) {
                    return;
                }

                this.isClearingHistory = true;

                try {
                    const response = await fetch("{{ route('progression.plans.clear-history') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.clearHistoryModal.show = false;
                        this.showSuccess(data.message);
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        this.showError(data.error || 'Falha ao limpar histórico.');
                    }
                } catch (err) {
                    this.showError('Ocorreu um erro ao limpar o histórico.');
                } finally {
                    this.isClearingHistory = false;
                }
            },

            clear() {
                this.selectedImages = [];
                this.exercises = [];
                this.workoutName = '';
                this.uuid = null;
                this.step = 1;
                this.state = 'IDLE';
                this.validationMessage = '';
                this.auditWarnings = [];
                this.aiMessages = [];
                this.activeReviewDay = '';
                this.processingStartedAt = null;
                this.processingSeconds = 0;
            }
        }));
    });
</script>
@endpush

@endsection
