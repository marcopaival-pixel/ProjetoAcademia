@extends('layouts.admin')

@section('title', 'Painel LGPD e Privacidade')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header/Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card: Total Consents -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] hover:bg-zinc-900/60 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Adesões LGPD</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 text-xs">
                    <i class="fas fa-file-contract"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">{{ $stats['total_consents'] }}</div>
            <div class="text-[9px] text-blue-400 font-bold mt-2 uppercase tracking-wide">Aceites confirmados</div>
        </div>

        <!-- Card: Open Incidents -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-{{ $stats['incidents_open'] > 0 ? 'amber' : 'emerald' }}-500/20 p-6 rounded-[2rem] hover:bg-zinc-900/60 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Incidentes Ativos</span>
                <div class="w-8 h-8 rounded-lg bg-{{ $stats['incidents_open'] > 0 ? 'amber' : 'emerald' }}-500/10 flex items-center justify-center text-{{ $stats['incidents_open'] > 0 ? 'amber' : 'emerald' }}-500 text-xs">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">{{ $stats['incidents_open'] }}</div>
            <div class="text-[9px] text-{{ $stats['incidents_open'] > 0 ? 'amber' : 'emerald' }}-400 font-bold mt-2 uppercase tracking-wide">Requerem atenção imediata</div>
        </div>

        <!-- Card: Quick Actions -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] flex flex-col justify-center gap-3">
            <a href="{{ route('admin.lgpd.consents') }}" class="w-full px-4 py-2 bg-blue-600/10 text-blue-500 border border-blue-500/20 rounded-xl text-xs font-bold uppercase tracking-widest text-center hover:bg-blue-600/20 transition-all">Ver Registros de Consentimento</a>
            <a href="{{ route('admin.lgpd.incidents') }}" class="w-full px-4 py-2 bg-zinc-800 text-zinc-300 border border-white/5 rounded-xl text-xs font-bold uppercase tracking-widest text-center hover:bg-zinc-700 transition-all">Reportar Incidente (DPO)</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Consents -->
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-xl font-black text-white tracking-tight">Últimos Aceites</h3>
                <i class="fas fa-check-double text-blue-500 text-sm"></i>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($stats['recent_consents'] as $consent)
                <div class="p-6 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
                    <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center border border-white/5 text-xs text-blue-500">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-zinc-200">{{ $consent->user->name ?? 'Usuário '.$consent->user_id }}</p>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">IP: {{ $consent->ip_address }} • {{ $consent->user_agent ? 'Browser' : 'API' }}</p>
                    </div>
                    <span class="text-[10px] text-zinc-600 font-black uppercase">{{ \Carbon\Carbon::parse($consent->created_at)->format('d/m/Y H:i') }}</span>
                    @if($consent->user)
                        <a href="{{ route('admin.lgpd.export-user', $consent->user_id) }}" class="ml-2 px-2 py-1 bg-white/5 rounded text-zinc-400 hover:text-white" title="Exportar Dados Seco">
                            <i class="fas fa-download text-xs"></i>
                        </a>
                    @endif
                </div>
                @empty
                <div class="py-20 text-center text-zinc-600 italic text-sm">Nenhum consentimento recente registrado.</div>
                @endforelse
            </div>
            <div class="p-6 bg-zinc-950/40 text-center border-t border-white/5">
                <a href="{{ route('admin.lgpd.consents') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-white transition-colors">Ver todos os registros &rarr;</a>
            </div>
        </div>

        <!-- Active Security Incidents -->
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-xl font-black text-white tracking-tight">Incidentes de Segurança</h3>
                <span class="px-2 py-1 rounded {{ $stats['incidents_open'] > 0 ? 'bg-amber-500/10 text-amber-500' : 'bg-zinc-800/50 text-zinc-500' }} text-[8px] font-black uppercase">
                    {{ $stats['incidents_open'] }} Abertos
                </span>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($stats['recent_incidents'] as $incident)
                <div class="p-6 flex flex-col gap-2 hover:bg-white/[0.02] transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase 
                                {{ $incident->severity === 'critical' ? 'bg-red-500/10 text-red-500' : 
                                  ($incident->severity === 'high' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500') }}">
                                Nível: {{ $incident->severity }}
                            </span>
                            <span class="ml-2 px-2 py-0.5 rounded text-[8px] font-black uppercase bg-white/5 text-zinc-400">
                                {{ $incident->status }}
                            </span>
                        </div>
                        <span class="text-[10px] text-zinc-600 font-black uppercase">{{ \Carbon\Carbon::parse($incident->created_at)->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm font-bold text-zinc-200 mt-2">{{ $incident->title }}</p>
                    <p class="text-xs text-zinc-500 truncate">{{ $incident->description }}</p>
                </div>
                @empty
                <div class="py-20 text-center text-zinc-600 italic text-sm">Nenhum incidente de segurança no sistema!</div>
                @endforelse
            </div>
            <div class="p-6 bg-zinc-950/40 text-center border-t border-white/5">
                <a href="{{ route('admin.lgpd.incidents') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-white transition-colors">Gerenciar incidentes &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection
