@extends('layouts.app')

@section('title', 'Central de Ajuda')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in space-y-16">
    <!-- Hero / Search Section -->
    <div class="text-center py-16 bg-gradient-to-br from-blue-600/20 to-zinc-900/40 rounded-[3rem] border border-white/5 space-y-8 shadow-2xl">
        <div class="space-y-2">
            <h2 class="text-4xl font-black text-white tracking-tight">Como podemos ajudar?</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Explore nossa base de conhecimento ou procure por um tópico</p>
        </div>
        
        <form action="{{ route('kb.search') }}" method="GET" class="max-w-2xl mx-auto px-4">
            <div class="relative group">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-zinc-500 group-focus-within:text-blue-500 transition-colors"></i>
                <input type="text" name="q" placeholder="Digite sua dúvida (ex: como importar alunos...)" class="w-full bg-zinc-950 border border-white/10 rounded-full px-14 py-6 text-white font-bold outline-none focus:border-blue-500/50 shadow-2xl transition-all">
            </div>
        </form>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
        <a href="{{ route('kb.category', $category) }}" class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-10 group hover:bg-zinc-900/60 hover:border-blue-500/30 transition-all shadow-xl">
            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-blue-500 mb-8 border border-white/5 group-hover:scale-110 transition-transform">
                <i class="fas fa-{{ $category->icon ?? 'book-open' }} text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-white mb-2">{{ $category->name }}</h3>
            <p class="text-xs text-zinc-600 font-medium leading-relaxed mb-6">{{ $category->description ?? 'Confira tutoriais sobre este tópico.' }}</p>
            <span class="text-[10px] text-blue-500 font-black uppercase tracking-widest">{{ $category->articles_count }} artigos &rarr;</span>
        </a>
        @endforeach
    </div>

    <!-- Popular Articles -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-12">
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-10 flex items-center gap-3">
             <i class="fas fa-star text-amber-500"></i> Tópicos Populares
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
            @foreach($popularArticles as $article)
            <a href="{{ route('kb.article', $article->slug) }}" class="flex items-center justify-between group py-4 border-b border-white/5 hover:border-blue-500/30 transition-all">
                <div class="flex items-center gap-4">
                    <i class="far fa-file-alt text-zinc-700 group-hover:text-blue-500 transition-colors"></i>
                    <span class="text-sm font-bold text-zinc-300 group-hover:text-white transition-colors">{{ $article->title }}</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-zinc-800 group-hover:text-blue-500 transition-colors"></i>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
