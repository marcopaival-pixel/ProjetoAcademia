@extends('professional.medical-records.layout')

@section('medical-content')
<div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 shadow-xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Treinos e Protocolos</h2>
            <p class="text-sm text-zinc-400">Gerencie os planos de treinamento do {{ mb_strtolower($patientLabel) }}.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('modal-protocol').classList.remove('hidden')" class="px-4 py-2 bg-zinc-800 text-zinc-300 rounded-xl hover:bg-zinc-700 transition-all text-sm font-bold flex items-center gap-2">
                <i class="fas fa-magic"></i> Aplicar Protocolo
            </button>
            <a href="{{ route('professional.patients.trainings.create', $patient->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all text-sm font-bold flex items-center gap-2">
                <i class="fas fa-plus"></i> Novo Treino
            </a>
        </div>
    </div>

    @if($plans->isEmpty())
        <div class="text-center py-12 bg-zinc-800/30 rounded-2xl border border-zinc-800/50">
            <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-dumbbell text-2xl text-zinc-500"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Nenhum treino prescrito</h3>
            <p class="text-zinc-400 max-w-sm mx-auto mb-6">Este {{ mb_strtolower($patientLabel) }} ainda não possui nenhum plano de treinamento ativo.</p>
            <a href="{{ route('professional.patients.trainings.create', $patient->id) }}" class="inline-flex px-4 py-2 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all text-sm font-bold items-center gap-2">
                Prescrever Treino
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($plans as $plan)
                <div class="p-5 bg-zinc-800/50 border border-zinc-800 rounded-2xl hover:border-zinc-700 transition-colors flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-lg font-bold text-white">{{ $plan->name }}</h4>
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $plan->status === 'Ativo' ? 'bg-green-500/20 text-green-400' : 'bg-zinc-700 text-zinc-300' }}">
                                {{ $plan->status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-zinc-400">
                            <span><i class="fas fa-bullseye text-blue-500 mr-1"></i> {{ $plan->goal ?? 'Geral' }}</span>
                            <span><i class="fas fa-calendar-alt text-amber-500 mr-1"></i> {{ $plan->frequency }}x na semana</span>
                            <span><i class="fas fa-dumbbell text-emerald-500 mr-1"></i> {{ $plan->exercises_count }} exercícios</span>
                            <span><i class="fas fa-clock text-purple-500 mr-1"></i> Criado em {{ $plan->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <!-- Funcionalidades futuras para visualização, edição e cópia -->
                        <button class="w-10 h-10 flex items-center justify-center bg-zinc-800 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-700 transition-colors" title="Visualizar/Imprimir">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal Protocolos -->
<div id="modal-protocol" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] w-full max-w-lg p-6 shadow-2xl relative">
        <button onclick="document.getElementById('modal-protocol').classList.add('hidden')" class="absolute top-6 right-6 text-zinc-400 hover:text-white">
            <i class="fas fa-times text-xl"></i>
        </button>
        
        <h3 class="text-xl font-bold text-white mb-2">Aplicar Protocolo de Treino</h3>
        <p class="text-zinc-400 text-sm mb-6">Selecione um protocolo modelo da clínica para aplicar a este {{ mb_strtolower($patientLabel) }}.</p>

        @if($protocols->isEmpty())
            <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-500 text-sm">
                Sua clínica ainda não possui protocolos de treino cadastrados. Cadastre-os no painel da clínica.
            </div>
        @else
            <form action="{{ route('professional.patients.trainings.apply-protocol', $patient->id) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Selecione o Protocolo</label>
                    <select name="protocol_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                        <option value="">Selecione...</option>
                        @foreach($protocols as $protocol)
                            <option value="{{ $protocol->id }}">{{ $protocol->name }} ({{ $protocol->frequency }}x semana - {{ $protocol->duration }} min)</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-protocol').classList.add('hidden')" class="px-5 py-2.5 rounded-xl font-bold text-zinc-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-600 transition-colors">
                        Aplicar ao {{ $patientLabel }}
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection



