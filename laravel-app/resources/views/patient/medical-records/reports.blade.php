@extends('layouts.app')

@section('title', 'Meus Laudos')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('patient.medical-records.index') }}" class="text-zinc-500 hover:text-white flex items-center gap-2 font-bold transition-colors">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Prontuário
        </a>
        <h1 class="text-2xl font-black text-white">Meus <span class="text-amber-500">Laudos</span></h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($reports as $report)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl hover:border-amber-500/30 transition-all group">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-medical-alt text-2xl"></i>
                    </div>
                    <span class="text-zinc-500 text-xs font-black uppercase tracking-widest">{{ $report->date->format('d/m/Y') }}</span>
                </div>

                <h3 class="text-xl font-black text-white mb-2 leading-tight group-hover:text-amber-500 transition-colors">{{ $report->title }}</h3>
                <p class="text-zinc-400 text-sm line-clamp-2 mb-8 leading-relaxed">{{ $report->description }}</p>

                <div class="flex gap-3">
                    <a href="{{ route('patient.medical-records.reports.download', $report->id) }}" class="flex-1 py-4 bg-amber-500 text-zinc-950 font-black rounded-2xl text-sm hover:bg-amber-400 transition-all flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                    <a href="{{ route('patient.medical-records.reports.download', $report->id) }}" class="px-5 py-4 bg-zinc-800 text-zinc-300 rounded-2xl hover:bg-zinc-700 transition-all">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2.5rem] p-16 text-center">
                <p class="text-zinc-500 italic">Nenhum laudo disponível para visualização.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
    </div>
</div>
@endsection
