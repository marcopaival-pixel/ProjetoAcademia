@extends('layouts.admin')

@section('title', 'Logs de API')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Acesso à API v1</h2>
        <p class="text-zinc-500 text-sm mt-1">Requisições autenticadas e públicas (exceto health).</p>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase block mb-1">Path</label>
            <input type="text" name="path" value="{{ request('path') }}" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white" placeholder="api/v1/...">
        </div>
        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase block mb-1">Status HTTP</label>
            <input type="number" name="status" value="{{ request('status') }}" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white w-24">
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-black uppercase rounded-xl">Filtrar</button>
        <a href="{{ route('admin.observability.api-logs.export', request()->query()) }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 text-xs font-black uppercase rounded-xl border border-white/10">Export CSV</a>
    </form>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Data</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Método</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Path</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Status</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">ms</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Usuário</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/5">
                            <td class="p-4 text-sm text-white tabular-nums">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="p-4 text-xs font-black text-zinc-400">{{ $log->method }}</td>
                            <td class="p-4 text-xs font-mono text-zinc-300 max-w-xs truncate" title="{{ $log->path }}">{{ $log->path }}</td>
                            <td class="p-4 text-sm {{ $log->status_code >= 400 ? 'text-red-400' : 'text-emerald-400' }}">{{ $log->status_code }}</td>
                            <td class="p-4 text-sm text-zinc-400 tabular-nums">{{ $log->duration_ms }}</td>
                            <td class="p-4 text-sm text-zinc-400">{{ $log->user?->email ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-12 text-center text-zinc-500">Nenhum registro encontrado.</td></tr>
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
