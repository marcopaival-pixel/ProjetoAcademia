@extends('layouts.admin')

@section('title', 'Logs de geração de PDF')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Logs de <span class="text-zinc-400">PDF</span></h1>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Pré-visualizações e descargas registadas</p>
        </div>
        <a href="{{ route('admin.pdf-templates.index') }}" class="text-[10px] font-black uppercase text-amber-500 hover:text-amber-400">← Modelos</a>
    </div>

    <div class="bg-zinc-900/40 border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.15em] border-b border-white/5 bg-white/[0.02]">
                        <th class="px-6 py-4">Data</th>
                        <th class="px-6 py-4">Utilizador</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Modelo</th>
                        <th class="px-6 py-4">Ação</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Ficheiro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 text-[10px] text-zinc-500 font-mono whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-xs text-zinc-300">{{ $log->user?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-[10px] text-zinc-400 uppercase">{{ str_replace('_', ' ', $log->document_type) }}</td>
                            <td class="px-6 py-4 text-xs text-zinc-400">{{ $log->template_name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($log->action === \App\Models\PdfGenerationLog::ACTION_PREVIEW)
                                    <span class="text-[9px] font-black uppercase text-blue-400 bg-blue-500/10 px-2 py-1 rounded-lg">Pré-visualizar</span>
                                @else
                                    <span class="text-[9px] font-black uppercase text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-lg">Descarga</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->status === 'success')
                                    <span class="text-[9px] font-black uppercase text-emerald-400">OK</span>
                                @else
                                    <span class="text-[9px] font-black uppercase text-red-400" title="{{ $log->error_message }}">Falhou</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[10px] text-zinc-500 max-w-[180px] truncate" title="{{ $log->filename }}">{{ $log->filename }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-zinc-500">Sem registos ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-white/5">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
