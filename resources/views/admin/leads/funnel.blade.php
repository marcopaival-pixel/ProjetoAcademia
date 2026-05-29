@extends('layouts.admin')

@section('title', 'Funil de Vendas')

@section('content')
<div class="space-y-8 animate-fade-in flex flex-col h-[calc(100vh-12rem)]">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Funil Comercial</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Acompanhe o progresso das oportunidades</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.leads.index') }}" class="px-6 py-3 bg-zinc-900 border border-white/5 rounded-2xl text-[10px] text-zinc-400 font-black uppercase tracking-widest hover:bg-zinc-800 transition-all flex items-center gap-2">
                <i class="fas fa-list"></i> Ver Lista
            </a>
            <a href="{{ route('admin.leads.create') }}" class="px-6 py-3 bg-blue-600 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
                <i class="fas fa-plus"></i> Novo Lead
            </a>
        </div>
    </div>

    <!-- Funnel Board -->
    <div class="flex-1 overflow-x-auto pb-4 custom-scrollbar">
        <div class="flex gap-6 h-full min-w-max">
            @foreach($statuses as $status)
            <div class="w-80 flex flex-col bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-4 group/lane">
                <div class="flex items-center justify-between mb-6 px-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-{{ $status == 'Convertido' ? 'emerald' : ($status == 'Perdido' ? 'red' : ($status == 'Em negociação' ? 'purple' : 'blue')) }}-500"></span>
                        <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $status }}</h3>
                    </div>
                    <span class="text-[10px] text-zinc-700 font-black">{{ count($leadsByStatus[$status] ?? []) }}</span>
                </div>

                <div class="flex-1 space-y-4 overflow-y-auto pr-1 kanban-lane" data-status="{{ $status }}">
                    @foreach($leadsByStatus[$status] ?? [] as $lead)
                    <div class="bg-zinc-900 border border-white/5 p-5 rounded-3xl hover:border-white/10 hover:bg-zinc-800/50 transition-all cursor-grab active:cursor-grabbing shadow-sm shadow-black/20 group/card relative" data-id="{{ $lead->id }}">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-[8px] text-zinc-600 font-black uppercase tracking-wider">{{ $lead->origem ?? 'Direto' }}</span>
                            <div class="w-2 h-2 rounded-full bg-{{$lead->responsavel_id ? 'blue' : 'zinc'}}-500/50"></div>
                        </div>
                        <h4 class="text-sm font-bold text-white mb-1">{{ $lead->nome }}</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase truncate">{{ $lead->empresa ?? 'Pessoa Física' }}</p>

                        @if($lead->valor_estimado > 0)
                        <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[10px] text-emerald-500 font-black tracking-tight">R$ {{ number_format($lead->valor_estimado, 2, ',', '.') }}</span>
                            <span class="text-[8px] text-zinc-600 font-black uppercase">{{ $lead->previsao_fechamento ? $lead->previsao_fechamento->format('d/m') : '' }}</span>
                        </div>
                        @endif

                        <a href="{{ route('admin.leads.show', $lead) }}" class="absolute inset-x-0 bottom-4 text-center opacity-0 group-hover/card:opacity-100 transition-opacity text-[8px] font-black text-blue-500 uppercase tracking-widest">Detalhes &rarr;</a>
                    </div>
                    @endforeach
                    
                    @if(!isset($leadsByStatus[$status]) || count($leadsByStatus[$status]) == 0)
                    <div class="h-32 border-2 border-dashed border-white/5 rounded-3xl flex items-center justify-center">
                        <span class="text-[10px] text-zinc-700 font-black uppercase tracking-widest">Vazio</span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lanes = document.querySelectorAll('.kanban-lane');
        
        lanes.forEach(lane => {
            new Sortable(lane, {
                group: 'funnel',
                animation: 250,
                ghostClass: 'bg-zinc-800/50',
                chosenClass: 'border-blue-500/50',
                onEnd: function (evt) {
                    const leadId = evt.item.dataset.id;
                    const newStatus = evt.to.dataset.status;
                    
                    updateLeadStatus(leadId, newStatus);
                }
            });
        });

        function updateLeadStatus(id, status) {
            fetch(`/admin/leads/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Feedback visual se necessário
                }
            });
        }
    });
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush
@endsection
