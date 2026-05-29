@extends('layouts.app')

@section('title', $article->titulo . ' — NexShape Help')

@section('content')
<div class="py-12 px-4 md:px-8 max-w-4xl mx-auto animate-kb-entry">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-600 mb-10">
        <a href="{{ route('kb.index') }}" class="hover:text-blue-400 transition-colors">Central de Ajuda</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-zinc-500">{{ $article->category->nome }}</span>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-blue-400">{{ $article->titulo }}</span>
    </nav>

    <!-- Article Content -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] overflow-hidden shadow-2xl">
        <div class="p-8 md:p-16 space-y-10">
            <div class="space-y-4">
                <span class="px-4 py-1.5 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">{{ $article->tipo_usuario }}</span>
                <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter italic leading-none">{{ $article->titulo }}</h1>
                <div class="flex items-center gap-4 text-zinc-500 text-xs font-bold uppercase tracking-widest pt-2">
                    <span>Atualizado em {{ $article->updated_at->format('d/m/Y') }}</span>
                    <span>•</span>
                    <span>Guia de Referência</span>
                </div>
            </div>

            <div class="prose prose-invert prose-zinc max-w-none text-zinc-400 font-medium text-lg leading-relaxed
                prose-headings:text-white prose-headings:font-black prose-headings:italic prose-headings:tracking-tighter
                prose-a:text-blue-400 prose-a:no-underline hover:prose-a:text-blue-300
                prose-strong:text-white prose-strong:font-black
                prose-p:mb-6">
                {!! $article->conteudo !!}
            </div>

            <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8" x-data="{ voted: false }">
                <div>
                    <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest" x-show="!voted">Isso foi útil?</p>
                    <p class="text-emerald-500 text-sm font-bold uppercase tracking-widest" style="display: none;" x-show="voted" x-transition>Obrigado pelo seu feedback!</p>
                    <div class="flex gap-4 mt-4" x-show="!voted">
                        <button @click="voted = true" type="button" class="px-8 py-3 bg-zinc-800 text-white font-black rounded-2xl hover:bg-emerald-600 transition-all text-xs border border-white/5 flex items-center gap-2">
                            <i class="fas fa-thumbs-up"></i> SIM
                        </button>
                        <button @click="voted = true" type="button" class="px-8 py-3 bg-zinc-800 text-white font-black rounded-2xl hover:bg-rose-600 transition-all text-xs border border-white/5 flex items-center gap-2">
                            <i class="fas fa-thumbs-down"></i> NÃO
                        </button>
                    </div>
                </div>
                
                <a href="{{ route('kb.index') }}" class="px-8 py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-blue-500 hover:text-white transition-all text-xs shadow-xl uppercase tracking-widest">
                    Voltar para Central
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes kb-entry { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-kb-entry { animation: kb-entry 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>
@endsection
