@extends('layouts.admin')

@section('title', 'Leads do Lançamento — Marketing')

@section('content')
<div class="animate-fade-in space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-blue-600/10 border border-blue-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    <span class="text-blue-400 text-[9px] font-black uppercase tracking-widest">Growth Analytics</span>
                </div>
                <span class="text-zinc-600 text-[10px] font-bold tracking-tight">• Lista de Interessados no App</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                Leads de <span class="text-blue-500">Lançamento</span>
            </h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.marketing.app-banner.index') }}" class="px-6 py-3 bg-zinc-900 border border-white/5 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-all flex items-center gap-3">
                <i data-lucide="arrow-left" class="w-4 h-4 text-zinc-500"></i>
                Voltar
            </a>
            <button class="px-6 py-3 bg-emerald-600 text-zinc-950 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-500 transition-all flex items-center gap-3 shadow-lg shadow-emerald-500/20">
                <i data-lucide="download" class="w-4 h-4"></i>
                Exportar CSV
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-zinc-900 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-950/50 border-b border-zinc-800">
                        <th class="px-8 py-5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nome do Usuário</th>
                        <th class="px-8 py-5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">E-mail</th>
                        <th class="px-8 py-5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Origem</th>
                        <th class="px-8 py-5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Data de Cadastro</th>
                        <th class="px-8 py-5 text-[10px] text-zinc-500 font-black uppercase tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-blue-500/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center border border-white/5 text-blue-500 font-black text-xs shadow-inner">
                                    {{ substr($lead->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-white uppercase tracking-tight">{{ $lead->name }}</p>
                                    @if($lead->user_id)
                                        <span class="text-[8px] text-emerald-500 font-black uppercase tracking-[0.2em] italic">Usuário Registrado</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs text-zinc-400 font-medium tracking-tight">{{ $lead->email }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-zinc-950 rounded-lg border border-white/5 text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $lead->source }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">{{ $lead->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[9px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                Aguardando
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-white/5 shadow-xl">
                                <i data-lucide="inbox" class="w-8 h-8 text-zinc-800"></i>
                            </div>
                            <p class="text-xs text-zinc-600 font-black uppercase tracking-widest italic">Nenhum lead registrado até o momento.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($leads->hasPages())
        <div class="p-8 border-t border-zinc-900">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
