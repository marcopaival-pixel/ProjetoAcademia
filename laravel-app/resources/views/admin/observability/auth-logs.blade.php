@extends('layouts.admin')

@section('title', 'Auditoria de Autenticação')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Auditoria de Autenticação</h2>
        <p class="text-zinc-500 text-sm mt-1">Login, logout e tentativas falhas.</p>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase block mb-1">Evento</label>
            <select name="event" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white">
                <option value="">Todos</option>
                @foreach(['login_success','login_failed','logout','password_reset'] as $ev)
                    <option value="{{ $ev }}" @selected(request('event') === $ev)>{{ $ev }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase block mb-1">E-mail</label>
            <input type="text" name="email" value="{{ request('email') }}" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white">
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-black uppercase rounded-xl">Filtrar</button>
        <a href="{{ route('admin.observability.auth-logs.export', request()->query()) }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 text-xs font-black uppercase rounded-xl border border-white/10">Export CSV</a>
    </form>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Data</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Evento</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">E-mail</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Sucesso</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/5">
                            <td class="p-4 text-sm text-white tabular-nums">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="p-4 text-xs font-mono text-blue-400">{{ $log->event }}</td>
                            <td class="p-4 text-sm text-zinc-300">{{ $log->email ?? $log->user?->email ?? '—' }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase {{ $log->success ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                    {{ $log->success ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="p-4 text-xs font-mono text-zinc-500">{{ $log->ip }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-12 text-center text-zinc-500">Nenhum registro encontrado.</td></tr>
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
