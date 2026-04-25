<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-white/5">
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Usuário / Empresa</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Plano</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status Atual</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Dias em Atraso</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Dívida Estimada</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($data as $item)
            <tr class="hover:bg-white/[0.01] transition-colors">
                <td class="p-5">
                    <div class="text-xs font-black text-white">{{ $item->user->name ?? 'N/D' }}</div>
                    <div class="text-[9px] text-zinc-600 font-bold uppercase">{{ $item->company->name ?? 'Independente' }}</div>
                </td>
                <td class="p-5 text-xs text-zinc-400">{{ $item->plan->name ?? 'Plano' }}</td>
                <td class="p-5">
                    <span class="px-2 py-0.5 rounded bg-rose-500/10 text-rose-500 text-[8px] font-black uppercase">
                        {{ $item->getFinancialStatus() }}
                    </span>
                </td>
                <td class="p-5 text-center text-xs font-black text-rose-400">
                    {{ $item->days_overdue }} dias
                </td>
                <td class="p-5 text-right">
                    <span class="text-sm font-black text-white">R$ {{ number_format($item->plan->price ?? 0, 2, ',', '.') }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-20 text-center text-zinc-600 italic">Nenhuma inadimplência encontrada. Operação saudável!</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
