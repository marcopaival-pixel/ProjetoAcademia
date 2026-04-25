@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in grid grid-cols-1 lg:grid-cols-4 gap-10">
    <!-- Main Content (Video & Text) -->
    <div class="lg:col-span-3 space-y-8">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-600">
            <a href="{{ route('training.index') }}" class="hover:text-white transition-colors">Academia</a>
            <i class="fas fa-chevron-right text-[8px] text-zinc-800"></i>
            <a href="{{ route('training.module', $module) }}" class="hover:text-white transition-colors">{{ $module->title }}</a>
        </nav>

        <!-- Video Player -->
        <div class="aspect-video w-full bg-zinc-950 rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl relative group">
            @if($lesson->video_url)
                @php
                    $videoUrl = $lesson->video_url;
                    if(str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                        $videoId = str_contains($videoUrl, 'v=') ? explode('v=', $videoUrl)[1] : basename($videoUrl);
                        $videoId = explode('&', $videoId)[0];
                        $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                    } elseif(str_contains($videoUrl, 'vimeo.com')) {
                        $videoId = basename($videoUrl);
                        $embedUrl = "https://player.vimeo.com/video/{$videoId}";
                    } else {
                        $embedUrl = $videoUrl;
                    }
                @endphp
                <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center space-y-4">
                    <i class="fas fa-video-slash text-zinc-800 text-6xl"></i>
                    <p class="text-xs text-zinc-700 font-black uppercase tracking-widest">Aguardando upload do vídeo</p>
                </div>
            @endif
        </div>

        <!-- Lesson Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-white/5">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">{{ $lesson->title }}</h1>
                <p class="text-[10px] text-blue-500 font-bold uppercase tracking-widest mt-1 italic">{{ $module->title }}</p>
            </div>
            <div class="flex items-center gap-3">
                 @if($prevLesson)
                    <a href="{{ route('training.lesson', [$module, $prevLesson]) }}" class="px-6 py-4 bg-zinc-900 border border-white/5 rounded-2xl text-[9px] text-zinc-400 font-black uppercase tracking-widest hover:text-white transition-all">
                        Anterior
                    </a>
                @endif
                @if($nextLesson)
                    <a href="{{ route('training.lesson', [$module, $nextLesson]) }}" class="px-8 py-4 bg-blue-600 rounded-2xl text-[9px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20">
                        Próxima Aula
                    </a>
                @endif
            </div>
        </div>

        <!-- Descrição / Material -->
        <div class="prose prose-invert max-w-none text-zinc-400 prose-headings:text-white prose-strong:text-zinc-200 leading-relaxed pb-20">
            {!! nl2br($lesson->content) !!}
        </div>
    </div>

    <!-- Sidebar (Lesson Menu) -->
    <div class="space-y-6">
        <div class="sticky top-24 bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
            <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                 <i class="fas fa-list text-zinc-700"></i> Conteúdo do Módulo
            </h4>
            <div class="space-y-3">
                @foreach($allLessons as $idx => $l)
                <a href="{{ route('training.lesson', [$module, $l]) }}" class="flex items-center gap-4 p-4 rounded-2xl transition-all {{ $l->id == $lesson->id ? 'bg-blue-600/10 border border-blue-500/30' : 'bg-transparent border border-transparent hover:bg-white/5' }}">
                    <div class="w-8 h-8 flex-shrink-0 rounded-lg {{ $l->id == $lesson->id ? 'bg-blue-600 text-white' : 'bg-zinc-950 text-zinc-700 border border-white/5' }} flex items-center justify-center text-[10px] font-black">
                        {{ $idx + 1 }}
                    </div>
                    <span class="text-[11px] font-bold {{ $l->id == $lesson->id ? 'text-white' : 'text-zinc-500' }} leading-tight">{{ $l->title }}</span>
                </a>
                @endforeach
            </div>
        </div>
        
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 text-center space-y-4">
             <i class="fas fa-headset text-zinc-800 text-3xl"></i>
             <p class="text-[10px] text-zinc-500 font-black uppercase tracking-tight leading-relaxed">Ficou com dúvida sobre esta aula?</p>
             <a href="{{ route('support.tickets.create') }}" class="block px-6 py-3 bg-white/5 text-[10px] text-white font-black uppercase tracking-widest rounded-xl hover:bg-white/10 transition-all border border-white/5">
                Abrir Ticket
             </a>
        </div>
    </div>
</div>
@endsection
