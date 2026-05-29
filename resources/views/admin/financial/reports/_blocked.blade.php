<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-white/5">
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data do Bloqueio</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Usuário / Empresa</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Plano Interrompido</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Motivo</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ação</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($data as $item)
            <tr class="hover:bg-red-500/[0.02] transition-colors group">
                <td class="p-5">
                    <div class="text-xs font-black text-white">{{ $item->updated_at->format('d/m/Y') }}</div>
                    <div class="text-[9px] text-rose-500 font-bold uppercase tracking-tighter">Status: Bloqueado</div>
                </td>
                <td class="p-5">
                    <div class="text-xs font-black text-white">{{ $item->user->name ?? 'N/D' }}</div>
                    <div class="text-[9px] text-zinc-600 font-bold uppercase">{{ $item->company->name ?? 'Independente' }}</div>
                </td>
                <td class="p-5">
                    <span class="px-2.5 py-1 rounded bg-zinc-950 border border-white/5 text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                        {{ $item->plan->name ?? 'N/D' }}
                    </span>
                </td>
                <td class="p-5 text-center">
                    <p class="text-[10px] text-zinc-500 italic max-w-xs mx-auto truncate">
                        {{ $item->reason_for_suspension ?? 'Bloqueio administrativo ou falta de pagamento prolongada' }}
                    </p>
                </td>
                <td class="p-5 text-right">
                    <a href="{{ route('admin.financial.management', ['search' => $item->user->email ?? '']) }}" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-400 transition-colors">
                        Revisar Bloqueio
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-20 text-center text-zinc-600 italic">Nenhum registro de bloqueio encontrado. Sistema operando normalmente.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
