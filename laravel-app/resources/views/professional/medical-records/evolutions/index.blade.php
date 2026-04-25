@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-notes-medical text-blue-500"></i>
            Evolução e Atendimentos
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-evolution')" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Novo Atendimento
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[1.5rem] p-4 flex flex-wrap gap-4 items-center">
        <form method="GET" class="flex flex-wrap gap-4 items-center w-full">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500"></i>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full bg-zinc-800 border-none rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <button type="submit" class="px-6 py-3 bg-zinc-800 text-white rounded-xl font-bold hover:bg-zinc-700 transition-all">
                Filtrar
            </button>
            @if(request()->anyFilled(['date']))
                <a href="{{ route('professional.patients.medical-records.evolutions.index', $patient->id) }}" class="text-zinc-500 hover:text-white text-sm font-bold">Limpar Filtros</a>
            @endif
        </form>
    </div>

    <!-- Evolutions List -->
    <div class="space-y-4">
        @forelse($evolutions as $evolution)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group">
                <div class="flex flex-col md:flex-row justify-between gap-6">
                    <div class="space-y-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-blue-500/10 text-blue-500 rounded-lg text-xs font-black uppercase">
                                    {{ $evolution->type ?: 'Consulta Geral' }}
                                </span>
                                <span class="text-zinc-500 text-sm font-medium">
                                    {{ $evolution->date->format('d/m/Y - H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Queixa Principal</p>
                                <p class="text-white text-sm">{{ $evolution->chief_complaint ?: '--' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Avaliação / Diagnóstico</p>
                                <p class="text-white text-sm">{{ $evolution->assessment ?: '--' }}</p>
                            </div>
                        </div>

                        <div class="space-y-1 pt-2 border-t border-zinc-800">
                            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Conduta / Recomendações</p>
                            <p class="text-zinc-300 text-sm leading-relaxed">{{ $evolution->conduct ?: 'Sem recomendações registradas.' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex md:flex-col gap-2 justify-end">
                        <button class="p-3 bg-zinc-800 text-zinc-400 rounded-xl hover:text-white hover:bg-zinc-700 transition-all" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="p-3 bg-zinc-800 text-zinc-400 rounded-xl hover:text-white hover:bg-zinc-700 transition-all" title="Ver Anexos">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-notes-medical text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhum atendimento registrado</h4>
                <p class="text-zinc-500 text-sm">Inicie o acompanhamento clínico deste paciente registrando sua primeira evolução.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $evolutions->links() }}
        </div>
    </div>
</div>

<!-- Modal Novo Atendimento -->
<x-modal name="add-evolution" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.evolutions.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Novo Atendimento</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Data e Hora</label>
                <input type="datetime-local" name="date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Tipo de Atendimento</label>
                <select name="type" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                    <option value="Consulta Inicial">Consulta Inicial</option>
                    <option value="Retorno">Retorno</option>
                    <option value="Acompanhamento">Acompanhamento</option>
                    <option value="Online">Online</option>
                </select>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Queixa Principal</label>
                <textarea name="chief_complaint" rows="2" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="O que trouxe o paciente hoje?"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Avaliação Clínica</label>
                <textarea name="assessment" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Resultados de exames físicos, observações clínicas..."></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Diagnóstico</label>
                <textarea name="diagnosis" rows="2" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Diagnóstico identificado..."></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Conduta e Recomendações</label>
                <textarea name="conduct" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Plano de ação, novos medicamentos, exercícios..."></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Observações Internas</label>
                <textarea name="observations" rows="2" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Notas que não aparecem para o paciente (opcional)"></textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">
                Cancelar
            </button>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
                Salvar Atendimento
            </button>
        </div>
    </form>
</x-modal>
@endsection
