@extends('professional.medical-records.layout')

@section('medical-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Patient Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-xl">
            <h3 class="text-xl font-black text-white mb-6 flex items-center gap-3">
                <i class="fas fa-user-circle text-blue-500"></i>
                Dados Principais
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Nome Completo</p>
                    <p class="text-white font-medium">{{ $patient->name }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Data de Nascimento</p>
                    <p class="text-white font-medium">{{ $patient->profile->birth_date ? $patient->profile->birth_date->format('d/m/Y') : 'Não informada' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Idade</p>
                    <p class="text-white font-medium">{{ $patient->profile->birth_date ? \Carbon\Carbon::parse($patient->profile->birth_date)->age . ' anos' : 'N/A' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Profissional Responsável</p>
                    <p class="text-white font-medium">{{ auth()->user()->name }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Último Atendimento</p>
                    <p class="text-white font-medium">{{ $lastEvolution ? $lastEvolution->date->format('d/m/Y H:i') : 'Nenhum registro' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Objetivo Principal</p>
                    <p class="text-white font-medium">{{ $patient->profile->goal ?? 'Não definido' }}</p>
                </div>
            </div>
        </div>

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-white flex items-center gap-3">
                    <i class="fas fa-stethoscope text-blue-500"></i>
                    Perfil Clínico
                </h3>
                <button x-data @click="$dispatch('open-modal', 'edit-summary')" class="text-blue-500 text-sm font-bold hover:underline">
                    <i class="fas fa-edit mr-1"></i> Editar
                </button>
            </div>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Diagnóstico Principal</p>
                    <p class="text-white bg-zinc-800/50 p-4 rounded-2xl border border-zinc-800">
                        {{ $pivot->main_diagnosis ?: 'Nenhum diagnóstico registrado até o momento.' }}
                    </p>
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Observações Importantes</p>
                    <p class="text-white bg-zinc-800/50 p-4 rounded-2xl border border-zinc-800">
                        {{ $pivot->important_notes ?: 'Nenhuma observação relevante registrada.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Health Alerts & Medications -->
    <div class="space-y-6">
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-xl">
            <h3 class="text-xl font-black text-white mb-6 flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
                Alertas e Alergias
            </h3>
            
            <div class="space-y-4">
                @if($patient->profile->has_allergy)
                    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl">
                        <p class="text-red-500 font-bold text-sm mb-1">Alergias Identificadas:</p>
                        <p class="text-zinc-300 text-sm">{{ $patient->profile->allergy_details }}</p>
                    </div>
                @else
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                        <p class="text-emerald-500 font-bold text-sm">Sem alergias conhecidas</p>
                    </div>
                @endif

                @if($patient->profile->has_disease)
                    <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl">
                        <p class="text-amber-500 font-bold text-sm mb-1">Patologias:</p>
                        <p class="text-zinc-300 text-sm">{{ $patient->profile->disease_details }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-xl">
            <h3 class="text-xl font-black text-white mb-6 flex items-center gap-3">
                <i class="fas fa-pills text-blue-500"></i>
                Medicamentos em Uso
            </h3>
            
            <div class="space-y-4">
                @if($patient->profile->uses_medication)
                    <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-2xl">
                        <p class="text-zinc-300 text-sm">{{ $patient->profile->medication_details }}</p>
                    </div>
                @else
                    <p class="text-zinc-500 text-sm italic">Nenhum medicamento registrado pelo {{ mb_strtolower($patientLabel) }}.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Resumo -->
<x-modal name="edit-summary" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.summary.update', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Editar Perfil Clínico</h2>
        
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Diagnóstico Principal</label>
                <textarea name="main_diagnosis" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Insira o diagnóstico principal do {{ mb_strtolower($patientLabel) }}...">{{ $pivot->main_diagnosis }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Observações Importantes</label>
                <textarea name="important_notes" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Insira observações relevantes (ex: limitações, restrições)...">{{ $pivot->important_notes }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-500 transition-all">Salvar Alterações</button>
        </div>
    </form>
</x-modal>
@endsection



