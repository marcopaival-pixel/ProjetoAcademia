@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-heart-pulse text-red-400"></i>
            Diário de dor
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-pain-record')" class="px-6 py-3 bg-red-600 text-white rounded-2xl font-black hover:bg-red-500 transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Novo Registro
        </button>
    </div>

    <div class="space-y-4">
        @forelse($painRecords as $record)
            @php($points = $record->pain_points ?? [])
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-300 border border-red-500/20 flex items-center justify-center text-xl font-black">{{ $record->eva_level }}</span>
                            <div>
                                <h4 class="text-white font-black">{{ $points['region'] ?? 'Região não informada' }}</h4>
                                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">{{ $record->assessment_date?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <p class="text-zinc-400 text-sm">
                            Lado: <span class="text-white font-bold">{{ $points['laterality'] ?? '--' }}</span>
                        </p>
                        @if($record->notes)
                            <p class="text-zinc-500 text-sm leading-relaxed">{{ $record->notes }}</p>
                        @endif
                        <div class="flex items-center gap-4">
                            <button x-data @click="$dispatch('open-modal', 'edit-pain-record-{{ $record->id }}')" class="text-[10px] font-black uppercase tracking-widest text-red-300 hover:text-red-200 transition-colors">
                                <i class="fas fa-pen mr-2"></i> Editar
                            </button>
                            <form method="POST" action="{{ route('professional.patients.medical-records.pain.destroy', [$patient->id, $record->id]) }}" onsubmit="return confirm('Remover este registro de dor?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-300 transition-colors">
                                    <i class="fas fa-trash-alt mr-2"></i> Remover
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="min-w-[180px]">
                        <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-2">Escala EVA</p>
                        <div class="h-3 rounded-full bg-zinc-800 overflow-hidden">
                            <div class="h-full bg-red-500" style="width: {{ $record->eva_level * 10 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <x-modal name="edit-pain-record-{{ $record->id }}" focusable>
                <form method="POST" action="{{ route('professional.patients.medical-records.pain.update', [$patient->id, $record->id]) }}" class="p-8">
                    @csrf
                    @method('PUT')
                    <h2 class="text-2xl font-black text-white mb-6">Editar Registro de Dor</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <input type="datetime-local" name="assessment_date" value="{{ $record->assessment_date?->format('Y-m-d\TH:i') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
                        <input type="number" name="eva_level" min="0" max="10" value="{{ $record->eva_level }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
                        <input type="text" name="region" value="{{ $points['region'] ?? '' }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
                        <select name="laterality" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
                            @foreach(['' => 'Não se aplica', 'Esquerdo' => 'Esquerdo', 'Direito' => 'Direito', 'Bilateral' => 'Bilateral', 'Central' => 'Central'] as $value => $label)
                                <option value="{{ $value }}" @selected(($points['laterality'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <textarea name="notes" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500 mt-5">{{ $record->notes }}</textarea>

                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
                        <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-xl font-black hover:bg-red-500 transition-all">Atualizar</button>
                    </div>
                </form>
            </x-modal>
        @empty
            <div class="bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-heart-pulse text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhum registro de dor</h4>
                <p class="text-zinc-500 text-sm">Registre intensidade, localização e evolução da dor deste {{ mb_strtolower($patientLabel) }}.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $painRecords->links() }}
    </div>
</div>

<x-modal name="add-pain-record" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.pain.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Novo Registro de Dor</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Data e hora</span>
                <input type="datetime-local" name="assessment_date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Intensidade EVA (0-10)</span>
                <input type="number" name="eva_level" min="0" max="10" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Região</span>
                <input type="text" name="region" required placeholder="Ex: Lombar, joelho, ombro" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Lado</span>
                <select name="laterality" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500">
                    <option value="">Não se aplica</option>
                    <option value="Esquerdo">Esquerdo</option>
                    <option value="Direito">Direito</option>
                    <option value="Bilateral">Bilateral</option>
                    <option value="Central">Central</option>
                </select>
            </label>
        </div>

        <label class="block space-y-2 mt-5">
            <span class="text-xs font-bold text-zinc-400 uppercase">Notas</span>
            <textarea name="notes" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-red-500"></textarea>
        </label>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-xl font-black hover:bg-red-500 transition-all">Salvar Registro</button>
        </div>
    </form>
</x-modal>
@endsection
