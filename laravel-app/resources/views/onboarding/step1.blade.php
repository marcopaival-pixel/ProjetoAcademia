@extends('layouts.onboarding-premium')

@section('title', 'Tipo de Conta')
@section('step_title', 'Como deseja utilizar o NexShape?')
@section('step_description', 'Selecione o perfil que melhor se adapta às suas necessidades para personalizarmos sua experiência.')

@section('content')
<form action="{{ route('onboarding-premium.start') }}" method="POST" id="onboarding-form" x-data="{ selected: '{{ old('account_type', '') }}' }">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Aluno -->
        <label class="card-type group relative p-8 rounded-[32px] glass glass-hover transition-all overflow-hidden" 
               :class="selected === 'aluno' ? 'selected' : ''"
               @click="selected = 'aluno'">
            <input type="radio" name="account_type" value="aluno" class="hidden" required :checked="selected === 'aluno'">
            <div class="absolute top-0 right-0 p-6 opacity-20 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-user-graduate text-4xl text-blue-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Aluno / Paciente</h3>
            <p class="text-zinc-400 text-sm leading-relaxed">Acesso individual para treinos, dietas e acompanhamento de saúde pessoal.</p>
            <div class="mt-6 flex items-center text-blue-400 text-xs font-bold uppercase tracking-widest">
                Começar agora <i class="fas fa-chevron-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </label>

        <!-- Profissional -->
        <label class="card-type group relative p-8 rounded-[32px] glass glass-hover transition-all overflow-hidden"
               :class="selected === 'profissional' ? 'selected' : ''"
               @click="selected = 'profissional'">
            <input type="radio" name="account_type" value="profissional" class="hidden" :checked="selected === 'profissional'">
            <div class="absolute top-0 right-0 p-6 opacity-20 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-user-md text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Profissional</h3>
            <p class="text-zinc-400 text-sm leading-relaxed">Para personal trainers e profissionais de saúde que atendem seus próprios alunos.</p>
            <div class="mt-6 flex items-center text-emerald-400 text-xs font-bold uppercase tracking-widest">
                Gerenciar alunos <i class="fas fa-chevron-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </label>

        <!-- Clínica -->
        <label class="card-type group relative p-8 rounded-[32px] glass glass-hover transition-all overflow-hidden"
               :class="selected === 'clinica' ? 'selected' : ''"
               @click="selected = 'clinica'">
            <input type="radio" name="account_type" value="clinica" class="hidden" :checked="selected === 'clinica'">
            <div class="absolute top-0 right-0 p-6 opacity-20 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-clinic-medical text-4xl text-purple-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Clínica / Academia</h3>
            <p class="text-zinc-400 text-sm leading-relaxed">Gestão multi-unidade, multi-profissional e controle administrativo completo.</p>
            <div class="mt-6 flex items-center text-purple-400 text-xs font-bold uppercase tracking-widest">
                Solução Enterprise <i class="fas fa-chevron-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </label>

        <!-- Franquia -->
        <label class="card-type group relative p-8 rounded-[32px] glass glass-hover transition-all overflow-hidden"
               :class="selected === 'franquia' ? 'selected' : ''"
               @click="selected = 'franquia'">
            <input type="radio" name="account_type" value="franquia" class="hidden" :checked="selected === 'franquia'">
            <div class="absolute top-0 right-0 p-6 opacity-20 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-sitemap text-4xl text-amber-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Franquia</h3>
            <p class="text-zinc-400 text-sm leading-relaxed">Controle centralizado de múltiplas marcas, unidades e faturamento consolidado.</p>
            <div class="mt-6 flex items-center text-amber-400 text-xs font-bold uppercase tracking-widest">
                Gestão de Rede <i class="fas fa-chevron-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </label>
    </div>

    <div class="mt-16 flex justify-end">
        <button type="submit" class="btn-premium flex items-center gap-3" :disabled="!selected">
            Confirmar Seleção <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
