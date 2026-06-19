@extends('layouts.admin')

@section('title', 'Erros JavaScript')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Erros do Cliente (JavaScript)</h2>
        <p class="text-zinc-500 text-sm mt-1">Capturados via <code class="text-zinc-400">resources/js/logger.js</code>.</p>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase block mb-1">URL</label>
            <input type="text" name="url" value="{{ request('url') }}" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white">
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-black uppercase rounded-xl">Filtrar</button>
        <a href="{{ route('admin.observability.client-errors.export', request()->query()) }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 text-xs font-black uppercase rounded-xl border border-white/10">Export CSV</a>
    </form>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Data</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Tipo</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Mensagem</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">URL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/5">
                            <td class="p-4 text-sm text-white tabular-nums">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="p-4 text-xs font-mono text-amber-400">{{ $log->type }}</td>
                            <td class="p-4 text-sm text-zinc-200 max-w-md truncate" title="{{ $log->message }}">{{ $log->message }}</td>
                            <td class="p-4 text-xs font-mono text-zinc-500 max-w-xs truncate" title="{{ $log->url }}">{{ $log->url }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-12 text-center text-zinc-500">Nenhum erro capturado ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-4 border-t border-white/5">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
