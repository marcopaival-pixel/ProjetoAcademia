@extends('layouts.app')

@section('title', 'Meus Atestados')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-32">
    <div class="py-10 px-6 max-w-2xl mx-auto">
        <x-patient.page-header 
            title="Meus Atestados" 
            subtitle="Licenças Médicas e Comprovantes" 
            backUrl="{{ route('patient.medical-records.index') }}"
            icon="fas fa-file-contract"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($certificates as $certificate)
                <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl group relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-24 h-24 bg-purple-500/5 blur-2xl rounded-full"></div>
                    
                    <div class="flex justify-between items-start mb-8 relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500 group-hover:scale-110 transition-transform shadow-inner">
                                <i class="fas fa-file-contract text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white leading-tight tracking-tighter uppercase italic">{{ $certificate->reason }}</h3>
                                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">{{ $certificate->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 rounded-[2rem] border border-white/5 p-6 mb-8 relative z-10">
                        <div class="flex justify-between items-center">
                            <div class="text-center">
                                <p class="text-[8px] font-black text-zinc-600 uppercase tracking-widest mb-1">Início</p>
                                <p class="text-white font-black text-xs">{{ $certificate->start_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="px-4 text-zinc-800">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-[8px] font-black text-zinc-600 uppercase tracking-widest mb-1">Término</p>
                                <p class="text-white font-black text-xs">{{ $certificate->end_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 relative z-10">
                        <a href="{{ route('patient.medical-records.certificates.download', $certificate->id) }}" class="flex-1 py-4 bg-purple-600 text-white font-black rounded-3xl text-[10px] uppercase tracking-widest hover:bg-purple-500 transition-all flex items-center justify-center gap-2 shadow-xl shadow-purple-600/20">
                            <i class="fas fa-download"></i> Baixar PDF
                        </a>
                        <a href="{{ route('patient.medical-records.certificates.download', $certificate->id) }}" class="px-5 py-4 bg-white/5 text-zinc-300 rounded-3xl border border-white/10 hover:bg-white/10 transition-all">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <x-patient.empty-state 
                        icon="fas fa-file-contract" 
                        title="Sem Atestados" 
                        description="Nenhum atestado médico ou comprovante de licença foi registrado para você até o momento."
                    />
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
@endsection
