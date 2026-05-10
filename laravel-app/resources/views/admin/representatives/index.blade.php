@extends('layouts.app')

@section('title', 'Gestão de Representantes — NexShape Pro')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Gestão de <span class="text-emerald-500">Representantes</span></h1>
            <p class="text-sm text-zinc-500 font-medium italic">Aprove e gerencie seus parceiros comerciais.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-[2rem] bg-zinc-900/50 border border-zinc-800 backdrop-blur-xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Total de Representantes</p>
            <p class="text-2xl font-bold text-white tracking-tight">{{ $representatives->total() }}</p>
        </div>
    </div>

    <div class="rounded-[2.5rem] bg-zinc-900/50 border border-zinc-800 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-800/50">
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Representante</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Cadastro em</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest italic text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @forelse($representatives as $rep)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center font-black text-zinc-500 italic">
                                    {{ substr($rep->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white tracking-tight">{{ $rep->name }}</p>
                                    <p class="text-[10px] text-zinc-500 font-medium">{{ $rep->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($rep->status === 'PENDENTE_APROVACAO' || $rep->status === 'pending' || $rep->status === 'PENDENTE')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-500 font-black uppercase tracking-widest italic">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pendente
                                </span>
                            @elseif($rep->isActive())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] text-emerald-500 font-black uppercase tracking-widest italic">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-[10px] text-red-400 font-black uppercase tracking-widest italic">
                                    Recusado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-[11px] text-zinc-500 font-medium">
                            {{ $rep->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-end gap-2">
                                @if($rep->status === 'PENDENTE_APROVACAO' || $rep->status === 'pending' || $rep->status === 'PENDENTE')
                                    <form action="{{ route('admin.representatives.approve', $rep) }}" method="POST" onsubmit="return confirm('Confirmar aprovação deste representante?')">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-lg shadow-emerald-500/5">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.representatives.reject', $rep) }}" method="POST" onsubmit="return confirm('Recusar cadastro deste representante?')">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/5">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i data-lucide="users-2" class="w-10 h-10 text-zinc-800"></i>
                                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em]">Nenhum representante encontrado</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($representatives->hasPages())
            <div class="px-6 py-4 border-t border-zinc-800/50">
                {{ $representatives->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
