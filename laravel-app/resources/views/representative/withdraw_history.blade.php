@extends('layouts.app')

@section('title', 'Histórico de Resgates')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div class="flex items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">
                Histórico de <span class="text-emerald-500">Resgates</span>
            </h1>
            <p class="text-zinc-500 font-medium mt-1">Acompanhe o status de suas solicitações de saque.</p>
        </div>
        <a href="{{ route('representative.withdraw.form') }}" class="px-8 py-4 bg-zinc-900 border border-zinc-800 text-white font-black rounded-2xl hover:bg-emerald-500 hover:text-zinc-950 transition-all text-[10px] uppercase tracking-widest">
            Novo Resgate
        </a>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-900/50">
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Data</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Valor</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Chave PIX</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Pagamento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900/30">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-zinc-900/20 transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-zinc-400">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-black text-white">R$ {{ number_format($withdrawal->amount, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <code class="text-[10px] font-bold text-zinc-500 bg-zinc-950 px-3 py-1.5 rounded-lg border border-zinc-900">{{ $withdrawal->pix_key }}</code>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest 
                                {{ $withdrawal->status === 'PENDENTE' ? 'bg-amber-500/10 text-amber-500' : 
                                   ($withdrawal->status === 'PAGO' ? 'bg-emerald-500/10 text-emerald-500' : 
                                   ($withdrawal->status === 'RECUSADO' ? 'bg-rose-500/10 text-rose-500' : 'bg-blue-500/10 text-blue-500')) }}">
                                {{ $withdrawal->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            @if($withdrawal->paid_at)
                                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                                    {{ $withdrawal->paid_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-[10px] font-black text-zinc-700 uppercase tracking-widest italic">Aguardando</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-zinc-600 font-medium italic">Você ainda não solicitou nenhum resgate.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="px-8 py-6 border-t border-zinc-900/50">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
