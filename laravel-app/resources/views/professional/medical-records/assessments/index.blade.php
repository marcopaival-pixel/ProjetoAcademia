@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-weight text-teal-400"></i>
            Avaliações
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-assessment')" class="px-6 py-3 bg-teal-600 text-white rounded-2xl font-black hover:bg-teal-500 transition-all shadow-lg shadow-teal-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Avaliação
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($assessments as $assessment)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data</p>
                        <h4 class="text-white font-black text-lg">{{ $assessment->assessment_date?->format('d/m/Y') ?? '--' }}</h4>
                    </div>
                    <span class="px-3 py-1 bg-teal-500/10 text-teal-300 rounded-full text-[9px] font-black uppercase tracking-widest">
                        {{ $assessment->status ?? 'approved' }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 bg-zinc-950/60 rounded-2xl border border-zinc-800">
                        <p class="text-[9px] text-zinc-500 font-black uppercase">Peso</p>
                        <p class="text-white font-black">{{ $assessment->weight_kg ?? '--' }}<span class="text-xs text-zinc-500"> kg</span></p>
                    </div>
                    <div class="p-3 bg-zinc-950/60 rounded-2xl border border-zinc-800">
                        <p class="text-[9px] text-zinc-500 font-black uppercase">Gordura</p>
                        <p class="text-white font-black">{{ $assessment->bf_percent ?? '--' }}<span class="text-xs text-zinc-500">%</span></p>
                    </div>
                    <div class="p-3 bg-zinc-950/60 rounded-2xl border border-zinc-800">
                        <p class="text-[9px] text-zinc-500 font-black uppercase">FC</p>
                        <p class="text-white font-black">{{ $assessment->heart_rate ?? '--' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs text-zinc-400">
                    <div>Cintura: <span class="text-white font-bold">{{ $assessment->waist ?? '--' }} cm</span></div>
                    <div>Tórax: <span class="text-white font-bold">{{ $assessment->chest ?? '--' }} cm</span></div>
                    <div>Quadril: <span class="text-white font-bold">{{ $assessment->hips ?? '--' }} cm</span></div>
                    <div>PA: <span class="text-white font-bold">{{ $assessment->blood_pressure ?? '--' }}</span></div>
                </div>

                @if($assessment->notes)
                    <p class="text-zinc-500 text-xs leading-relaxed border-t border-zinc-800 pt-4">{{ $assessment->notes }}</p>
                @endif

                <div class="border-t border-zinc-800 pt-4 flex items-center gap-4">
                    <button x-data @click="$dispatch('open-modal', 'edit-assessment-{{ $assessment->id }}')" class="text-[10px] font-black uppercase tracking-widest text-teal-300 hover:text-teal-200 transition-colors">
                        <i class="fas fa-pen mr-2"></i> Editar
                    </button>
                    <form method="POST" action="{{ route('professional.patients.medical-records.assessments.destroy', [$patient->id, $assessment->id]) }}" onsubmit="return confirm('Remover esta avaliação do prontuário?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-300 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Remover
                        </button>
                    </form>
                </div>
            </div>

            <x-modal name="edit-assessment-{{ $assessment->id }}" focusable>
                <form method="POST" action="{{ route('professional.patients.medical-records.assessments.update', [$patient->id, $assessment->id]) }}" class="p-8">
                    @csrf
                    @method('PUT')
                    <h2 class="text-2xl font-black text-white mb-6">Editar Avaliação</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <input type="date" name="assessment_date" value="{{ $assessment->assessment_date?->toDateString() }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" step="0.01" name="weight_kg" value="{{ $assessment->weight_kg }}" placeholder="Peso" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" step="0.01" name="bf_percent" value="{{ $assessment->bf_percent }}" placeholder="% gordura" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" step="0.01" name="muscle_percent" value="{{ $assessment->muscle_percent }}" placeholder="% músculo" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" step="0.01" name="waist" value="{{ $assessment->waist }}" placeholder="Cintura" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" step="0.01" name="chest" value="{{ $assessment->chest }}" placeholder="Tórax" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" step="0.01" name="hips" value="{{ $assessment->hips }}" placeholder="Quadril" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="text" name="blood_pressure" value="{{ $assessment->blood_pressure }}" placeholder="Pressão arterial" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                        <input type="number" name="heart_rate" value="{{ $assessment->heart_rate }}" placeholder="Frequência cardíaca" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
                    </div>

                    <textarea name="notes" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500 mt-5">{{ $assessment->notes }}</textarea>

                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
                        <button type="submit" class="px-8 py-3 bg-teal-600 text-white rounded-xl font-black hover:bg-teal-500 transition-all">Atualizar</button>
                    </div>
                </form>
            </x-modal>
        @empty
            <div class="xl:col-span-3 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-weight text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhuma avaliação registrada</h4>
                <p class="text-zinc-500 text-sm">Registre medidas, composição corporal e sinais vitais deste {{ mb_strtolower($patientLabel) }}.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $assessments->links() }}
    </div>
</div>

<x-modal name="add-assessment" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.assessments.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Nova Avaliação</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Data</span>
                <input type="date" name="assessment_date" value="{{ now()->toDateString() }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Peso (kg)</span>
                <input type="number" step="0.01" name="weight_kg" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">% gordura</span>
                <input type="number" step="0.01" name="bf_percent" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">% músculo</span>
                <input type="number" step="0.01" name="muscle_percent" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Cintura</span>
                <input type="number" step="0.01" name="waist" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Tórax</span>
                <input type="number" step="0.01" name="chest" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Quadril</span>
                <input type="number" step="0.01" name="hips" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Pressão arterial</span>
                <input type="text" name="blood_pressure" placeholder="120/80" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Frequência cardíaca</span>
                <input type="number" name="heart_rate" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500">
            </label>
        </div>

        <label class="block space-y-2 mt-5">
            <span class="text-xs font-bold text-zinc-400 uppercase">Observações</span>
            <textarea name="notes" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-teal-500"></textarea>
        </label>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-teal-600 text-white rounded-xl font-black hover:bg-teal-500 transition-all">Salvar Avaliação</button>
        </div>
    </form>
</x-modal>
@endsection
