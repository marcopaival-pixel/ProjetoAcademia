@extends('layouts.clinic-onboarding')

@section('title', 'Teste e Validação')

@section('content')
<div class="space-y-10">
    <div>
        <h3 class="text-white font-bold text-xl">Revisão Final</h3>
        <p class="text-zinc-500 text-sm">Verifique se todos os pontos fundamentais foram configurados corretamente.</p>
    </div>

    <div class="space-y-4">
        @foreach([
            ['title' => 'Identidade Visual Aplicada', 'desc' => 'Cores e logo configurados.', 'status' => true],
            ['title' => 'Usuários Administrativos', 'desc' => 'Ao menos um gestor cadastrado.', 'status' => $company->users()->count() > 0],
            ['title' => 'Especialidades Definidas', 'desc' => 'Áreas de atuação da clínica selecionadas.', 'status' => true],
            ['title' => 'Profissionais Vinculados', 'desc' => 'Equipe de atendimento pronta.', 'status' => $company->users()->whereHas('roles', fn($q) => $q->where('name', 'professional'))->count() > 0],
            ['title' => 'Agenda e Horários', 'desc' => 'Configurações de fluxo definidas.', 'status' => true],
        ] as $check)
        <div class="flex items-center justify-between p-6 bg-white/5 border border-white/5 rounded-2xl group">
            <div class="flex items-center space-x-6">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $check['status'] ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                    <i class="fas {{ $check['status'] ? 'fa-check' : 'fa-times' }}"></i>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm">{{ $check['title'] }}</h4>
                    <p class="text-zinc-500 text-[10px] uppercase tracking-wider">{{ $check['desc'] }}</p>
                </div>
            </div>
            @if(!$check['status'])
                <span class="text-red-400 text-[10px] font-black uppercase tracking-widest">Atenção Necessária</span>
            @endif
        </div>
        @endforeach
    </div>

    <div class="p-8 bg-blue-600/10 border border-blue-500/20 rounded-3xl">
        <div class="flex items-start">
            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white mr-6 shadow-lg shadow-blue-600/30">
                <i class="fas fa-vial text-xl"></i>
            </div>
            <div>
                <h5 class="text-white font-bold text-lg">Modo de Teste</h5>
                <p class="text-zinc-500 text-sm leading-relaxed mb-4">
                    Você pode realizar um agendamento de teste ou criar um prontuário fictício para validar o fluxo completo agora.
                </p>
                <button class="bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black py-3 px-6 rounded-xl transition-all uppercase tracking-widest">
                    Iniciar Teste Prático
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 11]) }}" method="POST" class="pt-8 border-t border-white/5 flex justify-between">
        @csrf
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 10]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Tudo Pronto! Avançar para Ativação
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>
</div>
@endsection
