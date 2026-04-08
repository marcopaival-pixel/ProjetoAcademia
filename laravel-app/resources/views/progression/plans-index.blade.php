@extends('layouts.app')

@section('title', 'Meus Treinos - Progressão de Carga')

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-white/5">
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-white tracking-tight">Planilhas de Treino</h1>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Acompanhe a sua evolução e estratégias</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('progression.charts') }}" class="px-5 py-3 bg-zinc-900 border border-white/10 text-white font-bold rounded-2xl hover:bg-zinc-800 transition-all text-xs flex items-center gap-2 shadow-xl">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                Evolução
            </a>
            <a href="{{ route('progression.plans.target-selection') }}" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-95 text-xs flex items-center gap-2 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Plano
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center gap-3 animate-fade-in shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($plans as $plan)
            <div class="group bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl hover:border-blue-500/30 transition-all duration-300 flex flex-col">
                <div class="p-8 flex-1 space-y-6">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            @if($plan->plan_label)
                                <span class="inline-block px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20 mb-3 shadow-[0_0_15px_rgba(59,130,246,0.1)]">
                                    {{ $plan->plan_label }}
                                </span>
                            @endif
                            <h3 class="text-xl font-black text-white group-hover:text-blue-400 transition-colors">{{ $plan->name }}</h3>
                        </div>
                        <span class="shrink-0 px-3 py-1 bg-zinc-950/50 text-zinc-400 text-[10px] font-bold uppercase rounded-xl border border-white/5">
                            {{ $plan->exercises_count }} Exs
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-black/20 p-4 rounded-2xl border border-white/5">
                            <span class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-1">Foco Principal</span>
                            <span class="text-sm font-bold text-zinc-300">{{ $plan->goal ?: 'Não definido' }}</span>
                        </div>
                        
                        <p class="text-xs text-zinc-400 font-medium leading-relaxed line-clamp-3">
                            {{ $plan->description ?: 'Sem descrição detalhada.' }}
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-zinc-950/50 border-t border-white/5 space-y-3">
                    <a href="{{ route('progression.log', $plan) }}" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-[0.98] text-[10px] uppercase tracking-widest flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Iniciar Treino
                    </a>
                    
                    <div class="flex gap-2 relative group/actions">
                        <a href="{{ route('progression.plans.show', $plan) }}" class="flex-1 py-3 bg-zinc-900 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800 font-bold rounded-xl transition-all text-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Gerenciar
                        </a>
                        
                        <!-- Mini Dropdown for secondary actions using pure CSS hover group -->
                        <div class="relative">
                            <button class="px-4 py-3 bg-zinc-900 border border-white/5 text-zinc-400 hover:text-white hover:bg-zinc-800 font-bold rounded-xl transition-all h-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                            </button>
                            <div class="absolute bottom-full right-0 mb-2 w-48 bg-zinc-900 border border-white/10 rounded-2xl shadow-xl opacity-0 invisible group-hover/actions:opacity-100 group-hover/actions:visible transition-all duration-200 z-50 p-2 pointer-events-none group-hover/actions:pointer-events-auto">
                                <form action="{{ route('progression.plans.duplicate', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-xs font-bold text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-xl transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        Duplicar Plano
                                    </button>
                                </form>
                                <a href="{{ route('progression.plans.pdf', $plan) }}" class="block px-4 py-3 text-xs font-bold text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-xl transition-colors flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Exportar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                <div class="flex flex-col items-center justify-center py-20 px-4 bg-zinc-900/20 border border-white/5 border-dashed rounded-[3rem] text-center">
                    <div class="w-20 h-20 bg-zinc-900 rounded-full flex items-center justify-center mb-6 border border-white/10 shadow-2xl">
                        <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h4 class="text-xl font-black text-white mb-2">Motor Primário Inativo</h4>
                    <p class="text-zinc-500 font-medium max-w-sm mb-8 text-sm">Ainda não existem rotinas planeadas de treino. Crie o seu primeiro plano e comece hoje mesmo a monitorizar todos os seus dados biométricos e cargas de evolução.</p>
                    <a href="{{ route('progression.plans.target-selection') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all shadow-xl shadow-blue-500/20 uppercase tracking-[0.2em] text-[10px]">
                        Orquestrar Plano Tático
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    body { background-color: #0b0e14; }
</style>
@endsection
