@extends('layouts.app', ['navCurrent' => 'profile'])

@section('title', 'Usuários Bloqueados')

@section('content')
<div class="max-w-4xl mx-auto py-10 animate-fade-up">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Usuários Bloqueados</h1>
            <p class="text-zinc-500 font-medium">Gerencie quem pode interagir com você no sistema.</p>
        </div>
        <a href="{{ route('profile') }}" class="btn btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
            Voltar ao Perfil
        </a>
    </div>

    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md rounded-[2.5rem] overflow-hidden">
        <div class="divide-y divide-white/5">
            @forelse($blockedUsers as $user)
                <div class="p-6 flex items-center justify-between group hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-white/5 flex items-center justify-center text-white font-black text-lg">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-white font-bold">{{ $user->name }}</h4>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('user.block', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600/10 hover:bg-blue-600 text-blue-400 hover:text-white px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                            Desbloquear
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Nenhum utilizador bloqueado</h3>
                    <p class="text-zinc-500 text-sm max-w-xs mx-auto">Sua lista de bloqueios está vazia. Você pode bloquear utilizadores através das mensagens ou correio interno.</p>
                </div>
            @endforelse
        </div>

        @if($blockedUsers->hasPages())
            <div class="p-6 border-t border-white/5">
                {{ $blockedUsers->links() }}
            </div>
        @endif
    </div>

    <!-- Info Card -->
    <div class="mt-8 p-6 rounded-[2rem] bg-amber-500/5 border border-amber-500/10 flex gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-500 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h5 class="text-amber-500 font-bold text-sm mb-1 uppercase tracking-tight">O que acontece ao bloquear?</h5>
            <p class="text-zinc-500 text-xs leading-relaxed">Utilizadores bloqueados não podem enviar-lhe mensagens diretas ou e-mails internos. Além disso, as mensagens deles serão filtradas das suas notificações e contadores de mensagens não lidas.</p>
        </div>
    </div>
</div>
@endsection
