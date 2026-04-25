@extends('layouts.app')

@section('title', $routine['title'] . ' — NexShape')

@section('content')
<div class="py-10 max-w-4xl mx-auto px-6 animate-fade-in">
    <!-- Breadcrumb -->
    <nav class="mb-8 flex items-center gap-2 text-zinc-500 text-xs font-bold uppercase tracking-widest">
        <a href="{{ route('active-rest.index') }}" class="hover:text-emerald-400 transition-colors">Descanso Ativo</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-white">{{ $routine['title'] }}</span>
    </nav>

    <!-- Header Section -->
    <div class="relative rounded-[3rem] overflow-hidden border border-white/5 bg-zinc-900 shadow-2xl mb-10">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-transparent to-zinc-950/80 z-0"></div>
        
        <div class="relative z-10 p-10 md:p-16 flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                {{ $routine['intensity'] }}
            </div>
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-none mb-6">
                {{ $routine['title'] }}
            </h1>
            <div class="flex items-center gap-6 text-zinc-400 font-bold text-sm">
                <span class="flex items-center gap-2">
                    <i class="far fa-clock text-emerald-400"></i>
                    {{ $routine['duration'] }}
                </span>
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-800"></span>
                <span class="flex items-center gap-2">
                    <i class="fas fa-dumbbell text-emerald-400"></i>
                    {{ count($routine['exercises']) }} Exercícios
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10" 
         x-data="recoveryApp({ 
            duration: '{{ $routine->duration }}',
            exercises: {{ json_encode($routine->exercises) }},
            steps: {{ json_encode($routine->execution_steps) }}
         })">
        
        <!-- Guided Execution Overlay -->
        <template x-if="sessionActive">
            <div class="fixed inset-0 z-[100] bg-zinc-950 flex flex-col items-center justify-center p-6 md:p-12 animate-fade-in">
                <div class="absolute top-8 right-8">
                    <button @click="stopSession()" class="w-12 h-12 rounded-full bg-zinc-900 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="max-w-2xl w-full space-y-12 text-center">
                    <div class="space-y-4">
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                            Exercício <span x-text="currentStepIndex + 1"></span> de <span x-text="steps.length"></span>
                        </span>
                        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tighter" x-text="steps[currentStepIndex]"></h2>
                    </div>

                    <div class="relative w-64 h-64 mx-auto flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="128" cy="128" r="120" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-900"/>
                            <circle cx="128" cy="128" r="120" stroke="currentColor" stroke-width="8" fill="transparent" class="text-emerald-500 transition-all duration-1000"
                                    :stroke-dasharray="754"
                                    :stroke-dashoffset="754 - (754 * stepTimeLeft / (60))"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <div class="text-6xl font-black text-white tabular-nums" x-text="formatTime(stepTimeLeft)"></div>
                            <div class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-2">Próximo: <span x-text="steps[currentStepIndex + 1] || 'Fim'"></span></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-8">
                        <button @click="prevStep()" :disabled="currentStepIndex === 0" class="w-16 h-16 rounded-2xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 disabled:opacity-30">
                            <i class="fas fa-step-backward"></i>
                        </button>
                        <button @click="toggleSessionTimer()" class="w-24 h-24 rounded-full bg-emerald-500 flex items-center justify-center text-zinc-950 text-3xl shadow-2xl shadow-emerald-500/20">
                            <i class="fas" :class="sessionPaused ? 'fa-play' : 'fa-pause'"></i>
                        </button>
                        <button @click="nextStep()" class="w-16 h-16 rounded-2xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400">
                            <i class="fas fa-step-forward"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Feedback Modal -->
        <template x-if="showFeedbackModal">
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-6 backdrop-blur-xl bg-zinc-950/80 animate-fade-in">
                <div class="bg-zinc-900 border border-white/10 rounded-[3rem] p-10 max-w-lg w-full space-y-8 shadow-2xl">
                    <div class="text-center space-y-2">
                        <div class="w-20 h-20 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-4 text-3xl">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="text-3xl font-black text-white">Treino Concluído!</h2>
                        <p class="text-zinc-500">Como você está se sentindo agora?</p>
                    </div>

                    <div class="flex justify-center gap-4">
                        <template x-for="n in 5">
                            <button @click="feedbackScore = n" 
                                    class="w-12 h-12 rounded-xl border flex items-center justify-center transition-all"
                                    :class="feedbackScore >= n ? 'bg-amber-500 border-amber-500 text-zinc-950' : 'bg-zinc-950 border-white/5 text-zinc-600'">
                                <i class="fas fa-star"></i>
                            </button>
                        </template>
                    </div>

                    <textarea x-model="feedbackNotes" 
                              placeholder="Notas opcionais (ex: senti o quadril mais solto)" 
                              class="w-full bg-zinc-950 border-white/5 rounded-2xl text-zinc-300 text-sm p-4 focus:ring-emerald-500 transition-all h-32"></textarea>

                    <button @click="submitFeedback()" 
                            class="w-full py-5 rounded-2xl bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-widest hover:bg-emerald-400 transition-all"
                            :disabled="submitting">
                        <span x-show="!submitting">Salvar e Concluir</span>
                        <span x-show="submitting"><i class="fas fa-circle-notch fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Focus Mode Toggle & Timer Bar -->
            <div class="flex flex-wrap items-center justify-between gap-4 p-6 rounded-3xl bg-zinc-900/60 border border-white/5 sticky top-4 z-50 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <button @click="toggleFocusMode()" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-widest hover:bg-emerald-500/20 transition-all">
                        <i class="fas" :class="focusMode ? 'fa-compress' : 'fa-expand'"></i>
                        <span x-text="focusMode ? 'Sair do Foco' : 'Modo Foco'"></span>
                    </button>
                    
                    <button @click="startSession()" class="flex items-center gap-2 px-6 py-2 rounded-xl bg-emerald-500 text-zinc-950 text-xs font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-play"></i>
                        Iniciar Protocolo
                    </button>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <div class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Relógio Total</div>
                        <div class="text-2xl font-black text-white tabular-nums" x-text="formatTime(timeLeft)">00:00</div>
                    </div>
                    <button @click="toggleTimer()" 
                        class="w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-lg"
                        :class="timerRunning ? 'bg-amber-500 text-zinc-950 shadow-amber-500/20' : 'bg-emerald-500 text-zinc-950 shadow-emerald-500/20'">
                        <i class="fas" :class="timerRunning ? 'fa-pause' : 'fa-play'"></i>
                    </button>
                    <button @click="resetTimer()" class="text-zinc-500 hover:text-white transition-colors">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>

            <!-- Video Placeholder/Link -->
            <section class="space-y-4">
                <h3 class="text-white font-black text-lg tracking-tight flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-xs">
                        <i class="fas fa-play"></i>
                    </span>
                    Guia Visual
                </h3>
                <div class="relative w-full rounded-3xl overflow-hidden border border-white/5 bg-zinc-950 shadow-2xl" style="padding-top: 56.25%;">
                    @if($routine->guide_image)
                        <a href="{{ $routine->guide_image }}" target="_blank" class="absolute inset-0 group">
                            <img src="{{ $routine->guide_image }}" alt="Guia Visual" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-zinc-950/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="bg-white text-zinc-950 px-6 py-3 rounded-full font-black text-xs uppercase tracking-widest shadow-2xl">
                                    Ver em Tela Cheia <i class="fas fa-expand ml-2"></i>
                                </span>
                            </div>
                        </a>
                    @else
                        <iframe 
                            class="absolute inset-0 w-full h-full" 
                            src="https://www.youtube-nocookie.com/embed/{{ $routine->video_id ?? '8-15-min-recovery' }}?rel=0&modestbranding=1&controls=1&showinfo=0" 
                            title="{{ $routine->title }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    @endif
                </div>
            </section>

            <!-- Steps -->
            <section class="space-y-6">
                <h3 class="text-white font-black text-lg tracking-tight flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-xs">
                        <i class="fas fa-list-ol"></i>
                    </span>
                    Passo a Passo
                </h3>
                <div class="space-y-4">
                    @foreach($routine->execution_steps as $idx => $step)
                    <div class="flex gap-6 p-6 rounded-2xl bg-zinc-900/40 border border-white/5 hover:border-emerald-500/20 transition-all group cursor-pointer"
                         x-data="{ completed: false }" @click="completed = !completed" :class="completed ? 'opacity-50 grayscale' : ''">
                        <span class="text-2xl font-black transition-colors" :class="completed ? 'text-emerald-500' : 'text-emerald-500/20'">
                            <i class="fas" :class="completed ? 'fa-check-circle' : ''"></i>
                            <span x-show="!completed">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </span>
                        <p class="text-zinc-300 font-medium leading-relaxed" :class="completed ? 'line-through' : ''">{{ $step }}</p>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8" :class="focusMode ? 'hidden lg:block' : ''">
            <!-- Benefits Card -->
            <div class="p-8 rounded-[2.5rem] bg-emerald-600/5 border border-emerald-500/10 space-y-4">
                <h4 class="text-emerald-400 font-black text-xs uppercase tracking-widest">O que esperar?</h4>
                <p class="text-white font-bold leading-snug">{{ $routine->benefit }}</p>
            </div>

            <!-- Pro-Tips -->
            <div class="space-y-4">
                <h4 class="text-white font-black text-sm tracking-tight px-2">Dicas de Specialist</h4>
                <div class="space-y-3">
                    @foreach($routine->tips as $tip)
                    <div class="p-4 rounded-xl bg-zinc-900/60 border border-white/5 flex gap-4 items-start">
                        <i class="fas fa-lightbulb text-amber-400 mt-1"></i>
                        <p class="text-zinc-400 text-xs font-medium leading-relaxed">{{ $tip }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Common Errors -->
            <div class="space-y-4">
                <h4 class="text-white font-black text-sm tracking-tight px-2">Evite estes erros</h4>
                <div class="space-y-3">
                    @foreach($routine->common_errors as $error)
                    <div class="p-4 rounded-xl bg-zinc-900/60 border border-white/5 flex gap-4 items-start border-l-red-500/50 border-l-4">
                        <i class="fas fa-times-circle text-red-500 mt-1"></i>
                        <p class="text-zinc-400 text-xs font-medium leading-relaxed">{{ $error }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Action -->
            <button @click="finishProtocol()" class="w-full py-5 rounded-2xl bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10">
                Concluir Protocolo
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('recoveryApp', (config) => ({
            focusMode: false,
            duration: parseInt(config.duration) || 10,
            timeLeft: (parseInt(config.duration) || 10) * 60,
            timerRunning: false,
            interval: null,

            // Guided Session State
            sessionActive: false,
            sessionPaused: false,
            currentStepIndex: 0,
            stepTimeLeft: 60, // 1 min per exercise by default
            sessionInterval: null,
            steps: config.steps,

            // Feedback State
            showFeedbackModal: false,
            feedbackScore: 5,
            feedbackNotes: '',
            submitting: false,

            startSession() {
                this.sessionActive = true;
                this.sessionPaused = false;
                this.currentStepIndex = 0;
                this.stepTimeLeft = 60;
                this.startStepTimer();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            stopSession() {
                this.sessionActive = false;
                clearInterval(this.sessionInterval);
            },

            toggleSessionTimer() {
                this.sessionPaused = !this.sessionPaused;
                if (this.sessionPaused) {
                    clearInterval(this.sessionInterval);
                } else {
                    this.startStepTimer();
                }
            },

            startStepTimer() {
                clearInterval(this.sessionInterval);
                this.sessionInterval = setInterval(() => {
                    if (this.stepTimeLeft > 0) {
                        this.stepTimeLeft--;
                    } else {
                        this.nextStep();
                    }
                }, 1000);
            },

            nextStep() {
                if (this.currentStepIndex < this.steps.length - 1) {
                    this.currentStepIndex++;
                    this.stepTimeLeft = 60;
                } else {
                    this.finishProtocol();
                }
            },

            prevStep() {
                if (this.currentStepIndex > 0) {
                    this.currentStepIndex--;
                    this.stepTimeLeft = 60;
                }
            },

            finishProtocol() {
                this.stopSession();
                this.showFeedbackModal = true;
            },

            async submitFeedback() {
                this.submitting = true;
                try {
                    const response = await fetch('{{ route('active-rest.store-log', $routine->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            duration_spent: (this.duration * 60) - this.timeLeft,
                            feedback_score: this.feedbackScore,
                            notes: this.feedbackNotes
                        })
                    });

                    if (response.ok) {
                        window.location.href = '{{ route('active-rest.index') }}';
                    }
                } catch (error) {
                    console.error('Erro ao salvar log:', error);
                    alert('Erro ao salvar o progresso. Tente novamente.');
                } finally {
                    this.submitting = false;
                }
            },

            toggleFocusMode() {
                this.focusMode = !this.focusMode;
                if (this.focusMode) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            toggleTimer() {
                if (this.timerRunning) {
                    clearInterval(this.interval);
                } else {
                    this.interval = setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                        } else {
                            this.timerRunning = false;
                            clearInterval(this.interval);
                        }
                    }, 1000);
                }
                this.timerRunning = !this.timerRunning;
            },

            resetTimer() {
                this.timerRunning = false;
                clearInterval(this.interval);
                this.timeLeft = this.duration * 60;
            },

            formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        }));
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    [x-cloak] { display: none !important; }

    @media print {
        body {
            background: white !important;
            color: black !important;
        }
        nav, .sticky, .lg\:col-span-1, .space-y-8, button, .fas.fa-compress, .fas.fa-expand {
            display: none !important;
        }
        .max-w-4xl {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .lg\:col-span-2 {
            width: 100% !important;
        }
        .space-y-6 {
            display: block !important;
        }
        h3.text-white {
            color: black !important;
            font-size: 24pt !important;
            margin-bottom: 20pt !important;
        }
        .bg-zinc-900\/40 {
            background: none !important;
            border: 1px solid #eee !important;
            color: black !important;
            page-break-inside: avoid;
        }
        .text-zinc-300 {
            color: black !important;
        }
    }
</style>
@endsection
