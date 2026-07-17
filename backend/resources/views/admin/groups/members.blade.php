@extends('layouts.admin')

@section('title', 'Membros do Grupo')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.groups.index') }}" class="text-zinc-500 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-black text-white tracking-tight">Membros: <span class="text-blue-500">{{ $group->name }}</span></h1>
            </div>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-7">Gerenciamento de permissões e moderação</p>
        </div>
        
        <div class="flex gap-3">
            <span class="px-6 py-3 bg-zinc-800 rounded-2xl text-[10px] text-zinc-300 font-black uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-users text-blue-500"></i>
                {{ $members->count() }} Total
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="text-xs font-bold uppercase tracking-wide">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-zinc-900/60">
                        <th class="px-8 py-6 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Usuário</th>
                        <th class="px-8 py-6 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Status</th>
                        <th class="px-8 py-6 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nível/Role</th>
                        <th class="px-8 py-6 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Entrada</th>
                        <th class="px-8 py-6 text-[10px] text-zinc-500 font-black uppercase tracking-widest text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($members as $member)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-sm font-black text-blue-500 border border-white/5">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white">{{ $member->name }}</span>
                                    <span class="text-[10px] text-zinc-500 font-medium lowercase tracking-tight">{{ $member->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest 
                                {{ $member->pivot->status === 'approved' ? 'bg-emerald-500/10 text-emerald-500' : 
                                   ($member->pivot->status === 'pending' ? 'bg-amber-500/10 text-amber-500' : 'bg-red-500/10 text-red-500') }}">
                                {{ $member->pivot->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <form action="{{ route('admin.groups.members.role', [$group, $member]) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <select name="role" onchange="this.form.submit()" class="bg-zinc-950 border border-white/5 rounded-lg px-3 py-1.5 text-[10px] text-zinc-300 font-black uppercase tracking-widest outline-none focus:border-blue-500 transition-all">
                                    <option value="member" {{ $member->pivot->role === 'member' ? 'selected' : '' }}>Membro</option>
                                    <option value="moderator" {{ $member->pivot->role === 'moderator' ? 'selected' : '' }}>Moderador</option>
                                    <option value="admin" {{ $member->pivot->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">
                                {{ $member->pivot->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <form action="{{ route('admin.groups.members.remove', [$group, $member]) }}" method="POST"
                            data-confirm-delete
                            data-confirm-title="Remover membro"
                            data-confirm-message="Remover este membro do grupo?">
                                @csrf
                                @method('DELETE')
                                <button class="p-2.5 bg-zinc-800 hover:bg-red-600 text-zinc-500 hover:text-white rounded-xl transition-all" title="Remover Membro">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <i class="fas fa-user-slash text-4xl text-zinc-800 mb-4"></i>
                            <p class="text-zinc-500 font-medium">Nenhum membro encontrado neste grupo.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
