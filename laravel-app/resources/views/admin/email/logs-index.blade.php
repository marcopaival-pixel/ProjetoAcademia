@extends('layouts.admin')

@section('title', 'Logs de envio de e-mail')

@section('content')
<div class="space-y-10 animate-fade-in max-w-7xl mx-auto">
    <div>
        <h2 class="text-3xl font-black text-white tracking-tight">Logs de envio</h2>
        <p class="text-zinc-500 text-sm mt-1">Auditoria de e-mails transacionais (serviço central).</p>
    </div>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2rem] overflow-x-auto">
        <table class="w-full text-left text-xs min-w-[800px]">
            <thead class="bg-zinc-950/80 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                <tr>
                    <th class="px-6 py-4">Data</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4">Destino</th>
                    <th class="px-6 py-4">Assunto</th>
                    <th class="px-6 py-4">Empresa</th>
                    <th class="px-6 py-4">Erro</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($logs as $log)
                    <tr class="hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-zinc-400 whitespace-nowrap">{{ $log->data_envio?->format('d/m/Y H:i:s') }}</td>
                        <td class="px-6 py-4">
                            @if($log->status === \App\Models\LogEnvioEmail::STATUS_ENVIADO)
                                <span class="text-emerald-500 font-bold">enviado</span>
                            @else
                                <span class="text-red-400 font-bold">falha</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-zinc-300 max-w-[200px] truncate">{{ $log->email_destino }}</td>
                        <td class="px-6 py-4 text-zinc-400 max-w-[220px] truncate">{{ $log->assunto }}</td>
                        <td class="px-6 py-4 text-zinc-500">{{ $log->empresa?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-red-400/80 max-w-xs truncate" title="{{ $log->erro }}">{{ $log->erro }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-zinc-500">Sem registos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection
