@extends('layouts.admin')

@section('title', 'Pontos de Fidelidade')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Pontos do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Consulte saldos e credite pontos manualmente</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.shop.orders.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-400"></i> Pedidos
            </a>
            <a href="{{ route('admin.shop.products.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Produtos
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400"></i>
        <p class="text-emerald-400 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-400"></i>
        <p class="text-red-400 text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <form action="{{ route('admin.shop.points.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por e-mail, nome ou ID do utilizador..."
                    class="w-full bg-zinc-950 border border-white/5 p-4 pl-14 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
            </div>
            <button type="submit" class="py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all">
                Buscar Utilizador
            </button>
        </form>
    </div>

    @if($user && $wallet)
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-user text-emerald-400"></i> Utilizador
                </h3>
                <div class="space-y-2 text-sm">
                    <p class="text-white font-bold">{{ $user->name }}</p>
                    <p class="text-zinc-400">{{ $user->email }}</p>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">ID #{{ $user->id }}</p>
                </div>
                <div class="pt-4 border-t border-white/5">
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Saldo atual</p>
                    <p class="text-3xl font-black text-emerald-400 mt-1">{{ number_format($wallet->balance_points, 0, ',', '.') }} <span class="text-sm text-zinc-500">pts</span></p>
                </div>
            </div>

            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
                <h3 class="text-sm font-black text-white uppercase tracking-wider">Creditar Pontos</h3>
                <form action="{{ route('admin.shop.points.credit') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Quantidade</label>
                        <input type="number" name="points" min="1" max="1000000" required placeholder="Ex: 500"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Motivo</label>
                        <input type="text" name="reason" required maxlength="255" placeholder="Ex: Compensação por atraso na entrega"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all">
                        Creditar Pontos
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-7">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-history text-emerald-400"></i> Últimas Movimentações
                </h3>
                @if($recentTransactions->isEmpty())
                    <p class="text-zinc-500 text-sm">Nenhuma transação registada.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentTransactions as $tx)
                        <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                            <div>
                                <p class="text-white text-sm font-bold">{{ $tx->description }}</p>
                                <p class="text-[10px] text-zinc-500 mt-1">{{ $tx->created_at->format('d/m/Y H:i') }} · {{ $tx->type }}</p>
                            </div>
                            <span class="font-black text-sm {{ $tx->points >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $tx->points >= 0 ? '+' : '' }}{{ number_format($tx->points, 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @elseif(request()->filled('search'))
    <div class="p-6 bg-amber-500/10 border border-amber-500/20 rounded-2xl text-amber-300 text-sm font-bold">
        Nenhum utilizador encontrado para esta busca.
    </div>
    @endif

    @if($topWallets->isNotEmpty())
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
        <h3 class="text-sm font-black text-white uppercase tracking-wider">Top 10 Saldos</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[10px] text-zinc-500 font-black uppercase tracking-wider text-left">
                        <th class="pb-3">Utilizador</th>
                        <th class="pb-3">E-mail</th>
                        <th class="pb-3 text-right">Saldo</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($topWallets as $w)
                    <tr>
                        <td class="py-3 text-white font-bold">{{ $w->user?->name ?? '—' }}</td>
                        <td class="py-3 text-zinc-400">{{ $w->user?->email ?? '—' }}</td>
                        <td class="py-3 text-right font-black text-emerald-400">{{ number_format($w->balance_points, 0, ',', '.') }} pts</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.shop.points.index', ['user_id' => $w->user_id]) }}" class="text-xs font-black uppercase tracking-wider text-emerald-400 hover:text-emerald-300">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
