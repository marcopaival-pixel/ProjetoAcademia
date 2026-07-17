@extends('layouts.clinic-onboarding')

@section('title', 'Especialidades da Clínica')

@section('content')
<form action="{{ route('admin.clinic-onboarding.step.save', [$company, 5]) }}" method="POST" class="space-y-8">
    @csrf
    
    <div>
        <h3 class="text-white font-bold text-xl">O que esta clínica oferece?</h3>
        <p class="text-zinc-500 text-sm">Selecione as especialidades que estarão disponíveis para agendamento.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($allSpecialties as $spec)
            <label class="relative flex items-center p-4 bg-white/5 border border-white/5 rounded-2xl cursor-pointer hover:bg-white/10 transition-all group has-[:checked]:border-blue-500/50 has-[:checked]:bg-blue-500/5">
                <input type="checkbox" name="specialties[]" value="{{ $spec->id }}" 
                    {{ in_array($spec->id, $selectedSpecialties) ? 'checked' : '' }}
                    class="hidden">
                <div class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-500 group-hover:text-blue-500 transition-colors mr-4">
                    <i class="{{ $spec->icone ?? 'fas fa-stethoscope' }}"></i>
                </div>
                <div class="flex-grow">
                    <span class="text-white font-semibold text-sm">{{ $spec->nome }}</span>
                    <p class="text-[10px] text-zinc-600 uppercase tracking-tighter">{{ $spec->profession->name ?? 'Geral' }}</p>
                </div>
                <div class="w-5 h-5 rounded-full border-2 border-zinc-800 flex items-center justify-center group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500">
                    <i class="fas fa-check text-[10px] text-white opacity-0 group-has-[:checked]:opacity-100"></i>
                </div>
            </label>
        @endforeach
    </div>

    <div class="pt-8 border-t border-white/5 flex justify-between">
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 4]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Salvar Especialidades
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </div>
</form>
@endsection
