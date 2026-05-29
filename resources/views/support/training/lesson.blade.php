@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in grid grid-cols-1 lg:grid-cols-4 gap-10">
    <!-- Main Content (Video & Text) -->
    <div class="lg:col-span-3 space-y-8" x-data="{ cinemaMode: false }">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-600">
            <a href="{{ route('training.index') }}" class="hover:text-white transition-colors">Academia</a>
            <i class="fas fa-chevron-right text-[8px] text-zinc-800"></i>
            <a href="{{ route('training.module', $module) }}" class="hover:text-white transition-colors">{{ $module->title }}</a>
        </nav>

        <!-- Immersive Video Player Container -->
        <div class="relative transition-all duration-700 ease-in-out" 
             :class="cinemaMode ? 'fixed inset-0 z-[100] bg-zinc-950 p-10 flex flex-col items-center justify-center' : 'w-full'">
            
            <!-- Cinema Mode Toggle -->
            <button @click="cinemaMode = !cinemaMode" 
                    class="absolute top-6 right-6 z-[110] w-12 h-12 rounded-2xl bg-zinc-950/80 backdrop-blur-xl border border-white/10 flex items-center justify-center text-zinc-400 hover:text-blue-500 transition-all hover:scale-110 shadow-2xl">
                <i class="fas" :class="cinemaMode ? 'fa-compress' : 'fa-expand'"></i>
            </button>

            <div class="aspect-video w-full bg-black rounded-[3rem] overflow-hidden border border-white/5 shadow-[0_40px_100px_-20px_rgba(0,0,0,0.8)] relative group transition-all duration-700"
                 :class="cinemaMode ? 'max-w-6xl shadow-[0_0_150px_rgba(59,130,246,0.15)]' : ''">
                @if($lesson->video_url)
                    @php
                        $videoUrl = $lesson->video_url;
                        if(str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                            $videoId = str_contains($videoUrl, 'v=') ? explode('v=', $videoUrl)[1] : basename($videoUrl);
                            $videoId = explode('&', $videoId)[0];
                            $embedUrl = "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&showinfo=0";
                        } elseif(str_contains($videoUrl, 'vimeo.com')) {
                            $videoId = basename($videoUrl);
                            $embedUrl = "https://player.vimeo.com/video/{$videoId}?badge=0&autopause=0";
                        } else {
                            $embedUrl = $videoUrl;
                        }
                    @endphp
                    <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center space-y-6 bg-gradient-to-br from-zinc-900 to-black">
                        <div class="w-24 h-24 rounded-full bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-800 shadow-inner">
                            <i class="fas fa-video-slash text-4xl"></i>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Aguardando Processamento de Vídeo</p>
                    </div>
                @endif
            </div>

            <template x-if="cinemaMode">
                <div class="mt-8 flex items-center gap-4 animate-fade-in">
                    <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter">{{ $lesson->title }}</h2>
                    <button @click="cinemaMode = false" class="text-xs text-zinc-500 font-bold uppercase tracking-widest hover:text-white">Sair do Modo Cinema</button>
                </div>
            </template>
        </div>

        <!-- Lesson Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-10 border-b border-white/5">
            <div class="space-y-1">
                <h1 class="text-4xl font-black text-white tracking-tighter italic uppercase leading-tight">{{ $lesson->title }}</h1>
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $module->title }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                 <form action="{{ route('training.lesson.toggle-completion', $lesson) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-8 py-5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all shadow-2xl flex items-center gap-3 group {{ $isCompleted ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-white text-zinc-950 hover:bg-blue-600 hover:text-white' }}">
                        <i class="fas {{ $isCompleted ? 'fa-check-circle' : 'fa-circle' }} group-hover:scale-110 transition-transform"></i>
                        {{ $isCompleted ? 'Aula Concluída' : 'Marcar como Concluída' }}
                    </button>
                 </form>
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
                @php $isLCompleted = $l->completions->isNotEmpty(); @endphp
                <a href="{{ route('training.lesson', [$module, $l]) }}" class="flex items-center gap-4 p-4 rounded-2xl transition-all {{ $l->id == $lesson->id ? 'bg-blue-600/10 border border-blue-500/30' : 'bg-transparent border border-transparent hover:bg-white/5' }}">
                    <div class="w-8 h-8 flex-shrink-0 rounded-lg {{ $l->id == $lesson->id ? 'bg-blue-600 text-white' : ($isLCompleted ? 'bg-emerald-600/20 text-emerald-500' : 'bg-zinc-950 text-zinc-700 border border-white/5') }} flex items-center justify-center text-[10px] font-black">
                        @if($isLCompleted)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="text-[11px] font-bold {{ $l->id == $lesson->id ? 'text-white' : ($isLCompleted ? 'text-zinc-400' : 'text-zinc-500') }} leading-tight">{{ $l->title }}</span>
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
