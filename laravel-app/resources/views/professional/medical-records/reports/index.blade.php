@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-file-medical-alt text-blue-500"></i>
            Laudos
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-report')" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Novo Laudo
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($reports as $report)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="text-lg font-black text-white group-hover:text-blue-500 transition-colors">{{ $report->title }}</h4>
                        <p class="text-zinc-500 text-xs font-medium uppercase mt-1">{{ $report->date->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('professional.patients.medical-records.reports.download', [$patient->id, $report->id]) }}" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-white transition-all"><i class="fas fa-file-pdf"></i></a>
                        <a href="#" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-white transition-all"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
                <p class="text-zinc-400 text-sm line-clamp-3 mb-4">{{ $report->description }}</p>
                <div class="flex items-center justify-between pt-4 border-t border-zinc-800">
                    <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Responsável: {{ auth()->user()->name }}</span>
                    <button class="text-blue-500 text-xs font-bold hover:underline">Ver Detalhes</button>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-file-medical-alt text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhum laudo emitido</h4>
                <p class="text-zinc-500 text-sm">Os laudos técnicos e pareceres emitidos aparecerão aqui.</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
</div>

<x-modal name="add-report" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.reports.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Novo Laudo</h2>
        
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Título do Laudo</label>
                <input type="text" name="title" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: Laudo Nutricional - Evolução Trimestral">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Data</label>
                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Descrição Técnica</label>
                <textarea name="description" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Descreva os achados e análises..."></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Conclusão</label>
                <textarea name="conclusion" rows="2" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Qual o parecer final?"></textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-500 transition-all">Salvar Laudo</button>
        </div>
    </form>
</x-modal>
@endsection
