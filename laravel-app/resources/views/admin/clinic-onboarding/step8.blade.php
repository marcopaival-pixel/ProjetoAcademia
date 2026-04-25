@extends('layouts.clinic-onboarding')

@section('title', 'Importação de Pacientes')

@section('content')
<div class="space-y-10">
    <div class="flex flex-col md:flex-row items-center gap-12">
        <div class="flex-grow space-y-6">
            <h3 class="text-white font-bold text-2xl">Traga sua base de dados</h3>
            <p class="text-zinc-500 leading-relaxed">
                Você pode importar seus pacientes existentes via planilha Excel ou CSV. 
                Isso agiliza a transição para o NexShape e garante que nenhum dado seja perdido.
            </p>
            
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.import.template', 'patients') }}" class="bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black py-4 px-8 rounded-2xl transition-all flex items-center uppercase tracking-widest border border-white/5">
                    <i class="fas fa-download mr-3 text-blue-500"></i> Baixar Modelo CSV
                </a>
            </div>
        </div>

        <div class="w-full md:w-80 h-80 bg-gradient-to-br from-blue-600/20 to-emerald-500/10 rounded-[40px] border border-white/10 flex flex-col items-center justify-center p-8 text-center relative overflow-hidden group">
            <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <i class="fas fa-file-csv text-6xl text-blue-500 mb-6 group-hover:scale-110 transition-transform"></i>
            <p class="text-white font-bold text-lg mb-2">Importar Agora</p>
            <p class="text-zinc-500 text-xs">Arraste seu arquivo ou clique para selecionar</p>
            <form action="{{ route('admin.import.submit', 'patients') }}" method="POST" enctype="multipart/form-data" class="absolute inset-0 opacity-0 cursor-pointer">
                @csrf
                <input type="file" name="file" class="w-full h-full cursor-pointer">
            </form>
        </div>
    </div>

    <div class="bg-white/5 border border-white/5 rounded-3xl p-8 flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center border border-blue-500/20">
                <i class="fas fa-user-check text-blue-400 text-2xl"></i>
            </div>
            <div>
                <p class="text-zinc-500 text-sm font-medium">Pacientes já cadastrados</p>
                <h4 class="text-white text-3xl font-black">{{ $patientCount }}</h4>
            </div>
        </div>
        <div class="text-right">
            <p class="text-zinc-600 text-[10px] uppercase font-black tracking-widest mb-1">Status da Importação</p>
            <span class="inline-flex items-center text-emerald-400 text-sm font-bold">
                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span> Sistema Pronto
            </span>
        </div>
    </div>

    <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 8]) }}" method="POST" class="pt-8 border-t border-white/5 flex justify-between">
        @csrf
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 7]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Pular / Continuar
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>
</div>
@endsection
