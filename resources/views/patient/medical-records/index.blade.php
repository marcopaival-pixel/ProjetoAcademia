@extends('layouts.app')

@section('title', 'Meu Prontuário')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
            <i class="fas fa-file-medical-alt text-8xl text-blue-500"></i>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-black text-white tracking-tight mb-2">Meu <span class="text-blue-500">Prontuário</span></h1>
            <p class="text-zinc-400 font-medium max-w-2xl">Acesse seus registros de atendimentos, laudos, receitas e documentos emitidos pelo seu profissional.</p>
        </div>
    </div>

    <!-- Quick Navigation Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('patient.medical-records.evolutions') }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] hover:border-blue-500/50 transition-all group">
            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-notes-medical text-xl"></i>
            </div>
            <h4 class="text-white font-black text-sm">Atendimentos</h4>
            <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mt-1">{{ $evolutions->count() }} Registros</p>
        </a>

        <a href="{{ route('patient.medical-records.reports') }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] hover:border-amber-500/50 transition-all group">
            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-medical-alt text-xl"></i>
            </div>
            <h4 class="text-white font-black text-sm">Laudos</h4>
            <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mt-1">{{ $reports->count() }} Arquivos</p>
        </a>

        <a href="{{ route('patient.medical-records.prescriptions') }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] hover:border-emerald-500/50 transition-all group">
            <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-prescription-bottle-alt text-xl"></i>
            </div>
            <h4 class="text-white font-black text-sm">Receitas</h4>
            <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mt-1">{{ $prescriptions->count() }} Itens</p>
        </a>

        <a href="{{ route('patient.medical-records.certificates') }}" class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] hover:border-purple-500/50 transition-all group">
            <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500 mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-contract text-xl"></i>
            </div>
            <h4 class="text-white font-black text-sm">Atestados</h4>
            <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mt-1">{{ $certificates->count() }} Documentos</p>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-black text-white flex items-center gap-3">
                <i class="fas fa-clock text-blue-500"></i>
                Atividades Recentes
            </h3>
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
                                @if($item instanceof \App\Models\MedicalEvolution) Atendimento Clinical @endif
                                @if($item instanceof \App\Models\MedicalReport) Laudo: {{ $item->title }} @endif
                                @if($item instanceof \App\Models\MedicalPrescription) Receita: {{ $item->medicine }} @endif
                                @if($item instanceof \App\Models\MedicalCertificate) Atestado: {{ $item->reason }} @endif
                            </p>
                            <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-0.5">{{ $item->date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <button class="text-zinc-500 hover:text-white transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            @empty
                <p class="text-zinc-500 text-center py-8 italic">Nenhuma atividade recente registrada.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
