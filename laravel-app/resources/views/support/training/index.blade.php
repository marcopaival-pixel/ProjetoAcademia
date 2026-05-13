@extends('layouts.app')

@section('title', 'Central de Treinamento')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10 pb-12 border-b border-white/5">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest shadow-inner">Hub de Conhecimento</span>
                <span class="text-zinc-800">•</span>
                <span class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">NexShape Academy</span>
            </div>
            <h2 class="text-5xl font-black text-white tracking-tighter italic uppercase leading-none">Academia <span class="text-blue-500">NexShape</span></h2>
            <p class="text-zinc-500 font-medium max-w-xl">Aprenda a extrair o máximo potencial do seu ecossistema de performance com tutoriais guiados pela nossa equipe.</p>
        </div>
        
        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl flex flex-col md:flex-row items-center gap-8 min-w-[400px]">
             <div class="relative w-20 h-20 shrink-0">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="6" fill="transparent" class="text-zinc-800" />
                    <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="6" fill="transparent" class="text-blue-500 transition-all duration-1000 ease-out" stroke-dasharray="226.2" stroke-dashoffset="{{ 226.2 * (1 - $globalProgress/100) }}" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-black text-white tabular-nums">{{ $globalProgress }}%</span>
                </div>
             </div>
             <div class="text-center md:text-left space-y-1">
                <p class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em] mb-1">Status de Certificação</p>
                <h4 class="text-lg font-black text-white uppercase italic tracking-tight">{{ $completedLessons }} / {{ $totalLessons }} <span class="text-zinc-700 not-italic font-light tracking-widest text-xs ml-1">Aulas</span></h4>
                <p class="text-[10px] text-blue-500/60 font-bold italic uppercase tracking-tighter">{{ $globalProgress == 100 ? 'NexMaster Certificado' : 'Evoluindo para o Próximo Nível' }}</p>
             </div>
        </div>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($modules as $module)
        @php $modProgress = $module->getProgressForUser(auth()->user()); @endphp
        <a href="{{ route('training.module', $module) }}" class="group relative bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden hover:bg-zinc-900/60 hover:border-blue-500/30 transition-all shadow-2xl flex flex-col h-full hover:scale-[1.02] duration-500">
            <div class="aspect-video w-full bg-zinc-950 overflow-hidden relative">
                @if($module->image)
                    <img src="{{ $module->image }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-40 group-hover:opacity-60">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-600/10 to-transparent italic text-zinc-800 text-6xl font-black uppercase">
                        {{ substr($module->title, 0, 1) }}
                    </div>
                @endif
                
                <!-- Progress Overlay -->
                @if($modProgress > 0)
                <div class="absolute bottom-0 left-0 w-full h-1.5 bg-zinc-800">
                    <div class="h-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ $modProgress }}%"></div>
                </div>
                @endif

                <div class="absolute top-6 right-6 flex flex-col gap-2">
                    <span class="px-3 py-1 bg-zinc-950/80 backdrop-blur-md border border-white/10 text-[8px] text-white font-black uppercase tracking-widest rounded-lg shadow-xl">
                        {{ $module->lessons_count }} aulas
                    </span>
                    @if($modProgress == 100)
                    <span class="px-3 py-1 bg-emerald-500 text-[8px] text-zinc-950 font-black uppercase tracking-widest rounded-lg shadow-xl shadow-emerald-500/20">
                        <i class="fas fa-check mr-1"></i> Concluído
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="p-10 flex flex-col flex-grow">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-black text-white group-hover:text-blue-400 transition-colors uppercase italic tracking-tight">{{ $module->title }}</h3>
                    <span class="text-[9px] font-black text-zinc-700 tabular-nums">{{ $modProgress }}%</span>
                </div>
                <p class="text-xs text-zinc-600 font-medium leading-relaxed line-clamp-2">{{ $module->description ?? 'Aprenda o passo a passo completo neste módulo.' }}</p>
                
                <div class="mt-auto pt-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-800 group-hover:text-blue-500 transition-colors">
                            <i class="fas fa-play text-[9px]"></i>
                        </div>
                        <span class="text-[9px] text-zinc-700 font-black uppercase tracking-widest group-hover:text-zinc-400 transition-colors">Acessar Aulas</span>
                    </div>
                    <i class="fas fa-chevron-right text-zinc-800 text-[10px] group-hover:translate-x-1 transition-transform"></i>
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
