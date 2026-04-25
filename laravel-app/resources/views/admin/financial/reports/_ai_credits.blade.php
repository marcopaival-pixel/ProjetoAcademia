<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-white/5">
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Usuário</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Ação / Funcionalidade</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data / Hora</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Créditos</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($data as $item)
            <tr class="hover:bg-white/[0.01] transition-colors">
                <td class="p-5">
                    <div class="text-xs font-black text-white">{{ $item->user->name ?? 'N/D' }}</div>
                    <div class="text-[9px] text-zinc-600 font-bold uppercase">{{ $item->user->email ?? '' }}</div>
                </td>
                <td class="p-5 text-xs text-zinc-400">
                    {{ $item->action_type ?? 'Uso Geral' }}
                    <span class="block text-[9px] text-zinc-600">{{ $item->metadata['model'] ?? '' }}</span>
                </td>
                <td class="p-5 text-xs text-zinc-500">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td class="p-5 text-right font-black text-purple-400">
                    - {{ $item->credits_consumed }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-20 text-center text-zinc-600 italic">Nenhum uso de créditos registrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
