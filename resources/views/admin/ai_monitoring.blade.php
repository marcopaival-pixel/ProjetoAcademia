@extends('layouts.admin')

@section('title', 'AI Governance & Matrix')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Matrix Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">NexShape <span class="text-indigo-500">AI Matrix</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Supervisão de interações neurais e consumo de tokens.</p>
        </div>
        
        <div class="flex items-center gap-3 bg-indigo-500/10 px-6 py-3 rounded-2xl border border-indigo-500/20 shadow-xl">
            <i class="fas fa-brain text-indigo-400 animate-pulse"></i>
            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Motor GPT-4o Online</span>
        </div>
    </div>

    <!-- Quick Stats HUD -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach([
            ['label' => 'Carga Total', 'val' => $totalMessagesCount, 'sub' => 'Mensagens processadas', 'icon' => 'fa-database', 'color' => 'blue'],
            ['label' => 'Janela Hoje', 'val' => $todayMessagesCount, 'sub' => 'Pico de atividade', 'icon' => 'fa-bolt', 'color' => 'emerald'],
            ['label' => 'Volume Neuronal', 'val' => number_format($estimatedTokens, 0, ',', '.'), 'sub' => 'Tokens estimados', 'icon' => 'fa-microchip', 'color' => 'indigo'],
            ['label' => 'Custo de Operação', 'val' => '$' . number_format(($estimatedTokens / 1000000) * 0.5, 4), 'sub' => 'Ref: GPT-4o mini', 'icon' => 'fa-dollar-sign', 'color' => 'amber']
        ] as $stat)
        <div class="bg-zinc-900/40 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/5 shadow-2xl group hover:border-{{ $stat['color'] }}-500/30 transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-{{ $stat['color'] }}-500/10 flex items-center justify-center text-{{ $stat['color'] }}-500 border border-{{ $stat['color'] }}-500/20 group-hover:bg-{{ $stat['color'] }}-500 group-hover:text-white transition-all shadow-lg">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </div>
            </div>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">{{ $stat['label'] }}</p>
            <h4 class="text-3xl font-black text-white leading-none tabular-nums">{{ $stat['val'] }}</h4>
            <p class="text-[10px] text-zinc-600 font-bold mt-3 uppercase tracking-tight">{{ $stat['sub'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10 items-start">
        <!-- Activity Ranking -->
        <div class="xl:col-span-5 bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] shadow-2xl">
            <h3 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                <i class="fas fa-trophy text-amber-500"></i>Power Users
            </h3>
            
            <div class="space-y-6">
                @foreach($topUsers as $top)
                <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-blue-500/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 font-black text-xs border border-blue-500/20">
                            {{ substr($top->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white leading-tight">{{ $top->name }}</p>
                            <p class="text-[10px] text-zinc-600 font-medium">{{ $top->email }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-zinc-900 rounded-lg text-[10px] font-black text-zinc-400 border border-white/5 tabular-nums">
                        {{ $top->total }} msg
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="xl:col-span-7 bg-zinc-900/20 border border-white/5 p-10 rounded-[3.5rem] shadow-2xl">
            <h3 class="text-xl font-black text-white mb-8 flex items-center gap-3">
                <i class="fas fa-terminal text-emerald-500"></i>Diálogos Recentes
            </h3>
            
            <div class="space-y-6">
                @foreach($recentChats as $user)
                    @php($lastMsg = $user->aiChats->first())
                    <div class="relative p-6 bg-zinc-950/50 rounded-3xl border border-white/5 overflow-hidden group hover:bg-zinc-950 transition-colors">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 group-hover:w-2 transition-all"></div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-sm font-black text-white underline decoration-blue-500/30 underline-offset-4">{{ $user->name }}</span>
                            <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">{{ $lastMsg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-zinc-400 line-clamp-2 leading-relaxed italic">
                            "{{ $lastMsg->message }}"
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
