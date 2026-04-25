@extends('layouts.app', ['navCurrent' => 'messages'])

@section('title', 'Nova Conversa')

@section('content')
<div class="max-w-2xl mx-auto animate-dashboard-entry">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('messages.index') }}" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-zinc-400 hover:text-white transition-colors border border-white/5">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-3xl font-black text-white">Nova Conversa</h1>
    </div>

    <!-- Search Box -->
    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md p-6 rounded-[2.5rem] mb-6">
        <form action="{{ route('messages.create') }}" method="GET" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $search }}"
                   placeholder="Pesquisar por nome..." 
                   class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-zinc-500 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </form>
    </div>

    <!-- Users List -->
    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md rounded-[2.5rem] overflow-hidden">
        <div class="divide-y divide-white/5">
            @forelse($users as $user)
                <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-500 font-black text-lg group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg group-hover:shadow-blue-600/20">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-white font-bold">{{ $user->name }}</h3>
                            <p class="text-xs text-zinc-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('messages.start') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <button type="submit" class="bg-zinc-800 hover:bg-blue-600 text-zinc-400 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-all uppercase tracking-widest border border-white/5 group-hover:border-blue-500/30">
                            Conversar
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-zinc-500 font-medium">Buscamos por todo o lado, mas não encontramos ninguém com esse nome.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
