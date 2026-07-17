@extends('layouts.admin')

@section('title', 'Solicitações de Entrada')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.groups.index') }}" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-black text-white tracking-tight">Solicitações <span class="text-amber-500">Pendentes</span></h1>
        </div>
        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-7">Aprovação ou rejeição de novos membros</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="text-xs font-bold uppercase tracking-wide">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($requests as $request)
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 hover:bg-zinc-900/60 transition-all group relative overflow-hidden">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-xl text-amber-500 border border-amber-500/20">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="flex flex-col">
                    <h3 class="text-lg font-black text-white tracking-tight">{{ $request->user_name }}</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $request->user_email }}</p>
                </div>
            </div>

            <div class="bg-zinc-950/50 p-4 rounded-2xl border border-white/5 mb-6">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Solicitou entrar em</span>
                <span class="text-xs font-bold text-blue-500 uppercase tracking-wide">{{ $request->group_name }}</span>
            </div>

            <div class="flex items-center justify-between text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-8">
                <span>Data do pedido</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <form action="{{ route('admin.groups.requests.reject', $request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-zinc-800 text-red-500 font-black rounded-xl hover:bg-red-500 hover:text-white transition-all text-[9px] uppercase tracking-widest border border-red-500/10">
                        Rejeitar
                    </button>
                </form>
                <form action="{{ route('admin.groups.requests.approve', $request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-500 transition-all text-[9px] uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                        Aprovar
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-zinc-900/20 rounded-[2.5rem] border border-dashed border-white/5">
            <i class="fas fa-inbox text-4xl text-zinc-800 mb-4"></i>
            <p class="text-zinc-500 font-medium tracking-tight">Não há solicitações de entrada pendentes.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
