@extends('layouts.app')

@section('title', 'Meus Atendimentos')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('patient.reports.index') }}" class="text-zinc-500 hover:text-white flex items-center gap-2 font-bold transition-colors">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Hub de Inteligência
        </a>
        <h1 class="text-2xl font-black text-white">Meus <span class="text-blue-500">Atendimentos</span></h1>
    </div>

    <div class="space-y-4">
        @forelse($evolutions as $evolution)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-500 rounded-lg text-xs font-black uppercase">
                        {{ $evolution->type ?: 'Consulta' }}
                    </span>
                    <span class="text-zinc-500 text-sm font-bold">{{ $evolution->date->format('d/m/Y \à\s H:i') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Avaliação do Profissional</p>
                        <p class="text-white text-sm leading-relaxed">{{ $evolution->assessment ?: 'Sem detalhes registrados.' }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Conduta e Orientações</p>
                        <p class="text-white text-sm leading-relaxed">{{ $evolution->conduct ?: 'Sem orientações registradas.' }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-zinc-800 rounded-full flex items-center justify-center text-xs text-zinc-500">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <span class="text-xs text-zinc-400 font-bold">Profissional: <span class="text-white">{{ $evolution->professional->name }}</span></span>
                    </div>
                    @if($evolution->attachments)
                        <button class="px-4 py-2 bg-zinc-800 text-zinc-300 rounded-xl text-xs font-bold hover:bg-zinc-700 transition-all flex items-center gap-2">
                            <i class="fas fa-paperclip"></i>
                            Ver Anexos
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-zinc-900 border border-dashed border-zinc-800 rounded-[2.5rem] p-16 text-center">
                <p class="text-zinc-500 italic">Nenhum atendimento registrado por este profissional.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $evolutions->links() }}
        </div>
    </div>
</div>
@endsection
