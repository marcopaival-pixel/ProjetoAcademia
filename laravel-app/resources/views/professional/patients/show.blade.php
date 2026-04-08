@extends('layouts.app')

@section('title', 'Bio-Intelligence — ' . $patient['name'])

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header Strategy: Professional Navigation + Breadcrumb -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <nav class="flex items-center gap-2 text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                <a href="{{ route('professional.patients.index') }}" class="hover:text-blue-400 transition-colors">Patients Directory</a>
                <span><svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg></span>
                <span class="text-white">Active Bio-Record</span>
            </nav>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                {{ $patient['name'] }} <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Insight</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <button class="p-3 bg-zinc-800 text-zinc-500 hover:text-white rounded-xl transition-all border border-white/5 group shadow-lg">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                </button>
                <button class="p-3 bg-zinc-800 text-zinc-500 hover:text-white rounded-xl transition-all border border-white/5 group shadow-lg">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9l-4 4v-4H3a2 2 0 01-2-2V10a2 2 0 012-2h2M9 21V5a2 2 0 012-2h2a2 2 0 012 2v16"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Sidebar Perfil (Col 4) - Biometrics Card -->
        <div class="lg:col-span-4 xl:col-span-3 space-y-8">
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] text-center overflow-hidden shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                
                <div class="relative z-10 space-y-8">
                    <div class="w-40 h-40 mx-auto rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-800 flex items-center justify-center text-white text-5xl font-black shadow-[0_30px_60px_-15px_rgba(59,130,246,0.5)] group-hover:scale-105 transition-transform duration-700">
                        {{ substr($patient['name'], 0, 1) }}{{ substr(strrchr($patient['name'], ' '), 1, 1) }}
                    </div>
                    
                    <div class="space-y-2">
                        <span class="px-4 py-1 bg-white/5 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-400/20 tracking-[0.2em] mb-2 inline-block">{{ $patient['biotype'] }}</span>
                        <h2 class="text-3xl font-black text-white tracking-tight">{{ $patient['name'] }}</h2>
                        <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-widest italic">{{ $patient['age'] }} anos — Status Ativo</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 py-8 border-t border-white/5">
                        <div class="text-left space-y-2">
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Bio-Height</p>
                            <p class="text-2xl font-black text-white tracking-tighter">{{ $patient['height'] }}<span class="text-xs text-zinc-500 ml-1">cm</span></p>
                        </div>
                        <div class="text-left space-y-2">
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Bio-Weight</p>
                            <p class="text-2xl font-black text-white tracking-tighter">{{ $patient['weight'] }}<span class="text-xs text-zinc-500 ml-1">kg</span></p>
                        </div>
                    </div>

                    <button class="w-full py-5 bg-white text-zinc-900 font-black rounded-3xl hover:bg-blue-400 hover:text-white transition-all shadow-2xl text-xs uppercase tracking-widest active:scale-95">
                        Update Measures
                    </button>
                </div>
            </div>

            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[3rem] space-y-6 shadow-2xl">
                <div class="flex items-center gap-4 px-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                    <h3 class="text-zinc-400 font-black text-[10px] uppercase tracking-[0.3em]">Operational Strategy</h3>
                </div>
                <div class="p-8 bg-zinc-950/50 rounded-[2rem] border border-blue-500/10 shadow-inner group hover:border-blue-500/30 transition-all">
                    <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-4">Core Objective</p>
                    <p class="text-white text-lg font-black leading-tight mb-4 group-hover:text-blue-400 transition-colors">{{ $patient['goal'] }}</p>
                    <div class="pt-4 border-t border-white/5 flex flex-col gap-1">
                        <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest">Protocol Matrix</span>
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
                             <h3 class="text-3xl font-black text-white tracking-tight">Evolução Ponderal</h3>
                        </div>
                        <div class="flex gap-2 p-1.5 bg-zinc-950/50 rounded-2xl border border-white/5">
                            <button class="px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black rounded-xl shadow-lg shadow-blue-500/20 uppercase tracking-widest">Weight Log</button>
                            <button class="px-5 py-2.5 text-zinc-500 hover:text-white text-[10px] font-black rounded-xl uppercase tracking-widest transition-all italic">Fat % Index</button>
                        </div>
                    </div>
                    
                    <div class="h-80 w-full flex items-end gap-6 px-4">
                        @foreach($evolutionData as $key => $point)
                        <div class="flex-1 flex flex-col items-center gap-6">
                            <div class="w-full bg-gradient-to-t from-blue-500/10 to-blue-500/30 rounded-t-2xl group/bar relative flex items-end justify-center transition-all hover:to-blue-400 hover:border-blue-400/20 border-t border-x border-white/5 shadow-inner" style="height: {{ ($point - 80) * 15 }}px">
                                <span class="opacity-0 group-hover/bar:opacity-100 absolute -top-12 bg-white text-zinc-900 text-[10px] font-black py-2 px-3 rounded-xl mb-2 shadow-2xl transition-all scale-75 group-hover/bar:scale-100">{{ $point }}kg</span>
                                <div class="w-full h-full absolute inset-0 bg-blue-400/10 opacity-0 group-hover/bar:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em]">{{ $dates[$key] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tabs de Acompanhamento - Interactive Bento Design -->
            <div class="space-y-8">
                <div class="flex border-b border-white/5 gap-12 px-6 overflow-x-auto scrollbar-hide">
                    <button class="pb-6 text-blue-400 font-black border-b-[3px] border-blue-400 text-[10px] uppercase tracking-[0.3em] whitespace-nowrap">Clinical Diary</button>
                    <button class="pb-6 text-zinc-600 font-black hover:text-white transition-all text-[10px] uppercase tracking-[0.3em] whitespace-nowrap">Training Matrix</button>
                    <button class="pb-6 text-zinc-600 font-black hover:text-white transition-all text-[10px] uppercase tracking-[0.3em] whitespace-nowrap">Bio-Evaluations</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Progress Card -->
                    <div class="group bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] space-y-8 shadow-xl hover:border-emerald-500/20 transition-all">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-black text-sm uppercase tracking-widest">Metas Bio-Nutricionais</h4>
                            <span class="text-[10px] text-emerald-400 font-black uppercase tracking-widest px-3 py-1 bg-emerald-500/5 rounded-full border border-emerald-500/20">92% COMPLIANCE</span>
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                    <span>Protein Load</span><span class="text-white">182g / 200g</span>
                                </div>
                                <div class="w-full bg-zinc-950 h-3 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: 91%"></div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                    <span>Glycogen Index</span><span class="text-white">210g / 250g</span>
                                </div>
                                <div class="w-full bg-zinc-950 h-3 rounded-full overflow-hidden p-0.5 border border-white/5 shadow-inner">
                                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 h-full rounded-full transition-all duration-1000" style="width: 84%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Card -->
                    <a href="{{ route('professional.ai-wizard.index') }}" class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] flex flex-col items-center justify-center text-center space-y-4 hover:bg-blue-600/10 hover:border-blue-500/30 transition-all shadow-xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="w-20 h-20 bg-blue-500/10 rounded-[2rem] border border-blue-500/20 flex items-center justify-center text-blue-400 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 z-10 shadow-2xl">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div class="z-10">
                            <p class="text-white font-black text-xl tracking-tight mb-2">Engage AI Wizard</p>
                            <p class="text-zinc-500 font-bold uppercase text-[9px] tracking-widest leading-relaxed">Gerar novo protocolo dinâmico<br>baseado no histórico clínico</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes dashboard-entry {
        from { opacity: 0; transform: translateY(40px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-dashboard-entry {
        animation: dashboard-entry 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    body {
        background-color: #080a0f;
        background-image: 
            radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.1) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0, transparent 40%),
            radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar Perfil (Col 4) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-8 rounded-3xl text-center space-y-4">
                <div class="w-32 h-32 mx-auto rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center text-white text-4xl font-bold shadow-2xl">
                    {{ substr($patient['name'], 0, 1) }}{{ substr(strrchr($patient['name'], ' '), 1, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $patient['name'] }}</h2>
                    <p class="text-zinc-500 text-sm italic">{{ $patient['biotype'] }} • {{ $patient['age'] }} anos</p>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                    <div class="text-left">
                        <p class="text-[10px] text-zinc-500 uppercase font-bold">Altura</p>
                        <p class="text-white font-bold">{{ $patient['height'] }} cm</p>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] text-zinc-500 uppercase font-bold">Peso Atual</p>
                        <p class="text-white font-bold">{{ $patient['weight'] }} kg</p>
                    </div>
                </div>
                <button class="w-full py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold rounded-2xl border border-white/5 transition-all text-sm">
                    Atualizar Medidas
                </button>
            </div>

            <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-6 rounded-3xl space-y-4">
                <h3 class="text-white font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.994 7.994 0 002 12a7.994 7.994 0 007 7.196V4.804zM11 4.804v14.392A7.994 7.994 0 0018 12a7.994 7.994 0 00-7-7.196z"></path></svg>
                    Estratégia Atual
                </h3>
                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-blue-500/20">
                    <p class="text-[10px] text-blue-400 uppercase font-bold mb-1">Apenas Profissional</p>
                    <p class="text-zinc-200 text-sm font-medium">{{ $patient['goal'] }}</p>
                    <p class="text-zinc-500 text-xs mt-2">Protocolo: {{ $patient['formula'] }}</p>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal (Col 8) -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Gráfico de Evolução -->
            <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-8 rounded-3xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-white">Evolução Ponderal</h3>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 bg-white/5 text-zinc-400 text-xs rounded-lg border border-white/10 hover:text-white transition-all">Peso (kg)</button>
                        <button class="px-3 py-1 bg-zinc-800 text-white text-xs rounded-lg border border-white/10 opacity-50">BF (%)</button>
                    </div>
                </div>
                
                <div class="h-64 w-full flex items-end gap-4">
                    @foreach($evolutionData as $key => $point)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-blue-500/20 rounded-t-xl group relative flex items-end justify-center transition-all hover:bg-blue-500/40" style="height: {{ ($point - 80) * 15 }}px">
                            <span class="opacity-0 group-hover:opacity-100 absolute -top-8 bg-white text-zinc-900 text-[10px] font-bold py-1 px-2 rounded mb-2">{{ $point }}kg</span>
                        </div>
                        <span class="text-[10px] text-zinc-500 font-bold uppercase">{{ $dates[$key] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tabs de Acompanhamento -->
            <div class="space-y-4">
                <div class="flex border-b border-white/5 gap-8">
                    <button class="pb-4 text-blue-400 font-bold border-b-2 border-blue-400 text-sm">Diário Alimentar</button>
                    <button class="pb-4 text-zinc-500 font-medium hover:text-zinc-300 transition-all text-sm">Treinos Atuais</button>
                    <button class="pb-4 text-zinc-500 font-medium hover:text-zinc-300 transition-all text-sm">Avaliações Físicas</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-zinc-900/40 border border-white/5 p-5 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-semibold text-sm">Meta de Hoje</h4>
                            <span class="text-[10px] text-emerald-400 font-bold uppercase">92% Atingido</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs text-zinc-500">
                                <span>Proteína</span><span>182g de 200g</span>
                            </div>
                            <div class="w-full bg-zinc-950 h-1 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full w-[91%]"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-900/40 border border-white/5 p-5 rounded-2xl flex flex-col justify-center text-center space-y-2 cursor-pointer hover:bg-zinc-800 transition-all">
                        <div class="w-10 h-10 bg-blue-500/10 rounded-full flex items-center justify-center mx-auto text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-white font-bold text-sm">Nova Prescrição IA</p>
                        <p class="text-zinc-500 text-xs">Gerar plano baseado na evolução atual</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.8s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
