@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-lock text-violet-400"></i>
            Registros de sessao
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-session-note')" class="px-6 py-3 bg-violet-600 text-white rounded-2xl font-black hover:bg-violet-500 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Novo Registro
        </button>
    </div>

    <div class="rounded-2xl border border-violet-500/20 bg-violet-500/10 p-4 text-sm text-violet-100">
        Area restrita. O acesso depende de permissao sensivel concedida pelo paciente e gera auditoria.
    </div>

    <div class="space-y-4">
        @forelse($sessionNotes as $note)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <span class="px-3 py-1 bg-violet-500/10 text-violet-300 rounded-lg text-xs font-black uppercase">Sessao</span>
                    <span class="text-zinc-500 text-sm font-bold">{{ $note->date?->format('d/m/Y H:i') }}</span>
                </div>
                @if($note->chief_complaint)
                    <div>
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Demanda</p>
                        <p class="text-white text-sm mt-1">{{ $note->chief_complaint }}</p>
                    </div>
                @endif
                @if($note->assessment)
                    <div>
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Registro tecnico</p>
                        <p class="text-zinc-300 text-sm mt-1 leading-relaxed">{{ $note->assessment }}</p>
                    </div>
                @endif
                @if($note->conduct)
                    <div>
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Conduta</p>
                        <p class="text-zinc-300 text-sm mt-1 leading-relaxed">{{ $note->conduct }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <h4 class="text-white font-bold mb-2">Nenhum registro restrito</h4>
                <p class="text-zinc-500 text-sm">Registre notas sensiveis de sessao somente quando necessario.</p>
            </div>
        @endforelse
    </div>

    {{ $sessionNotes->links() }}
</div>

<x-modal name="add-session-note" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.session-notes.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Novo Registro de Sessao</h2>
        <div class="space-y-5">
            <input type="datetime-local" name="date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-violet-500">
            <textarea name="chief_complaint" rows="2" placeholder="Demanda principal" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-violet-500"></textarea>
            <textarea name="assessment" rows="5" placeholder="Registro tecnico da sessao" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-violet-500"></textarea>
            <textarea name="conduct" rows="3" placeholder="Conduta / encaminhamentos" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-violet-500"></textarea>
            <textarea name="observations" rows="2" placeholder="Observacoes internas" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-violet-500"></textarea>
        </div>
        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-violet-600 text-white rounded-xl font-black hover:bg-violet-500 transition-all">Salvar</button>
        </div>
    </form>
</x-modal>
@endsection
