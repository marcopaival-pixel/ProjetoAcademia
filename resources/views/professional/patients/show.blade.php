@extends('layouts.app')

@section('title', 'Bio-Intelligence — ' . $patient['name'])

@section('content')
    <div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6"
        x-data="{ activeTab: 'diary', chartMode: 'weight', showAssessmentModal: false }">
        <!-- Header Strategy: Professional Navigation + Breadcrumb -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pb-4 border-b border-white/5">
            <div class="space-y-3">
                <nav class="flex items-center gap-2 text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                    <a href="{{ route('professional.patients.index') }}"
                        class="hover:text-blue-400 transition-colors">Paciente Cadastrado</a>
                    <span>
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                    <span class="text-white">Registro Biológico Ativo</span>
                </nav>
                <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                    {{ $patient['name'] }} <span
                        class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Insight</span>
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                    <button onclick="window.print()"
                        class="p-3 bg-zinc-800 text-zinc-500 hover:text-white rounded-xl transition-all border border-white/5 group shadow-lg"
                        title="Imprimir Prontuário">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                    </button>
                    <button onclick="location.href='{{ route('messages.index') }}'"
                        class="p-3 bg-zinc-800 text-zinc-500 hover:text-white rounded-xl transition-all border border-white/5 group shadow-lg"
                        title="Enviar Mensagem">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Sidebar Perfil (Col 4) - Biometrics Card -->
            <div class="lg:col-span-4 xl:col-span-3 space-y-8">
                <div
                    class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] text-center overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>

                    <div class="relative z-10 space-y-8">
                        <div
                            class="w-40 h-40 mx-auto rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-800 flex items-center justify-center text-white text-5xl font-black shadow-[0_30px_60px_-15px_rgba(59,130,246,0.5)] group-hover:scale-105 transition-transform duration-700">
                            @php
                                $parts = explode(' ', $patient['name']);
                                $initials = count($parts) > 1
                                    ? mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1)
                                    : mb_substr($parts[0], 0, 2);
                            @endphp
                            {{ strtoupper($initials) }}
                        </div>

                        <div class="space-y-2">
                            <span
                                class="px-4 py-1 bg-white/5 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-400/20 tracking-[0.2em] mb-2 inline-block">{{ $patient['biotype'] }}</span>
                            <h2 class="text-3xl font-black text-white tracking-tight">{{ $patient['name'] }}</h2>
                            <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-widest italic">
                                {{ $patient['age'] }} anos — Status Ativo
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-6 py-8 border-t border-white/5">
                            <div class="text-left space-y-2">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Bio-Height</p>
                                <p class="text-2xl font-black text-white tracking-tighter">{{ $patient['height'] }}<span
                                        class="text-xs text-zinc-500 ml-1">cm</span></p>
                            </div>
                            <div class="text-left space-y-2">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Bio-Weight</p>
                                <p class="text-2xl font-black text-white tracking-tighter">{{ $patient['weight'] }}<span
                                        class="text-xs text-zinc-500 ml-1">kg</span></p>
                            </div>
                        </div>

                        <button @click="showAssessmentModal = true"
                            class="w-full py-5 bg-white text-zinc-900 font-black rounded-3xl hover:bg-emerald-400 hover:text-white transition-all shadow-2xl text-xs uppercase tracking-widest active:scale-95">
                            Atualizar Medidas
                        </button>
                    </div>
                </div>

                <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[3rem] space-y-6 shadow-2xl">
                    <div class="flex items-center gap-4 px-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                        <h3 class="text-zinc-400 font-black text-[10px] uppercase tracking-[0.3em]">Estratégia Operacional
                            <h3>
                    </div>
                    <div
                        class="p-8 bg-zinc-950/50 rounded-[2rem] border border-blue-500/10 shadow-inner group hover:border-blue-500/30 transition-all">
                        <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-4">Objetivo principal
                        </p>
                        <p
                            class="text-white text-lg font-black leading-tight mb-4 group-hover:text-blue-400 transition-colors">
                            {{ $patient['goal'] }}
                        </p>
                        <div class="pt-4 border-t border-white/5 flex flex-col gap-1">
                            <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest">Matriz de
                                Protocolos</span>
                            <span class="text-zinc-400 text-xs font-bold font-mono">{{ $patient['formula'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal (Col 8) -->
            <div class="lg:col-span-8 xl:col-span-9 space-y-10">
                <!-- Gráfico de Evolução - High Precision Design -->
                <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[4rem] shadow-2xl overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 blur-[100px] -mr-32 -mt-32 rounded-full"></div>

                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                            <div>
                                <span class="px-4 py-1 bg-white/5 text-zinc-500 text-[9px] font-black uppercase rounded-full border border-white/10 tracking-[0.2em] mb-4 inline-block italic">Bio-Stats Historical Feed</span>
                                <h3 class="text-3xl font-black text-white tracking-tight">Evolução Biométrica</h3>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span class="text-[8px] font-black uppercase text-zinc-500">Peso (kg)</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-[8px] font-black uppercase text-zinc-500">BF (%)</span>
                                </div>
                            </div>
                        </div>

                        <div id="evolutionChart" class="h-80 w-full"></div>
                    </div>
                </div>

                <!-- Anatomical Map Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] flex items-center gap-8 group">
                        <div class="relative w-32 shrink-0">
                            <img src="{{ asset('images/body/' . ($gender == 'F' ? 'female' : 'male') . '_front.png') }}" class="w-full opacity-20 grayscale brightness-200" alt="Silhouette">
                            <div class="absolute top-[25%] left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
                            <div class="absolute top-[40%] left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
                            <div class="absolute top-[55%] left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
                        </div>
                        <div class="flex-1 space-y-4">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-2">Resumo de Medidas</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-0.5">
                                    <span class="text-[7px] font-black text-zinc-600 uppercase">Tórax</span>
                                    <div class="text-sm font-black text-white italic">{{ $latest->chest ?? '--' }}cm</div>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-[7px] font-black text-zinc-600 uppercase">Cintura</span>
                                    <div class="text-sm font-black text-white italic">{{ $latest->waist ?? '--' }}cm</div>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-[7px] font-black text-zinc-600 uppercase">Abdômen</span>
                                    <div class="text-sm font-black text-white italic">{{ $latest->abdomen ?? '--' }}cm</div>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-[7px] font-black text-zinc-600 uppercase">Quadril</span>
                                    <div class="text-sm font-black text-white italic">{{ $latest->hips ?? '--' }}cm</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delta Stats Card -->
                    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] flex flex-col justify-center space-y-6">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Deltas de Evolução (Última vs Penúltima)</h4>
                        <div class="flex items-center gap-10">
                            <div class="space-y-1">
                                <span class="text-[8px] font-black text-zinc-600 uppercase">Variação Peso</span>
                                <div class="flex items-center gap-2">
                                    <div class="text-2xl font-black italic {{ $deltaWeight > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                        {{ $deltaWeight > 0 ? '+' : '' }}{{ number_format($deltaWeight, 1) }}kg
                                    </div>
                                    <i class="fas fa-caret-{{ $deltaWeight > 0 ? 'up' : 'down' }} {{ $deltaWeight > 0 ? 'text-red-400' : 'text-emerald-400' }}"></i>
                                </div>
                            </div>
                            <div class="w-px h-10 bg-white/5"></div>
                            <div class="space-y-1">
                                <span class="text-[8px] font-black text-zinc-600 uppercase">Variação BF</span>
                                <div class="flex items-center gap-2">
                                    <div class="text-2xl font-black italic {{ $deltaBf > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                        {{ $deltaBf > 0 ? '+' : '' }}{{ number_format($deltaBf, 1) }}%
                                    </div>
                                    <i class="fas fa-caret-{{ $deltaBf > 0 ? 'up' : 'down' }} {{ $deltaBf > 0 ? 'text-red-400' : 'text-emerald-400' }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs de Acompanhamento - Interactive Bento Design -->
                <div class="space-y-8">
                    <div class="flex border-b border-white/5 gap-12 px-6 overflow-x-auto scrollbar-hide">
                        <button onclick="switchTab('diary')" id="tab-diary"
                            class="tab-btn pb-6 text-blue-400 font-black border-b-[3px] border-blue-400 text-[10px] uppercase tracking-[0.3em] whitespace-nowrap">Clinical
                            Diary</button>
                        <button onclick="switchTab('training')" id="tab-training"
                            class="tab-btn pb-6 text-zinc-600 font-black hover:text-white transition-all text-[10px] uppercase tracking-[0.3em] whitespace-nowrap">Training
                            Matrix</button>
                        <button onclick="switchTab('evaluations')" id="tab-evaluations"
                            class="tab-btn pb-6 text-zinc-600 font-black hover:text-white transition-all text-[10px] uppercase tracking-[0.3em] whitespace-nowrap">Bio-Evaluations</button>
                    </div>

                    <div id="content-diary" class="tab-content grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Progress Card -->
                        <div
                            class="group bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] space-y-8 shadow-xl hover:border-emerald-500/20 transition-all">
                            <div class="flex items-center justify-between">
                                <h4 class="text-white font-black text-sm uppercase tracking-widest">Metas Bio-Nutricionais
                                </h4>
                                <span
                                    class="text-[10px] text-emerald-400 font-black uppercase tracking-widest px-3 py-1 bg-emerald-500/5 rounded-full border border-emerald-500/20">92%
                                    COMPLIANCE</span>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                        <span>Protein Load</span><span class="text-white">182g / 200g</span>
                                    </div>
                                    <div
                                        class="w-full bg-zinc-950 h-3 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
                                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.5)]"
                                            style="width: 91%"></div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                        <span>Glycogen Index</span><span class="text-white">210g / 250g</span>
                                    </div>
                                    <div
                                        class="w-full bg-zinc-950 h-3 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
                                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 h-full rounded-full transition-all duration-1000"
                                            style="width: 84%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <a href="{{ route('professional.ai-wizard.index') }}"
                            class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] flex flex-col items-center justify-center text-center space-y-4 hover:bg-blue-600/10 hover:border-blue-500/30 transition-all shadow-xl overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            </div>

                            <div
                                class="w-20 h-20 bg-blue-500/10 rounded-[2rem] border border-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 z-10 shadow-2xl">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="z-10">
                                <p class="text-white font-black text-xl tracking-tight mb-2">Engage AI Wizard</p>
                                <p class="text-zinc-500 font-bold uppercase text-[9px] tracking-widest leading-relaxed">
                                    Gerar novo protocolo dinâmico<br>baseado no histórico clínico</p>
                            </div>
                        </a>
                    </div>

                    <div id="content-training" class="tab-content hidden animate-fade-in">
                        <div
                            class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[3rem] text-center space-y-6">
                            <div
                                class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-400 mx-auto">
                                <i class="fas fa-dumbbell text-2xl"></i>
                            </div>
                            <h3 class="text-white font-black text-2xl italic tracking-tight">Training Plan Level II
                                Activated</h3>
                            <p class="text-zinc-500 text-sm max-w-md mx-auto">O plano de treinamento atual está focado em
                                hipertrofia sarcoplasmática com técnicas avançadas de tempo sob tensão.</p>
                            <button
                                class="px-8 py-3 bg-white text-zinc-900 font-black rounded-xl text-[10px] uppercase tracking-widest hover:bg-blue-400 hover:text-white transition-all">Ver
                                PDF do Treino</button>
                        </div>
                    </div>

                    <div id="content-evaluations" class="tab-content hidden animate-fade-in">
                        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] space-y-6">
                            <div class="flex items-center justify-between border-b border-white/5 pb-6">
                                <h4 class="text-white font-black text-sm uppercase tracking-widest">Últimas Avaliações</h4>
                                <button @click="showAssessmentModal = true" class="text-emerald-400 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">+ Nova Avaliação</button>
                            </div>
                            <div class="space-y-4">
                                @foreach(['Avaliação Antropométrica - Abr/2026', 'Protocolo de Dobras - Fev/2026'] as $eval)
                                    <div
                                        class="flex items-center justify-between p-4 bg-zinc-950/40 rounded-2xl border border-white/5 hover:border-blue-500/30 transition-all group">
                                        <span class="text-zinc-400 text-xs font-bold">{{ $eval }}</span>
                                        <i
                                            class="fas fa-chevron-right text-zinc-700 group-hover:text-blue-400 transition-colors"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal de Avaliação -->
        <div x-show="showAssessmentModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="showAssessmentModal" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showAssessmentModal = false"></div>
            <div x-show="showAssessmentModal" x-transition.scale.origin.bottom class="relative bg-zinc-900 border border-white/10 rounded-[2rem] w-full max-w-2xl overflow-hidden shadow-2xl z-10 flex flex-col max-h-[90vh]">
                <div class="p-6 border-b border-white/5 flex items-center justify-between bg-zinc-900/50 backdrop-blur-xl z-20">
                    <h3 class="text-xl font-black text-white italic tracking-tighter uppercase">Nova Avaliação Biométrica</h3>
                    <button @click="showAssessmentModal = false" class="text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-8 overflow-y-auto custom-scrollbar">
                    <form action="{{ route('assessments.store') }}" method="POST" id="assessmentForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient['id'] }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data da Avaliação</label>
                                <input type="date" name="assessment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Peso (kg)</label>
                                <input type="number" step="0.1" name="weight_kg" placeholder="Ex: 75.5" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Percentual de Gordura (%)</label>
                                <input type="number" step="0.1" name="bf_percent" placeholder="Ex: 15.2 (Opcional)" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Massa Muscular (%)</label>
                                <input type="number" step="0.1" name="muscle_percent" placeholder="Opcional" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-white/5">
                            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-4">Medidas (Perimetria)</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Tórax</label>
                                    <input type="number" step="0.1" name="chest" placeholder="cm" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:border-emerald-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Cintura</label>
                                    <input type="number" step="0.1" name="waist" placeholder="cm" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:border-emerald-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Abdômen</label>
                                    <input type="number" step="0.1" name="abdomen" placeholder="cm" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:border-emerald-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Quadril</label>
                                    <input type="number" step="0.1" name="hips" placeholder="cm" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:border-emerald-500 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Observações Clínicas</label>
                            <textarea name="notes" rows="3" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none resize-none" placeholder="Anotações sobre a avaliação..."></textarea>
                        </div>

                        <div class="pt-4 flex gap-4">
                            <button type="button" @click="showAssessmentModal = false" class="flex-1 py-4 bg-zinc-800 text-zinc-300 font-black rounded-2xl hover:bg-zinc-700 transition-all text-xs uppercase tracking-widest">Cancelar</button>
                            <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all text-xs uppercase tracking-widest shadow-lg shadow-emerald-500/20">Salvar Avaliação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = {
                series: [{
                    name: 'Peso (kg)',
                    data: {!! json_encode($chartData['weight']) !!}
                }, {
                    name: 'BF (%)',
                    data: {!! json_encode($chartData['bf']) !!}
                }],
                chart: {
                    height: 320,
                    type: 'area',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    background: 'transparent',
                    foreColor: '#52525b'
                },
                colors: ['#3b82f6', '#10b981'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: {!! json_encode($chartData['dates']) !!},
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: {
                            fontSize: '10px',
                            fontWeight: 900
                        }
                    }
                },
                yaxis: {
                    show: false
                },
                grid: {
                    show: true,
                    borderColor: 'rgba(255, 255, 255, 0.03)',
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: false } }
                },
                legend: { show: false },
                tooltip: {
                    theme: 'dark',
                    x: { show: true },
                    y: {
                        formatter: function(val) { return val ? val.toFixed(1) : '--' }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#evolutionChart"), options);
            chart.render();
        });

        function switchTab(tabId) {
            // Update Buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-blue-400', 'border-b-[3px]', 'border-blue-400');
                btn.classList.add('text-zinc-600');
            });
            const activeBtn = document.getElementById('tab-' + tabId);
            activeBtn.classList.add('text-blue-400', 'border-b-[3px]', 'border-blue-400');
            activeBtn.classList.remove('text-zinc-600');

            // Update Content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('content-' + tabId).classList.remove('hidden');
        }

        // Inicializar aba padrão
        window.addEventListener('load', () => switchTab('diary'));
    </script>

    <style>
        @keyframes dashboard-entry {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-dashboard-entry {
            animation: dashboard-entry 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        body {
            background-color: #080a0f;
            background-image:
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.1) 0, transparent 40%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0, transparent 40%),
                radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
            background-attachment: fixed;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection