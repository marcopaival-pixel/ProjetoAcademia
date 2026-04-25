@extends('layouts.app')

@section('title', $exercise->name)

@section('header_title', $exercise->name)
@section('header_subtitle', 'Detalhes técnicos e execução do exercício')

@section('content')
<div class="animate-fade-in max-w-5xl mx-auto">
    <a href="{{ route('exercise') }}" class="inline-flex items-center gap-2 text-zinc-500 hover:text-white mb-10 transition-colors group">
        <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
        <span class="text-[10px] font-black uppercase tracking-widest">Voltar ao Mural</span>
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Media & Info Block -->
        <div class="lg:col-span-12">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-10 rounded-[3rem] shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Media Section -->
                    <div class="space-y-6">
                        @if($exercise->video_url)
                            <div class="aspect-video rounded-[2rem] overflow-hidden bg-zinc-950 border border-white/10 relative group">
                                @php
                                    $vUrl = $exercise->video_url;
                                    if(str_contains($vUrl, 'youtube.com/watch?v=')) {
                                        $vId = explode('v=', $vUrl)[1];
                                        $vId = explode('&', $vId)[0];
                                        $vUrl = "https://www.youtube.com/embed/" . $vId;
                                    } elseif(str_contains($vUrl, 'youtu.be/')) {
                                        $vId = explode('youtu.be/', $vUrl)[1];
                                        $vUrl = "https://www.youtube.com/embed/" . $vId;
                                    }
                                @endphp
                                <iframe class="w-full h-full" src="{{ $vUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @else
                            <div class="aspect-video rounded-[2rem] border border-dashed border-white/10 flex flex-col items-center justify-center text-zinc-700 space-y-4">
                                <i class="fas fa-video-slash text-5xl"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Vídeo Indisponível</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5 text-center">
                                <span class="text-[8px] text-zinc-500 font-black uppercase tracking-widest block mb-2">Grupo</span>
                                <span class="text-xs text-white font-black truncate">{{ $exercise->muscle_group }}</span>
                            </div>
                            <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5 text-center">
                                <span class="text-[8px] text-zinc-500 font-black uppercase tracking-widest block mb-2">Nível</span>
                                <span class="text-xs text-white font-black truncate">{{ ucfirst($exercise->difficulty) }}</span>
                            </div>
                            <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5 text-center">
                                <span class="text-[8px] text-zinc-500 font-black uppercase tracking-widest block mb-2">Material</span>
                                <span class="text-xs text-white font-black truncate">{{ $exercise->equipment ?: 'Livre' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Section -->
                    <div class="flex flex-col h-full">
                        <div class="flex-1 space-y-8">
                            <div>
                                <h3 class="text-2xl font-black text-white italic tracking-tight mb-6">Guia de Execução</h3>
                                <div class="prose prose-invert prose-sm max-w-none text-zinc-400 leading-relaxed bg-zinc-950/30 p-8 rounded-[2rem] border border-white/5">
                                    {!! nl2br(e($exercise->instructions ?: 'Nenhuma instrução detalhada cadastrada para este exercício.')) !!}
                                </div>
                            </div>

                            @if($exercise->tips)
                            <div class="bg-blue-600/10 border border-blue-500/20 p-8 rounded-[2.5rem]">
                                <h5 class="flex items-center gap-3 text-blue-400 font-black text-[10px] uppercase tracking-widest mb-4">
                                    <i class="fas fa-lightbulb"></i> Dica de Especialista
                                </h5>
                                <p class="text-sm text-zinc-300 leading-relaxed italic">
                                    {{ $exercise->tips }}
                                </p>
                            </div>
                            @endif
                        </div>

                        <div class="mt-8 pt-8 border-t border-white/5 flex gap-4">
                            <button onclick="window.print()" class="flex-1 py-4 bg-zinc-950 border border-white/5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white hover:bg-zinc-900 transition-all">
                                <i class="fas fa-print mr-2"></i> Imprimir Guia
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
