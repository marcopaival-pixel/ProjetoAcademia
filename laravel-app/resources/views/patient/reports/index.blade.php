@extends('layouts.app')

@section('title', 'Intelligence Hub — NexShape')

@section('content')
<div class="py-10 space-y-16 animate-fade-in mx-auto px-4 md:px-6 max-w-[1400px] relative">
    <!-- Background Glow Effects -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute top-[40%] -right-[10%] w-[30%] h-[30%] bg-indigo-600/10 blur-[100px] rounded-full animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <!-- Header Section -->
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-10 pb-12 border-b border-white/5">
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <span class="px-4 py-1.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-[0.2em] border border-blue-500/20">Performance Analytics</span>
                @if(!$isPremium)
                    <span class="px-4 py-1.5 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-[0.2em] border border-amber-500/20 italic shadow-lg shadow-amber-500/5">Free Tier</span>
                @else
                    <span class="px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] border border-emerald-500/20 italic shadow-lg shadow-emerald-500/5">Premium Access</span>
                @endif
            </div>
            <h1 class="text-6xl md:text-7xl font-black tracking-tighter text-white leading-[0.9]">
                Intelligence<br><span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400">Hub</span>
            </h1>
            <p class="text-zinc-500 font-medium text-xl max-w-2xl leading-relaxed">Sua jornada traduzida em dados. Explore insights profundos, acompanhe sua evolução e tome decisões baseadas em ciência.</p>
        </div>

        @if(!$isPremium)
        <div class="group relative bg-zinc-900/40 backdrop-blur-3xl p-8 rounded-[3rem] border border-white/10 shadow-2xl flex items-center gap-8 transition-all hover:border-blue-500/30">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-500/20 group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-crown text-2xl"></i>
            </div>
            <div>
                <p class="text-white font-black text-lg leading-tight">Desbloqueie o Próximo Nível</p>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-1">Histórico completo e exportação em alta fidelidade</p>
                <a href="{{ route('plano') }}" class="mt-4 inline-flex items-center gap-2 text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] hover:text-white transition-colors group/link">
                    Evoluir Agora <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
        @endif
    </div>

    @if(session('premium_required'))
    <div class="relative z-10 bg-amber-500/10 border border-amber-500/20 p-8 rounded-[2.5rem] flex items-center gap-6 text-amber-500 animate-bounce-slow shadow-2xl shadow-amber-500/5">
        <div class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center">
            <i class="fas fa-lock text-xl"></i>
        </div>
        <div>
            <p class="font-black text-sm uppercase tracking-[0.2em]">Funcionalidade Premium Requerida</p>
            <p class="text-xs font-bold opacity-80 mt-1">O histórico completo e relatórios avançados estão disponíveis exclusivamente no Plano Premium. Ative agora para visualizar seus dados.</p>
        </div>
    </div>
    @endif

    @php
        $freeReports = collect($reports['free'] ?? [])->where('kind', 'report');
        $freeShortcuts = collect($reports['free'] ?? [])->where('kind', 'shortcut');
    @endphp

    <!-- Bento Grid: Main Reports -->
    <div class="relative z-10 space-y-10">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] italic">Análises de Performance</h2>
            <span class="h-[1px] flex-grow mx-10 bg-white/5"></span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($freeReports as $index => $report)
                <a href="{{ route($report['route']) }}" 
                   class="group relative bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-12 rounded-[3.5rem] hover:bg-white/5 transition-all hover:-translate-y-2 overflow-hidden {{ $index === 0 ? 'md:col-span-2' : '' }}">
                    
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-600/5 blur-[100px] group-hover:bg-blue-600/10 transition-all duration-700"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="w-20 h-20 rounded-3xl bg-zinc-950/80 border border-white/10 flex items-center justify-center text-zinc-600 group-hover:bg-blue-600 group-hover:text-white group-hover:scale-110 transition-all duration-500 shadow-2xl mb-12">
                            <i class="{{ $report['icon'] }} text-3xl"></i>
                        </div>
                        
                        <div class="mt-auto">
                            <h3 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-none mb-4 group-hover:text-blue-400 transition-colors">{{ $report['label'] }}</h3>
                            <p class="text-zinc-500 text-lg font-medium max-w-md leading-relaxed">Dados agregados no período do seu plano. Visualize sua performance de forma consolidada e objetiva.</p>
                            
                            <div class="mt-12 flex items-center justify-between border-t border-white/5 pt-8">
                                <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] bg-blue-500/10 px-5 py-2 rounded-full border border-blue-500/20">Acesso Liberado</span>
                                <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center group-hover:border-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all shadow-xl">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Operation Shortcuts -->
    <div class="relative z-10 space-y-10">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] italic">Atalhos Operacionais</h2>
            <span class="h-[1px] flex-grow mx-10 bg-white/5"></span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($freeShortcuts as $report)
                <a href="{{ route($report['route']) }}" 
                   class="group relative bg-zinc-900/20 backdrop-blur-xl border border-white/5 p-10 rounded-[3rem] hover:bg-zinc-900/40 transition-all hover:-translate-y-1 overflow-hidden">
                    
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-emerald-500/5 blur-[60px] group-hover:bg-emerald-500/10 transition-all duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-950/50 border border-white/10 flex items-center justify-center text-zinc-600 group-hover:text-emerald-400 transition-all duration-500 mb-8">
                            <i class="{{ $report['icon'] }} text-xl"></i>
                        </div>
                        
                        <h3 class="text-2xl font-black text-white tracking-tight mb-2 group-hover:text-emerald-400 transition-colors">{{ $report['label'] }}</h3>
                        <p class="text-zinc-600 text-sm font-medium">Acesso rápido à área operacional relacionada.</p>
                        
                        <div class="mt-8 flex items-center justify-between border-t border-white/5 pt-6">
                            <span class="text-[9px] font-black text-zinc-700 uppercase tracking-[0.2em]">Shortcut Link</span>
                            <i class="fas fa-external-link-alt text-zinc-800 group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Premium Section: Intelligence Pro -->
    <div class="relative z-10 space-y-12">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-xs font-black text-purple-400 uppercase tracking-[0.4em] italic">Intelligence Pro</h2>
                <span class="px-3 py-1 rounded-md bg-purple-500/10 border border-purple-500/20 text-[8px] font-black text-purple-400 uppercase tracking-widest">Next-Gen Analytics</span>
            </div>
            <span class="h-[1px] flex-grow mx-10 bg-white/5"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($reports['premium'] as $report)
            <div class="group relative bg-zinc-900/30 backdrop-blur-3xl border {{ $isPremium ? 'border-white/5 hover:bg-zinc-900/50 hover:border-purple-500/30' : 'border-purple-500/10' }} p-10 rounded-[3rem] transition-all hover:-translate-y-2 overflow-hidden shadow-2xl">
                
                @if(!$isPremium)
                    <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center gap-6 opacity-0 group-hover:opacity-100 transition-all duration-500">
                        <div class="w-14 h-14 rounded-full bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400">
                            <i class="fas fa-lock text-xl"></i>
                        </div>
                        <span class="bg-purple-600 text-white text-[9px] font-black px-5 py-2 rounded-full uppercase tracking-[0.2em] shadow-xl shadow-purple-600/20">Upgrade to Pro</span>
                    </div>
                @endif

                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl {{ $isPremium ? 'bg-purple-600/10 text-purple-400' : 'bg-zinc-800 text-zinc-700' }} border border-white/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500">
                        <i class="{{ $report['icon'] }} text-xl"></i>
                    </div>
                    
                    <h3 class="text-xl font-black text-white mb-2 leading-tight tracking-tight">{{ $report['label'] }}</h3>
                    <p class="text-zinc-600 text-[9px] font-bold uppercase tracking-[0.15em] mb-8 italic">Advanced Insights</p>
                    
                    <div class="pt-6 border-t border-white/5">
                        @if($isPremium)
                            @if(!empty($report['route']))
                                <a href="{{ route($report['route'], $report['route_params'] ?? []) }}" class="flex items-center justify-between group/link">
                                    <span class="text-[10px] font-black text-purple-400 uppercase tracking-[0.2em]">
                                        {{ $report['id'] === 'export_pdf' ? 'Download PDF' : 'Ver Análise' }}
                                    </span>
                                    <i class="fas {{ $report['id'] === 'export_pdf' ? 'fa-download' : 'fa-chevron-right' }} text-[10px] text-purple-500 group-hover/link:translate-x-0.5 transition-transform"></i>
                                </a>
                            @else
                                <div class="flex items-center gap-2 text-zinc-700">
                                    <i class="fas fa-clock text-[10px]"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Em Pipeline</span>
                                </div>
                            @endif
                        @else
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-[8px] font-black text-zinc-700 uppercase tracking-widest">
                                    <span>Análise Bloqueada</span>
                                    <span>PRO</span>
                                </div>
                                <div class="w-full h-1 bg-zinc-800/50 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-600/40 w-1/3"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-bounce-slow { animation: bounceSlow 3s infinite; }
    
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(30px) scale(0.98); } 
        to { opacity: 1; transform: translateY(0) scale(1); } 
    }
    
    @keyframes bounceSlow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    body {
        background-color: #06080c;
        background-image: 
            radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.05) 0px, transparent 50%);
    }

    /* Custom Scrollbar for better aesthetics */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #06080c; }
    ::-webkit-scrollbar-thumb { background: #1a1c24; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #2a2c34; }
</style>
@endsection

