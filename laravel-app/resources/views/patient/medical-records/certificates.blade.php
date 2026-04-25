@extends('layouts.app')

@section('title', 'Meus Atestados')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('patient.medical-records.index') }}" class="text-zinc-500 hover:text-white flex items-center gap-2 font-bold transition-colors">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Prontuário
        </a>
        <h1 class="text-2xl font-black text-white">Meus <span class="text-purple-500">Atestados</span></h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($certificates as $certificate)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl group">
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-contract text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white leading-tight">{{ $certificate->reason }}</h3>
                            <p class="text-zinc-500 text-xs font-bold">{{ $certificate->date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-800/30 rounded-2xl border border-zinc-800 p-6 mb-8">
                    <div class="flex justify-between items-center">
                        <div class="text-center">
                            <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-1">Início</p>
                            <p class="text-white font-black">{{ $certificate->start_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="px-4 text-zinc-700">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <div class="text-center">
                            <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-1">Término</p>
                            <p class="text-white font-black">{{ $certificate->end_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('patient.medical-records.certificates.download', $certificate->id) }}" class="flex-1 py-4 bg-purple-600 text-white font-black rounded-2xl text-sm hover:bg-purple-500 transition-all flex items-center justify-center gap-2 shadow-lg shadow-purple-600/20">
                        <i class="fas fa-download"></i> Baixar Documento
                    </a>
                    <a href="{{ route('patient.medical-records.certificates.download', $certificate->id) }}" class="px-5 py-4 bg-zinc-800 text-zinc-300 rounded-2xl hover:bg-zinc-700 transition-all">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2.5rem] p-16 text-center">
                <p class="text-zinc-500 italic">Nenhum atestado disponível.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $certificates->links() }}
    </div>
</div>
@endsection
