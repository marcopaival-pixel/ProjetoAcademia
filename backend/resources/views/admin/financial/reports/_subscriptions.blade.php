<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b border-white/5">
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Início / Renovação</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Assinante / Empresa</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Plano</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Status</th>
                <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($data as $item)
            <tr class="hover:bg-white/[0.01] transition-colors">
                <td class="p-5">
                    <div class="text-xs font-black text-white">{{ $item->created_at->format('d/m/Y') }}</div>
                    <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-tighter">Exp: {{ $item->expires_at ? $item->expires_at->format('d/m/Y') : 'N/D' }}</div>
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
                    @php
                        $statusColors = [
                            'active' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                            'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                            'overdue' => 'bg-red-500/10 text-red-500 border-red-500/20',
                            'suspended' => 'bg-zinc-500/10 text-zinc-500 border-zinc-500/20',
                            'canceled' => 'bg-zinc-800 text-zinc-600 border-white/5',
                        ];
                        $colorClass = $statusColors[$item->status] ?? 'bg-zinc-950 text-zinc-600 border-white/5';
                    @endphp
                    <span class="px-3 py-1 rounded-full border text-[9px] font-black uppercase tracking-[0.2em] {{ $colorClass }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="p-5 text-right">
                    <a href="{{ route('admin.financial.management', ['search' => $item->user->email ?? '']) }}" class="text-[10px] font-black text-purple-400 uppercase tracking-widest hover:text-purple-300 transition-colors">
                        Gerenciar
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-20 text-center text-zinc-600 italic">Nenhuma assinatura encontrada para os critérios selecionados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
