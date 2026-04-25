@extends('layouts.app')

@section('title', 'Criar Novo Plano de Treino')

@section('content')
<div x-data="trainingPlanBuilder()" 
     x-init="init()"
     class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto pb-32">
    
    {{-- Barra de Progresso Superior --}}
    <div class="fixed top-0 left-0 w-full h-1.5 bg-zinc-900 z-[60]">
        <div class="h-full bg-blue-600 transition-all duration-500 shadow-[0_0_15px_rgba(37,99,235,0.5)]" 
             :style="'width: ' + (step * 20) + '%'"></div>
    </div>

    <header class="pb-8 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-4xl font-black text-white tracking-tight">Criar Plano de Treino</h1>
            <div class="flex items-center gap-4 mt-3">
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-[0.3em] flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Etapa <span x-text="step"></span> de 5: <span x-text="stepTitle()"></span>
                </p>
                <template x-if="autoSaved">
                    <span class="text-[9px] text-emerald-500 font-black uppercase tracking-widest flex items-center gap-1">
                        <i class="fas fa-check-circle text-[8px]"></i>
                        Salvo automaticamente
                    </span>
                </template>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            @if(!Auth::user()->isPremiumActive())
                <a href="{{ route('plano') }}" class="group relative px-5 py-3 bg-zinc-950 border border-amber-500/20 rounded-[2rem] flex items-center gap-4 transition-all hover:border-amber-500/40 shadow-2xl">
                    <div class="absolute inset-0 bg-amber-500/5 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20 shadow-lg shadow-amber-500/10">
                        <i class="fas fa-crown text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Plano Free</p>
                        <p class="text-[9px] text-zinc-500 font-medium">Upgrade para desbloquear tudo</p>
                    </div>
                </a>
            @else
                <div class="px-5 py-3 bg-zinc-950 border border-blue-500/20 rounded-[2rem] flex items-center gap-4 shadow-2xl">
                    <div class="w-10 h-10 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 shadow-lg shadow-blue-500/10">
                        <i class="fas fa-crown text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Plano Pro</p>
                        <p class="text-[9px] text-zinc-500 font-medium">Acesso Ilimitado Ativo</p>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-2 bg-zinc-950 border border-white/5 rounded-2xl p-1 px-4">
                <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Status:</span>
                <select name="status" x-model="formData.status" class="bg-transparent border-0 text-[10px] font-black text-blue-500 uppercase focus:ring-0 cursor-pointer">
                    <option value="Rascunho" class="bg-zinc-900">Rascunho</option>
                    <option value="Ativo" class="bg-zinc-900">Ativo</option>
                    <option value="Pausado" class="bg-zinc-900">Pausado</option>
                    <option value="Finalizado" class="bg-zinc-900">Finalizado</option>
                </select>
            </div>
        </div>
    </header>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-10 shadow-2xl relative overflow-hidden group/card">
        {{-- Efeito de Glow de Fundo --}}
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-600/5 blur-[120px] rounded-full transition-all group-hover/card:bg-blue-600/10"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-600/5 blur-[120px] rounded-full transition-all group-hover/card:bg-indigo-600/10"></div>

        {{-- Erros de Validação --}}
        @if ($errors->any())
            <div class="mb-8 p-6 bg-red-500/10 border border-red-500/20 rounded-3xl animate-fade-in">
                <div class="flex items-center gap-3 mb-4 text-red-500">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h4 class="text-[10px] font-black uppercase tracking-widest">Ops! Encontramos alguns problemas:</h4>
                </div>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-xs text-zinc-400 font-medium list-disc ml-4">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('progression.plans.store') }}" method="POST" id="planForm" @submit.prevent="submitForm()" class="space-y-12 relative z-10">
            @csrf
            
            {{-- ETAPA 1: INFORMAÇÕES BÁSICAS --}}
            <div x-show="step === 1" x-transition.opacity.duration.400ms class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Identificador -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Identificador do Treino</label>
                        <div class="relative group/select">
                            <select name="plan_label" x-model="formData.plan_label" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="" class="bg-zinc-900 text-zinc-500">Nenhum...</option>
                                <option value="Treino A" class="bg-zinc-900">Treino A</option>
                                <option value="Treino B" class="bg-zinc-900">Treino B</option>
                                <option value="Treino C" class="bg-zinc-900">Treino C</option>
                                <option value="Treino D" class="bg-zinc-900">Treino D</option>
                                <option value="Treino E" class="bg-zinc-900">Treino E</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 group-hover/select:text-blue-400 transition-colors pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Nome do Plano -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nome do Plano <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all placeholder:text-zinc-800" placeholder="Ex: Peito e Tríceps">
                    </div>

                    <!-- Objetivo -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Objetivo do Treino</label>
                        <div class="relative group/select">
                            <select name="goal" x-model="formData.goal" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="" class="bg-zinc-900">Selecione...</option>
                                <option value="Hipertrofia" class="bg-zinc-900">Hipertrofia</option>
                                <option value="Emagrecimento" class="bg-zinc-900">Emagrecimento</option>
                                <option value="Força" class="bg-zinc-900">Força</option>
                                <option value="Resistência" class="bg-zinc-900">Resistência</option>
                                <option value="Definição muscular" class="bg-zinc-900">Definição muscular</option>
                                <option value="Condicionamento físico" class="bg-zinc-900">Condicionamento físico</option>
                                <option value="Reabilitação" class="bg-zinc-900">Reabilitação</option>
                                <option value="Performance esportiva" class="bg-zinc-900">Performance esportiva</option>
                                <option value="Saúde e bem-estar" class="bg-zinc-900">Saúde e bem-estar</option>
                                <option value="Outro objetivo" class="bg-zinc-900">Outro objetivo</option>
                            </select>
                            <i class="fas fa-bullseye absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 group-hover/select:text-blue-400 transition-colors pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Outro Objetivo (Condicional) -->
                    <div class="space-y-3 transition-all duration-300" x-show="formData.goal === 'Outro objetivo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Descreva o objetivo</label>
                        <input type="text" name="goal_custom" x-model="formData.goal_custom" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all placeholder:text-zinc-800" placeholder="Especifique o foco...">
                    </div>

                    <!-- Perfil do Aluno -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Perfil do Aluno</label>
                        <div class="relative group/select">
                            <select name="student_profile" x-model="formData.student_profile" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="Iniciante" class="bg-zinc-900">Iniciante</option>
                                <option value="Intermediário" class="bg-zinc-900" selected>Intermediário</option>
                                <option value="Avançado" class="bg-zinc-900">Avançado</option>
                                <option value="Idoso" class="bg-zinc-900">Idoso</option>
                                <option value="Reabilitação" class="bg-zinc-900">Reabilitação</option>
                                <option value="Atleta" class="bg-zinc-900">Atleta</option>
                                <option value="Gestante" class="bg-zinc-900">Gestante</option>
                                <option value="Pós-operatório" class="bg-zinc-900">Pós-operatório</option>
                            </select>
                            <i class="fas fa-user absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 group-hover/select:text-blue-400 transition-colors pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Tipo de Divisão -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Tipo de Divisão</label>
                        <div class="relative group/select">
                            <select name="split_type" x-model="formData.split_type" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="Full Body" class="bg-zinc-900">Full Body</option>
                                <option value="ABC" class="bg-zinc-900">ABC</option>
                                <option value="ABCD" class="bg-zinc-900">ABCD</option>
                                <option value="ABCDE" class="bg-zinc-900">ABCDE</option>
                                <option value="Upper / Lower" class="bg-zinc-900">Upper / Lower</option>
                                <option value="Push / Pull / Legs" class="bg-zinc-900">Push / Pull / Legs</option>
                                <option value="Personalizado" class="bg-zinc-900">Personalizado</option>
                            </select>
                            <i class="fas fa-layer-group absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 group-hover/select:text-blue-400 transition-colors pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Frequência Semanal -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Treinos por Semana</label>
                        <div class="relative group/select">
                            <select name="frequency" x-model="formData.frequency" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all appearance-none cursor-pointer">
                                @foreach(range(1, 7) as $i)
                                    <option value="{{ $i }}" class="bg-zinc-900">{{ $i }}x na semana</option>
                                @endforeach
                            </select>
                            <i class="fas fa-calendar-check absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 group-hover/select:text-blue-400 transition-colors pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Dias da Semana (Multi-select) -->
                    <div class="space-y-3 md:col-span-2">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Dias Recomendados</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="day in ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo']">
                                <button type="button" 
                                        @click="toggleDay(day)"
                                        :class="formData.days_of_week.includes(day) ? 'bg-blue-600 text-white border-blue-500' : 'bg-zinc-950/40 text-zinc-500 border-white/5'"
                                        class="px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95">
                                    <span x-text="day"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Nível de Dificuldade -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nível de Exigência</label>
                        <div class="relative group/select">
                            <select name="difficulty" x-model="formData.difficulty" class="w-full bg-zinc-950/60 border border-white/5 rounded-2xl px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all appearance-none cursor-pointer">
                                <option value="Iniciante" class="bg-zinc-900">Iniciante</option>
                                <option value="Intermediário" class="bg-zinc-900">Intermediário</option>
                                <option value="Avançado" class="bg-zinc-900">Avançado</option>
                                <option value="Elite" class="bg-zinc-900">Elite</option>
                            </select>
                            <i class="fas fa-fire absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 group-hover/select:text-blue-400 transition-colors pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Duração Automática -->
                    <div class="space-y-3">
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Duração Estimada</label>
                        <div class="relative">
                            <input type="number" readonly name="estimated_duration" x-model="formData.estimated_duration" class="w-full bg-zinc-900/40 border border-white/5 rounded-2xl px-6 py-5 text-zinc-400 text-sm outline-none cursor-not-allowed transition-all" placeholder="0">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 text-[10px] font-black uppercase">min</span>
                        </div>
                        <p class="text-[9px] text-zinc-600 font-medium ml-2 uppercase">* Calculado com base no volume de exercícios</p>
                    </div>
                </div>

                <!-- Descrição -->
                <div class="space-y-3">
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Notas Estratégicas</label>
                    <textarea name="description" x-model="formData.description" rows="4" class="w-full bg-zinc-950/60 border border-white/5 rounded-[2rem] p-8 text-white text-sm outline-none focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 transition-all resize-none placeholder:text-zinc-800" placeholder="Orientações, métodos de intensidade ou observações gerais..."></textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="step = 2" class="px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-500/20 text-[10px] uppercase tracking-[0.3em] flex items-center gap-4">
                        Próxima Etapa: Exercícios
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- ETAPA 2: MONTAGEM DE EXERCÍCIOS --}}
            <div x-show="step === 2" x-transition.opacity.duration.400ms class="space-y-8" x-cloak>
                {{-- Resumo do Treino --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-8 bg-zinc-950/40 border border-white/5 rounded-[2.5rem]">
                    <div class="space-y-1">
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Exercícios</p>
                        <p class="text-2xl font-black text-white" x-text="exercises.length"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Músculos Focados</p>
                        <p class="text-[11px] font-bold text-blue-400 truncate" x-text="musclesWorked().join(', ') || 'Nenhum'"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Volume Total</p>
                        <p class="text-xl font-black text-white"><span x-text="totalVolume()"></span> <span class="text-xs text-zinc-600">kg</span></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Tempo Estimado</p>
                        <p class="text-xl font-black text-white"><span x-text="formData.estimated_duration"></span> <span class="text-xs text-zinc-600">min</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-blue-500 shadow-xl">
                        <i class="fas fa-dumbbell text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-black text-white uppercase tracking-widest">Seleção de Exercícios</h2>
                        <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest mt-1">
                            Arraste para reordenar ou adicione novos movimentos
                        </p>
                    </div>
                    <button type="button" onclick="openExerciseModal()" class="hidden md:flex items-center gap-3 px-6 py-3 bg-blue-600/10 hover:bg-blue-600 text-blue-500 hover:text-white border border-blue-500/20 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">
                        <i class="fas fa-magic"></i>
                        Sugestão Inteligente
                    </button>
                    <span class="flex-1 h-px bg-gradient-to-r from-white/10 to-transparent"></span>
                </div>

                <div id="exerciseList" class="space-y-4 min-h-[200px]">
                    {{-- Estado Vazio --}}
                    <div x-show="exercises.length === 0" class="py-20 border-2 border-dashed border-white/5 rounded-[3rem] flex flex-col items-center justify-center gap-4 group transition-all hover:border-blue-500/20">
                        <div class="w-20 h-20 rounded-[2rem] bg-zinc-950 flex items-center justify-center text-zinc-800 group-hover:text-blue-500 transition-all shadow-inner">
                            <i class="fas fa-layer-group text-3xl"></i>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-bold text-zinc-500 uppercase tracking-widest">Seu plano está vazio</p>
                            <p class="text-[10px] text-zinc-700 font-bold mt-1">ADICIONE EXERCÍCIOS PARA COMEÇAR A ESTRUTURAR</p>
                        </div>
                        <button type="button" onclick="openExerciseModal()" class="mt-4 px-8 py-3 bg-blue-600 border border-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 hover:scale-105 transition-all shadow-lg shadow-blue-500/20">
                            <i class="fas fa-plus mr-2"></i> Adicionar Exercício Inteligente
                        </button>
                    </div>

                    {{-- Lista de Exercícios (Row Template) --}}
                    <template x-for="(exercise, exIdx) in exercises" :key="exercise.tempId">
                        <div class="bg-zinc-900/50 border border-white/5 rounded-3xl p-6 shadow-xl flex items-center justify-between group/ex animate-fade-left">
                            <div class="flex items-center gap-6">
                                <div class="w-10 h-10 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-700 font-black text-sm" x-text="exIdx + 1"></div>
                                <div>
                                    <h4 class="text-white font-black text-base" x-text="exercise.name"></h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <template x-for="muscle in (exercise.muscles || [])">
                                            <span class="text-[8px] px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded-md font-black uppercase tracking-tighter" x-text="muscle"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right mr-4 hidden md:block">
                                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Séries</p>
                                    <p class="text-xs font-bold text-white" x-text="exercise.sets.length"></p>
                                </div>
                                <button type="button" @click="removeExercise(exIdx)" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 opacity-0 group-hover/ex:opacity-100 transition-all hover:bg-red-500 hover:text-white flex items-center justify-center">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-8 pt-8 border-t border-white/5">
                    <button type="button" onclick="openExerciseModal()" class="w-full sm:w-auto px-8 py-5 bg-zinc-950 hover:bg-zinc-900 text-white font-black rounded-2xl border border-white/10 transition-all text-[10px] uppercase tracking-widest flex items-center justify-center gap-4 group shadow-2xl">
                        <div class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus"></i>
                        </div>
                        Adicionar Exercício Inteligente
                    </button>
                    
                    <div class="flex items-center gap-6 w-full sm:w-auto">
                        <button type="button" @click="step = 1" class="px-6 py-5 text-[10px] font-black text-zinc-600 hover:text-white uppercase tracking-widest transition-colors">Voltar</button>
                        <button type="button" @click="step = 3" class="w-full sm:w-auto px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-500/20 text-[10px] uppercase tracking-[0.3em]">
                            Configurar Séries
                        </button>
                    </div>
                </div>
            </div>

            {{-- ETAPA 3: SÉRIES E CARGAS --}}
            <div x-show="step === 3" x-transition.opacity.duration.400ms class="space-y-10" x-cloak>
                <template x-for="(exercise, exIdx) in exercises" :key="exercise.tempId">
                    <div class="bg-zinc-900/50 border border-white/5 rounded-3xl p-8 shadow-xl">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-blue-500 font-black text-lg" x-text="exIdx + 1"></div>
                                <h3 class="text-xl font-black text-white" x-text="exercise.name"></h3>
                            </div>
                            <button type="button" @click="removeExercise(exIdx)" class="text-red-500 hover:text-red-400 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-12 gap-4 px-2">
                                <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-widest">Série</div>
                                <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-widest">Reps</div>
                                <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-widest">Peso (kg)</div>
                                <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-widest">Descanso (s)</div>
                                <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-widest flex items-center justify-center gap-1">
                                    RPE
                                    @lockIcon('rpe_control')
                                </div>
                                <div class="col-span-2 text-[9px] font-black uppercase text-zinc-600 text-center tracking-widest flex items-center justify-center gap-1">
                                    Cadência
                                    @lockIcon('cadence_control')
                                </div>
                            </div>

                            <template x-for="(set, setIdx) in exercise.sets" :key="set.tempId">
                                <div class="grid grid-cols-12 gap-2 bg-zinc-950/80 border border-white/5 rounded-2xl p-2 items-center group/set">
                                    <div class="col-span-2">
                                        <select x-model="set.type" class="w-full bg-zinc-900 border-0 rounded-xl text-[10px] font-black text-zinc-400 uppercase focus:ring-1 focus:ring-blue-500/30">
                                            <option value="work">Trabalho</option>
                                            <template x-if="isPremium">
                                                <optgroup label="Avançado">
                                                    <option value="warmup">Aquecimento</option>
                                                    <option value="drop">Drop Set</option>
                                                    <option value="failure">Até a Falha</option>
                                                    <option value="rest-pause">Rest-Pause</option>
                                                </optgroup>
                                            </template>
                                            <template x-if="!isPremium">
                                                <option disabled>💎 PRO Only</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" x-model="set.reps" class="w-full bg-zinc-900 border-0 rounded-xl text-white text-center font-bold py-3 focus:ring-1 focus:ring-blue-500/30">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" x-model="set.weight" class="w-full bg-zinc-900 border-0 rounded-xl text-white text-center font-bold py-3 focus:ring-1 focus:ring-blue-500/30">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" x-model="set.rest" class="w-full bg-zinc-900 border-0 rounded-xl text-zinc-500 text-center font-bold py-3 focus:ring-1 focus:ring-blue-500/30">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" x-model="set.rpe" :disabled="!isPremium" :class="!isPremium ? 'opacity-20 cursor-not-allowed' : ''" class="w-full bg-zinc-900 border-0 rounded-xl text-blue-400 text-center font-bold py-3 focus:ring-1 focus:ring-blue-500/30">
                                    </div>
                                    <div class="col-span-2 flex items-center gap-2">
                                        <input type="text" x-model="set.cadence" :disabled="!isPremium" :class="!isPremium ? 'opacity-20 cursor-not-allowed' : ''" class="w-full bg-zinc-900 border-0 rounded-xl text-zinc-500 text-center font-bold py-3 focus:ring-1 focus:ring-blue-500/30 text-[10px]">
                                        <button type="button" @click="removeSet(exIdx, setIdx)" class="text-zinc-700 hover:text-red-500 transition-colors p-2">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addSet(exIdx)" class="w-full py-4 border-2 border-dashed border-white/5 rounded-2xl text-[10px] font-black text-zinc-600 uppercase tracking-widest hover:border-blue-500/30 hover:text-blue-500 transition-all">
                                <i class="fas fa-plus mr-2"></i> Adicionar Série
                            </button>
                        </div>
                    </div>
                </template>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-8 pt-8 border-t border-white/5">
                    <button type="button" @click="step = 2" class="px-6 py-5 text-[10px] font-black text-zinc-600 hover:text-white uppercase tracking-widest transition-colors">Voltar</button>
                    <button type="button" @click="step = 4" class="w-full sm:w-auto px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-500/20 text-[10px] uppercase tracking-[0.3em]">
                        Revisar Treino
                    </button>
                </div>
            </div>

            {{-- ETAPA 4: REVISÃO --}}
            <div x-show="step === 4" x-transition.opacity.duration.400ms class="space-y-10" x-cloak>
                <div class="bg-zinc-950/40 border border-white/5 rounded-[3rem] overflow-hidden">
                    <div class="p-10 border-b border-white/5 bg-zinc-900/40">
                        <h3 class="text-2xl font-black text-white uppercase tracking-tight">Ficha de Revisão</h3>
                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-2">Valide as informações antes de finalizar</p>
                    </div>

                    <div class="p-10 space-y-10">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                            <div class="space-y-1">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Plano</p>
                                <p class="text-white font-bold" x-text="formData.name"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Objetivo</p>
                                <p class="text-white font-bold" x-text="formData.goal"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Frequência</p>
                                <p class="text-white font-bold" x-text="formData.frequency + 'x/semana'"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Duração</p>
                                <p class="text-white font-bold" x-text="formData.estimated_duration + ' min'"></p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Resumo da Rotina</p>
                            <div class="space-y-4">
                                <template x-for="(exercise, exIdx) in exercises">
                                    <div class="flex items-center justify-between p-5 bg-zinc-900/60 rounded-2xl border border-white/5">
                                        <div class="flex items-center gap-4">
                                            <span class="text-zinc-700 font-black" x-text="(exIdx + 1).toString().padStart(2, '0')"></span>
                                            <span class="text-white font-bold" x-text="exercise.name"></span>
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <span class="text-[10px] text-zinc-500 font-black uppercase" x-text="exercise.sets.length + ' séries'"></span>
                                            <span class="text-[10px] text-blue-500 font-black uppercase" x-text="exerciseTotalWeight(exercise) + 'kg volume'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-8 pt-8 border-t border-white/5">
                    <button type="button" @click="step = 3" class="px-6 py-5 text-[10px] font-black text-zinc-600 hover:text-white uppercase tracking-widest transition-colors">Voltar</button>
                    <button type="button" @click="step = 5" class="w-full sm:w-auto px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-blue-500/20 text-[10px] uppercase tracking-[0.3em]">
                        Ir para Finalização
                    </button>
                </div>
            </div>

            {{-- ETAPA 5: FINALIZAR --}}
            <div x-show="step === 5" x-transition.opacity.duration.400ms class="space-y-10 text-center py-20" x-cloak>
                <div class="max-w-md mx-auto space-y-8">
                    <div class="w-24 h-24 bg-blue-600/20 rounded-[2.5rem] flex items-center justify-center text-blue-500 mx-auto border border-blue-500/20 shadow-2xl">
                        <i class="fas fa-rocket text-4xl animate-bounce"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-white uppercase tracking-tight">Tudo Pronto!</h3>
                        <p class="text-zinc-500 font-bold mt-4">Deseja salvar este treino como um modelo reutilizável ou apenas consolidar o plano?</p>
                    </div>

                        <div class="relative group">
                            @feature('create_workout_model')
                            <button type="button" 
                                    @click="toggleTemplate()"
                                    :class="formData.is_template ? 'bg-amber-500 border-amber-400 text-zinc-950' : 'bg-zinc-950 border-white/5 text-white'"
                                    class="w-full py-5 rounded-2xl border font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-star" :class="formData.is_template ? 'text-zinc-950' : 'text-amber-500'"></i>
                                Salvar como Modelo Reutilizável
                            </button>
                            @else
                            <button type="button" 
                                    @click="window.location.href='{{ route('plano') }}'"
                                    class="w-full py-5 bg-zinc-950 border border-amber-500/20 text-zinc-500 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-center gap-3 opacity-60 hover:opacity-100">
                                <i class="fas fa-lock text-amber-500"></i>
                                Salvar como Modelo (PRO)
                            </button>
                            @endfeature
                        </div>

                        <button type="submit" class="w-full py-6 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-2xl shadow-blue-500/40 text-[12px] uppercase tracking-[0.4em]">
                            Consolidar Plano de Treino
                        </button>
                        
                        <button type="button" @click="step = 4" class="text-zinc-600 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors py-4">Revisar Informações</button>
                    </div>
                </div>
            </div>

            {{-- Hidden JSON Input for exercises (Sincronizado manualmente no submit para máxima compatibilidade) --}}
            <input type="hidden" name="exercises_json" id="exercises_json_field" value="{{ old('exercises_json') }}">
            
            <input type="hidden" name="is_template" :value="formData.is_template ? 1 : 0">
            <input type="hidden" name="total_volume" :value="totalVolume()">
            <input type="hidden" name="muscles_worked" :value="JSON.stringify(musclesWorked())">
            <input type="hidden" name="days_of_week" :value="JSON.stringify(formData.days_of_week)">
        </form>
    </div>
</div>

@include('progression.partials.exercise-modal')

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    .animate-fade-left { animation: fadeLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.1); border-radius: 20px; }
    body { background-color: #0b0e14; }

    [x-cloak] { display: none !important; }
</style>

<script>
    function trainingPlanBuilder() {
        return {
            step: 1,
            isPremium: {{ Auth::user()->isPremiumActive() ? 'true' : 'false' }},
            autoSaved: false,
            formData: {
                name: '',
                plan_label: '',
                goal: '',
                goal_custom: '',
                student_profile: 'Intermediário',
                split_type: 'ABC',
                frequency: 3,
                days_of_week: [],
                difficulty: 'Intermediário',
                estimated_duration: 0,
                description: '',
                status: 'Rascunho',
                is_template: false
            },
            exercises: [],
            
            init() {
                // Hidratação a partir de input antigo (caso validação falhe no servidor)
                const oldExercises = {!! json_encode(old('exercises_json')) !!};
                if (oldExercises) {
                    try {
                        this.exercises = JSON.parse(oldExercises);
                        this.calculateDuration();
                        // Se houver erros, volta para o passo final para revisão
                        this.step = 5;
                    } catch(e) {
                        this.exercises = [];
                    }
                }

                this.$watch('exercises', () => {
                    this.calculateDuration();
                }, { deep: true });

                // Escutar evento do modal
                window.addEventListener('add-exercise', (e) => {
                    this.addExercise(e.detail.id, e.detail.name, e.detail.muscles);
                });

                // Auto-save logic
                setInterval(() => {
                    this.autoSave();
                }, 15000);
            },

            stepTitle() {
                const titles = { 1: 'Dados', 2: 'Exercícios', 3: 'Séries', 4: 'Revisão', 5: 'Finalizar' };
                return titles[this.step];
            },

            toggleDay(day) {
                if (this.formData.days_of_week.includes(day)) {
                    this.formData.days_of_week = this.formData.days_of_week.filter(d => d !== day);
                } else {
                    this.formData.days_of_week.push(day);
                }
            },

            addExercise(id, name, musclesStr = '') {
                const muscles = musclesStr ? musclesStr.split(', ') : [];
                const newExercise = {
                    tempId: Date.now() + Math.random(),
                    id: parseInt(id),
                    name: name,
                    muscles: muscles,
                    sets: [
                        { tempId: Date.now() + 1, type: 'work', reps: 12, weight: 0, rest: 60, rpe: null, cadence: '' },
                        { tempId: Date.now() + 2, type: 'work', reps: 12, weight: 0, rest: 60, rpe: null, cadence: '' },
                        { tempId: Date.now() + 3, type: 'work', reps: 12, weight: 0, rest: 60, rpe: null, cadence: '' }
                    ]
                };
                
                this.exercises = [...this.exercises, newExercise];
                this.calculateDuration();
                this.showToast('Exercício adicionado!');
            },

            removeExercise(index) {
                this.exercises.splice(index, 1);
            },

            addSet(exIdx) {
                this.exercises[exIdx].sets.push({
                    tempId: Date.now() + Math.random(),
                    type: 'work', reps: 12, weight: 0, rest: 60, rpe: null, cadence: ''
                });
            },

            removeSet(exIdx, setIdx) {
                this.exercises[exIdx].sets.splice(setIdx, 1);
            },

            calculateDuration() {
                let totalSeconds = 0;
                this.exercises.forEach(ex => {
                    ex.sets.forEach(set => {
                        totalSeconds += 40 + (parseInt(set.rest) || 60);
                    });
                });
                this.formData.estimated_duration = Math.ceil(totalSeconds / 60);
            },

            totalVolume() {
                let total = 0;
                this.exercises.forEach(ex => {
                    ex.sets.forEach(set => {
                        total += (parseInt(set.reps) || 0) * (parseFloat(set.weight) || 0);
                    });
                });
                return total;
            },

            exerciseTotalWeight(ex) {
                let total = 0;
                ex.sets.forEach(set => {
                    total += (parseInt(set.reps) || 0) * (parseFloat(set.weight) || 0);
                });
                return total;
            },

            musclesWorked() {
                const muscles = new Set();
                this.exercises.forEach(ex => {
                    if (ex.muscles) ex.muscles.forEach(m => muscles.add(m));
                });
                return Array.from(muscles);
            },

            toggleTemplate() {
                if (!this.isPremium) return;
                this.formData.is_template = !this.formData.is_template;
            },

            autoSave() {
                this.autoSaved = true;
                setTimeout(() => this.autoSaved = false, 3000);
            },

            showToast(message, type = 'success') {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
            },

            submitForm() {
                if (!this.formData.name || this.formData.name.trim() === '') {
                    this.showToast('O nome do plano de treino é obrigatório!', 'error');
                    this.step = 1;
                    return;
                }

                if (this.exercises.length === 0) {
                    this.showToast('Adicione exercícios!', 'error');
                    this.step = 2;
                    return;
                }

                // Garantir que o JSON está atualizado no input antes do submit final
                const exercisesJson = JSON.stringify(this.exercises);
                document.getElementById('exercises_json_field').value = exercisesJson;
                
                document.getElementById('planForm').submit();
            }
        };
    }
</script>
@endsection
