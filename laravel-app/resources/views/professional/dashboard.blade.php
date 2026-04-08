@extends('layouts.app')

@section('title', 'Portal Business — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1600px] mx-auto px-6">
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Ambiente Profissional Ativo</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Gestão, <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">{{ explode(' ', Auth::user()->name)[0] }}</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Central de inteligência e performance para sua base de pacientes. Otimizando resultados com tecnologia NexShape.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <a href="{{ route('professional.patients.index') }}" class="group relative px-6 py-3 bg-blue-600 text-white font-bold rounded-xl overflow-hidden transition-all hover:pr-10 active:scale-95">
                    <span class="relative z-10">Ver Pacientes</span>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </a>
                <a href="{{ route('professional.ai-wizard') }}" class="px-6 py-3 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 font-bold rounded-xl transition-all border border-emerald-500/10">Prescrição IA</a>
            </div>
        </div>
    </div>

    <!-- Layout Bento Moderno (Métricas de Gestão) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        @php
            $metrics = [
                ['label' => 'Pacientes Ativos', 'val' => $stats['active_patients'], 'change' => '+12%', 'color' => 'blue', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Prescrições Ativas', 'val' => $stats['active_plans'], 'change' => '+8%', 'color' => 'purple', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ['label' => 'Taxa de Retenção', 'val' => $stats['retention_rate'].'%', 'change' => 'Alta', 'color' => 'amber', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['label' => 'Faturamento Mensal', 'val' => $stats['revenue_month'], 'change' => '+15%', 'color' => 'emerald', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp

        @foreach($metrics as $m)
        <div class="group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2.5rem] overflow-hidden shadow-2xl transition-all hover:border-{{ $m['color'] }}-500/50 hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-{{ $m['color'] }}-500/10 rounded-2xl flex items-center justify-center text-{{ $m['color'] }}-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $m['icon'] }}"></path></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-{{ $m['color'] }}-400 bg-{{ $m['color'] }}-400/10 px-3 py-1 rounded-full border border-{{ $m['color'] }}-400/20">{{ $m['change'] }}</span>
            </div>
            <p class="text-zinc-500 font-bold text-xs uppercase tracking-tighter">{{ $m['label'] }}</p>
            <h3 class="text-3xl font-black text-white mt-1">{{ $m['val'] }}</h3>
        </div>
        @endforeach
    </div>

    <!-- Dashboard Core Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Engajamento da Base (8 colunas) -->
        <div class="lg:col-span-12 xl:col-span-8 group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3.5rem] overflow-hidden shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)]">
            <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h3 class="text-2xl font-black text-white">Engajamento da Base</h3>
                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Percentual de adesão aos treinos/dietas</p>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-2xl border border-white/5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Tempo Real</span>
                </div>
            </div>

            <div class="h-64 flex items-end gap-3 px-2 mb-8 relative">
                <!-- Grid Lines -->
                <div class="absolute inset-x-0 top-0 h-px bg-white/5"></div>
                <div class="absolute inset-x-0 top-1/2 h-px bg-white/5"></div>

                @foreach($engagementData as $val)
                <div class="flex-1 group/bar relative h-full flex flex-col justify-end">
                    <div class="w-full bg-gradient-to-t from-blue-600/20 to-blue-500/80 rounded-2xl transition-all duration-700 group-hover/bar:to-emerald-400 group-hover/bar:scale-x-105" style="height: {{ $val }}%;">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover/bar:opacity-100 transition-all bg-white text-zinc-900 text-[10px] font-black py-1 px-3 rounded-full shadow-2xl">
                            {{ $val }}%
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="flex justify-between px-4 text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em]">
                <span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span><span>Dom</span>
            </div>
        </div>

        <!-- Alertas Inteligentes (4 colunas) -->
        <div class="lg:col-span-12 xl:col-span-4 space-y-10">
            <div class="group relative bg-zinc-900/60 backdrop-blur-2xl p-10 rounded-[3.5rem] border border-white/10 overflow-hidden shadow-2xl transition-all hover:border-white/20">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                            <span class="text-sm font-black italic">AI</span>
                        </div>
                        <h3 class="text-xl font-black text-white">NexSense Alerts</h3>
                    </div>
                    <span class="text-zinc-600 text-[10px] font-black uppercase animate-pulse">Scanning...</span>
                </div>

                <div class="space-y-6">
                    @foreach($tasks as $task)
                    <div class="p-5 bg-white/5 rounded-3xl border border-white/5 hover:bg-white/10 transition-colors cursor-pointer group/item">
                        <div class="flex items-start gap-4">
                            <div class="mt-1.5 w-2 h-2 rounded-full @if($task['priority'] == 'high') bg-red-500 shadow-[0_0_8px_#ef4444] @elseif($task['priority'] == 'medium') bg-amber-500 shadow-[0_0_8px_#f59e0b] @else bg-blue-500 @endif"></div>
                            <div class="flex-1">
                                <p class="text-sm text-zinc-200 font-bold leading-tight line-clamp-2">{{ $task['msg'] }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $task['priority'] }} priority</span>
                                    <span class="text-[9px] text-blue-400 font-bold uppercase tracking-widest group-hover/item:translate-x-1 transition-transform">Agir &rarr;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <a href="{{ route('professional.ai-wizard') }}" class="mt-10 flex items-center justify-between w-full p-2 pr-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-blue-400 hover:text-white transition-all group/btn">
                    <div class="h-12 w-12 bg-zinc-900 text-white rounded-2xl flex items-center justify-center group-hover/btn:bg-white group-hover/btn:text-blue-500 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM14.502 8.993L8.913 14.586a1 1 0 01-1.417 0l-3.087-3.088a1 1 0 111.414-1.414l2.38 2.38 4.885-4.885a1 1 0 111.414 1.414z"></path></svg>
                    </div>
                    <span>NOVA PRESCRIÇÃO AI</span>
                    <svg class="w-5 h-5 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Strategic Shortcuts Module -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <a href="{{ route('professional.patients.index') }}" class="group bg-zinc-900/60 backdrop-blur-md border border-white/10 p-8 rounded-[3rem] hover:border-emerald-500/30 transition-all shadow-xl">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-white font-black text-xl tracking-tight leading-none">Base Global</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Directory Management</p>
                </div>
            </div>
            <p class="text-zinc-500 text-sm font-medium leading-relaxed">Acesse o ecossistema completo de prontuários, evoluções e históricos de pacientes.</p>
        </a>

        <a href="{{ route('professional.branding.index') }}" class="group bg-zinc-900/60 backdrop-blur-md border border-white/10 p-8 rounded-[3rem] hover:border-purple-500/30 transition-all shadow-xl">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-400 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                </div>
                <div>
                    <h3 class="text-white font-black text-xl tracking-tight leading-none">Branding Studio</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">White-Label Engine</p>
                </div>
            </div>
            <p class="text-zinc-500 text-sm font-medium leading-relaxed">Personalize a identidade visual da sua plataforma para uma experiência única.</p>
        </a>

        <a href="{{ route('hydration.index') }}" class="group bg-zinc-900/60 backdrop-blur-md border border-white/10 p-8 rounded-[3rem] hover:border-blue-500/30 transition-all shadow-xl">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-16 0m16 0v10l-8 4-8-4V7m16 0l-8 4-8-4"></path></svg>
                </div>
                <div>
                    <h3 class="text-white font-black text-xl tracking-tight leading-none">NexHydra</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Bio-Balance Control</p>
                </div>
            </div>
            <p class="text-zinc-500 text-sm font-medium leading-relaxed">Gestão inteligente de hidratação para otimização de performance metabólica.</p>
        </a>
    </div>

    <!-- Tabela de Pacientes (UX High-Performance) -->
    <div class="bg-zinc-900/60 backdrop-blur-md border border-white/10 rounded-[3.5rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-white/5 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-black text-white">Pacientes Recentes</h3>
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Status de consumo e treino em tempo real</p>
            </div>
            <a href="{{ route('professional.patients.index') }}" class="px-6 py-2 bg-zinc-800 text-zinc-300 font-bold rounded-2xl border border-white/5 hover:bg-zinc-700 transition-all text-sm">Base Completa</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
                        <th class="px-10 py-6">Paciente</th>
                        <th class="px-10 py-6">Status Bio</th>
                        <th class="px-10 py-6">Aderência</th>
                        <th class="px-10 py-6 text-right">Painel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach([
                        ['name' => 'Carlos Silva', 'bio' => 'Hypertrophy • Mesomorph', 'status' => 'Stable', 'engage' => 95, 'initials' => 'CS', 'color' => 'from-blue-500 to-cyan-500'],
                        ['name' => 'Maria Oliveira', 'bio' => 'Fat Loss • Endomorph', 'status' => 'Review Needed', 'engage' => 72, 'initials' => 'MO', 'color' => 'from-purple-500 to-pink-500'],
                        ['name' => 'João Santos', 'bio' => 'Maintenance • Ectomorph', 'status' => 'Pending Rehab', 'engage' => 45, 'initials' => 'JS', 'color' => 'from-amber-500 to-orange-500'],
                    ] as $p)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-[1.25rem] bg-gradient-to-tr {{ $p['color'] }} flex items-center justify-center text-white font-black text-lg shadow-lg">
                                    {{ $p['initials'] }}
                                </div>
                                <div>
                                    <p class="text-white font-black text-lg group-hover:text-blue-400 transition-colors">{{ $p['name'] }}</p>
                                    <p class="text-zinc-500 text-xs font-bold">{{ $p['bio'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full @if($p['engage'] > 80) bg-emerald-500 @elseif($p['engage'] > 60) bg-amber-500 @else bg-red-500 @endif"></span>
                                <span class="text-zinc-300 font-bold text-sm">{{ $p['status'] }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-zinc-300">
                           <div class="flex items-center gap-4">
                                <div class="w-32 h-2 bg-zinc-950 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                    <div class="h-full bg-gradient-to-r @if($p['engage'] > 80) from-emerald-600 to-emerald-400 @elseif($p['engage'] > 60) from-amber-600 to-amber-400 @else from-red-600 to-red-400 @endif rounded-full" style="width: {{ $p['engage'] }}%"></div>
                                </div>
                                <span class="text-xs font-black">{{ $p['engage'] }}%</span>
                           </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <button class="p-3 bg-zinc-800 rounded-2xl hover:bg-blue-600 hover:text-white transition-all border border-white/5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
