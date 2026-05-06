@extends('layouts.app')

@section('title', 'Minhas Indicações')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div>
        <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">
            Minhas <span class="text-emerald-500">Indicações</span>
        </h1>
        <p class="text-zinc-500 font-medium mt-1">Lista completa de todos os usuários que se cadastraram através do seu link.</p>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-900/50">
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Data Cadastro</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Usuário</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Plano Atual</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em]">Total Gerado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900/30">
                    @forelse($referrals as $referral)
                    <tr class="hover:bg-zinc-900/20 transition-colors">
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold text-zinc-400">{{ $referral->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-zinc-950 rounded-xl border border-zinc-800 flex items-center justify-center text-zinc-700">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white">{{ $referral->name }}</span>
                                    <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">{{ $referral->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest {{ $referral->status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-zinc-800 text-zinc-500' }}">
                                {{ $referral->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-bold {{ $referral->is_premium ? 'text-emerald-500' : 'text-zinc-500' }}">
                                {{ $referral->plan?->name ?? 'Nenhum' }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $totalCommissions = \App\Models\Commission::where('representative_id', auth()->id())
                                    ->where('user_id', $referral->id)
                                    ->where('status', '!=', 'CANCELADO')
                                    ->sum('commission_amount');
                            @endphp
                            <span class="text-sm font-black text-white">R$ {{ number_format($totalCommissions, 2, ',', '.') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-zinc-600 font-medium italic">Você ainda não possui indicações registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($referrals->hasPages())
        <div class="px-8 py-6 border-t border-zinc-900/50">
            {{ $referrals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
