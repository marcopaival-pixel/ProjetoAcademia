@extends('layouts.admin')

@section('title', 'Métricas de APIs e Integrações Externas')

@section('content')
<div class="space-y-8 animate-fade-in text-white">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Métricas de Integrações</h2>
        <p class="text-zinc-500 text-sm mt-1">Status, tempo de resposta e taxa de disponibilidade para todas as APIs de terceiros conectadas.</p>
    </div>

    <!-- Integrations Table Card -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-xl">
        <div class="p-6 border-b border-white/5 bg-white/5">
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400">APIs Monitoradas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">API / Integração</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Total Chamadas</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Erros</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Timeouts</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Latência Média</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Disponibilidade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($integrations as $api)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold">
                                        <i class="fas fa-plug"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-black text-white block">{{ $api->api_name }}</span>
                                        <span class="text-[9px] text-zinc-500 font-mono block mt-0.5">Disponibilidade real</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-6 text-sm text-zinc-300 font-bold tabular-nums">{{ number_format($api->total) }}</td>
                            <td class="p-6">
                                <span class="px-2 py-0.5 bg-red-500/10 text-red-400 text-[10px] font-black uppercase rounded border border-red-500/20 tabular-nums">
                                    {{ $api->errors }}
                                </span>
                            </td>
                            <td class="p-6">
                                <span class="px-2 py-0.5 bg-amber-500/10 text-amber-400 text-[10px] font-black uppercase rounded border border-amber-500/20 tabular-nums">
                                    {{ $api->timeouts }}
                                </span>
                            </td>
                            <td class="p-6 text-sm text-zinc-300 font-bold tabular-nums">
                                {{ $api->avg_time }} <span class="text-[10px] text-zinc-500">ms</span>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-black @if($api->availability < 95) text-red-400 @elseif($api->availability < 99) text-amber-400 @else text-emerald-400 @endif tabular-nums">
                                        {{ $api->availability }}%
                                    </span>
                                    <div class="w-20 bg-zinc-950 h-2 rounded-full overflow-hidden border border-white/5">
                                        <div class="h-full @if($api->availability < 95) bg-red-500 @elseif($api->availability < 99) bg-amber-500 @else bg-emerald-500 @endif" style="width: {{ $api->availability }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-zinc-500">Nenhum registro de API externa coletado no banco ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
