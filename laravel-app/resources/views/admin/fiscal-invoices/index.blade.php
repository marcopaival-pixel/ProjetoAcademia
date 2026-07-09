@extends('layouts.admin')

@section('title', 'Notas Fiscais')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Financeiro</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Notas Fiscais</h1>
        </div>
        <a href="{{ route('admin.financial.fiscal-settings.edit') }}" class="px-4 py-2 rounded-xl bg-zinc-900 border border-white/10 text-xs font-black uppercase text-zinc-300">Configuração fiscal</a>
        <form method="GET" class="flex gap-2">
            <input name="search" value="{{ request('search') }}" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white" placeholder="Cliente, pagamento ou nota">
            <select name="status" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white">
                <option value="">Todos</option>
                @foreach(['pending', 'blocked', 'processing', 'issued', 'rejected', 'failed', 'cancel_pending', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ strtoupper($status) }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-black uppercase">Filtrar</button>
        </form>
    </div>

    <div class="overflow-x-auto bg-[#11141b] border border-white/5 rounded-2xl">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Pagamento</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Cliente</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Status</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Nota</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Valor</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase">Erro</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="p-4 text-xs text-zinc-300">
                            #{{ $invoice->payment?->gateway_id ?? $invoice->payment_id }}
                            <div class="text-[10px] text-zinc-600">{{ $invoice->payment?->gateway ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-xs text-zinc-300">
                            {{ $invoice->payment?->user?->name ?? 'N/D' }}
                            <div class="text-[10px] text-zinc-600">{{ $invoice->payment?->user?->email ?? '' }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded bg-white/5 text-[10px] font-black uppercase text-zinc-300">{{ $invoice->status }}</span>
                        </td>
                        <td class="p-4 text-xs text-zinc-300">
                            {{ $invoice->invoice_number ?? '-' }}
                            @if($invoice->official_url)
                                <a href="{{ $invoice->official_url }}" target="_blank" rel="noopener" class="block text-emerald-400 text-[10px] font-bold">Link oficial</a>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-zinc-300">R$ {{ number_format((float) $invoice->net_amount, 2, ',', '.') }}</td>
                        <td class="p-4 text-xs text-rose-300 max-w-xs truncate" title="{{ $invoice->last_error }}">{{ $invoice->last_error ?? '-' }}</td>
                        <td class="p-4 text-right">
                            @if(!in_array($invoice->status, ['issued', 'processing', 'cancelled'], true))
                                <form method="POST" action="{{ route('admin.financial.fiscal-invoices.retry', $invoice) }}">
                                    @csrf
                                    <button class="px-3 py-2 rounded-lg bg-amber-600/20 text-amber-300 text-[10px] font-black uppercase">Tentar novamente</button>
                                </form>
                            @elseif($invoice->status === 'issued')
                                <form method="POST" action="{{ route('admin.financial.fiscal-invoices.cancel', $invoice) }}" class="inline-flex gap-2">
                                    @csrf
                                    <input name="reason" required minlength="5" class="w-44 bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-white" placeholder="Motivo">
                                    <button class="px-3 py-2 rounded-lg bg-rose-600/20 text-rose-300 text-[10px] font-black uppercase">Cancelar</button>
                                </form>
                            @else
                                <span class="text-[10px] text-zinc-600">Sem ação</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-sm text-zinc-500">Nenhuma nota fiscal registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $invoices->links() }}
</div>
@endsection
