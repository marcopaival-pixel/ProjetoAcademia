@extends('layouts.admin')

@section('title', 'Gestão de Usuários')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Quick Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                ['l' => 'Contas Totais', 'v' => $overview['total_users'], 'i' => 'fas fa-users', 'c' => 'blue'],
                ['l' => 'Novos (7d)', 'v' => $overview['new_users_7d'], 'i' => 'fas fa-user-plus', 'c' => 'emerald'],
                ['l' => 'Ativos (7d)', 'v' => $overview['distinct_food_loggers_7d'], 'i' => 'fas fa-fire', 'c' => 'amber'],
                ['l' => 'Premium Ativo', 'v' => $overview['premium_subscriptions_active'], 'i' => 'fas fa-crown', 'c' => 'purple'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-3xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-{{ $s['c'] }}-500/10 flex items-center justify-center text-{{ $s['c'] }}-500">
                    <i class="{{ $s['i'] }} text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $s['l'] }}</span>
                    <p class="text-2xl font-black text-white leading-none mt-1">{{ $s['v'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Enhanced Filter Bar -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl">
        <form action="{{ route('admin.users') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5 space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Pesquisar Identidade</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, E-mail ou ID..." 
                        class="w-full bg-zinc-950 border border-white/5 p-4 pl-12 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                </div>
            </div>

            <div class="md:col-span-2 space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status Premium</label>
                <select name="premium" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                    <option value="">Todos</option>
                    <option value="yes" {{ request('premium') === 'yes' ? 'selected' : '' }}>Premium</option>
                    <option value="no" {{ request('premium') === 'no' ? 'selected' : '' }}>Grátis</option>
                </select>
            </div>

            <div class="md:col-span-2 space-y-2">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Acesso</label>
                <select name="admin" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                    <option value="">Todos</option>
                    <option value="yes" {{ request('admin') === 'yes' ? 'selected' : '' }}>Admins</option>
                    <option value="no" {{ request('admin') === 'no' ? 'selected' : '' }}>Usuários</option>
                </select>
            </div>

            <div class="md:col-span-3 flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-black text-[10px] uppercase tracking-[0.2em] py-4 rounded-2xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">Filtro Ativo</button>
                <a href="{{ route('admin.users') }}" class="w-14 bg-zinc-950 border border-white/5 flex items-center justify-center rounded-2xl text-zinc-500 hover:text-white transition-colors">
                    <i class="fas fa-redo-alt text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Main Results Table -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl">
        <div class="p-8 border-b border-white/5 flex items-center justify-between bg-zinc-950/20">
            <div>
                <h2 class="text-xl font-black text-white tracking-tight">Base de Dados de Atletas</h2>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Exibindo registros conforme filtros ativos</p>
            </div>
            <a href="{{ route('admin.export.users') }}" class="px-6 py-2.5 bg-zinc-950 border border-white/10 rounded-xl text-zinc-400 text-[10px] font-black uppercase tracking-widest hover:text-white transition-all flex items-center gap-2">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[9px] font-black uppercase tracking-[0.25em] border-b border-white/5">
                        <th class="px-8 py-6">ID & Perfil</th>
                        <th class="px-8 py-6">Status Comercial</th>
                        <th class="px-8 py-6">Data de Ingresso</th>
                        <th class="px-8 py-6 text-right">Ações de Gestão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($users as $u)
                        <tr class="hover:bg-white/[0.02] transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=18181b&color=3b82f6" class="w-11 h-11 rounded-full border border-white/10 p-0.5">
                                        @if($u->is_admin)
                                            <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-amber-500 rounded-full border-4 border-zinc-900 flex items-center justify-center text-[8px] text-zinc-900" title="Admin">
                                                <i class="fas fa-shield-alt"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-white leading-none">{{ $u->name }}</p>
                                        <p class="text-[10px] text-zinc-500 font-medium mt-1">{{ $u->email }} <span class="text-zinc-700 ml-2">#{{ $u->id }}</span></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($u->is_premium)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase rounded-lg border border-blue-500/20">
                                        <span class="w-1 h-1 rounded-full bg-blue-400 animate-pulse"></span> Premium
                                    </span>
                                @else
                                    <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest pl-2">Stand-by</span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-zinc-300 text-xs font-bold">{{ $u->created_at->format('d/m/Y') }}</p>
                                <p class="text-[9px] text-zinc-600 font-black uppercase mt-0.5">{{ $u->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-xl">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.lgpd.export-user', $u->id) }}" title="Privacidade / LGPD" class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all shadow-xl">
                                        <i class="fas fa-fingerprint text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
        <div class="px-8 py-6 bg-zinc-950/40 flex items-center justify-between border-t border-white/5">
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest italic">Páginas de Resultados NexShape</span>
            <div class="flex gap-2">
                @if ($users->onFirstPage())
                    <span class="px-4 py-2 bg-zinc-900 text-zinc-700 text-[10px] font-black rounded-lg cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 bg-zinc-900 text-zinc-300 text-[10px] font-black rounded-lg hover:bg-blue-600 hover:text-white transition-all">Anterior</a>
                @endif

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 bg-zinc-900 text-zinc-300 text-[10px] font-black rounded-lg hover:bg-blue-600 hover:text-white transition-all">Próximo</a>
                @else
                    <span class="px-4 py-2 bg-zinc-900 text-zinc-700 text-[10px] font-black rounded-lg cursor-not-allowed">Próximo</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
