@extends('layouts.admin')

@section('title', 'Menu de Cadastros')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex flex-col gap-2 mb-8">
        <h2 class="text-zinc-400 text-sm font-medium uppercase tracking-widest">Selecione o tipo de registro</h2>
        <p class="text-zinc-500 text-sm">Escolha o perfil adequado para o novo usuário do sistema.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($isAdmin || $isProfessionalUnico)
            <!-- Profissional Único -->
            <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 hover:border-blue-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-3xl -mr-16 -mt-16"></div>
                <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/5">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Profissional Único</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">Cadastro de profissionais independentes (Nutricionistas, Personals, Médicos) com agenda própria.</p>
                <a href="{{ route('admin.registrations.professional-unico') }}" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-blue-600/10 border border-blue-500/20 text-blue-400 rounded-2xl text-sm font-bold hover:bg-blue-600 hover:text-white transition-all">
                    Novo Profissional <i class="fas fa-plus text-xs"></i>
                </a>
            </div>
            
            <!-- Paciente do Profissional -->
            <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 hover:border-emerald-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 blur-3xl -mr-16 -mt-16"></div>
                <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-emerald-500/5">
                    <i class="fas fa-user-injured text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Paciente Individual</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">Cadastrar novo paciente vinculado diretamente ao seu portfólio profissional.</p>
                <a href="{{ route('admin.registrations.paciente-profissional') }}" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-emerald-600/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-sm font-bold hover:bg-emerald-600 hover:text-white transition-all">
                    Novo Paciente <i class="fas fa-plus text-xs"></i>
                </a>
            </div>
        @endif

        @if($isAdmin || $isClinic)
            <!-- Profissional da Clínica -->
            <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 hover:border-indigo-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 blur-3xl -mr-16 -mt-16"></div>
                <div class="w-14 h-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/5">
                    <i class="fas fa-hospital-user text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Profissional da Clínica</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">Cadastro de profissionais vinculados à clínica, com gestão de unidade e vínculo contratual.</p>
                <a href="{{ route('admin.registrations.professional-clinica') }}" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 rounded-2xl text-sm font-bold hover:bg-indigo-600 hover:text-white transition-all">
                    Vincular Profissional <i class="fas fa-link text-xs"></i>
                </a>
            </div>

            <!-- Funcionário da Clínica -->
            <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 hover:border-amber-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 blur-3xl -mr-16 -mt-16"></div>
                <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-amber-500/5">
                    <i class="fas fa-id-card text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Staff / Administrativo</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">Cadastro de recepcionistas, gerentes e administradores para operação da unidade.</p>
                <a href="{{ route('admin.registrations.funcionario-clinica') }}" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-amber-600/10 border border-amber-500/20 text-amber-400 rounded-2xl text-sm font-bold hover:bg-amber-600 hover:text-white transition-all">
                    Novo Staff <i class="fas fa-plus text-xs"></i>
                </a>
            </div>

            <!-- Paciente da Clínica -->
            <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 hover:border-rose-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/5 blur-3xl -mr-16 -mt-16"></div>
                <div class="w-14 h-14 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-rose-500/5">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Paciente da Clínica</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">Cadastrar novo paciente vinculado à clínica, suportando convênios e múltiplos profissionais.</p>
                <a href="{{ route('admin.registrations.paciente-clinica') }}" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-rose-600/10 border border-rose-500/20 text-rose-400 rounded-2xl text-sm font-bold hover:bg-rose-600 hover:text-white transition-all">
                    Registrar Paciente <i class="fas fa-plus text-xs"></i>
                </a>
            </div>
        @endif

        @if($isAdmin)
            <!-- Gestão de Músculos -->
            <div class="bg-[#0d121f] border border-white/5 rounded-3xl p-8 hover:border-blue-500/30 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-3xl -mr-16 -mt-16"></div>
                <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/5">
                    <i class="fas fa-dna text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Anatomia / Músculos</h3>
                <p class="text-zinc-400 text-sm leading-relaxed mb-8">Gerencie a base de dados anatômica, incluindo grupos musculares e músculos individuais.</p>
                <a href="{{ route('admin.muscles.index') }}" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-blue-600/10 border border-blue-500/20 text-blue-400 rounded-2xl text-sm font-bold hover:bg-blue-600 hover:text-white transition-all">
                    Gerenciar Anatomia <i class="fas fa-cog text-xs"></i>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
