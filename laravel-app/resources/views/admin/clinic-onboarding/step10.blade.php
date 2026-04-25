@extends('layouts.clinic-onboarding')

@section('title', 'Treinamento')

@section('content')
<div class="space-y-10">
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <h3 class="text-white font-black text-3xl">Quase lá!</h3>
        <p class="text-zinc-500">
            Preparamos uma série de guias rápidos para garantir que sua equipe tire o máximo proveito do NexShape. 
            Recomendamos assistir aos vídeos abaixo.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
            ['title' => 'Gestão de Pacientes', 'icon' => 'fa-user-friends', 'color' => 'blue'],
            ['title' => 'Uso do Prontuário', 'icon' => 'fa-file-medical-alt', 'color' => 'emerald'],
            ['title' => 'Financeiro e Planos', 'icon' => 'fa-dollar-sign', 'color' => 'purple']
        ] as $video)
        <div class="bg-white/5 border border-white/5 rounded-3xl p-6 group hover:border-{{ $video['color'] }}-500/30 transition-all cursor-pointer">
            <div class="aspect-video bg-zinc-900 rounded-2xl mb-6 flex items-center justify-center relative overflow-hidden">
                <i class="fas {{ $video['icon'] }} text-3xl text-zinc-800 group-hover:scale-110 transition-transform"></i>
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="w-12 h-12 bg-{{ $video['color'] }}-600 rounded-full flex items-center justify-center shadow-xl">
                        <i class="fas fa-play text-white ml-1 text-sm"></i>
                    </div>
                </div>
            </div>
            <h4 class="text-white font-bold group-hover:text-{{ $video['color'] }}-400 transition-colors">{{ $video['title'] }}</h4>
            <p class="text-zinc-500 text-[10px] uppercase font-black tracking-widest mt-2">Duração: 3min</p>
        </div>
        @endforeach
    </div>

    <div class="p-8 bg-zinc-900/50 border border-white/5 rounded-3xl flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <div class="w-14 h-14 bg-zinc-800 rounded-2xl flex items-center justify-center">
                <i class="fas fa-book-open text-zinc-500"></i>
            </div>
            <div>
                <h5 class="text-white font-bold">Base de Conhecimento</h5>
                <p class="text-zinc-500 text-xs">Acesse manuais detalhados e tutoriais em texto.</p>
            </div>
        </div>
        <a href="#" class="text-blue-500 text-xs font-black uppercase tracking-widest hover:text-blue-400">Ver Central de Ajuda</a>
    </div>

    <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 10]) }}" method="POST" class="pt-8 border-t border-white/5 flex justify-between">
        @csrf
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 9]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Concluí o Treinamento
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>
</div>
@endsection
