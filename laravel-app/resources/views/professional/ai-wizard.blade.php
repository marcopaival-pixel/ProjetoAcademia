@extends('layouts.app')

@section('title', 'AI Prescription Wizard — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Atendimento Multidisciplinar</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Powered by DeepSense v3</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                AI <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Prescription</span> Wizard
            </h1>
            <div class="flex items-center gap-3">
                <p class="text-zinc-500 font-medium max-w-xl">Plataforma inteligente para prescrição multidisciplinar.</p>
                @if(!Auth::user()->isPremiumActive())
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase tracking-widest rounded-full border border-amber-500/20 shadow-lg shadow-amber-500/5">
                        <i class="fas fa-crown text-[8px] mr-1"></i>
                        FREE: 0 Créditos IA
                    </span>
                @else
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase tracking-widest rounded-full border border-blue-500/20 shadow-lg shadow-blue-500/5">
                        <i class="fas fa-crown text-[8px] mr-1"></i>
                        PRO: {{ Auth::user()->getRemainingAiCredits() }} Créditos
                    </span>
                @endif
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <a href="{{ route('professional.templates.index') }}" class="px-4 py-2 bg-zinc-800 text-zinc-400 font-bold rounded-xl hover:bg-zinc-700 transition-all text-[10px] flex items-center gap-2">
                    <i class="fas fa-file-code"></i> Templates
                </a>
                @if(auth()->user()->hasRole(['manager', 'admin']))
                <a href="{{ route('admin.clinic.protocols.index') }}" class="px-4 py-2 bg-zinc-800 text-zinc-400 font-bold rounded-xl hover:bg-zinc-700 transition-all text-[10px] flex items-center gap-2">
                    <i class="fas fa-notes-medical"></i> Protocolos
                </a>
                @endif
                <a href="{{ route('professional.dashboard') }}" class="px-6 py-3 bg-zinc-800 text-zinc-300 font-bold rounded-xl hover:bg-zinc-700 transition-all border border-white/5 flex items-center gap-2">
                    Painel Central
                </a>
            </div>
        </div>
    </div>

    <x-plan-upgrade-banner />


    <!-- Main Wizard Strategy -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Input Area (Left) - Glass Card -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-8">
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-blue-500/20">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                
                <div class="space-y-8 relative z-10">
                    <!-- Paciente -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Paciente Selecionado</label>
                        <select id="patient-id" class="w-full bg-zinc-950/50 border border-white/5 rounded-[1.5rem] px-5 py-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer" onchange="loadPatientHistory()">
                            <option value="">Selecione um paciente...</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Especialidade -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Especialidade do Atendimento</label>
                        <select id="specialty-id" class="w-full bg-zinc-950/50 border border-white/5 rounded-[1.5rem] px-5 py-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Selecione a especialidade...</option>
                            @foreach($specialties as $s)
                                <option value="{{ $s->id }}">{{ $s->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Protocolo -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Tipo de Protocolo</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button id="btn-train" class="py-3 rounded-2xl border border-blue-500/30 bg-blue-500/10 text-blue-400 font-black text-[10px] flex flex-col items-center justify-center gap-1 transition-all hover:bg-blue-500/20 active:scale-95 shadow-lg shadow-blue-500/10" onclick="setType('training')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                TREINO
                            </button>
                            <button id="btn-nutri" class="py-3 rounded-2xl border border-zinc-800 bg-white/5 text-zinc-500 font-black text-[10px] flex flex-col items-center justify-center gap-1 transition-all hover:bg-zinc-800 active:scale-95" onclick="setType('nutrition')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                NUTRIÇÃO
                            </button>
                            <button id="btn-medical" class="py-3 rounded-2xl border border-zinc-800 bg-white/5 text-zinc-500 font-black text-[10px] flex flex-col items-center justify-center gap-1 transition-all hover:bg-zinc-800 active:scale-95" onclick="setType('medical')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.341A8 8 0 1120.283 5.693L20.5 8.5M12 12V3"></path></svg>
                                CLÍNICO
                            </button>
                        </div>
                    </div>

                    <!-- Prompt -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center px-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500">Prompt de Comando</label>
                            <div class="flex gap-4">
                                <select id="template-selector" class="bg-transparent text-[9px] text-blue-400 font-bold outline-none border-none cursor-pointer" onchange="applyTemplate()">
                                    <option value="" class="bg-zinc-900 text-zinc-400">Meus Templates...</option>
                                    @foreach($templates as $t)
                                        <option value="{{ $t->id }}" data-content="{{ $t->content }}" data-specialty="{{ $t->especialidade_id }}" class="bg-zinc-900 text-white">{{ $t->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <textarea id="ai-prompt" rows="6" class="w-full bg-zinc-950/50 border border-white/5 rounded-[2rem] p-6 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all placeholder:text-zinc-700 resize-none shadow-inner" placeholder="Descreva o objetivo e parâmetros específicos..."></textarea>
                    </div>

                    <!-- Clinical Protocols (Optional) -->
                    @if(count($clinicProtocols) > 0)
                    <div id="clinic-protocols-container">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4 px-2">Protocolos da Clínica</label>
                        <select id="clinic-protocol-id" class="w-full bg-zinc-950/50 border border-white/5 rounded-1.5rem px-5 py-4 text-white text-xs font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer" onchange="applyClinicProtocol()">
                            <option value="">Nenhum protocolo selecionado</option>
                            @foreach($clinicProtocols as $cp)
                                <option value="{{ $cp->id }}" data-type="{{ $cp->type }}" data-objective="{{ $cp->objective }}" data-protocol="{{ $cp->protocol }}" data-freq="{{ $cp->frequency }}" data-dur="{{ $cp->duration }}">{{ $cp->name }} ({{ $cp->specialty->nome }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @feature('ai_training')
                    <button id="btn-generate" class="group relative flex items-center justify-between w-full p-2 pr-8 bg-white text-zinc-900 font-black rounded-[2rem] hover:bg-blue-400 hover:text-white transition-all overflow-hidden shadow-2xl active:scale-95" onclick="generatePlan()">
                        <div id="btn-spinner-container" class="h-14 w-14 bg-zinc-900 text-white rounded-2xl flex items-center justify-center transition-colors group-hover:bg-white group-hover:text-blue-500 shadow-xl">
                            <svg id="btn-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM14.502 8.993L8.913 14.586a1 1 0 01-1.417 0l-3.087-3.088a1 1 0 111.414-1.414l2.38 2.38 4.885-4.885a1 1 0 111.414 1.414z"></path></svg>
                            <svg id="btn-spinner" class="hidden animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <span id="btn-text">GERAR PRESCRIÇÃO AI</span>
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                    @else
                    <button onclick="window.location.href='{{ route('plano') }}'" class="group relative flex items-center justify-between w-full p-2 pr-8 bg-zinc-950 text-zinc-500 font-black rounded-[2rem] border border-amber-500/20 hover:border-amber-500/50 transition-all overflow-hidden shadow-2xl active:scale-95">
                        <div class="h-14 w-14 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center border border-amber-500/20 shadow-xl">
                            <i class="fas fa-lock"></i>
                        </div>
                        <span>DESBLOQUEAR AI (PRO)</span>
                        <i class="fas fa-crown text-amber-500 text-xs"></i>
                    </button>
                    @endfeature

                </div>
            </div>

            <!-- Outras Especialidades (Read Only) -->
            <div id="history-container" class="hidden group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[3rem] overflow-hidden shadow-2xl transition-all">
                <h3 class="text-white font-black text-xs uppercase tracking-widest mb-6 px-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Histórico Multidisciplinar
                </h3>
                <div id="history-list" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    <!-- Injected by JS -->
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
                <h3 class="text-zinc-400 font-black text-2xl uppercase tracking-tighter">Assistente Aguardando</h3>
                <p class="text-zinc-600 font-bold max-w-sm mt-4 uppercase text-[10px] tracking-widest leading-relaxed">Selecione o paciente e a especialidade. O motor NexShape AI irá consolidar protocolos e sugerir a melhor conduta.</p>
            </div>

            <div id="result-state" class="hidden space-y-10 animate-scale-up">
                <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[4rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.6)] overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 blur-[100px] -mr-32 -mt-32 rounded-full"></div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                            <div>
                                <span class="px-4 py-1 bg-white/5 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-400/20 tracking-[0.2em] mb-3 inline-block" id="result-badge">AI Suggested Protocol</span>
                                <h2 id="plan-title" class="text-4xl font-black text-white tracking-tight"></h2>
                            </div>
                        </div>

                        <!-- Structured Fields for Clinical -->
                        <div id="clinical-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Objetivo</label>
                                <div id="res-objective" class="p-5 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-300 font-bold text-sm"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Protocolo</label>
                                <div id="res-protocol" class="p-5 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-300 font-bold text-sm"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Frequência</label>
                                <div id="res-frequency" class="p-5 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-300 font-bold text-sm"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Duração</label>
                                <div id="res-duration" class="p-5 bg-zinc-950/50 rounded-2xl border border-white/5 text-zinc-300 font-bold text-sm"></div>
                            </div>
                        </div>

                        <div class="p-8 bg-zinc-950/50 rounded-[2.5rem] border border-white/5 shadow-inner mb-12">
                             <h4 class="text-white font-black text-xs uppercase mb-4">Conduta / Medicação</h4>
                             <p id="plan-desc" class="text-zinc-400 font-medium leading-relaxed"></p>
                        </div>

                        <div id="plan-content" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Injected Content (Exercises or Meals) -->
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-6 mt-16 pt-10 border-t border-white/5">
                            <button class="w-full sm:flex-1 py-5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-500 hover:text-white font-black rounded-3xl border border-white/5 transition-all uppercase text-[10px] tracking-widest" onclick="location.reload()">Descartar e Refinar</button>
                            <button class="w-full sm:flex-1 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-3xl transition-all shadow-2xl shadow-blue-500/30 uppercase text-[10px] tracking-widest active:scale-[0.98]" onclick="savePrescription()">Registrar no Prontuário</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentType = 'training';
    let lastResult = null;

    function setType(type) {
        currentType = type;
        const mapping = {
            'training': 'btn-train',
            'nutrition': 'btn-nutri',
            'medical': 'btn-medical'
        };

        ['btn-train', 'btn-nutri', 'btn-medical'].forEach(id => {
            const btn = document.getElementById(id);
            if (id === mapping[type]) {
                btn.className = 'py-3 rounded-2xl border border-blue-500/30 bg-blue-500/10 text-blue-400 font-black text-[10px] flex flex-col items-center justify-center gap-1 transition-all hover:bg-blue-500/20 active:scale-95 shadow-lg shadow-blue-500/10';
            } else {
                btn.className = 'py-3 rounded-2xl border border-zinc-800 bg-white/5 text-zinc-500 font-black text-[10px] flex flex-col items-center justify-center gap-1 transition-all hover:bg-zinc-800 active:scale-95';
            }
        });

        filterProtocolsByType();
    }

    function filterProtocolsByType() {
        const select = document.getElementById('clinic-protocol-id');
        if (!select) return;

        const options = select.querySelectorAll('option');
        let visibleCount = 0;

        options.forEach(opt => {
            if (!opt.value) return;
            if (opt.dataset.type === currentType) {
                opt.classList.remove('hidden');
                opt.disabled = false;
                visibleCount++;
            } else {
                opt.classList.add('hidden');
                opt.disabled = true;
            }
        });

        // Se não houver protocolos para este tipo, esconde o seletor
        const container = document.getElementById('clinic-protocols-container');
        if (visibleCount === 0) {
            container.classList.add('hidden');
        } else {
            container.classList.remove('hidden');
            select.value = ""; // Reseta seleção ao mudar de tipo
        }
    }

    async function loadPatientHistory() {
        const patientId = document.getElementById('patient-id').value;
        if (!patientId) {
            document.getElementById('history-container').classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`/professional/medical-records/${patientId}/prescriptions-json`);
            const data = await response.json();
            
            const list = document.getElementById('history-list');
            list.innerHTML = '';
            
            if (data.length > 0) {
                document.getElementById('history-container').classList.remove('hidden');
                data.forEach(p => {
                    list.innerHTML += `
                        <div class="p-4 bg-zinc-950/30 border border-white/5 rounded-2xl">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[9px] font-black text-blue-400 uppercase tracking-tighter">${p.specialty || 'Geral'}</span>
                                <span class="text-[8px] text-zinc-600 font-bold">${p.date_formatted}</span>
                            </div>
                            <h4 class="text-zinc-300 font-bold text-xs mb-1">${p.medicine}</h4>
                            <p class="text-[10px] text-zinc-500 italic">${p.dosage || ''} - ${p.frequency || ''}</p>
                        </div>
                    `;
                });
            } else {
                document.getElementById('history-container').classList.add('hidden');
            }
        } catch (e) {
            console.error('Erro ao carregar histórico:', e);
        }
    }

    async function generatePlan() {
        const prompt = document.getElementById('ai-prompt').value;
        const patientId = document.getElementById('patient-id').value;
        const specialtyId = document.getElementById('specialty-id').value;

        if (!patientId) return alert('Por favor, selecione um paciente.');
        if (!specialtyId) return alert('Por favor, selecione a especialidade.');
        if (!prompt) return alert('Por favor, descreva o objetivo.');

        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');
        const btnSpinner = document.getElementById('btn-spinner');
        const btn = document.getElementById('btn-generate');

        btnText.textContent = 'PROCESSANDO ALGORITMO...';
        btnIcon.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        btn.disabled = true;

        try {
            const response = await fetch('{{ route('professional.ai-wizard.generate') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ prompt, type: currentType, patient_id: patientId, specialty_id: specialtyId })
            });

            const result = await response.json();
            
            if (result.success) {
                lastResult = result.data || result.content;
                renderResult(lastResult);
            } else {
                alert('Erro na geração: ' + (result.error || 'Tente ser mais específico.'));
            }
        } catch (e) {
            alert('Falha na conexão com o motor AI.');
        } finally {
            btnText.textContent = 'GERAR PRESCRIÇÃO AI';
            btnIcon.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
            btn.disabled = false;
        }
    }

    function renderResult(data) {
        document.getElementById('welcome-state').classList.add('hidden');
        document.getElementById('result-state').classList.remove('hidden');
        
        document.getElementById('plan-title').textContent = data.name;
        document.getElementById('plan-desc').textContent = data.description || data.strategy || data.observations || '';

        const contentDiv = document.getElementById('plan-content');
        const clinicalFields = document.getElementById('clinical-fields');
        contentDiv.innerHTML = '';
        clinicalFields.classList.add('hidden');

        if (currentType === 'training') {
            data.exercises.forEach(ex => {
                contentDiv.innerHTML += `
                    <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:bg-blue-500/10 hover:border-blue-500/20 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-white font-black text-sm">${ex.name}</h4>
                            <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-500/20">${ex.sets}x${ex.reps}</span>
                        </div>
                        <p class="text-[10px] text-zinc-500 font-bold">${ex.notes || ''}</p>
                    </div>
                `;
            });
        } else if (currentType === 'nutrition') {
            data.meals.forEach(m => {
                contentDiv.innerHTML += `
                    <div class="p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:bg-emerald-500/10 hover:border-emerald-500/20 transition-all">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-white font-black text-sm">${m.time}</h4>
                            <span class="text-emerald-400 text-[9px] font-black uppercase bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">${m.macros || m.macros_est}</span>
                        </div>
                        <p class="text-[11px] text-zinc-400 font-bold">${m.foods}</p>
                    </div>
                `;
            });
        } else {
            clinicalFields.classList.remove('hidden');
            document.getElementById('res-objective').textContent = data.objective || 'N/A';
            document.getElementById('res-protocol').textContent = data.protocol || 'N/A';
            document.getElementById('res-frequency').textContent = data.frequency || 'N/A';
            document.getElementById('res-duration').textContent = data.duration || 'N/A';
            
            contentDiv.innerHTML = `
                <div class="col-span-full p-8 bg-blue-500/5 border border-blue-500/10 rounded-[2.5rem]">
                    <h4 class="text-blue-400 font-black text-[10px] uppercase mb-4 tracking-widest">Conduta Sugerida</h4>
                    <p class="text-white font-bold text-lg">${data.medicine || 'Conforme observações'}</p>
                    <p class="text-zinc-500 text-sm mt-2">${data.dosage || ''}</p>
                </div>
            `;
        }
    }

    async function savePrescription() {
        if (!lastResult) return;
        
        const patientId = document.getElementById('patient-id').value;
        const specialtyId = document.getElementById('specialty-id').value;
        
        const payload = {
            patient_id: patientId,
            especialidade_id: specialtyId,
            date: new Date().toISOString().split('T')[0],
            objective: lastResult.objective || '',
            protocol: lastResult.protocol || '',
            medicine: lastResult.medicine || lastResult.name,
            dosage: lastResult.dosage || '',
            frequency: lastResult.frequency || '',
            duration: lastResult.duration || '',
            observations: lastResult.observations || lastResult.description || lastResult.strategy || ''
        };

        try {
            const response = await fetch('{{ route('professional.ai-wizard.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (result.success) {
                alert('Prescrição registrada com sucesso no prontuário!');
                loadPatientHistory();
            } else {
                alert('Erro ao salvar.');
            }
        } catch (e) {
            alert('Falha ao registrar.');
        }
    }

    function applyTemplate() {
        const select = document.getElementById('template-selector');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;
        
        const promptArea = document.getElementById('ai-prompt');
        promptArea.value = opt.dataset.content;
    }

    function filterTemplatesBySpecialty() {
        const specialtyId = document.getElementById('specialty-id').value;
        const templates = document.querySelectorAll('#template-selector option');
        
        templates.forEach(opt => {
            if (!opt.value) return;
            if (!specialtyId || opt.dataset.specialty === specialtyId) {
                opt.classList.remove('hidden');
                opt.disabled = false;
            } else {
                opt.classList.add('hidden');
                opt.disabled = true;
            }
        });
    }

    document.getElementById('specialty-id').addEventListener('change', filterTemplatesBySpecialty);
    document.addEventListener('DOMContentLoaded', () => {
        filterTemplatesBySpecialty();
        filterProtocolsByType();
    });

    function applyClinicProtocol() {
        const select = document.getElementById('clinic-protocol-id');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;
        
        const promptArea = document.getElementById('ai-prompt');
        promptArea.value = `[PROTOCOLO CLÍNICO INSTITUCIONAL]\nProtocolo: ${opt.text}\nObjetivo: ${opt.dataset.objective}\nProtocolo: ${opt.dataset.protocol}\nFrequência: ${opt.dataset.freq}\nDuração: ${opt.dataset.dur}`;
        setType('medical');
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    
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
