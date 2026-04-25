@extends('layouts.admin')

@section('title', 'Vincular Profissionais')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 shadow-2xl mb-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-rose-500/10 rounded-xl flex items-center justify-center text-rose-500">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-xl">{{ $user->name }}</h3>
                <p class="text-zinc-500 text-sm">Paciente da Clínica: {{ $user->academyCompany->name ?? 'N/A' }}</p>
            </div>
        </div>

        <form action="{{ route('admin.registrations.paciente.vincular.store', $user->id) }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <select name="professional_id" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-rose-500/50 outline-none transition-all">
                    <option value="">Selecione um profissional para vincular...</option>
                    @foreach($professionals as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->name }} ({{ $prof->professionalProfile->specialty ?? 'Sem especialidade' }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl transition-all flex items-center gap-2">
                <i class="fas fa-link"></i> Vincular
            </button>
        </form>
    </div>

    <div class="bg-[#0d121f] border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-white/5 bg-white/5">
            <h4 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-user-md text-rose-500"></i> Profissionais Vinculados
            </h4>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($linkedProfessionals as $prof)
                <div class="p-4 flex items-center justify-between hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-400">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <p class="text-white font-medium">{{ $prof->name }}</p>
                            <p class="text-zinc-500 text-xs">{{ $prof->professionalProfile->specialty ?? 'Especialista' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.registrations.paciente.vincular.remove', [$user->id, $prof->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-zinc-600 hover:text-red-500 transition-colors" title="Remover vínculo">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-zinc-600">Nenhum profissional vinculado a este paciente.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ route('admin.registrations.index') }}" class="px-8 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold rounded-xl transition-all">
            Concluir e Voltar
        </a>
    </div>
</div>
@endsection
