@extends('layouts.admin')

@section('title', 'Histórico de Comunicações')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-fade-in pb-20">
    
    <!-- Header Context -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight italic uppercase">Logs de <span class="text-emerald-500">Transmissão</span></h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Auditoria centralizada de e-mails transacionais e notificações</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-zinc-950 border border-white/5 rounded-xl flex items-center gap-3">
                <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Total Retidos:</span>
                <span class="text-xs font-black text-white italic">{{ $logs->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="glass-card overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-950/80 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Timestamp</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Destinatário</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Assunto / Tipo</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Unidade</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 text-right">Diagnóstico</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-zinc-300 uppercase tracking-tight">{{ $log->data_envio?->format('d M, Y') }}</span>
                                    <span class="text-[10px] font-bold text-zinc-600 mt-0.5 tracking-widest">{{ $log->data_envio?->format('H:i:s') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($log->status === \App\Models\LogEnvioEmail::STATUS_ENVIADO)
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                        <span class="text-[9px] font-black uppercase text-emerald-500 tracking-widest">Enviado</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-500/10 border border-rose-500/20 rounded-lg">
                                        <i data-lucide="x" class="w-3 h-3 text-rose-500"></i>
                                        <span class="text-[9px] font-black uppercase text-rose-500 tracking-widest">Falha</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-black text-white tracking-tight lowercase truncate block max-w-[180px]">{{ $log->email_destino }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col max-w-[220px]">
                                    <span class="text-xs font-black text-zinc-400 uppercase tracking-tight truncate">{{ $log->assunto }}</span>
                                    <span class="text-[9px] text-zinc-600 font-bold tracking-widest uppercase mt-0.5">{{ $log->tipo_envio ?? 'Transactional' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $log->empresa?->name ?? 'Sistema Global' }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                @if($log->erro)
                                    <button type="button" class="text-rose-500/60 hover:text-rose-500 transition-colors" title="{{ $log->erro }}" onclick="alert('Detalhe do Erro:\n\n{{ addslashes($log->erro) }}')">
                                        <i data-lucide="message-square-warning" class="w-4 h-4"></i>
                                    </button>
                                @else
                                    <span class="text-zinc-800"><i data-lucide="minus" class="w-4 h-4"></i></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center gap-4 opacity-20">
                                    <i data-lucide="mail-search" class="w-12 h-12"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em]">Nenhum log de transmissão encontrado</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center pt-8">
        {{ $logs->links() }}
    </div>

    <!-- Summary Context -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-zinc-950/50 border border-white/5 rounded-3xl flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 shrink-0">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Retenção de Dados</h4>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                    Os logs são mantidos para fins de auditoria e debugging. Em ambientes de produção, recomenda-se a limpeza periódica de registos com mais de 90 dias.
                </p>
            </div>
        </div>
        <div class="p-6 bg-zinc-950/50 border border-white/5 rounded-3xl flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shrink-0">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Health Status</h4>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed">
                    Taxa de entrega global: <span class="text-emerald-500">99.8%</span>. Monitore falhas recorrentes para identificar problemas com provedores SMTP específicos.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
