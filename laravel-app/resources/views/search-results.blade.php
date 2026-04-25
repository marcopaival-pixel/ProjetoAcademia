@extends(request()->is('admin*') ? 'layouts.admin' : 'layouts.app')

@section('title', 'Resultados da Busca')

@section('content')
<div class="space-y-8 animate-fade-in">
    <header class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight italic">Resultados para: <span class="text-blue-500">"{{ $query }}"</span></h1>
            @php
                $totalResults = ($results['exercises'] ?? collect())->count() + 
                                ($results['users'] ?? collect())->count() + 
                                ($results['errors'] ?? collect())->count();
            @endphp
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">
                Busca Inteligente NexShape • {{ $totalResults }} {{ $totalResults == 1 ? 'resultado encontrado' : 'resultados encontrados' }}
            </p>
        </div>
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-zinc-900 border border-white/5 rounded-xl text-xs text-zinc-400 hover:text-white transition-all">
            &larr; Voltar
        </a>
    </header>

    @if(empty($results['exercises']) && empty($results['users']) && empty($results['errors']))
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-20 text-center">
            <div class="w-16 h-16 bg-zinc-950 rounded-2xl border border-white/5 flex items-center justify-center mx-auto mb-6 text-zinc-700">
                <i class="fas fa-search-minus text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Nada foi encontrado</h2>
            <p class="text-sm text-zinc-500 max-w-xs mx-auto">Tente usar termos mais genéricos ou verifique se digitou corretamente.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-8">
            {{-- Resultados de Exercícios --}}
            @if(!empty($results['exercises']) && $results['exercises']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-blue-600/5 flex items-center gap-3">
                        <i class="fas fa-dumbbell text-blue-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Exercícios no Catálogo</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['exercises'] as $ex)
                            <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-zinc-950 rounded-xl border border-white/5 flex items-center justify-center text-xs text-zinc-500 group-hover:border-blue-500/30 transition-all">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">{{ $ex->name }}</h4>
                                        <p class="text-[10px] text-zinc-500 uppercase font-black tracking-tight">{{ $ex->muscle_group }} • {{ $ex->equipment ?: 'Sem equipamento' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[8px] px-2 py-0.5 rounded bg-zinc-950 border border-white/10 text-zinc-500 font-bold uppercase tracking-widest">{{ $ex->difficulty }}</span>
                                    @if(auth()->user()->is_admin)
                                        <a href="{{ route('admin.exercises.edit', $ex->id) }}" class="text-blue-500 hover:text-white transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Resultados de Usuários (Admin) --}}
            @if(!empty($results['users']) && $results['users']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-amber-600/5 flex items-center gap-3">
                        <i class="fas fa-users text-amber-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Utilizadores do Sistema</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['users'] as $user)
                            <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=18181b&color=a1a1aa' }}" class="w-10 h-10 rounded-full border border-white/5 group-hover:border-amber-500/30 transition-all">
                                    <div>
                                        <h4 class="text-sm font-bold text-white">{{ $user->name }}</h4>
                                        <p class="text-[10px] text-zinc-500 font-bold tracking-tight">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($user->is_premium)
                                        <span class="text-[8px] px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-amber-500 font-bold uppercase tracking-widest">PREMIUM</span>
                                    @endif
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-amber-500 hover:text-white transition-colors">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Resultados de Erros (Admin) --}}
            @if(!empty($results['errors']) && $results['errors']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-red-600/5 flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Logs de Erros Relacionados</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['errors'] as $error)
                            <div class="p-6 hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] text-red-500 font-black uppercase">{{ $error->type }}</span>
                                    <span class="text-[9px] text-zinc-600 font-bold uppercase">{{ $error->created_at->diffForHumans() }}</span>
                                </div>
                                <h4 class="text-xs font-bold text-zinc-300 line-clamp-1">{{ $error->message }}</h4>
                                <p class="text-[9px] text-zinc-500 mt-1 font-mono break-all">{{ $error->url }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
