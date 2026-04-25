@extends('layouts.app')

@section('title', 'Dashboard — NexShape')

@section('content')
    <div class="py-10 space-y-12 animate-dashboard-entry mx-auto px-4 md:px-6">
        <!-- Header Strategy: Glassmorphic Floating Header -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span
                        class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Modo
                        Performance Ativo</span>
                    <span class="text-zinc-600">•</span>
                    <span class="text-zinc-400 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
                </div>
                <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                    Força, <span
                        class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400">{{ explode(' ', Auth::user()->name)[0] }}</span>!
                </h1>
            </div>

            @if($showChecklist)
            <!-- Onboarding Progress Bar -->
            <div class="hidden lg:flex flex-col items-end gap-2 max-w-xs w-full">
                <div class="flex items-center justify-between w-full text-[10px] font-black uppercase tracking-widest">
                    <span class="text-zinc-500 italic">SETUP DA PERFORMANCE</span>
                    <span class="text-blue-400">{{ round($setupPercentage) }}%</span>
                </div>
                <div class="h-2 w-full bg-zinc-900 rounded-full border border-white/5 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-600 to-indigo-400 transition-all duration-1000" style="width: {{ $setupPercentage }}%"></div>
                </div>
            </div>
            @elseif(Auth::user()->profile_completion_percentage < 100)
            <div class="hidden lg:flex flex-col items-end gap-2 max-w-xs w-full">
                <div class="flex items-center justify-between w-full text-[10px] font-black uppercase tracking-widest">
                    <span class="text-zinc-500 italic">PERFIL BIO-HUMANO</span>
                    <span class="text-blue-400">{{ Auth::user()->profile_completion_percentage }}%</span>
                </div>
                <div class="h-2 w-full bg-zinc-900 rounded-full border border-white/5 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-400 transition-all duration-1000" style="width: {{ Auth::user()->profile_completion_percentage }}%"></div>
                </div>
            </div>
            @endif

            <div class="flex flex-wrap items-center gap-4">
                <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                    <a href="{{ route('diary') }}"
                        class="group relative px-6 py-3 bg-blue-600 text-white font-bold rounded-xl overflow-hidden transition-all hover:pr-10 active:scale-95">
                        <span class="relative z-10">Refeição</span>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-0 group-hover:opacity-100 transition-all"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                    <a href="{{ route('exercise') }}"
                        class="px-6 py-3 text-zinc-400 hover:text-white font-bold rounded-xl transition-all">Treino</a>
                </div>

                <div class="relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000">
                    </div>
                    <div
                        class="relative flex items-center gap-3 bg-zinc-900 px-5 py-2.5 rounded-2xl border border-white/10">
                        <span class="text-2xl animate-bounce-slow">🔥</span>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-tighter">Streak Atual</p>
                            <p class="text-white font-black text-lg leading-none">12 Dias</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reminder Banner for Incomplete Profile -->
        @if(Auth::user()->isOnboardingPending())
        <div class="bg-gradient-to-r from-blue-600/10 to-indigo-600/10 border border-blue-500/20 rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between gap-8 shadow-2xl backdrop-blur-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-blue-500 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-blue-500/20 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                    <i class="fas fa-user-astronaut text-3xl relative z-10"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight">Potencialize seu NexShape</h3>
                    <p class="text-zinc-400 font-medium">Seu perfil está <span class="text-blue-400 font-bold">{{ Auth::user()->profile_completion_percentage }}%</span> completo. Finalize para liberar insights biométricos.</p>
                </div>
            </div>
            <button @click="$dispatch('open-onboarding')" class="px-10 py-4 bg-white text-zinc-950 font-black rounded-2xl hover:bg-blue-500 hover:text-white transition-all transform hover:scale-105 active:scale-95 shadow-xl whitespace-nowrap">
                COMPLETAR AGORA
            </button>
        </div>
        @endif

        <!-- Layout Bento Moderno -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            <!-- Bloco de Calorias Master (Full Width & Centralizado) -->
            <div class="lg:col-span-12 space-y-12">
                <div
                    class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] overflow-hidden shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] transition-all hover:border-white/10">
                    <!-- Glossy Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                    <div
                        class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500/10 blur-[150px] rounded-full group-hover:scale-125 transition-transform duration-1000">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center relative z-10">
                        <!-- Progress Hub (Centralizado e Aumentado) -->
                        <div class="flex flex-col items-center justify-center w-full">
                            <div class="relative w-full max-w-[450px] aspect-square flex items-center justify-center">
                                <!-- Inner Glow -->
                                <div class="absolute inset-10 rounded-full bg-blue-500/5 blur-3xl"></div>

                                <svg class="w-full h-full -rotate-90" viewBox="0 0 450 450">
                                    <circle cx="225" cy="225" r="200" stroke="currentColor" stroke-width="4"
                                        fill="transparent" class="text-zinc-800/20" />
                                    <circle cx="225" cy="225" r="200" stroke="currentColor" stroke-width="12"
                                        fill="transparent" class="text-zinc-800/50" />
                                    <circle cx="225" cy="225" r="200" stroke="url(#cyber_gradient)" stroke-width="24"
                                        fill="transparent" stroke-dasharray="1256"
                                        stroke-dashoffset="{{ 1256 - (1256 * min($consumed / ($calorieTarget ?: 2000), 1)) }}"
                                        stroke-linecap="round"
                                        class="transition-all duration-1000 ease-in-out drop-shadow-[0_0_15px_rgba(59,130,246,0.5)]" />
                                    <defs>
                                        <linearGradient id="cyber_gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#3b82f6" />
                                            <stop offset="50%" stop-color="#2dd4bf" />
                                            <stop offset="100%" stop-color="#3b82f6" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                    <div class="relative">
                                        <span
                                            class="text-8xl font-black text-white tracking-tight tabular-nums">{{ number_format($remaining) }}</span>
                                        <span class="absolute -top-3 -right-8 text-blue-400 font-bold text-lg leading-none">kcal</span>
                                    </div>
                                    <span
                                        class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] mt-5 bg-zinc-800/50 px-5 py-1.5 rounded-full">Disponíveis</span>
                                </div>
                            </div>
                        </div>

                        <!-- Macro Architecture -->
                        <div class="space-y-12">
                            <div class="grid grid-cols-1 gap-8">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between font-bold">
                                        <span class="text-zinc-400 text-[11px] uppercase tracking-widest">Consumo
                                            Diário</span>
                                        <span
                                            class="text-white text-lg">{{ number_format(($consumed / ($calorieTarget ?: 2000)) * 100, 0) }}%</span>
                                    </div>
                                    <div class="h-4 w-full bg-zinc-950 rounded-full p-1 border border-white/5 shadow-inner">
                                        <div class="h-full bg-gradient-to-r from-blue-600 via-emerald-400 to-blue-400 rounded-full transition-all duration-1000 relative"
                                            style="width: {{ min(($consumed / ($calorieTarget ?: 2000)) * 100, 100) }}%">
                                            <div class="absolute top-0 right-0 h-full w-4 bg-white/20 blur-sm rounded-full">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6">
                                @php
                                    $macros = [
                                        ['label' => 'Prot', 'val' => $sumProt, 'color' => 'blue', 'icon' => '🍖'],
                                        ['label' => 'Carb', 'val' => $sumCarb, 'color' => 'purple', 'icon' => '🥖'],
                                        ['label' => 'Gord', 'val' => $sumFat, 'color' => 'amber', 'icon' => '🥑'],
                                    ];
                                @endphp
                                @foreach($macros as $m)
                                    <div class="relative group/macro cursor-help">
                                        <div
                                            class="absolute -inset-2 bg-{{ $m['color'] }}-500/0 group-hover/macro:bg-{{ $m['color'] }}-500/5 rounded-3xl transition-all duration-300">
                                        </div>
                                        <div class="text-center relative">
                                            <div class="text-xl mb-2">{{ $m['icon'] }}</div>
                                            <p class="text-[10px] text-zinc-500 font-black uppercase mb-1 tracking-tighter">
                                                {{ $m['label'] }}</p>
                                            <p class="text-white font-black text-xl">{{ number_format($m['val'], 0) }}g</p>
                                            <div class="mt-2 h-1 w-8 mx-auto bg-zinc-800 rounded-full overflow-hidden">
                                                @php
                                                    $targetKey = $m['label'] == 'Prot' ? 'p' : ($m['label'] == 'Carb' ? 'c' : 'f');
                                                    $percent = ($hasMacroTargets && ($macroTargets[$targetKey] ?? 0) > 0) 
                                                        ? min(($m['val'] / $macroTargets[$targetKey]) * 100, 100) 
                                                        : 0;
                                                @endphp
                                                <div class="h-full bg-{{ $m['color'] }}-500 shadow-[0_0_8px_rgba(var(--tw-color-{{ $m['color'] }}-500),0.5)]"
                                                    style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                
                <!-- Advanced Performance Metrics Bar -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div
                        class="relative group bg-zinc-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5 transition-all hover:bg-zinc-800/40">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center text-xl">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Gordura Corporal
                                    (Estimada)</p>
                                <h4 class="text-3xl font-black text-white">
                                    {{ $latestAssessment ? $latestAssessment->bf_percent . '%' : 'S/ Dados' }}</h4>
                            </div>
                        </div>
                        @if($latestAssessment)
                            <p class="mt-4 text-[10px] text-zinc-600 font-bold italic uppercase tracking-tighter">* Método da
                                Marinha via Circunferência</p>
                        @endif
                    </div>

                    <div
                        class="relative group bg-zinc-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5 transition-all hover:bg-zinc-800/40">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-amber-500/20 text-amber-400 rounded-xl flex items-center justify-center text-xl">
                                <i class="fas fa-weight-hanging"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Melhor 1RM
                                    Estimado</p>
                                <h4 class="text-3xl font-black text-white">
                                    {{ $topExercisePR ? round($topExercisePR->one_rm) . 'kg' : '---' }}
                                </h4>
                            </div>
                        </div>
                        @if($topExercisePR)
                            <p class="mt-4 text-[10px] text-zinc-600 font-bold uppercase tracking-tighter">
                                {{ $topExercisePR->exercise_name }} ({{ round($topExercisePR->weight_kg) }}kg x
                                {{ $topExercisePR->reps_done }})</p>
                        @endif
                    </div>

                    <!-- NOVO: Widget de Predição IA (Premium) -->
                    <a @if(!$isPremium) data-premium-locked @elseif($nextTraining) href="{{ route('progression.log', $nextTraining->id) }}" @else href="{{ route('progression.plans.index') }}" @endif
                        class="relative group bg-indigo-900/20 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-indigo-500/20 shadow-lg shadow-indigo-500/5 cursor-pointer overflow-hidden transition-all hover:bg-indigo-900/40 no-underline block">
                        @if(!$isPremium)
                            <div class="absolute inset-0 bg-zinc-950/40 backdrop-blur-[4px] z-20 flex flex-col items-center justify-center text-center p-6">
                                <i class="fas fa-lock text-indigo-400 text-xl mb-2"></i>
                                <p class="text-[9px] text-white font-black uppercase tracking-widest">Previsão IA Premium</p>
                            </div>
                        @endif
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-500/20 text-indigo-400 rounded-xl flex items-center justify-center text-xl">
                                <i class="fas fa-brain animate-pulse"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Atalhar Resultados</p>
                                <h4 class="text-2xl font-black text-white">Sugerir Cargas</h4>
                            </div>
                        </div>
                        
                        @if($isPremium && $neuralPrediction)
                            <div class="mt-6 p-4 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl">
                                <p class="text-[10px] text-indigo-300 font-black uppercase tracking-tighter mb-1">{{ $topExercisePR->exercise_name }}</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-white">{{ $neuralPrediction['suggested_weight'] }} <small class="text-xs">kg</small></span>
                                    <span class="text-[10px] text-zinc-500 font-bold">({{ $neuralPrediction['confidence'] }}% Confiança)</span>
                                </div>
                                <p class="mt-3 text-[10px] text-indigo-100 font-medium leading-relaxed italic">"{{ $neuralPrediction['message'] }}"</p>
                            </div>
                        @else
                            <p class="mt-4 text-[10px] text-indigo-300 font-bold uppercase tracking-tighter">Otimização Neural Ativa</p>
                        @endif
                    </a>
                </div>

                @if($showChecklist)
                <!-- Checklist de Performance (Onboarding) -->
                <div class="lg:col-span-12 space-y-8 animate-fade-in mt-6">
                    <div class="flex items-center gap-3">
                        <div class="h-[1px] flex-grow bg-white/5"></div>
                        <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] italic">Roadmap de Elite</h2>
                        <div class="h-[1px] flex-grow bg-white/5"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach($setupChecklist as $key => $task)
                        <a href="{{ $task['route'] }}" 
                           @if($task['premium'] && !$isPremium) data-premium-locked @endif
                           class="group relative p-6 bg-zinc-900/40 backdrop-blur-3xl border {{ $task['done'] ? 'border-emerald-500/20' : 'border-white/5' }} rounded-3xl transition-all hover:scale-[1.02] no-underline">
                            
                            @if($task['premium'] && !$isPremium)
                                <div class="absolute top-3 right-3 text-amber-500/50">
                                    <i class="fas fa-crown text-[10px]"></i>
                                </div>
                            @endif

                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors {{ $task['done'] ? 'bg-emerald-500 text-white' : 'bg-zinc-800 text-zinc-500' }}">
                                    @if($task['done'])
                                        <i class="fas fa-check"></i>
                                    @else
                                        <span class="font-black text-xs">{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                                <div class="flex-grow">
                                    <p class="text-xs font-black {{ $task['done'] ? 'text-white' : 'text-zinc-400' }} leading-tight">{{ $task['label'] }}</p>
                                    @if($task['premium'])
                                        <p class="text-[8px] text-blue-400 font-bold uppercase tracking-tighter mt-1 italic">Recurso PRO</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Dashboard Dynamic Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Pro Training: Neon Rail Effect -->
                    <div
                        class="group relative bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3.5rem] border border-white/5 overflow-hidden transition-all hover:scale-[1.01] hover:shadow-2xl">
                        <div
                            class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-blue-500/50 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-700">
                        </div>

                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-blue-600 to-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white">Treino diário</h3>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Sugerido para
                                            você</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($nextTraining)
                            <div class="mb-10 p-6 bg-white/5 rounded-3xl border border-white/5">
                                <h4 class="text-xl font-bold text-white mb-2">{{ $nextTraining->name }}</h4>
                                <p class="text-zinc-400 text-sm italic">"Prepare-se para superar seus limites hoje."</p>
                            </div>
                        @endif

                        <a href="{{ route('exercise') }}"
                            class="flex items-center justify-between w-full p-2 pr-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-blue-400 hover:text-white transition-all group/btn">
                            <div
                                class="h-14 w-14 bg-zinc-900 text-white rounded-2xl flex items-center justify-center group-hover/btn:bg-white group-hover/btn:text-blue-500 transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-lg">COMEÇAR AGORA</span>
                            <svg class="w-5 h-5 transition-transform group-hover/btn:translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- AI Intelligence: Particle Card -->
                    <div
                        class="group bg-gradient-to-br from-indigo-950/40 to-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3.5rem] border border-white/5 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 opacity-5 group-hover:opacity-10 transition-opacity">
                            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z" />
                            </svg>
                        </div>

                        <div class="flex items-center justify-between mb-8">
                            <div
                                class="px-3 py-1 rounded-lg bg-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest border border-indigo-500/30">
                                {{ $isPremium ? 'Genetic Performance Analysis (IA)' : 'Insights NexBot (Padrão)' }}
                            </div>
                            @if(!$isPremium)
                                <div data-premium-locked class="w-6 h-6 bg-amber-500/20 text-amber-500 rounded-lg flex items-center justify-center cursor-pointer hover:bg-amber-500/40 transition-all">
                                    <i class="fas fa-lock text-[10px]"></i>
                                </div>
                            @endif
                        </div>

                        <div class="relative">
                            <svg class="absolute -left-6 -top-4 w-12 h-12 text-indigo-500/20" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M14.017 21L14.017 18C14.017 16.8954 13.1216 16 12.017 16H9.01703V14H12.017C14.2262 14 16.017 12.2091 16.017 10V7C16.017 5.89543 15.1216 5 14.017 5H11.017V3H14.017C16.2262 3 18.017 4.79086 18.017 7V10C18.017 12.9066 15.9392 15.3283 13.1678 15.8458C13.5936 16.3533 14.017 17.0706 14.017 18V21H14.017Z" />
                            </svg>
                            <p class="text-2xl font-bold text-white leading-relaxed tracking-tight pl-4">
                                "{{ $aiInsight }}"
                            </p>
                        </div>

                        <div class="mt-12 flex items-center justify-between border-t border-white/5 pt-8">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                    <span class="text-white font-black">AI</span>
                                </div>
                                <span class="text-xs text-zinc-500 font-bold">NexShape Neural</span>
                            </div>
                            <button class="text-zinc-600 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mentor / Profissional Vínculo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="group relative bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3.5rem] border {{ $linkedProfessional ? 'border-indigo-500/20' : ($pendingRequest ? 'border-amber-500/20' : 'border-white/5') }} overflow-hidden transition-all hover:scale-[1.01]">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg {{ $linkedProfessional ? 'bg-indigo-600' : 'bg-zinc-800' }}">
                                    @if($linkedProfessional)
                                        <i class="fas fa-user-md text-2xl"></i>
                                    @else
                                        <i class="fas fa-user-plus text-2xl text-zinc-500"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white">
                                        {{ $linkedProfessional ? $linkedProfessional->name : ($pendingRequest ? 'Solicitação Enviada' : 'Mentor Profissional') }}
                                    </h3>
                                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">
                                        {{ $linkedProfessional ? 'Acompanhamento Ativo' : ($pendingRequest ? 'Aguardando Aprovação' : 'Aumente sua Performance') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($linkedProfessional)
                            <div class="mb-10 p-6 bg-white/5 rounded-3xl border border-white/5">
                                <p class="text-zinc-400 text-sm leading-relaxed">Você está sendo acompanhado por <span class="text-white font-bold">{{ $linkedProfessional->name }}</span>. Suas medidas e planos são validados por este mentor.</p>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{ route('internal-email.create', ['to' => $linkedProfessional->id]) }}" class="flex-1 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl transition-all text-[10px] uppercase tracking-widest text-center no-underline">
                                    Enviar Mensagem
                                </a>
                                <a href="{{ route('assessments.create') }}" class="flex-1 py-4 bg-white/5 hover:bg-white/10 text-white font-black rounded-2xl border border-white/5 transition-all text-[10px] uppercase tracking-widest text-center no-underline">
                                    Enviar Medidas
                                </a>
                            </div>
                        @elseif($pendingRequest)
                            <div class="mb-10 p-6 bg-amber-500/5 rounded-3xl border border-amber-500/10">
                                <p class="text-amber-400 text-xs font-medium leading-relaxed italic">"Sua solicitação de vínculo com {{ $pendingRequest->professional->name }} está em análise. Você será notificado assim que for aprovado."</p>
                            </div>
                        @else
                            <div class="mb-10 p-6 bg-white/5 rounded-3xl border border-white/5">
                                <p class="text-zinc-500 text-sm leading-relaxed">Conecte-se a um nutricionista ou personal para ter acompanhamento profissional direto na plataforma NexShape.</p>
                            </div>
                            <a href="{{ route('patient.professionals.search') }}" class="flex items-center justify-between w-full p-2 pr-6 bg-indigo-600 text-white font-black rounded-3xl hover:bg-indigo-500 transition-all group/btn no-underline">
                                <div class="h-14 w-14 bg-white text-indigo-600 rounded-2xl flex items-center justify-center group-hover/btn:scale-110 transition-transform">
                                    <i class="fas fa-search text-xl"></i>
                                </div>
                                <span class="text-lg uppercase tracking-tighter">Encontrar Mentor</span>
                                <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

                </div>

                <!-- Painel de Performance Lateral (Convertido em Grid Horizontal Inferior) -->
                <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 items-stretch">

                <!-- WATER BLOCK 2.0: Hydro-Responsive Card -->
                <div
                    class="relative group bg-zinc-900/60 backdrop-blur-xl p-10 rounded-[3.5rem] border border-white/5 overflow-hidden shadow-2xl">
                    <!-- Water Background Pulse -->
                    <div
                        class="absolute inset-0 bg-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-1000">
                    </div>

                    <div class="relative z-20 flex flex-col h-full">
                        <div class="flex items-start justify-between mb-8">
                            <div>
                                <h3 class="text-2xl font-black text-white flex items-center gap-2">NexHydra <span
                                        class="text-[10px] bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded-full border border-blue-500/20 uppercase tracking-tighter">Live</span>
                                </h3>
                                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Smart Water
                                    Analytics</p>
                            </div>
                            <div class="text-right">
                                <span class="text-4xl font-black text-blue-400 tabular-nums">{{ $waterConsumed }}</span>
                                <span class="text-xs text-zinc-500 font-bold block">ml / {{ $waterTarget }}</span>
                            </div>
                        </div>

                        <!-- Water Progress Visualization: Interactive Wave Center -->
                        <div
                            class="relative h-64 bg-zinc-950/80 rounded-[2.5rem] border border-white/5 shadow-inner flex items-center justify-center overflow-hidden mb-8">
                            <!-- Dual Wave Animation -->
                            <div class="absolute bottom-0 left-0 w-[300%] h-full transition-all duration-1000 cubic-bezier(0.4, 0, 0.2, 1)"
                                style="transform: translateY({{ 100 - min(($waterConsumed / ($waterTarget ?: 1)) * 100, 100) }}%)">
                                <svg class="absolute bottom-full left-0 w-full h-24 animate-wave-slow fill-blue-600/30"
                                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                                    <path
                                        d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5,73.84-4.36,147.54,16.88,218.2,35.26,69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113,2,1200,34.47V0Z">
                                    </path>
                                </svg>
                                <svg class="absolute bottom-full left-[-100%] w-full h-20 animate-wave-fast fill-blue-500/50"
                                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                                    <path
                                        d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5,73.84-4.36,147.54,16.88,218.2,35.26,69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113,2,1200,34.47V0Z">
                                    </path>
                                </svg>
                                <div class="w-full h-full bg-gradient-to-b from-blue-500/50 to-blue-700/80"></div>
                            </div>

                            <!-- Percentage HUD -->
                            <div class="relative z-30 text-center">
                                <span
                                    class="text-6xl font-black text-white drop-shadow-[0_4px_8px_rgba(0,0,0,0.5)]">{{ number_format(($waterConsumed / ($waterTarget ?: 1)) * 100, 0) }}%</span>
                                <div class="flex items-center gap-2 justify-center mt-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                    <span class="text-[10px] text-zinc-300 font-black uppercase tracking-[0.2em]">Carga de
                                        Hidratação</span>
                                </div>
                            </div>

                            <!-- Level Bubbles -->
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute bottom-10 left-1/4 w-3 h-3 bg-white/20 rounded-full animate-float-up">
                                </div>
                                <div
                                    class="absolute bottom-20 left-2/3 w-2 h-2 bg-white/20 rounded-full animate-float-up delay-700">
                                </div>
                                <div
                                    class="absolute bottom-5 right-1/4 w-4 h-4 bg-white/10 rounded-full animate-float-up delay-300">
                                </div>
                            </div>
                        </div>

                        <!-- Hydro Controls: Segmented Adders -->
                        <form action="" method="POST" class="grid grid-cols-2 gap-4">
                            @csrf
                            <button type="submit" name="water_add" value="250"
                                class="group/btn relative h-16 bg-zinc-800/50 rounded-2xl border border-white/5 overflow-hidden transition-all hover:bg-blue-600 active:scale-95">
                                <span
                                    class="relative z-10 text-white font-black text-sm group-hover/btn:scale-110 transition-transform flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                                        </path>
                                    </svg>
                                    250ml
                                </span>
                            </button>
                            <button type="submit" name="water_add" value="500"
                                class="group/btn relative h-16 bg-zinc-800/50 rounded-2xl border border-white/5 overflow-hidden transition-all hover:bg-blue-600 active:scale-95">
                                <span
                                    class="relative z-10 text-white font-black text-sm group-hover/btn:scale-110 transition-transform flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    500ml
                                </span>
                            </button>
                        </form>

                        <a href="{{ route('hydration.index') }}"
                            class="mt-6 flex items-center justify-center gap-2 p-4 bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white rounded-2xl border border-white/5 transition-all group/link no-underline">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Painel de Performance</span>
                            <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Profile Evolution Spark -->
                <div
                    class="bg-gradient-to-br from-zinc-900 via-zinc-900 to-blue-900/40 p-10 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-blue-500/5 blur-3xl rounded-full"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-3">Sua Transformação
                            </p>
                            <div class="flex items-baseline gap-3">
                                <h4 class="text-5xl font-black text-white">{{ $lastWeight ? $lastWeight->weight : '--' }}
                                </h4>
                                <span class="text-xl font-bold text-zinc-400">kg</span>
                            </div>
                            <div class="mt-4 flex items-center gap-2 px-3 py-1 bg-emerald-500/10 rounded-full w-fit">
                                <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-[10px] text-emerald-400 font-black">-0.8kg este mês</span>
                            </div>
                        </div>
                        <a href="{{ route('assessments.index') }}"
                            class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/40 group-hover:scale-110 active:scale-95 transition-all">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Achievements & Emails Highlights -->
                <div class="bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3.5rem] border border-white/5 space-y-10">
                    <!-- Internal Emails -->
                    <div>
                        <h3 class="text-xl font-black text-white mb-6 flex items-center justify-between">
                            Mensagens
                            @if($unreadEmails > 0)
                                <span
                                    class="px-2 py-0.5 bg-blue-600 text-white text-[10px] rounded-full animate-pulse">{{ $unreadEmails }}</span>
                            @endif
                        </h3>
                        <a href="{{ route('internal-email.inbox') }}"
                            class="flex items-center gap-5 group cursor-pointer p-3 rounded-2xl hover:bg-white/5 transition-colors no-underline">
                            <div
                                class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-zinc-800 border border-white/5 group-hover:scale-105 transition-transform text-blue-400">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div>
                                <p class="font-bold text-white text-sm tracking-tight">Caixa de Entrada</p>
                                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">
                                    {{ $unreadEmails > 0 ? 'Você tem novas mensagens' : 'Nenhuma mensagem pendente' }}
                                </p>
                            </div>
                        </a>
                    </div>

                    <!-- Personal Records (PRs) -->
                    <div class="border-t border-white/5 pt-8">
                        <h3 class="text-xl font-black text-white mb-6 flex items-center justify-between">
                            Conquistas
                            @if($prsCount > 0)
                                <span
                                    class="px-2 py-0.5 bg-amber-500 text-black text-[10px] font-black rounded-full">{{ $prsCount }}</span>
                            @endif
                        </h3>
                        <a href="{{ route('progression.charts') }}"
                            {!! !$isPremium ? 'data-premium-locked' : '' !!}
                            class="flex items-center gap-5 group cursor-pointer p-3 rounded-2xl hover:bg-white/5 transition-colors no-underline">
                            <div
                                class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl bg-zinc-800 border border-white/5 group-hover:scale-105 transition-transform text-amber-500">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-white text-sm tracking-tight">Recordes Pessoais</p>
                                    <i class="fas fa-lock text-[9px] text-amber-500/80"></i>
                                </div>
                                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">
                                    {{ $prsCount > 0 ? 'Você superou ' . $prsCount . ' limites este mês' : 'Continue treinando para bater recordes' }}
                                </p>
                            </div>
                        </a>
                    </div>
        </div>
    </div>

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

        @keyframes wave-slow {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes wave-fast {
            0% {
                transform: translateX(-25%);
            }

            100% {
                transform: translateX(25%);
            }
        }

        .animate-wave-slow {
            animation: wave-slow 8s linear infinite;
        }

        .animate-wave-fast {
            animation: wave-fast 6s linear infinite;
            opacity: 0.7;
        }

        @keyframes float-up {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }

            20% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-100px) scale(1.5);
                opacity: 0;
            }
        }

        .animate-float-up {
            animation: float-up 4s ease-in infinite;
        }

        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 3s ease-in-out infinite;
        }

        body {
            background-color: #080a0f;
            background-image:
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.1) 0, transparent 40%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0, transparent 40%),
                radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
            background-attachment: fixed;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
@endsection