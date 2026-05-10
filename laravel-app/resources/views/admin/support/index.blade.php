@extends('layouts.admin')

@section('title', 'Suporte Técnico')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Painel de Atendimento</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Gerencie chamados e solicitações dos clientes</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6 flex items-center justify-between group hover:bg-zinc-900/60 transition-all">
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Chamados Abertos</p>
                <h3 class="text-xl font-bold text-white">{{ $stats['open'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                <i class="fas fa-envelope-open text-xl"></i>
            </div>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6 flex items-center justify-between group hover:bg-zinc-900/60 transition-all">
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Em Atendimento</p>
                <h3 class="text-xl font-bold text-amber-500">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6 flex items-center justify-between group hover:bg-zinc-900/60 transition-all">
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Resolvidos</p>
                <h3 class="text-xl font-bold text-emerald-500">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="px-8 py-6 bg-white/[0.02] border-b border-white/5 flex items-center justify-between">
            <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Lista de Chamados</h4>
            <div class="flex gap-4">
                <select onchange="window.location.href='?status='+this.value" class="bg-zinc-950 border border-white/5 rounded-xl px-4 py-2 text-[10px] text-zinc-400 font-black uppercase tracking-widest outline-none">
                    <option value="">Status: Todos</option>
                    <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Abertos</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>Em Progresso</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Fechados</option>
                </select>
            </div>
        </div>
        
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.01] border-bottom border-white/5">
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">ID / Assunto</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Cliente</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Prioridade</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-white/[0.01] transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">{{ $ticket->subject }}</span>
                            <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-1">#{{ $ticket->id }} &bull; {{ $ticket->category ?? 'Individual' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-zinc-300">{{ $ticket->user->name }}</span>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase">{{ $ticket->user->email }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @php
                            $prioColors = ['Low' => 'zinc', 'Medium' => 'blue', 'High' => 'amber', 'Critical' => 'red'];
                            $prioColor = $prioColors[$ticket->priority] ?? 'zinc';
                        @endphp
                        <span class="px-3 py-1 bg-{{ $prioColor }}-500/10 border border-{{ $prioColor }}-500/20 text-{{ $prioColor }}-500 text-[8px] font-black uppercase rounded-lg">
                            {{ $ticket->priority }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                         @php
                            $statusColors = ['Open' => 'blue', 'In Progress' => 'amber', 'Resolved' => 'emerald', 'Closed' => 'zinc'];
                            $statusColor = $statusColors[$ticket->status] ?? 'zinc';
                        @endphp
                        <span class="px-3 py-1 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 text-{{ $statusColor }}-500 text-[8px] font-black uppercase rounded-lg">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('admin.support.show', $ticket) }}" class="inline-flex w-8 h-8 rounded-lg bg-zinc-800 border border-white/5 items-center justify-center text-zinc-400 hover:text-white transition-all opacity-0 group-hover:opacity-100 shadow-xl shadow-black/50">
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-headset text-4xl text-zinc-800 mb-6"></i>
                            <p class="text-zinc-600 font-bold uppercase tracking-widest text-[10px]">Nenhum chamado encontrado</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($tickets->hasPages())
        <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
