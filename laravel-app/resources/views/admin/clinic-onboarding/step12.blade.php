@extends('layouts.clinic-onboarding')

@section('title', 'Ativação da Clínica')

@section('content')
<div class="space-y-12">
    <div class="text-center space-y-6 py-8">
        <div class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-teal-400 rounded-[32px] flex items-center justify-center mx-auto shadow-2xl shadow-emerald-500/30 animate-bounce">
            <i class="fas fa-rocket text-white text-4xl"></i>
        </div>
        <div class="space-y-2">
            <h3 class="text-white font-black text-4xl">Pronto para decolar!</h3>
            <p class="text-zinc-500 text-lg max-w-lg mx-auto">
                A implantação da <strong>{{ $company->name }}</strong> foi concluída com sucesso. 
                Ao clicar no botão abaixo, a clínica será ativada e estará pronta para atendimento.
            </p>
        </div>
    </div>

    <div class="bg-white/5 border border-white/5 rounded-3xl p-10 space-y-6">
        <h4 class="text-white font-bold text-center uppercase tracking-[0.3em] text-xs">Resumo da Implantação</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <p class="text-zinc-600 text-[10px] font-black uppercase mb-1">Profissionais</p>
                <p class="text-white font-bold text-xl">{{ $company->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'))->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-zinc-600 text-[10px] font-black uppercase mb-1">Pacientes</p>
                <p class="text-white font-bold text-xl">{{ $company->users()->whereHas('roles', fn($q) => $q->where('name', 'paciente'))->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-zinc-600 text-[10px] font-black uppercase mb-1">Documentos</p>
                <p class="text-white font-bold text-xl">Ativados</p>
            </div>
            <div class="text-center">
                <p class="text-zinc-600 text-[10px] font-black uppercase mb-1">Agenda</p>
                <p class="text-white font-bold text-xl">Configurada</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 12]) }}" method="POST" class="flex flex-col items-center space-y-6">
        @csrf
        <button type="submit" class="w-full max-w-md group bg-emerald-600 hover:bg-emerald-500 text-white font-black py-6 rounded-3xl transition-all flex items-center justify-center shadow-2xl shadow-emerald-600/30 text-lg uppercase tracking-widest">
            Ativar Clínica Agora
            <i class="fas fa-check-double ml-4 group-hover:scale-110 transition-transform"></i>
        </button>
        <p class="text-zinc-600 text-[10px] uppercase font-black tracking-tighter">Ao ativar, a clínica passará a ser cobrada conforme o plano selecionado.</p>
    </form>

    <div class="pt-8 border-t border-white/5 flex justify-start">
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 11]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar para Revisão
        </a>
    </div>
</div>
@endsection
