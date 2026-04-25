@extends('layouts.app')

@section('title', $module->title)

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in space-y-10">
    <!-- Breadcrumbs & Header -->
    <div class="space-y-6">
        <a href="{{ route('training.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-[8px]"></i> Voltar para Módulos
        </a>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-2 border-b border-white/5">
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight">{{ $module->title }}</h1>
                <p class="text-xs text-zinc-600 font-bold uppercase tracking-widest mt-1">{{ $lessons->count() }} aulas disponíveis</p>
            </div>
             <div class="px-6 py-3 bg-zinc-900/40 border border-white/5 rounded-2xl">
                <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Seu Progresso</p>
                <div class="flex items-center gap-3">
                    <div class="w-32 h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: 0%"></div>
                    </div>
                    <span class="text-[10px] text-white font-bold">0%</span>
                </div>
            </div>
        </div>
        <p class="text-sm text-zinc-400 leading-relaxed max-w-3xl">{{ $module->description }}</p>
    </div>

    <!-- Lessons List -->
    <div class="space-y-4 pb-20">
        @foreach($lessons as $index => $lesson)
        <a href="{{ route('training.lesson', [$module, $lesson]) }}" class="group flex items-center justify-between p-6 bg-zinc-900/40 border border-white/5 rounded-3xl hover:bg-zinc-900/60 hover:border-blue-500/30 transition-all shadow-xl">
            <div class="flex items-center gap-6">
                <div class="w-12 h-12 rounded-2xl bg-zinc-950 flex items-center justify-center text-xs font-black text-zinc-700 group-hover:text-blue-500 group-hover:bg-blue-600/10 transition-all border border-white/5 shadow-inner">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </div>
                <div>
                    <h3 class="text-sm font-black text-white group-hover:text-blue-400 transition-colors">{{ $lesson->title }}</h3>
                    <div class="flex items-center gap-3 mt-1">
                        @if($lesson->video_url)
                            <span class="flex items-center gap-1.5 text-[8px] text-zinc-600 font-bold uppercase">
                                <i class="fas fa-video text-[7px] text-zinc-800"></i> Videoaula
                            </span>
                        @endif
                        @if($lesson->content)
                            <span class="flex items-center gap-1.5 text-[8px] text-zinc-600 font-bold uppercase">
                                <i class="fas fa-file-text text-[7px] text-zinc-800"></i> Material Complementar
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="w-10 h-10 rounded-full border border-white/5 flex items-center justify-center text-zinc-800 group-hover:text-blue-500 group-hover:scale-110 transition-all">
                 <i class="fas fa-chevron-right text-[10px]"></i>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
