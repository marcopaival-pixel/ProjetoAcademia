@extends('layouts.admin')

@section('title', 'Gestão de Leads')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Leads Comerciais</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Gerencie interessados e oportunidades no funil</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.leads.funnel') }}" class="px-6 py-3 bg-zinc-900 border border-white/5 rounded-2xl text-[10px] text-zinc-400 font-black uppercase tracking-widest hover:bg-zinc-800 transition-all flex items-center gap-2">
                <i class="fas fa-filter"></i> Ver Funil
            </a>
            <a href="{{ route('admin.leads.create') }}" class="px-6 py-3 bg-blue-600 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
                <i class="fas fa-plus"></i> Novo Lead
            </a>
        </div>
    </div>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden backdrop-blur-3xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Lead</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Empresa/Origem</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Responsável</th>
                        <th class="px-6 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($leads as $lead)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center border border-white/5 text-blue-500">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $lead->nome }}</p>
                                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $lead->email ?? 'Sem email' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-xs font-bold text-zinc-300">{{ $lead->empresa ?? 'Pessoa Física' }}</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-0.5">{{ $lead->origem ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-6">
                            @php
                                $statusColors = [
                                    'Novo' => 'blue',
                                    'Em contato' => 'amber',
                                    'Em negociação' => 'purple',
                                    'Convertido' => 'emerald',
                                    'Perdido' => 'red',
                                ];
                                $color = $statusColors[$lead->status] ?? 'zinc';
                            @endphp
                            <span class="px-3 py-1 bg-{{ $color }}-500/10 border border-{{ $color }}-500/20 text-{{ $color }}-500 text-[10px] font-black uppercase rounded-lg">
                                {{ $lead->status }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-xs font-bold text-zinc-400">{{ $lead->responsavel?->name ?? 'Não atribuído' }}</p>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.leads.show', $lead) }}" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                                <a href="{{ route('admin.leads.edit', $lead) }}" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-zinc-600 italic text-sm">Nenhum lead encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="px-6 py-4 border-t border-white/5 bg-zinc-950/20">
            {{ $leads->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
