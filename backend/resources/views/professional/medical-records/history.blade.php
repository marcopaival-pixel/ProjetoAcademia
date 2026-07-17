@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <h3 class="text-xl font-black text-white flex items-center gap-3">
        <i class="fas fa-history text-blue-500"></i>
        Histórico de Ações
    </h3>

    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-[2.25rem] top-0 bottom-0 w-px bg-zinc-800"></div>

            <div class="space-y-8">
                @forelse($histories as $history)
                    <div class="relative flex gap-6 items-start">
                        <!-- Icon -->
                        <div class="relative z-10 w-12 h-12 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-500 shadow-xl">
                            @if($history->module == 'evolution')
                                <i class="fas fa-notes-medical text-blue-500"></i>
                            @elseif($history->module == 'report')
                                <i class="fas fa-file-medical-alt text-amber-500"></i>
                            @elseif($history->module == 'prescription')
                                <i class="fas fa-prescription text-emerald-500"></i>
                            @elseif($history->module == 'certificate')
                                <i class="fas fa-file-contract text-purple-500"></i>
                            @else
                                <i class="fas fa-history"></i>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 pt-1">
                            <div class="flex justify-between items-center mb-1">
                                <h4 class="text-white font-bold text-sm">{{ $history->description }}</h4>
                                <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-zinc-500">Por: <strong class="text-zinc-400">{{ $history->user->name }}</strong></span>
                                <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                                <span class="text-[10px] font-black text-zinc-600 uppercase">{{ $history->module }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p class="text-zinc-500 italic">Nenhum registro de atividade encontrado.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-zinc-800">
            {{ $histories->links() }}
        </div>
    </div>
</div>
@endsection
