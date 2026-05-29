@extends('layouts.onboarding-premium')

@section('title', 'Sucesso')
@section('step_title', 'Parabéns, seu cadastro foi concluído!')
@section('step_description', 'Sua clínica já está configurada e pronta para operar com o máximo de performance.')

@section('content')
<div class="text-center space-y-12 py-8">
    <div class="relative inline-block">
        <div class="w-32 h-32 bg-emerald-500 rounded-full flex items-center justify-center shadow-2xl shadow-emerald-500/40 relative z-10">
            <i class="fas fa-check text-white text-5xl"></i>
        </div>
        <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-20"></div>
    </div>

    <div class="space-y-4">
        <h3 class="text-2xl font-bold text-white">Bem-vindo à Elite do Fitness</h3>
        <p class="text-zinc-400 max-w-md mx-auto">Enviamos um e-mail de confirmação para o endereço cadastrado. Agora você pode acessar todas as funcionalidades premium.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
        <div class="p-6 glass rounded-3xl">
            <i class="fas fa-users text-blue-500 mb-3 block text-xl"></i>
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-500">Próximo Passo</span>
            <p class="text-white font-bold mt-1">Cadastrar Alunos</p>
        </div>
        <div class="p-6 glass rounded-3xl">
            <i class="fas fa-dumbbell text-purple-500 mb-3 block text-xl"></i>
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-500">Personalizar</span>
            <p class="text-white font-bold mt-1">Criar Treinos</p>
        </div>
        <div class="p-6 glass rounded-3xl">
            <i class="fas fa-chart-line text-emerald-500 mb-3 block text-xl"></i>
            <span class="text-xs font-bold uppercase tracking-widest text-zinc-500">Monitorar</span>
            <p class="text-white font-bold mt-1">Ver Dashboard</p>
        </div>
    </div>

    <div class="pt-8">
        <a href="{{ route('admin.dashboard') }}" class="btn-premium inline-flex items-center gap-3">
            Acessar Meu Painel <i class="fas fa-external-link-alt"></i>
        </a>
    </div>
</div>
@endsection
