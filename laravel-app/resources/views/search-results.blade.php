@extends(request()->is('admin*') ? 'layouts.admin' : 'layouts.app')

@section('title', 'Resultados da Busca')

@section('content')
<div class="space-y-8 animate-fade-in">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight italic">
                @if(!empty($query))
                    Resultados para: <span class="text-{{ request()->is('admin*') ? 'amber' : 'emerald' }}-500">"{{ $query }}"</span>
                @else
                    Explorar <span class="text-{{ request()->is('admin*') ? 'amber' : 'emerald' }}-500">Catálogo</span>
                @endif
            </h1>
            @php
                $totalResults = collect($results)->sum(fn($items) => $items->count());
                $isClinica = ($experienceClass ?? '') === 'experience-clinica';
                $accentColor = request()->is('admin*') ? 'amber' : ($isClinica ? 'blue' : 'emerald');
            @endphp
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">
                Busca Inteligente NexShape • {{ $totalResults }} {{ $totalResults == 1 ? 'resultado encontrado' : 'resultados encontrados' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('global.search') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="q" value="{{ $query }}">
                
                {{-- Filtro de Categoria --}}
                <select name="category" onchange="this.form.submit()" class="bg-zinc-900 border border-white/5 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest text-zinc-400 focus:border-{{ $accentColor }}-500/50 transition-all outline-none">
                    <option value="">Todas Categorias</option>
                    <option value="exercises" {{ $category === 'exercises' ? 'selected' : '' }}>Exercícios</option>
                    <option value="workouts" {{ $category === 'workouts' ? 'selected' : '' }}>Meus Treinos</option>
                    <option value="help" {{ $category === 'help' ? 'selected' : '' }}>Ajuda</option>
                    <option value="foods" {{ $category === 'foods' ? 'selected' : '' }}>Alimentos</option>
                    @if(auth()->user()->isAdministrator())
                        <option value="users" {{ $category === 'users' ? 'selected' : '' }}>Usuários</option>
                        <option value="errors" {{ $category === 'errors' ? 'selected' : '' }}>Erros</option>
                    @endif
                </select>

                {{-- Filtro de Músculo (Só aparece se categoria for exercícios ou geral) --}}
                @if(!$category || $category === 'exercises')
                    <select name="muscle" onchange="this.form.submit()" class="bg-zinc-900 border border-white/5 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest text-zinc-400 focus:border-{{ $accentColor }}-500/50 transition-all outline-none">
                        <option value="">Todos Músculos</option>
                        @foreach($muscles as $m)
                            <option value="{{ $m }}" {{ $muscle === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                @endif

                @if($category || $muscle)
                    <a href="{{ route('global.search', ['q' => $query]) }}" class="px-4 py-2 bg-zinc-950 border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-white transition-all">
                        Limpar
                    </a>
                @endif
            </form>

            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-zinc-900 border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white transition-all">
                &larr; Voltar
            </a>
        </div>
    </header>

    @if($aiResponse)
        <section class="bg-zinc-900/40 border border-emerald-500/20 rounded-[2.5rem] overflow-hidden p-8 relative group">
            <div class="absolute top-0 right-0 p-6 opacity-20 group-hover:opacity-40 transition-opacity">
                <i class="fas fa-brain-circuit text-4xl text-emerald-500"></i>
            </div>
            
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl border border-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-magic text-emerald-500"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">NexShape Intelligence</h3>
                    <p class="text-[9px] text-emerald-500/60 font-bold uppercase tracking-widest">IA interpretou sua intenção</p>
                </div>
            </div>

            <div class="prose prose-invert prose-zinc max-w-none text-zinc-300 text-sm leading-relaxed">
                {!! \App\Support\SafeHtml::markdown($aiResponse['text']) !!}
            </div>

            @if(!empty($aiResponse['action']))
                <div class="mt-6 pt-6 border-t border-white/5 flex items-center gap-4">
                    @if($aiResponse['action']['acao'] === 'criar_treino')
                        <button class="px-6 py-3 bg-emerald-500 text-zinc-950 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-emerald-500/20">
                            Executar: Criar Treino "{{ $aiResponse['action']['dados']['name'] ?? 'Novo Treino' }}"
                        </button>
                    @endif
                    <p class="text-[9px] text-zinc-500 italic">Nota: Ações automáticas requerem sua confirmação final.</p>
                </div>
            @endif
        </section>
    @endif

    @if($totalResults === 0 && !$aiResponse)
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

            {{-- Resultados de Treinos --}}
            @if(!empty($results['workouts']) && $results['workouts']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-emerald-600/5 flex items-center gap-3">
                        <i class="fas fa-clipboard-list text-emerald-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Meus Treinos</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['workouts'] as $workout)
                            <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-zinc-950 rounded-xl border border-white/5 flex items-center justify-center text-xs text-zinc-500 group-hover:border-emerald-500/30 transition-all">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">{{ $workout->name }}</h4>
                                        <p class="text-[10px] text-zinc-500 uppercase font-black tracking-tight">{{ $workout->goal }} • {{ $workout->difficulty }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('training.index') }}" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-600 hover:text-emerald-500 transition-all">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Resultados de Ajuda --}}
            @if(!empty($results['help']) && $results['help']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-blue-600/5 flex items-center gap-3">
                        <i class="fas fa-question-circle text-blue-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Ajuda e Suporte</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['help'] as $article)
                            <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-zinc-950 rounded-xl border border-white/5 flex items-center justify-center text-xs text-zinc-500 group-hover:border-blue-500/30 transition-all">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">{{ $article->title }}</h4>
                                        <p class="text-[10px] text-zinc-500 uppercase font-black tracking-tight">{{ $article->category->name ?? 'Geral' }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('kb.article', $article->slug) }}" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-600 hover:text-blue-500 transition-all">
                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Resultados de Comunicados --}}
            @if(!empty($results['announcements']) && $results['announcements']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-amber-600/5 flex items-center gap-3">
                        <i class="fas fa-bullhorn text-amber-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Comunicados</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['announcements'] as $ann)
                            <div class="p-6 hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center gap-4 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                    <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $ann->created_at->format('d/m/Y') }}</span>
                                </div>
                                <p class="text-xs text-zinc-300 font-medium leading-relaxed">{{ $ann->content }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Resultados de Alimentos --}}
            @if(!empty($results['foods']) && $results['foods']->count() > 0)
                <section class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <header class="p-6 border-b border-white/5 bg-lime-600/5 flex items-center gap-3">
                        <i class="fas fa-apple-alt text-lime-500"></i>
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Alimentos e Nutrição</h3>
                    </header>
                    <div class="divide-y divide-white/5">
                        @foreach($results['foods'] as $food)
                            <div class="p-6 flex items-center justify-between hover:bg-white/[0.02] transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-zinc-950 rounded-xl border border-white/5 flex items-center justify-center text-xs text-zinc-500 group-hover:border-lime-500/30 transition-all">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">{{ $food->name }}</h4>
                                        <p class="text-[10px] text-zinc-500 uppercase font-black tracking-tight">{{ $food->brand ?: 'Genérico' }} • Base: {{ $food->base_amount }}{{ $food->unit }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('nutrition.index') }}" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-600 hover:text-lime-500 transition-all">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
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
