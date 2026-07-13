@extends('layouts.app')

@section('title', 'Meu Prontuario')

@section('content')
@php
    $moduleKeys = array_keys($activeModules ?? []);
    $hasClinicalDocs = in_array('clinical_docs', $moduleKeys, true);
    $hasPrescriptions = in_array('prescriptions', $moduleKeys, true);

    $cards = [
        [
            'route' => 'patient.medical-records.evolutions',
            'icon' => 'fas fa-notes-medical',
            'label' => 'Atendimentos',
            'count' => $evolutions->count(),
            'suffix' => 'Registros',
            'classes' => 'text-blue-500 bg-blue-500/10 hover:border-blue-500/50',
            'enabled' => true,
        ],
        [
            'route' => 'patient.medical-records.reports',
            'icon' => 'fas fa-file-medical-alt',
            'label' => 'Laudos',
            'count' => $reports->count(),
            'suffix' => 'Arquivos',
            'classes' => 'text-amber-500 bg-amber-500/10 hover:border-amber-500/50',
            'enabled' => $hasClinicalDocs,
        ],
        [
            'route' => 'patient.medical-records.prescriptions',
            'icon' => 'fas fa-prescription-bottle-alt',
            'label' => 'Receitas',
            'count' => $prescriptions->count(),
            'suffix' => 'Itens',
            'classes' => 'text-emerald-500 bg-emerald-500/10 hover:border-emerald-500/50',
            'enabled' => $hasPrescriptions,
        ],
        [
            'route' => 'patient.medical-records.certificates',
            'icon' => 'fas fa-file-contract',
            'label' => 'Atestados',
            'count' => $certificates->count(),
            'suffix' => 'Documentos',
            'classes' => 'text-purple-500 bg-purple-500/10 hover:border-purple-500/50',
            'enabled' => $hasClinicalDocs,
        ],
    ];
@endphp

<div class="space-y-6">
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
            <i class="fas fa-file-medical-alt text-8xl text-blue-500"></i>
        </div>

        <div class="relative z-10">
            <h1 class="text-4xl font-black text-white tracking-tight mb-2">Meu <span class="text-blue-500">Prontuario</span></h1>
            <p class="text-zinc-400 font-medium max-w-2xl">Acesse seus registros de atendimentos, laudos, receitas e documentos conforme os modulos ativos da sua clinica.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($cards as $card)
            @if($card['enabled'])
                <a href="{{ route($card['route']) }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] transition-all group {{ $card['classes'] }}">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform {{ $card['classes'] }}">
                        <i class="{{ $card['icon'] }} text-xl"></i>
                    </div>
                    <h4 class="text-white font-black text-sm">{{ $card['label'] }}</h4>
                    <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mt-1">{{ $card['count'] }} {{ $card['suffix'] }}</p>
                </a>
            @endif
        @endforeach
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h3 class="text-xl font-black text-white flex items-center gap-3">
                <i class="fas fa-clock text-blue-500"></i>
                Atividades Recentes
            </h3>
            <a href="{{ route('patient.my-professionals.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-rose-500/10 text-rose-300 border border-rose-500/20 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500/20 transition-all">
                <i class="fas fa-user-shield"></i>
                Permissoes sensiveis
            </a>
        </div>

        <div class="space-y-4">
            @forelse(collect($evolutions)->merge($reports)->merge($prescriptions)->merge($certificates)->sortByDesc('date')->take(5) as $item)
                <div class="flex items-center justify-between p-4 bg-zinc-800/30 rounded-2xl border border-zinc-800/50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs
                            @if($item instanceof \App\Models\MedicalEvolution) bg-blue-500/10 text-blue-500 @endif
                            @if($item instanceof \App\Models\MedicalReport) bg-amber-500/10 text-amber-500 @endif
                            @if($item instanceof \App\Models\MedicalPrescription) bg-emerald-500/10 text-emerald-500 @endif
                            @if($item instanceof \App\Models\MedicalCertificate) bg-purple-500/10 text-purple-500 @endif
                        ">
                            @if($item instanceof \App\Models\MedicalEvolution) <i class="fas fa-notes-medical"></i> @endif
                            @if($item instanceof \App\Models\MedicalReport) <i class="fas fa-file-medical-alt"></i> @endif
                            @if($item instanceof \App\Models\MedicalPrescription) <i class="fas fa-prescription-bottle-alt"></i> @endif
                            @if($item instanceof \App\Models\MedicalCertificate) <i class="fas fa-file-contract"></i> @endif
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">
                                @if($item instanceof \App\Models\MedicalEvolution) Atendimento clinico @endif
                                @if($item instanceof \App\Models\MedicalReport) Laudo: {{ $item->title }} @endif
                                @if($item instanceof \App\Models\MedicalPrescription) Receita: {{ $item->medicine }} @endif
                                @if($item instanceof \App\Models\MedicalCertificate) Atestado: {{ $item->reason }} @endif
                            </p>
                            <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-0.5">{{ $item->date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-zinc-500 text-center py-8 italic">Nenhuma atividade recente registrada.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
