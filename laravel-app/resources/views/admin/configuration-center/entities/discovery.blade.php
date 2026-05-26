@extends('layouts.admin')

@section('title', 'Explorar Banco de Dados')

@section('content')
<div class="glass-card p-8">
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white tracking-tight">Explorar Tabelas do Sistema</h2>
        <p class="text-xs text-zinc-500 mt-1">O sistema identificou as seguintes tabelas físicas que ainda não estão sob gestão do Configuration Center.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tables as $table)
        <div class="group bg-zinc-900/30 border border-white/5 rounded-2xl p-6 hover:bg-zinc-900/50 hover:border-emerald-500/20 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 group-hover:text-emerald-500 shadow-xl transition-colors">
                    <i data-lucide="database" class="w-6 h-6"></i>
                </div>
                <div class="px-2 py-1 rounded-md bg-zinc-950 border border-white/5 text-[8px] font-black text-zinc-600 uppercase tracking-widest">
                    Inativo
                </div>
            </div>
            
            <h3 class="text-sm font-black text-white uppercase tracking-wider mb-1">{{ str_replace('_', ' ', $table) }}</h3>
            <p class="text-[10px] text-zinc-500 font-mono mb-4">{{ $table }}</p>
            
            <form action="{{ route('admin.configuration-center.entities.auto-register') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="{{ $table }}">
                <button type="submit" class="w-full py-2.5 bg-zinc-950 border border-white/5 text-zinc-400 rounded-xl font-black text-[9px] uppercase tracking-widest group-hover:bg-emerald-500 group-hover:text-zinc-950 group-hover:border-transparent transition-all">
                    Registrar Automaticamente
                </button>
            </form>
        </div>
        @endforeach

        @if(empty($tables))
        <div class="col-span-full py-20 text-center">
            <div class="w-16 h-16 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center mx-auto mb-4 text-zinc-700">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-white font-bold">Tudo sob controle!</h3>
            <p class="text-zinc-500 text-sm mt-1">Todas as tabelas relevantes já foram registradas ou estão na lista de exclusão.</p>
        </div>
        @endif
    </div>
</div>
@endsection
