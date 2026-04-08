@extends('layouts.app')

@section('title', 'AI Engine — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Prescrição Gamificada</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Powered by DeepSense v3</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                AI <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Prescription</span> Wizard
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Converta objetivos em protocolos de alta performance com a precisão da inteligência artificial NexShape.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <a href="{{ route('professional.dashboard') }}" class="px-6 py-3 bg-zinc-800 text-zinc-300 font-bold rounded-xl hover:bg-zinc-700 transition-all border border-white/5 flex items-center gap-2">
                    Painel Central
                </a>
            </div>
        </div>
    </div>

    <!-- Main Wizard Strategy -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Input Area (Left) - Glass Card -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-8">
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-blue-500/20">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                
                <div class="space-y-8 relative z-10">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Tipo de Protocolo</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button id="btn-train" class="py-4 rounded-3xl border border-blue-500/30 bg-blue-500/10 text-blue-400 font-black text-xs flex items-center justify-center gap-2 transition-all hover:bg-blue-500/20 active:scale-95 shadow-lg shadow-blue-500/10" onclick="setType('training')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                TREINO
                            </button>
                            <button id="btn-nutri" class="py-4 rounded-3xl border border-zinc-800 bg-white/5 text-zinc-500 font-black text-xs flex items-center justify-center gap-2 transition-all hover:bg-zinc-800 active:scale-95" onclick="setType('nutrition')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                NUTRIÇÃO
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2 px-2">Prompt de Comando</label>
                        <textarea id="ai-prompt" rows="8" class="w-full bg-zinc-950/50 border border-white/5 rounded-[2rem] p-6 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all placeholder:text-zinc-700 resize-none shadow-inner" placeholder="E.g. Protocolo FullBody foco em força p/ atleta endomorfo, low-carb..."></textarea>
                    </div>

                    <div class="p-6 bg-blue-500/5 rounded-3xl border border-blue-500/10 backdrop-blur-3xl">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest">Dica Premium</p>
                        </div>
                        <p class="text-xs text-zinc-400 font-medium leading-relaxed italic">"Quanto mais específico o perfil (Gasto calórico, patologias, split semanal), mais refinada será a resposta."</p>
                    </div>

                    <button id="btn-generate" class="group relative flex items-center justify-between w-full p-2 pr-8 bg-white text-zinc-900 font-black rounded-[2rem] hover:bg-blue-400 hover:text-white transition-all overflow-hidden shadow-2xl active:scale-95" onclick="generatePlan()">
                        <div id="btn-spinner-container" class="h-14 w-14 bg-zinc-900 text-white rounded-2xl flex items-center justify-center transition-colors group-hover:bg-white group-hover:text-blue-500 shadow-xl">
                            <svg id="btn-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM14.502 8.993L8.913 14.586a1 1 0 01-1.417 0l-3.087-3.088a1 1 0 111.414-1.414l2.38 2.38 4.885-4.885a1 1 0 111.414 1.414z"></path></svg>
                            <svg id="btn-spinner" class="hidden animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <span id="btn-text">GERAR ESTRATÉGIA</span>
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Output Preview (Right) - High Scale Result Card -->
        <div class="lg:col-span-7 xl:col-span-8">
            <div id="welcome-state" class="group relative h-full flex flex-col items-center justify-center text-center p-20 bg-zinc-900/40 border border-dashed border-white/5 rounded-[4rem] shadow-inner">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none rounded-[4rem]"></div>
                <div class="w-24 h-24 bg-zinc-950 rounded-full flex items-center justify-center mb-8 border border-white/5 shadow-2xl group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-10 h-10 text-zinc-700 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.341A8 8 0 1120.283 5.693L20.5 8.5M12 12V3"></path></svg>
                </div>
                <h3 class="text-zinc-400 font-black text-2xl uppercase tracking-tighter">Motor Prévio Inativo</h3>
                <p class="text-zinc-600 font-bold max-w-sm mt-4 uppercase text-[10px] tracking-widest leading-relaxed">Configure os parâmetros à esquerda. A inteligência artificial organizará as variáveis de volume, intensidade e macros.</p>
            </div>

            <div id="result-state" class="hidden space-y-10 animate-scale-up">
                <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[4rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.6)] overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 blur-[100px] -mr-32 -mt-32 rounded-full"></div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                            <div>
                                <span class="px-4 py-1 bg-white/5 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-400/20 tracking-[0.2em] mb-3 inline-block">Pro-Strategy Draft</span>
                                <h2 id="plan-title" class="text-4xl font-black text-white tracking-tight"></h2>
                            </div>
                            <div class="flex gap-2">
                                 <button class="p-3 bg-zinc-900 rounded-2xl text-zinc-500 hover:text-white border border-white/5 shadow-xl transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
                                 <button class="p-3 bg-zinc-900 rounded-2xl text-zinc-500 hover:text-white border border-white/5 shadow-xl transition-all"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg></button>
                            </div>
                        </div>

                        <div class="p-8 bg-zinc-950/50 rounded-[2.5rem] border border-white/5 shadow-inner mb-12">
                             <p id="plan-desc" class="text-zinc-400 font-medium leading-relaxed"></p>
                        </div>

                        <div id="plan-content" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Injected Content -->
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-6 mt-16 pt-10 border-t border-white/5">
                            <button class="w-full sm:flex-1 py-5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-500 hover:text-white font-black rounded-3xl border border-white/5 transition-all uppercase text-xs tracking-widest" onclick="location.reload()">Descartar e Refinar</button>
                            <button class="w-full sm:flex-1 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-3xl transition-all shadow-2xl shadow-blue-500/30 uppercase text-xs tracking-widest active:scale-[0.98]">Salvar e Publicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentType = 'training';

    function setType(type) {
        currentType = type;
        const btnTrain = document.getElementById('btn-train');
        const btnNutri = document.getElementById('btn-nutri');

        if (type === 'training') {
            btnTrain.className = 'py-4 rounded-3xl border border-blue-500/30 bg-blue-500/10 text-blue-400 font-black text-xs flex items-center justify-center gap-2 transition-all hover:bg-blue-500/20 active:scale-95 shadow-lg shadow-blue-500/10';
            btnNutri.className = 'py-4 rounded-3xl border border-zinc-800 bg-white/5 text-zinc-500 font-black text-xs flex items-center justify-center gap-2 transition-all hover:bg-zinc-800 active:scale-95';
        } else {
            btnNutri.className = 'py-4 rounded-3xl border border-blue-500/30 bg-blue-500/10 text-blue-400 font-black text-xs flex items-center justify-center gap-2 transition-all hover:bg-blue-500/20 active:scale-95 shadow-lg shadow-blue-500/10';
            btnTrain.className = 'py-4 rounded-3xl border border-zinc-800 bg-white/5 text-zinc-500 font-black text-xs flex items-center justify-center gap-2 transition-all hover:bg-zinc-800 active:scale-95';
        }
    }

    async function generatePlan() {
        const prompt = document.getElementById('ai-prompt').value;
        if (!prompt) return alert('Por favor, descreva o perfil e objetivo.');

        // UI States
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');
        const btnSpinner = document.getElementById('btn-spinner');
        const btn = document.getElementById('btn-generate');

        btnText.textContent = 'CONSULTANDO MOTOR...';
        btnIcon.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        btn.disabled = true;

        try {
            const response = await fetch('{{ route('professional.ai-wizard.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ prompt: prompt, type: currentType })
            });

            const result = await response.json();
            
            if (result.success) {
                renderResult(result.data || result.content);
            } else {
                alert('Erro na geração: ' + (result.error || 'Tente ser mais específico.'));
            }
        } catch (e) {
            console.error(e);
            alert('Falha na rede Neural.');
        } finally {
            btnText.textContent = 'GERAR ESTRATÉGIA';
            btnIcon.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
            btn.disabled = false;
        }
    }

    function renderResult(data) {
        document.getElementById('welcome-state').classList.add('hidden');
        document.getElementById('result-state').classList.remove('hidden');
        
        document.getElementById('plan-title').textContent = data.name;
        document.getElementById('plan-desc').textContent = data.description || data.strategy;

        const contentDiv = document.getElementById('plan-content');
        contentDiv.innerHTML = '';

        if (currentType === 'training') {
            data.exercises.forEach(ex => {
                contentDiv.innerHTML += `
                    <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:bg-blue-500/10 hover:border-blue-500/20 transition-all group/card">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-white font-black group-hover:text-blue-400 transition-colors">${ex.name}</h4>
                            <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase rounded-full border border-blue-500/20 shadow-lg shadow-blue-500/10">${ex.sets} x ${ex.reps}</span>
                        </div>
                        <p class="text-xs text-zinc-500 font-bold leading-relaxed">${ex.notes || ''}</p>
                    </div>
                `;
            });
        } else {
            data.meals.forEach(m => {
                contentDiv.innerHTML += `
                    <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:bg-emerald-500/10 hover:border-emerald-500/20 transition-all">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-white font-black">${m.time}</h4>
                            <span class="text-emerald-400 text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20 shadow-lg shadow-emerald-500/10">${m.macros || m.macros_est}</span>
                        </div>
                        <p class="text-sm text-zinc-400 font-bold leading-relaxed">${m.foods}</p>
                    </div>
                `;
            });
        }
    }
</script>

<style>
    @keyframes dashboard-entry {
        from { opacity: 0; transform: translateY(40px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-dashboard-entry {
        animation: dashboard-entry 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes scale-up {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-scale-up {
        animation: scale-up 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    body {
        background-color: #080a0f;
        background-image: 
            radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.1) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0, transparent 40%),
            radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }
</style>
@endsection

