<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-white/5">
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Usuário</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Transação ID</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Método</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Valor</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($data as $item)
            <tr class="hover:bg-white/[0.01] transition-colors">
                <td class="p-5 text-xs text-zinc-400">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td class="p-5">
                    <div class="text-xs font-black text-white">{{ $item->user->name ?? 'N/D' }}</div>
                    <div class="text-[9px] text-zinc-600 font-bold uppercase">{{ $item->user->email ?? '' }}</div>
                </td>
                <td class="p-5 text-[10px] font-mono text-zinc-500">{{ $item->transaction_id }}</td>
                <td class="p-5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $item->payment_method_id }}</td>
                <td class="p-5 text-right">
                    <span class="text-sm font-black text-emerald-500">R$ {{ number_format($item->transaction_amount, 2, ',', '.') }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-20 text-center text-zinc-600 italic">Nenhum registro de receita encontrado para o período.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr class="bg-zinc-950/50">
                <td colspan="4" class="p-5 text-right text-[10px] font-black text-zinc-500 uppercase tracking-widest">Total Geral:</td>
                <td class="p-5 text-right text-lg font-black text-white">R$ {{ number_format($data->sum('transaction_amount'), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
