@extends('layouts.app')

@section('title', 'Prontuario - ' . $patient->name)

@section('content')
<div class="space-y-6">
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 text-2xl font-bold">
                    {{ mb_substr($patient->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">{{ $patient->name }}</h1>
                    <div class="flex items-center gap-3 text-sm text-zinc-400 mt-1">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-id-card text-blue-500"></i>
                            {{ $patient->cpf ?? 'Sem CPF' }}
                        </span>
                        <span class="w-1 h-1 bg-zinc-700 rounded-full"></span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-calendar text-blue-500"></i>
                            {{ $patient->profile->birth_date ? $patient->profile->birth_date->format('d/m/Y') : 'N/A' }}
                            ({{ $patient->profile->birth_date ? \Carbon\Carbon::parse($patient->profile->birth_date)->age : '?' }} anos)
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('professional.patients.show', $patient->id) }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 rounded-xl hover:bg-zinc-700 transition-all text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-chevron-left"></i> Voltar ao Perfil
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-2 border-t border-zinc-800 pt-6">
            @php
                $menuItems = $medicalRecordMenuItems
                    ?? app(\App\Services\MedicalRecordModuleManager::class)->navigationForProfessional(auth()->user(), $patient);
            @endphp

            @foreach($menuItems as $item)
                <a href="{{ route($item['route'], $patient->id) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ request()->routeIs($item['route']) ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'bg-zinc-800/50 text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                    <i class="{{ $item['icon'] }}"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    @yield('medical-content')
</div>
@endsection
