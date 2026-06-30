@extends('layouts.app')

@section('title', 'Meus Pontos — Shopping')

@section('content')
<div class="max-w-[900px] mx-auto px-4 py-8">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('shopping.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Meus Pontos</h1>
            <p class="text-xs text-zinc-500">Programa de fidelidade do shopping</p>
        </div>
    </div>

    @if($error)
    <div class="p-6 bg-amber-500/10 border border-amber-500/20 rounded-2xl text-amber-300 text-sm font-bold">
        {{ $error }}
    </div>
    @elseif($wallet)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Saldo disponível</p>
            <p class="text-4xl font-black text-emerald-400 mt-2">{{ number_format($wallet->balance_points, 0, ',', '.') }}</p>
            <p class="text-xs text-zinc-500 mt-1">pontos</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 flex flex-col justify-center">
            <p class="text-sm text-zinc-400">Use seus pontos no checkout para pagar pedidos.</p>
            <a href="{{ route('shopping.index') }}" class="mt-4 inline-flex items-center gap-2 text-emerald-400 font-black text-xs uppercase tracking-widest hover:text-emerald-300">
                <i class="fas fa-store"></i> Ir à loja
            </a>
        </div>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
        <h2 class="text-sm font-black text-white uppercase tracking-widest mb-5">Histórico</h2>
        @if($transactions->isEmpty())
            <p class="text-zinc-500 text-sm">Nenhuma movimentação ainda.</p>
        @else
            <div class="space-y-3">
                @foreach($transactions as $tx)
                <div class="flex items-center justify-between py-3 border-b border-zinc-800 last:border-0">
                    <div>
                        <p class="text-white text-sm font-bold">{{ $tx->description }}</p>
                        <p class="text-[10px] text-zinc-500 mt-1">{{ $tx->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="font-black text-sm {{ $tx->points >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $tx->points >= 0 ? '+' : '' }}{{ number_format($tx->points, 0, ',', '.') }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
