@extends('layouts.admin')

@section('title', 'AI Credits Report')

@section('content')
<div class="animate-fade-in space-y-6">
    
    <!-- Header -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-purple-600/10 border border-purple-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                    <span class="text-purple-400 text-[9px] font-black uppercase tracking-widest">Relatório Detalhado por Usuário</span>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter">
                Auditoria de <span class="text-purple-500">Consumo</span>
            </h1>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('admin.financial.ai-credits.dashboard') }}" class="px-6 py-3 bg-zinc-900 border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white transition-all">
                <i class="fas fa-chevron-left mr-2"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="glass-card p-6 rounded-2xl mb-6">
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Buscar Usuário</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou e-mail..." class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Tipo de Usuário</label>
                <select name="user_type" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
                    <option value="">Todos</option>
                    <option value="aluno" {{ request('user_type') == 'aluno' ? 'selected' : '' }}>Aluno</option>
                    <option value="professional" {{ request('user_type') == 'professional' ? 'selected' : '' }}>Profissional</option>
                    <option value="paciente" {{ request('user_type') == 'paciente' ? 'selected' : '' }}>Paciente</option>
                </select>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-purple-500 transition-all shadow-xl shadow-purple-600/20">
                Filtrar Resultados
            </button>
        </form>
    </div>

    <!-- Tabela -->
    <div class="glass-card rounded-2xl overflow-hidden min-h-[400px]">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Usuário</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Tipo / Plano</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Créditos</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Consumo</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Saldo</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Último Uso</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $user)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=18181b&color=a1a1aa" class="w-10 h-10 rounded-full border border-white/10">
                                <div>
                                    <div class="text-xs font-black text-white">{{ $user->name }}</div>
                                    <div class="text-[9px] text-zinc-600 font-bold uppercase">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 text-center">
                            <div class="inline-flex flex-col gap-1">
                                <span class="px-2 py-0.5 rounded bg-zinc-900 border border-white/5 text-[9px] font-black uppercase text-zinc-400">
                                    {{ $user->roles->first()?->label ?? 'N/D' }}
                                </span>
                                <span class="text-[9px] text-purple-500 font-bold uppercase">{{ $user->plan?->name ?? 'N/D' }}</span>
                            </div>
                        </td>
                        <td class="p-6 text-center">
                            <span class="text-xs font-black text-blue-400">{{ number_format($user->credits_bought_sum_credits_amount ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td class="p-6 text-center">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-rose-500">{{ number_format($user->total_used, 0, ',', '.') }}</span>
                                <span class="text-[9px] text-zinc-600 font-bold uppercase">Total</span>
                            </div>
                        </td>
                        <td class="p-6 text-center">
                            <span class="text-lg font-black {{ $user->ai_credits > 5 ? 'text-emerald-500' : 'text-amber-500' }}">
                                {{ number_format($user->ai_credits, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            <span class="text-[10px] text-zinc-500 font-bold uppercase">
                                {{ $user->aiUsage()->latest()->first()?->created_at?->diffForHumans() ?? 'Nunca' }}
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            @if($user->status == 'active')
                                <span class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase">Ativo</span>
                            @else
                                <span class="px-2.5 py-1 rounded bg-rose-500/10 text-rose-500 text-[9px] font-black uppercase">Bloqueado</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-20 text-center text-zinc-600 italic">Nenhum usuário encontrado com os filtros aplicados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
