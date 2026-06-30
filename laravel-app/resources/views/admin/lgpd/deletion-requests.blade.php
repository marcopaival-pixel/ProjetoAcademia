@extends('layouts.admin')

@section('title', 'Pedidos de Exclusão (LGPD)')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-white">Pedidos de Exclusão de Conta</h2>
            <p class="text-sm text-zinc-400 mt-1">Titulares que solicitaram anonimização. Processamento manual ou automático após 15 dias.</p>
        </div>
        <a href="{{ route('admin.lgpd.index') }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 border border-white/5 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-zinc-700 transition-all">
            &larr; Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.lgpd.deletion-requests.batch') }}" method="POST" class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        @csrf
        <div class="p-8 border-b border-white/5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-white">Fila pendente</h3>
                <p class="text-xs text-zinc-500 mt-1">{{ $pendingUsers->count() }} pedido(s) aguardando anonimização</p>
            </div>
            @if($pendingUsers->isNotEmpty())
            <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all"
                    onclick="return confirm('Confirmar anonimização dos utilizadores selecionados? Esta ação é irreversível.')">
                Processar selecionados
            </button>
            @endif
        </div>

        <div class="p-8 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">
                        <th class="pb-4 pt-2 w-10">
                            @if($pendingUsers->isNotEmpty())
                            <input type="checkbox" id="select-all-lgpd" class="rounded border-zinc-600 bg-zinc-950">
                            @endif
                        </th>
                        <th class="pb-4 pt-2 font-medium">Utilizador</th>
                        <th class="pb-4 pt-2 font-medium">Pedido em</th>
                        <th class="pb-4 pt-2 font-medium">Organização</th>
                        <th class="pb-4 pt-2 font-medium text-right">Acções</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-white/5">
                    @forelse($pendingUsers as $user)
                    @php
                        $requestConsent = $user->consents->first();
                    @endphp
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-4">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="lgpd-user-checkbox rounded border-zinc-600 bg-zinc-950">
                        </td>
                        <td class="py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-200">{{ $user->name }}</span>
                                <span class="text-[10px] text-zinc-500">#{{ $user->id }} · {{ $user->email }}</span>
                            </div>
                        </td>
                        <td class="py-4 text-zinc-400 font-mono text-xs">
                            {{ $requestConsent ? \Carbon\Carbon::parse($requestConsent->created_at)->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="py-4 text-zinc-500 text-xs">
                            {{ $user->academy_company_id ? 'Empresa #'.$user->academy_company_id : '—' }}
                        </td>
                        <td class="py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.lgpd.export-user', $user) }}" class="px-3 py-1.5 bg-white/5 rounded-lg text-[10px] font-bold uppercase text-zinc-400 hover:text-white" title="Exportar antes de anonimizar">
                                    Exportar
                                </a>
                                <form action="{{ route('admin.lgpd.deletion-requests.process', $user) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Anonimizar utilizador #{{ $user->id }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600/20 border border-amber-500/30 rounded-lg text-[10px] font-bold uppercase text-amber-400 hover:bg-amber-600 hover:text-white transition-all">
                                        Anonimizar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-zinc-600 italic">Nenhum pedido de exclusão pendente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

@if($pendingUsers->isNotEmpty())
<script>
document.getElementById('select-all-lgpd')?.addEventListener('change', function () {
    document.querySelectorAll('.lgpd-user-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endif
@endsection
