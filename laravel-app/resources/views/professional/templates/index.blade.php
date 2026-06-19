@extends('layouts.app')

@section('title', 'Meus Templates — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Produtividade Pro</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Meus <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Templates</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Crie modelos de prescrição e orientações para agilizar seu atendimento no Wizard AI.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('professional.templates.create') }}" class="px-8 py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-blue-400 hover:text-white transition-all shadow-2xl flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                NOVO TEMPLATE
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($templates as $template)
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] overflow-hidden shadow-2xl transition-all hover:border-blue-500/20">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-[50px] -mr-16 -mt-16 rounded-full group-hover:bg-blue-500/10 transition-all"></div>
                
                <div class="relative z-10 space-y-6">
                    <div class="flex justify-between items-start">
                        <span class="px-3 py-1 bg-white/5 text-blue-400 text-[9px] font-black uppercase rounded-full border border-blue-400/20 tracking-widest">{{ $template->specialty->nome }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('professional.templates.edit', $template) }}" class="p-2 bg-zinc-950/50 rounded-xl text-zinc-500 hover:text-white transition-all border border-white/5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>
                            <form action="{{ route('professional.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Excluir este template?')">
                                @csrf @method('DELETE')
                                <button class="p-2 bg-zinc-950/50 rounded-xl text-zinc-500 hover:text-red-400 transition-all border border-white/5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </form>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-black text-white group-hover:text-blue-400 transition-colors">{{ $template->title }}</h3>
                    
                    <p class="text-xs text-zinc-500 line-clamp-4 font-medium leading-relaxed">{{ $template->content }}</p>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-zinc-900/20 border border-dashed border-white/5 rounded-[3rem]">
                <p class="text-zinc-500 font-bold uppercase tracking-widest">Nenhum template criado ainda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection



