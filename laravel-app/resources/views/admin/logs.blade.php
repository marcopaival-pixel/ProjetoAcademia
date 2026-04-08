@extends('layouts.admin')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $logs */
@endphp

@section('title', 'Logs de Segurança & Auditoria')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Filters / Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Atividades Administrativas</h2>
            <p class="text-zinc-500 text-sm mt-1">Rastreio em tempo real de ações críticas no sistema.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase rounded-xl border border-blue-500/20 tracking-widest">
                <i class="fas fa-shield-alt me-2"></i>Monitoramento Ativo
            </span>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="table-wrap overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Timestamp</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Operador</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Ação</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">IP Origem</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Payload / Dados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="p-6">
                                <span class="text-sm font-bold text-white tabular-nums">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="block text-[10px] text-zinc-500 font-medium tabular-nums mt-1">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-xs font-black text-zinc-400">
                                        {{ substr($log->user?->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-zinc-300">{{ $log->user?->name ?? 'SISTEMA' }}</span>
                                </div>
                            </td>
                            <td class="p-6">
                                <span class="px-3 py-1 bg-zinc-800 text-zinc-400 text-[10px] font-black uppercase rounded-lg border border-white/5 group-hover:border-blue-500/30 transition-all">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-6">
                                <code class="text-[10px] text-zinc-500 font-mono bg-zinc-950 px-2 py-1 rounded">{{ $log->ip_address }}</code>
                            </td>
                            <td class="p-6">
                                @if($log->payload)
                                    <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 flex items-center gap-2">
                                        <i class="fas fa-code"></i> Ver JSON
                                    </button>
                                    <div class="hidden mt-4">
                                        <pre class="text-[10px] bg-zinc-950 p-4 rounded-2xl border border-white/5 text-zinc-400 overflow-auto max-w-[400px] shadow-inner font-mono leading-relaxed">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                @else
                                    <span class="text-zinc-700 font-black text-[10px]">---</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-20 text-center">
                                <div class="w-16 h-16 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-700">
                                    <i class="fas fa-history text-2xl"></i>
                                </div>
                                <h3 class="text-white font-black">Nenhum registro encontrado</h3>
                                <p class="text-zinc-600 text-sm mt-1">O sistema ainda não capturou nenhuma movimentação administrativa.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="p-6 border-t border-white/5 bg-zinc-900/40">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Pagination Overrides */
    .pagination { @apply flex gap-2; }
    .page-link { @apply bg-zinc-900 border-white/5 text-zinc-500 rounded-xl px-4 py-2 hover:bg-zinc-800 transition-all; }
    .page-item.active .page-link { @apply bg-blue-600 text-white border-blue-500; }
</style>
@endsection
