@extends('layouts.app')

@section('title', 'Minhas Comissões')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div>
        <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">
            Minhas <span class="text-emerald-500">Comissões</span>
        </h1>
        <p class="text-zinc-500 font-medium mt-1">Histórico detalhado de todos os ganhos gerados pelas suas indicações.</p>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-900/50">
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Data</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Clínica/Usuário</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Valor Venda</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Comissão</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Gerado</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Já Pago</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Pendente</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Previsto Para</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900/30">
                    @forelse($commissions as $commission)
                    <tr class="hover:bg-zinc-900/20 transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-zinc-400">{{ $commission->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-white">{{ $commission->clinic ? $commission->clinic->name : ($commission->user ? $commission->user->name : '-') }}</span>
                                <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">{{ $commission->subscription?->plan?->name ?? 'Venda Direta' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-zinc-500">R$ {{ number_format($commission->base_amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-zinc-500">
                                {{ $commission->commission_type === 'percentual' ? number_format($commission->commission_rate, 1) . '%' : 'Fixa' }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-black text-emerald-500">R$ {{ number_format($commission->commission_amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-black text-blue-500">R$ {{ number_format($commission->paid_amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-black text-amber-500">R$ {{ number_format($commission->pending_amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-zinc-400">{{ $commission->available_at ? $commission->available_at->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest 
                                {{ $commission->status === 'PENDENTE' ? 'bg-amber-500/10 text-amber-500' : 
                                   ($commission->status === 'DISPONIVEL' ? 'bg-emerald-500/10 text-emerald-500' : 
                                   ($commission->status === 'CANCELADO' ? 'bg-rose-500/10 text-rose-500' : 'bg-blue-500/10 text-blue-500')) }}">
                                {{ $commission->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center text-zinc-600 font-medium italic">Nenhuma comissão registrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($commissions->hasPages())
        <div class="px-8 py-6 border-t border-zinc-900/50">
            {{ $commissions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
