@extends('professional.medical-records.layout')

@section('medical-content')
@php
    $modules = $medicalRecordModules ?? app(\App\Services\MedicalRecordModuleManager::class)->forProfessional(auth()->user(), $patient);
    $colorClasses = [
        'blue' => 'text-blue-500 bg-blue-500/10 border-blue-500/20',
        'cyan' => 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
        'orange' => 'text-orange-400 bg-orange-500/10 border-orange-500/20',
        'green' => 'text-green-400 bg-green-500/10 border-green-500/20',
        'amber' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
        'emerald' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
        'purple' => 'text-purple-400 bg-purple-500/10 border-purple-500/20',
        'pink' => 'text-pink-400 bg-pink-500/10 border-pink-500/20',
        'teal' => 'text-teal-400 bg-teal-500/10 border-teal-500/20',
        'indigo' => 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20',
        'red' => 'text-red-400 bg-red-500/10 border-red-500/20',
        'violet' => 'text-violet-400 bg-violet-500/10 border-violet-500/20',
        'rose' => 'text-rose-400 bg-rose-500/10 border-rose-500/20',
        'lime' => 'text-lime-400 bg-lime-500/10 border-lime-500/20',
        'fuchsia' => 'text-fuchsia-400 bg-fuchsia-500/10 border-fuchsia-500/20',
        'zinc' => 'text-zinc-300 bg-zinc-800/60 border-zinc-700',
    ];
@endphp

<div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 md:p-12 shadow-xl">
    <div class="max-w-3xl">
        <div class="w-20 h-20 bg-blue-500/10 rounded-3xl flex items-center justify-center mb-8 text-blue-500">
            <i class="fas fa-file-medical text-3xl"></i>
        </div>

        <h2 class="text-3xl font-black text-white mb-4 tracking-tight">Prontuario de <span class="text-blue-500">{{ $patient->name }}</span></h2>
        <p class="text-zinc-400 font-medium leading-relaxed mb-8">
            Nucleo comum do {{ mb_strtolower($patientLabel) }} com modulos ativados conforme profissao, vinculo e portfolio da clinica. Use os atalhos abaixo para registrar atendimentos, consultar documentos e acompanhar a evolucao com trilha de auditoria.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($modules as $module)
            @php
                $classes = $colorClasses[$module['color']] ?? $colorClasses['zinc'];
                $isClickable = ! empty($module['route']) && ($module['status'] === 'active' || ($module['can_access'] ?? false));
            @endphp

            @if($isClickable)
                <a href="{{ route($module['route'], $patient->id) }}" class="group p-6 bg-zinc-800/50 border border-zinc-800 rounded-3xl text-left transition-all hover:border-blue-500/40 hover:bg-zinc-800">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center {{ $classes }}">
                            <i class="{{ $module['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <h4 class="text-white font-bold mb-2">{{ $module['label'] }}</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">{{ $module['description'] }}</p>
                </a>
            @else
                <div class="group p-6 bg-zinc-800/50 border border-zinc-800 rounded-3xl text-left transition-all opacity-80">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center {{ $classes }}">
                            <i class="{{ $module['icon'] }} text-lg"></i>
                        </div>
                        @if($module['status'] === 'planned')
                            <span class="px-3 py-1 rounded-full bg-zinc-950 text-zinc-500 border border-zinc-800 text-[9px] font-black uppercase tracking-widest">Planejado</span>
                    @elseif($module['status'] === 'restricted')
                        <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-black uppercase tracking-widest">Bloqueado</span>
                    @endif
                </div>
                <h4 class="text-white font-bold mb-2">{{ $module['label'] }}</h4>
                <p class="text-zinc-500 text-xs leading-relaxed">{{ $module['description'] }}</p>
                @if(! empty($module['access_message']))
                    <p class="text-rose-300/80 text-[10px] font-bold uppercase tracking-widest mt-4">{{ $module['access_message'] }}</p>
                @endif
            </div>
            @endif
        @endforeach
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="p-6 bg-zinc-950/50 border border-zinc-800 rounded-3xl">
            <div class="flex items-center gap-3 mb-3 text-emerald-400">
                <i class="fas fa-shield-alt"></i>
                <h4 class="text-white font-bold">Permissoes por sensibilidade</h4>
            </div>
            <p class="text-zinc-500 text-xs leading-relaxed">Modulos restritos ficam sinalizados para dados sensiveis, como registros psicologicos e observacoes internas.</p>
        </div>
        <div class="p-6 bg-zinc-950/50 border border-zinc-800 rounded-3xl">
            <div class="flex items-center gap-3 mb-3 text-purple-400">
                <i class="fas fa-history"></i>
                <h4 class="text-white font-bold">Auditoria do prontuario</h4>
            </div>
            <p class="text-zinc-500 text-xs leading-relaxed">O historico registra alteracoes com responsavel e data, preservando rastreabilidade para operacao e LGPD.</p>
        </div>
    </div>
</div>
@endsection
