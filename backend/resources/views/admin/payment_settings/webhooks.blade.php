@extends('layouts.admin')

@section('title', 'Inspecionador de Webhooks')

@section('content')
<div class="space-y-10 animate-fade-in" x-data="{ selectedLog: null }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.settings.payments') }}" class="text-zinc-500 hover:text-white transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h2 class="text-3xl font-black text-white tracking-tight">Inspecionador de <span class="text-emerald-500">Webhooks</span></h2>
            </div>
            <p class="text-zinc-500 text-sm">Monitoramento em tempo real das notificações recebidas dos gateways de pagamento.</p>
        </div>
        
        <div class="flex items-center gap-4 bg-zinc-900/50 px-6 py-3 rounded-2xl border border-white/5 shadow-xl">
             <div class="relative w-3 h-3">
                <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-75"></div>
                <div class="relative w-3 h-3 bg-emerald-500 rounded-full"></div>
             </div>
             <span class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em]">Live Monitoring</span>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glass-card p-6 border-white/5">
            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest block mb-1">Total Notificações</span>
            <span class="text-2xl font-black text-white">{{ $logs->total() }}</span>
        </div>
        <div class="glass-card p-6 border-emerald-500/20">
            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest block mb-1">Processados (200 OK)</span>
            <span class="text-2xl font-black text-emerald-500">{{ $logs->where('status_code', 200)->count() }}</span>
        </div>
        <div class="glass-card p-6 border-rose-500/20">
            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest block mb-1">Falhas (Error)</span>
            <span class="text-2xl font-black text-rose-500">{{ $logs->where('status_code', '!=', 200)->count() }}</span>
        </div>
        <div class="glass-card p-6 border-blue-500/20">
            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest block mb-1">Tempo Médio Process.</span>
            <span class="text-2xl font-black text-blue-500">{{ number_format($logs->avg('processing_time'), 3) }}s</span>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="glass-card overflow-hidden border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Gateway</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Evento / ID Externo</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Data / Hora</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                    <tr class="group hover:bg-white/[0.01] transition-colors">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-zinc-900 flex items-center justify-center border border-white/5">
                                    @if($log->gateway === 'mercadopago') <i class="fas fa-hand-holding-usd text-blue-400 text-xs"></i>
                                    @elseif($log->gateway === 'asaas') <i class="fas fa-university text-emerald-400 text-xs"></i>
                                    @else <i class="fas fa-credit-card text-zinc-400 text-xs"></i> @endif
                                </div>
                                <span class="text-xs font-black text-white uppercase tracking-wider">{{ $log->gateway }}</span>
                            </div>
                        </td>
                        <td class="p-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">{{ $log->event_type ?? 'webhook.received' }}</span>
                                <span class="text-[9px] font-mono text-zinc-500 uppercase tracking-tighter">ID: {{ $log->external_id ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="p-6">
                            @if($log->status_code === 200)
                                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-500/20">
                                    200 OK
                                </span>
                            @else
                                <span class="px-3 py-1 bg-rose-500/10 text-rose-500 rounded-full text-[9px] font-black uppercase tracking-widest border border-rose-500/20">
                                    {{ $log->status_code ?? 'ERR' }} {{ $log->status_message ?? 'ERROR' }}
                                </span>
                            @endif
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td class="p-6 text-right">
                            <button @click="selectedLog = {{ json_encode($log) }}" class="p-3 bg-zinc-950 border border-white/5 rounded-xl text-zinc-400 hover:text-white hover:border-emerald-500/30 transition-all">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-20 text-center">
                            <div class="w-16 h-16 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-6 border border-white/5">
                                <i data-lucide="inbox" class="w-8 h-8 text-zinc-700"></i>
                            </div>
                            <h4 class="text-lg font-bold text-white">Nenhum webhook recebido</h4>
                            <p class="text-zinc-500 text-sm mt-1">Os logs aparecerão assim que os gateways começarem a enviar notificações.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="p-6 bg-white/[0.02] border-t border-white/5">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    <!-- Modal de Inspeção -->
    <div x-show="selectedLog" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-zinc-900 border border-white/10 rounded-[2.5rem] w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-3xl" @click.away="selectedLog = null">
            <!-- Modal Header -->
            <div class="p-8 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <i data-lucide="code-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">Payload <span class="text-emerald-500">Inspection</span></h3>
                        <p class="text-zinc-500 text-xs">Dados brutos recebidos da requisição HTTP.</p>
                    </div>
                </div>
                <button @click="selectedLog = null" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest ml-1">Headers da Requisição</span>
                        <div class="bg-zinc-950 p-6 rounded-3xl border border-white/5 overflow-x-auto">
                            <pre class="text-[10px] text-zinc-400 font-mono" x-text="JSON.stringify(selectedLog?.headers, null, 2)"></pre>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest ml-1">Corpo da Mensagem (Payload)</span>
                        <div class="bg-zinc-950 p-6 rounded-3xl border border-white/5 overflow-x-auto">
                            <pre class="text-[10px] text-emerald-500 font-mono" x-text="JSON.stringify(selectedLog?.payload, null, 2)"></pre>
                        </div>
                    </div>
                </div>

                <div x-show="selectedLog?.error" class="space-y-2">
                    <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest ml-1">Erro de Processamento</span>
                    <div class="bg-rose-500/5 p-6 rounded-3xl border border-rose-500/10">
                        <p class="text-xs text-rose-500 font-medium" x-text="selectedLog?.error"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 border-t border-white/5 bg-white/[0.02] flex justify-end gap-4">
                <button @click="selectedLog = null" class="px-8 py-3 bg-white/5 text-zinc-400 font-black text-[10px] uppercase tracking-widest rounded-xl hover:text-white transition-all">
                    Fechar Inspetor
                </button>
                <button class="px-8 py-3 bg-emerald-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-emerald-500 transition-all flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-3 h-3"></i> Reprocessar Agora
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.1); }
</style>
@endsection
