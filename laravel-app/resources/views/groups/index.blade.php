@extends('layouts.app')

@section('title', 'Grupos de Comunicação')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-dashboard-entry">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Comunidade e <span class="text-blue-500">Grupos</span></h1>
            <p class="text-zinc-400 mt-2">Participe de grupos para liberar a comunicação com outros membros.</p>
        </div>
        
        <div class="flex gap-3">
            <div class="bg-zinc-800/50 backdrop-blur-md border border-zinc-700/50 rounded-2xl px-6 py-3 flex items-center gap-3">
                <i class="fas fa-users text-blue-500"></i>
                <span class="text-white font-bold">{{ $groups->count() }} Grupos Disponíveis</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-500 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- My Groups Section -->
    @if(count($approvedGroupIds) > 0 || count($pendingGroupIds) > 0)
    <div class="mb-12">
        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fas fa-star text-amber-500"></i>
            Minhas Participações
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groups->whereIn('id', array_merge($approvedGroupIds, $pendingGroupIds)) as $group)
                @php($isPending = in_array($group->id, $pendingGroupIds))
                <div class="bg-zinc-900 border {{ $isPending ? 'border-amber-500/30' : 'border-zinc-800' }} rounded-[2rem] p-6 hover:border-blue-500/50 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center text-xl group-hover:bg-blue-500/10 group-hover:text-blue-500 transition-colors">
                            <i class="fas fa-comments"></i>
                        </div>
                        @if($isPending)
                            <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-xs font-bold rounded-full border border-amber-500/20">PENDENTE</span>
                        @else
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-xs font-bold rounded-full border border-emerald-500/20">ATIVO</span>
                        @endif
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">{{ $group->name }}</h3>
                    <p class="text-zinc-500 text-sm line-clamp-2 mb-6">{{ $group->description ?: 'Sem descrição disponível.' }}</p>
                    
                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-zinc-400 text-xs font-medium">
                            <i class="fas fa-user-friends mr-1"></i> {{ $group->members_count }} membros
                        </span>
                        
                        <form action="{{ route('groups.leave', $group) }}" method="POST"
                            data-confirm-delete
                            data-confirm-title="Sair do grupo"
                            data-confirm-message="Sair deste grupo? Perderá permissão para falar com membros exclusivos deste grupo."
                            data-confirm-primary-label="Sair">
                            @csrf
                            <button class="text-zinc-500 hover:text-red-500 text-sm font-bold transition-colors">Sair</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Groups Section -->
    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
        <i class="fas fa-search text-blue-500"></i>
        Explorar Grupos
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($groups->whereNotIn('id', array_merge($approvedGroupIds, $pendingGroupIds)) as $group)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 hover:border-zinc-700 transition-all duration-300 flex flex-col">
                <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center text-xl mb-4 text-zinc-400">
                    <i class="fas fa-hashtag"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">{{ $group->name }}</h3>
                <p class="text-zinc-500 text-sm line-clamp-2 mb-6">{{ $group->description ?: 'Sem descrição disponível.' }}</p>
                
                <div class="mt-auto pt-6 border-t border-zinc-800/50 flex items-center justify-between">
                    <span class="text-zinc-400 text-xs font-medium">
                        <i class="fas fa-user-friends mr-1"></i> {{ $group->members_count }} membros
                    </span>
                    
                    <form action="{{ route('groups.join', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-xl text-sm font-bold transition-all transform hover:scale-105">
                            {{ $group->is_private ? 'Solicitar' : 'Entrar' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-zinc-900/50 rounded-[2.5rem] border border-dashed border-zinc-800">
                <i class="fas fa-layer-group text-4xl text-zinc-800 mb-4"></i>
                <p class="text-zinc-500 font-medium">Nenhum grupo novo disponível no momento.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
