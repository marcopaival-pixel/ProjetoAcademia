@extends('layouts.admin')

@section('title', 'Gestão Financeira')

@section('content')
<div class="animate-fade-in space-y-6">
    
    <!-- Header -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-blue-600/10 border border-blue-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    <span class="text-blue-400 text-[9px] font-black uppercase tracking-widest">Painel de Controle de Faturamento</span>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter">
                Gestão <span class="text-blue-500">Operacional</span>
            </h1>
        </div>

        <!-- Filtros Rápidos -->
        <div class="flex gap-4">
            <form action="" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Buscar usuário ou empresa..." value="{{ request('search') }}" class="bg-[#11141b]/80 border border-white/5 rounded-xl px-4 py-2 text-xs text-white focus:border-blue-500 outline-none w-64">
                <select name="status" class="bg-[#11141b]/80 border border-white/5 rounded-xl px-4 py-2 text-xs text-white focus:border-blue-500 outline-none">
                    <option value="">Todos Status</option>
                    <option value="ATIVO" {{ request('status') == 'ATIVO' ? 'selected' : '' }}>Ativos</option>
                    <option value="ATRASADO" {{ request('status') == 'ATRASADO' ? 'selected' : '' }}>Atrasados</option>
                    <option value="SUSPENSO" {{ request('status') == 'SUSPENSO' ? 'selected' : '' }}>Suspensos</option>
                    <option value="BLOQUEADO" {{ request('status') == 'BLOQUEADO' ? 'selected' : '' }}>Bloqueados</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-500 transition-all">Filtrar</button>
            </form>
        </div>
    </div>

    <!-- Tabela de Gestão -->
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Usuário / Empresa</th>
                        <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Plano / Valor</th>
                        <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status</th>
                        <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Dias Atraso</th>
                        <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Próx. Cobrança</th>
                        <th class="p-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Ações Financeiras</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($subscriptions as $sub)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($sub->user->name ?? 'User') }}&background=18181b&color=3b82f6" class="w-9 h-9 rounded-full border border-white/10">
                                <div>
                                    <div class="text-xs font-black text-white">{{ $sub->user->name ?? 'N/D' }}</div>
                                    <div class="text-[9px] text-zinc-500 font-bold uppercase">{{ $sub->company->name ?? 'Assinatura Direta' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-5">
                            <div class="text-xs font-black text-white">{{ $sub->plan->name ?? 'Plano Antigo' }}</div>
                            <div class="text-[10px] text-emerald-500 font-bold uppercase">R$ {{ number_format($sub->plan->price ?? 0, 2, ',', '.') }}</div>
                        </td>
                        <td class="p-5">
                            @php
                                $statusClass = match($sub->getFinancialStatus()) {
                                    'ATIVO' => 'bg-emerald-500/10 text-emerald-500',
                                    'ATRASADO' => 'bg-amber-500/10 text-amber-500',
                                    'SUSPENSO' => 'bg-rose-500/10 text-rose-500',
                                    'BLOQUEADO' => 'bg-zinc-800 text-zinc-400',
                                    default => 'bg-blue-500/10 text-blue-500'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase {{ $statusClass }}">
                                {{ $sub->getFinancialStatus() }}
                            </span>
                        </td>
                        <td class="p-5 text-center">
                            <span class="text-xs font-black {{ $sub->days_overdue > 0 ? 'text-rose-500' : 'text-zinc-600' }}">
                                {{ $sub->days_overdue }} dias
                            </span>
                        </td>
                        <td class="p-5">
                            <div class="text-[10px] font-black text-zinc-400">
                                {{ $sub->next_billing_date ? $sub->next_billing_date->format('d/m/Y') : 'N/D' }}
                            </div>
                        </td>
                        <td class="p-5">
                            <div class="flex justify-center gap-2">
                                @if($sub->getFinancialStatus() !== 'ATIVO')
                                <form action="{{ route('admin.financial.actions', [$sub->id, 'release']) }}" method="POST" 
                                      data-confirm-delete="true"
                                      data-confirm-title="Confirmar Liberação"
                                      data-confirm-message="Deseja liberar o acesso deste usuário manualmente?">
                                    @csrf
                                    <button title="Liberar Acesso" class="w-8 h-8 rounded-lg bg-emerald-600/10 text-emerald-500 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center">
                                        <i class="fas fa-unlock text-[10px]"></i>
                                    </button>
                                </form>
                                @endif

                                @if($sub->getFinancialStatus() === 'ATIVO' || $sub->getFinancialStatus() === 'ATRASADO')
                                <form action="{{ route('admin.financial.actions', [$sub->id, 'suspend']) }}" method="POST"
                                      data-confirm-delete="true"
                                      data-confirm-title="Suspender Assinatura"
                                      data-confirm-message="O usuário perderá o acesso premium até que a situação seja regularizada.">
                                    @csrf
                                    <button title="Suspender" class="w-8 h-8 rounded-lg bg-rose-600/10 text-rose-500 hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center">
                                        <i class="fas fa-pause text-[10px]"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('admin.financial.actions', [$sub->id, 'reprocess']) }}" method="POST">
                                    @csrf
                                    <button title="Reprocessar Cobrança" class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-500 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center">
                                        <i class="fas fa-sync text-[10px]"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.financial.actions', [$sub->id, 'block']) }}" method="POST"
                                      data-confirm-delete="true"
                                      data-confirm-title="BLOQUEIO DEFINITIVO"
                                      data-confirm-message="Esta ação é crítica. O usuário perderá acesso total ao sistema imediatamente.">
                                    @csrf
                                    <button title="Bloquear" class="w-8 h-8 rounded-lg bg-zinc-950 text-zinc-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center border border-white/5">
                                        <i class="fas fa-ban text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-20 text-center text-zinc-600 italic text-sm">
                            Nenhuma assinatura encontrada com os filtros aplicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
        <div class="p-5 border-t border-white/5">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
