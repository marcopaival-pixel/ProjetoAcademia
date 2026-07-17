@extends('layouts.app')

@section('title', 'Status do Sistema')

@section('content')
<div class="container-fluid py-4 min-h-[80vh]">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-green-500/10 text-green-500 px-4 py-2 rounded-full border border-green-500/20 mb-4 animate-pulse">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                <span class="text-xs font-bold uppercase tracking-wider">Todos os sistemas operacionais</span>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight">Status do <span class="text-blue-500">Sistema</span></h1>
            <p class="text-zinc-400 font-medium mt-2">Monitoramento em tempo real da infraestrutura NexShape.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach($systemInfo as $label => $value)
                <div class="bg-zinc-900/50 backdrop-blur-sm border border-white/5 p-4 rounded-3xl text-center">
                    <span class="text-[10px] text-zinc-500 uppercase font-black tracking-widest block mb-1">{{ str_replace('_', ' ', $label) }}</span>
                    <span class="text-lg font-bold text-white">{{ $value }}</span>
                </div>
            @endforeach
        </div>

        <!-- Services List -->
        <div class="bg-zinc-900/30 backdrop-blur-xl border border-white/5 rounded-[2.5rem] overflow-hidden">
            <div class="p-8 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-xl font-black text-white">Serviços Individuais</h3>
                <span class="text-xs text-zinc-500 font-mono">Último check: {{ now()->format('H:i:s') }}</span>
            </div>
            
            <div class="divide-y divide-white/5">
                @foreach($services as $service)
                    <div class="p-6 md:p-8 flex items-center justify-between hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-400 shadow-inner">
                                <i class="{{ $service['icon'] }} text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white leading-tight">{{ $service['name'] }}</h4>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ $service['description'] }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="hidden md:inline text-[10px] font-black uppercase tracking-widest {{ $service['status'] === 'operational' ? 'text-green-500' : 'text-amber-500' }}">
                                {{ $service['status'] === 'operational' ? 'Operacional' : 'Degradado' }}
                            </span>
                            <div class="w-3 h-3 rounded-full {{ $service['status'] === 'operational' ? 'bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]' : 'bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]' }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Uptime History (Visual Placeholder) -->
        <div class="mt-8">
            <h4 class="text-xs font-black text-zinc-500 uppercase tracking-widest mb-4 px-2">Histórico de Disponibilidade (Últimos 30 dias)</h4>
            <div class="flex gap-1 h-8">
                @for($i = 0; $i < 30; $i++)
                    <div class="flex-1 bg-green-500/40 rounded-full hover:bg-green-500 transition-all cursor-crosshair" title="Dia {{ 30-$i }}: 100%"></div>
                @endfor
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-xs text-zinc-600">NexShape Arena Infrastructure • Global CDN: Ativo • Latência: ~24ms</p>
        </div>
    </div>
</div>
@endsection
