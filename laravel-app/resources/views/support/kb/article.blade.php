@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in space-y-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-500">
        <a href="{{ route('kb.index') }}" class="hover:text-white transition-colors">Base de Conhecimento</a>
        <i class="fas fa-chevron-right text-[8px] text-zinc-800"></i>
        <a href="{{ route('kb.category', $article->category) }}" class="hover:text-white transition-colors">{{ $article->category->name }}</a>
    </nav>

    <!-- Post Content -->
    <article class="bg-zinc-900/40 border border-white/5 rounded-[3rem] p-12 lg:p-16 shadow-2xl">
        <header class="mb-12 pb-12 border-b border-white/5">
            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">{{ $article->title }}</h1>
            <div class="flex items-center gap-6 mt-8">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest italic">Visualizações:</span>
                    <span class="text-[10px] text-zinc-400 font-bold tracking-tight">{{ number_format($article->views) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest italic">Última atualização:</span>
                    <span class="text-[10px] text-zinc-400 font-bold tracking-tight">{{ $article->updated_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </header>

        <div class="prose prose-invert max-w-none text-zinc-300 prose-headings:text-white prose-a:text-blue-500 prose-img:rounded-3xl leading-relaxed">
            {!! nl2br($article->content) !!}
        </div>

        @if($article->tags)
        <div class="mt-16 pt-8 border-t border-white/5 flex flex-wrap gap-2">
            @foreach(explode(',', $article->tags) as $tag)
            <span class="px-3 py-1 bg-white/5 text-[9px] text-zinc-500 font-black uppercase tracking-widest rounded-lg">#{{ trim($tag) }}</span>
            @endforeach
        </div>
        @endif
    </article>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-10">
        <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-6">Artigos Relacionados</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedArticles as $related)
            <a href="{{ route('kb.article', $related->slug) }}" class="p-6 bg-zinc-950/50 border border-white/5 rounded-2xl group hover:border-blue-500/30 transition-all">
                <h5 class="text-xs font-black text-white group-hover:text-blue-400 transition-colors leading-tight mb-2">{{ $related->title }}</h5>
                <span class="text-[8px] text-zinc-600 font-bold italic">{{ $related->updated_at->diffForHumans() }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Feedback -->
    <div class="bg-blue-600/10 border border-blue-500/20 rounded-[2.5rem] p-8 text-center space-y-4">
        <h4 class="text-sm font-black text-white">Este artigo foi útil?</h4>
        <div class="flex justify-center gap-4">
            <button class="px-6 py-2 bg-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-white hover:bg-emerald-500 transition-all flex items-center gap-2">
                <i class="far fa-thumbs-up"></i> Sim
            </button>
            <button class="px-6 py-2 bg-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-white hover:bg-red-500 transition-all flex items-center gap-2">
                <i class="far fa-thumbs-down"></i> Não
            </button>
        </div>
    </div>
</div>
@endsection
