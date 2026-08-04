@extends('layouts.admin')

@section('title', 'Bug Surgeon | Incidentes')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Bug <span class="text-amber-500">Surgeon</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Diagnóstico, aprovação com escopo fechado e rastreio de correções.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-zinc-900/50 px-5 py-2.5 rounded-2xl border border-white/5 flex items-center gap-3">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Abertos</span>
                <span class="text-sm font-black text-white tabular-nums">{{ $stats['open'] }}</span>
            </div>
            <div class="bg-amber-500/10 px-5 py-2.5 rounded-2xl border border-amber-500/20 flex items-center gap-3">
                <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Aguardando aprovação</span>
                <span class="text-sm font-black text-amber-400 tabular-nums">{{ $stats['waiting'] }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm font-bold">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-2xl text-blue-400 text-sm font-bold">{{ session('info') }}</div>
    @endif

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="table-wrap overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Incidente</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Estado</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Resumo</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Confiança</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Risco</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($incidents as $incident)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-6">
                                <span class="text-sm font-black text-white font-mono">{{ $incident->incident_code }}</span>
                                <span class="block text-[10px] text-zinc-600 mt-1">{{ $incident->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="p-6">
                                @php
                                    $stateColors = [
                                        'WAITING_APPROVAL' => 'amber',
                                        'APPROVED' => 'emerald',
                                        'CLOSED' => 'zinc',
                                        'ROLLED_BACK' => 'red',
                                    ];
                                    $c = $stateColors[$incident->state] ?? 'blue';
                                @endphp
                                <span class="px-3 py-1 bg-{{ $c }}-500/10 text-{{ $c }}-400 text-[9px] font-black uppercase rounded-lg border border-{{ $c }}-500/20">
                                    {{ str_replace('_', ' ', $incident->state) }}
                                </span>
                            </td>
                            <td class="p-6 max-w-xs">
                                <p class="text-sm text-zinc-300 line-clamp-2">{{ $incident->error_summary ?? '—' }}</p>
                                @if($incident->file_path)
                                    <code class="text-[10px] text-zinc-600 font-mono">{{ $incident->file_path }}@if($incident->line_start):{{ $incident->line_start }}@endif</code>
                                @endif
                            </td>
                            <td class="p-6">
                                @if($incident->confidence !== null)
                                    <span class="text-sm font-black text-white">{{ $incident->confidence }}%</span>
                                @else
                                    <span class="text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="p-6">
                                <span class="text-[10px] font-black uppercase text-zinc-500">{{ $incident->risk_level ?? '—' }}</span>
                            </td>
                            <td class="p-6">
                                <a href="{{ route('admin.bug-surgeon.show', $incident) }}" class="px-4 py-2 bg-zinc-950 text-white text-[10px] font-black uppercase rounded-xl border border-white/10 hover:border-amber-500/50 transition-all">
                                    Abrir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-zinc-600">
                                Nenhum incidente registrado. Abra um a partir de <a href="{{ route('admin.system-errors') }}" class="text-amber-500 hover:text-white">Logs de Erro</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incidents->hasPages())
            <div class="p-6 border-t border-white/5">{{ $incidents->links() }}</div>
        @endif
    </div>
</div>
@endsection
