@extends('layouts.app')

@section('title', 'Base de Clientes — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Base de Inteligência</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ count($patients) }} Pacientes Conectados</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Gestão de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Pacientes</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Acompanhamento granular da evolução biométrica e adesão às prescrições do ecossistema NexShape.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                <button class="px-6 py-3 bg-zinc-800 text-zinc-300 font-bold rounded-xl hover:bg-zinc-700 transition-all border border-white/5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    Exportar
                </button>
                <button class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Novo Paciente
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca de Alta Performance -->
    <div class="group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-6 rounded-[2.5rem] overflow-hidden shadow-xl transition-all hover:border-white/20">
        <div class="flex flex-col md:flex-row gap-6 items-center">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl py-4 pl-14 pr-6 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all placeholder:text-zinc-600" placeholder="Filtrar por nome, perfil metabólico ou objetivo...">
            </div>
            <div class="flex gap-4 w-full md:w-auto">
                <select class="bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-zinc-400 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none min-w-[160px]">
                    <option>Todos Status</option>
                    <option>Ativos (Em Dia)</option>
                    <option>Atenção (Platô)</option>
                </select>
                <select class="bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-zinc-400 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none min-w-[180px]">
                    <option>Objetivo: Todos</option>
                    <option>Hipertrofia</option>
                    <option>Emagrecimento</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grid de Cards Inteligentes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @foreach($patients as $patient)
        <div class="group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-blue-500/30 hover:scale-[1.02]">
            <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
            
            <div class="flex items-start justify-between mb-8">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-tr from-zinc-800 to-zinc-700 flex items-center justify-center text-white font-black text-xl shadow-inner group-hover:from-blue-600 group-hover:to-indigo-500 transition-all duration-500">
                        {{ $patient['avatar'] }}
                    </div>
                    <div>
                        <h3 class="text-white font-black text-xl leading-tight group-hover:text-blue-400 transition-colors">{{ $patient['name'] }}</h3>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-1">{{ $patient['goal'] }}</p>
                    </div>
                </div>
                <div class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $patient['status'] == 'active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20' }}">
                    {{ $patient['status'] == 'active' ? 'Em Progresso' : 'Ação Necessária' }}
                </div>
            </div>

            <div class="space-y-6 mb-8">
                <div class="space-y-3">
                    <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                        <span class="text-zinc-500">Adesão ao Protocolo</span>
                        <span class="text-emerald-400">{{ 75 }}%</span>
                    </div>
                    <div class="w-full bg-zinc-950 rounded-full h-2 overflow-hidden border border-white/5 p-0.5">
                        <div class="bg-gradient-to-r from-blue-600 to-emerald-400 h-full rounded-full" style="width: 75%"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[9px] text-zinc-500 font-bold uppercase mb-1">Evolução Peso</p>
                        <p class="text-white font-black">{{ $patient['weight_evolution'] > 0 ? '+' : '' }}{{ $patient['weight_evolution'] }}kg</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[9px] text-zinc-500 font-bold uppercase mb-1">Gordura (BF)</p>
                        <p class="text-emerald-400 font-black">-1.2%</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-white/5">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                    <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $patient['last_activity'] }}</span>
                </div>
                <a href="{{ route('professional.patients.show', $patient['id']) }}" class="flex items-center justify-center w-12 h-12 bg-zinc-800 text-white rounded-2xl border border-white/5 hover:bg-blue-600 hover:border-blue-500 transition-all group/btn">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
