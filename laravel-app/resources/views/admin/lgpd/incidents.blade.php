@extends('layouts.admin')

@section('title', 'Incidentes de Segurança (LGPD)')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-white">Registro de Incidentes</h2>
            <p class="text-sm text-zinc-400 mt-1">DPO e Administradores: Registrem aqui suspeitas de violação de dados ou falhas de conformidade.</p>
        </div>
        <a href="{{ route('admin.lgpd.index') }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 border border-white/5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-zinc-700 transition-all">
            &larr; Voltar
        </a>
    </div>

    <!-- Novo Incidente -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden p-8">
        <h3 class="text-lg font-black text-white mb-6">Declarar Novo Incidente</h3>
        <form action="{{ route('admin.lgpd.incidents.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Título Resumido</label>
                <input type="text" name="title" required class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-bold" placeholder="Ex: Acesso indevido a dados de perfil">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Gravidade / Risco</label>
                    <select name="severity" required class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-bold">
                        <option value="low">Baixo - Sem risco crítico</option>
                        <option value="medium">Médio - Risco contido</option>
                        <option value="high">Alto - Risco de Exposição PII</option>
                        <option value="critical">Crítico - Fuga de Dados / Intervenção da ANPD requerida</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Descrição Completa e Ações Tomadas</label>
                <textarea name="description" required rows="4" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono text-sm" placeholder="Detalhes técnicos, IP da ameaça e contenções aplicadas..."></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-red-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-500 transition-all">
                    Gravar Incidente no Sistema
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Incidentes -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="p-8 border-b border-white/5">
            <h3 class="text-xl font-black text-white tracking-tight">Histórico</h3>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($incidents as $incident)
            <div class="p-6">
                <div class="flex items-center gap-4 mb-3">
                    <span class="px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest 
                        {{ $incident->severity === 'critical' ? 'bg-red-500/10 border border-red-500/20 text-red-500' : 
                          ($incident->severity === 'high' ? 'bg-amber-500/10 border border-amber-500/20 text-amber-500' : 
                          ($incident->severity === 'medium' ? 'bg-yellow-500/10 border border-yellow-500/20 text-yellow-500' : 'bg-blue-500/10 border border-blue-500/20 text-blue-500')) }}">
                        {{ strtoupper($incident->severity) }}
                    </span>
                    <span class="px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest bg-zinc-800 text-zinc-300">
                        {{ strtoupper($incident->status) }}
                    </span>
                    <span class="text-xs text-zinc-500 font-mono">{{ \Carbon\Carbon::parse($incident->created_at)->format('d/m/Y H:i:s') }}</span>
                </div>
                <h4 class="text-white font-bold mb-2">{{ $incident->title }}</h4>
                <p class="text-sm text-zinc-400 font-mono whitespace-pre-wrap">{{ $incident->description }}</p>
                <div class="mt-4 pt-4 border-t border-white/5 text-xs text-zinc-600">
                    ID Alerta: #{{ str_pad($incident->id, 5, '0', STR_PAD_LEFT) }} • Reportado por Oficial DPO: #{{ $incident->reporter_id }}
                </div>
            </div>
            @empty
            <div class="py-20 text-center text-zinc-500">
                <i class="fas fa-shield-check text-4xl text-emerald-500/20 mb-4 block"></i>
                <span class="font-bold">Nenhum incidente na base. A infraestrutura está íntegra e blindada.</span>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
