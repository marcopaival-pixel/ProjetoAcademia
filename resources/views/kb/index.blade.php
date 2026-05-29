@extends('layouts.app')

@section('title', 'Central de Ajuda — NexShape')

@section('content')
<div class="py-12 px-4 md:px-8 max-w-7xl mx-auto space-y-12 animate-kb-entry">
    <!-- Header: Search Section -->
    <div class="relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-12 overflow-hidden group shadow-2xl">
        <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        <div class="relative z-10 text-center space-y-6">
            <div class="flex flex-col items-center">
                <span class="px-4 py-1.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] border border-blue-500/20 shadow-[0_0_20px_rgba(59,130,246,0.15)] mb-6">Suporte {{ $userType }}</span>
                <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-none italic uppercase">
                    Central de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-emerald-400">Inteligência</span>
                </h1>
                <p class="text-zinc-500 font-medium text-lg mt-4 max-w-2xl mx-auto">Tudo o que você precisa para dominar o ecossistema NexShape e acelerar seus resultados.</p>
            </div>

            <form action="{{ route('kb.index') }}" method="GET" class="max-w-2xl mx-auto relative group/search">
                <input type="text" name="search" value="{{ $search }}" placeholder="Qual a sua dúvida hoje?" 
                    class="w-full bg-zinc-950/80 border-2 border-white/5 rounded-[2rem] py-6 px-8 text-white placeholder-zinc-700 focus:border-blue-500/50 focus:ring-0 transition-all text-lg font-bold shadow-3xl">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 w-14 h-14 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center transition-all active:scale-95 shadow-lg shadow-blue-500/20">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </form>
        </div>
    </div>

    @if($search)
        <!-- Search Results -->
        <div class="space-y-8">
            <div class="flex items-center justify-between border-b border-white/5 pb-6">
                <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Resultados para: <span class="text-blue-400">"{{ $search }}"</span></h3>
                <a href="{{ route('kb.index') }}" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest hover:text-white transition-colors">Limpar Busca &times;</a>
            </div>

            @if($articles && $articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                    <a href="{{ route('kb.show', $article->slug) }}" class="group bg-zinc-900/60 backdrop-blur-2xl border border-white/5 p-8 rounded-[3rem] transition-all hover:border-blue-500/30 hover:-translate-y-1 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-zinc-950 rounded-full text-zinc-500 text-[9px] font-black uppercase tracking-widest border border-white/5">{{ $article->category->nome }}</span>
                            <i class="fas fa-chevron-right text-zinc-800 group-hover:text-blue-400 transition-colors text-xs"></i>
                        </div>
                        <h4 class="text-xl font-black text-white leading-tight group-hover:text-blue-400 transition-colors">{{ $article->titulo }}</h4>
                        <p class="text-zinc-500 text-sm mt-3 line-clamp-2 font-medium">{{ strip_tags($article->conteudo) }}</p>
                    </a>
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-zinc-900/20 rounded-[4rem] border border-dashed border-white/5">
                    <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search-minus text-3xl text-zinc-600"></i>
                    </div>
                    <h4 class="text-zinc-500 font-black text-xl">Nenhum artigo encontrado.</h4>
                    <p class="text-zinc-700 text-sm mt-2">Tente usar palavras-chave mais simples.</p>
                </div>
            @endif
        </div>
    @else
        <!-- Categories Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-8 space-y-12">
                @foreach($categories as $category)
                    @if($category->articles->count() > 0)
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                            <div class="w-10 h-10 bg-blue-600/10 rounded-xl flex items-center justify-center border border-blue-500/10 text-blue-400">
                                <i class="fas fa-folder-open text-xs"></i>
                            </div>
                            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">{{ $category->nome }}</h3>
                            <span class="text-zinc-700 text-sm font-bold uppercase ml-auto">{{ $category->articles->count() }} artigos</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($category->articles->take(4) as $article)
                            <a href="{{ route('kb.show', $article->slug) }}" class="group flex items-center gap-6 p-6 bg-white/5 rounded-[2rem] border border-white/5 hover:border-blue-500/20 transition-all shadow-lg">
                                <div class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-500 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-xl">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-white font-black text-sm group-hover:text-blue-400 transition-colors leading-tight">{{ $article->titulo }}</h4>
                                    <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest mt-1">Guia Completo</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-10">
                <!-- Help Status -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-10 rounded-[4rem] shadow-2xl text-white relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10 text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/20 mx-auto mb-6 shadow-2xl">
                            <i class="fas fa-headset text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-black italic tracking-tighter uppercase mb-2">Suporte Direto</h3>
                        <p class="text-sm font-medium opacity-80 mb-8 leading-relaxed">Não encontrou o que procurava? Nossa equipe técnica está disponível para suporte personalizado.</p>
                        <a href="{{ route('support.tickets.create') }}" class="block w-full py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-zinc-900 hover:text-white transition-all shadow-xl uppercase text-xs tracking-widest">Abrir Ticket</a>
                    </div>
                </div>

                <!-- Featured Card -->
                <div class="bg-zinc-900/60 border border-white/5 p-10 rounded-[4rem] shadow-2xl">
                    <h4 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-6 italic">Principais Dúvidas</h4>
                    <div class="space-y-6">
                        @foreach($categories->flatMap->articles->take(5) as $popular)
                        <a href="{{ route('kb.show', $popular->slug) }}" class="flex items-center gap-4 group">
                            <div class="w-2 h-2 rounded-full bg-blue-500 group-hover:scale-150 transition-transform shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                            <span class="text-sm font-black text-zinc-400 group-hover:text-white transition-colors">{{ $popular->titulo }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
@keyframes kb-entry { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-kb-entry { animation: kb-entry 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>
@endsection
