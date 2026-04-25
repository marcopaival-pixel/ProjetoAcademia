@extends('layouts.app')

@section('title', 'Central de Treinamento')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-white/5">
        <div>
            <h2 class="text-4xl font-black text-white tracking-tight">Academia NexShape</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Domine todas as funcionalidades da plataforma</p>
        </div>
        <div class="flex items-center gap-4 bg-zinc-900/40 border border-white/5 px-6 py-3 rounded-2xl shadow-xl">
             <div class="flex -space-x-2">
                @for($i=1; $i<=3; $i++)
                    <img src="https://i.pravatar.cc/100?u={{ $i }}" class="w-6 h-6 rounded-full border-2 border-zinc-900 shadow-xl">
                @endfor
             </div>
             <p class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">+120 alunos aprendendo agora</p>
        </div>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($modules as $module)
        <a href="{{ route('training.module', $module) }}" class="group relative bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden hover:bg-zinc-900/60 hover:border-blue-500/30 transition-all shadow-2xl flex flex-col h-full">
            <div class="aspect-video w-full bg-zinc-950 overflow-hidden">
                @if($module->image)
                    <img src="{{ $module->image }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-60">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-600/10 to-transparent italic text-zinc-800 text-6xl font-black uppercase">
                        {{ substr($module->title, 0, 1) }}
                    </div>
                @endif
                <div class="absolute top-6 right-6 px-3 py-1 bg-blue-600 text-[8px] text-white font-black uppercase tracking-widest rounded-lg shadow-xl shadow-blue-600/30">
                    {{ $module->lessons_count }} aulas
                </div>
            </div>
            
            <div class="p-8 flex flex-col flex-grow">
                <h3 class="text-xl font-black text-white mb-3 group-hover:text-blue-400 transition-colors">{{ $module->title }}</h3>
                <p class="text-xs text-zinc-600 font-medium leading-relaxed line-clamp-2">{{ $module->description ?? 'Aprenda o passo a passo completo neste módulo.' }}</p>
                <div class="mt-auto pt-8 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="far fa-clock text-zinc-800 text-[10px]"></i>
                        <span class="text-[9px] text-zinc-700 font-bold uppercase tracking-tight">Conteúdo Vitalício</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-white group-hover:bg-blue-600 group-hover:scale-110 transition-all">
                        <i class="fas fa-play text-[10px]"></i>
                    </div>
                </div>
            </div>
        </a>
        @empty
         <div class="col-span-full py-20 bg-zinc-950/30 border border-dashed border-white/5 rounded-[3rem] text-center">
            <i class="fas fa-video-slash text-zinc-800 text-4xl mb-6"></i>
            <p class="text-xs text-zinc-600 font-black uppercase tracking-widest">Nenhum módulo de treinamento disponível no momento</p>
         </div>
        @endforelse
    </div>

    <!-- Quick Manual Card -->
    <div class="relative bg-gradient-to-br from-indigo-600/20 to-zinc-900/40 border border-indigo-500/10 rounded-[3rem] p-12 overflow-hidden shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
            <div class="space-y-4 text-center md:text-left">
                <h3 class="text-2xl font-black text-white tracking-tight">Prefere ler o Manual?</h3>
                <p class="text-sm text-zinc-400 max-w-xl">Baixe nosso guia completo em PDF com capturas de tela e tutoriais passo a passo para configurar toda a sua academia em minutos.</p>
            </div>
            <a href="#" class="px-10 py-5 bg-white text-zinc-950 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-200 transition-all shadow-xl shadow-white/5 flex items-center gap-3 whitespace-nowrap">
                <i class="fas fa-download"></i> Baixar Guia (PDF)
            </a>
        </div>
        <i class="fas fa-book-open absolute -right-6 -bottom-6 text-9xl text-white/5 -rotate-12"></i>
    </div>
</div>
@endsection
