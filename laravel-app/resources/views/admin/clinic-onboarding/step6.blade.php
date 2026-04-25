@extends('layouts.clinic-onboarding')

@section('title', 'Cadastro de Profissionais')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-white font-bold text-xl">Corpo Clínico</h3>
            <p class="text-zinc-500 text-sm">Médicos, Nutricionistas, Fisioterapeutas e outros especialistas.</p>
        </div>
        <a href="{{ route('admin.users.create', ['academy_company_id' => $company->id, 'role' => 'professional']) }}" target="_blank"
            class="bg-white/5 hover:bg-white/10 text-white text-xs font-black py-3 px-6 rounded-xl border border-white/10 transition-all flex items-center uppercase tracking-widest">
            <i class="fas fa-user-md mr-2 text-emerald-500"></i> Novo Profissional
        </a>
    </div>

    <div class="space-y-4">
        @forelse($professionals as $pro)
            <div class="bg-white/5 border border-white/5 rounded-2xl p-6 flex items-center justify-between group hover:border-white/10 transition-all">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-2xl flex items-center justify-center border border-white/5 shadow-xl">
                        @if($pro->profile_photo_path)
                            <img src="{{ Storage::url($pro->profile_photo_path) }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <i class="fas fa-user-md text-zinc-600 text-xl"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-white font-bold">{{ $pro->name }}</h4>
                        <p class="text-zinc-500 text-xs">{{ $pro->professionalProfile->profession->name ?? 'Especialista' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] uppercase font-black tracking-widest px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/20">
                        Ativo
                    </span>
                    <button class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-zinc-500 hover:text-white transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="py-16 text-center border-2 border-dashed border-white/5 rounded-3xl">
                <div class="w-20 h-20 bg-zinc-900/50 rounded-full flex items-center justify-center mx-auto mb-6 border border-white/5">
                    <i class="fas fa-user-nurse text-3xl text-zinc-800"></i>
                </div>
                <p class="text-zinc-600 font-medium max-w-xs mx-auto">Nenhum profissional cadastrado. Comece adicionando o primeiro especialista da clínica.</p>
            </div>
        @endforelse
    </div>

    <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 6]) }}" method="POST" class="pt-8 border-t border-white/5 flex justify-between">
        @csrf
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 5]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Avançar
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>
</div>
@endsection
