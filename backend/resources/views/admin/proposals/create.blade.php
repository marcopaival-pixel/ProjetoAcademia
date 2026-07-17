@extends('layouts.admin')

@section('title', 'Nova Proposta')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('admin.proposals.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Gerar Proposta Comercial</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Configure a oferta personalizada para o lead</p>
        </div>
    </div>

    <form action="{{ route('admin.proposals.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Selecionar Lead</label>
                    <select name="lead_id" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none appearance-none font-bold">
                        <option value="">Selecione um lead...</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}" {{ $selectedLeadId == $lead->id ? 'selected' : '' }}>
                                {{ $lead->nome }} ({{ $lead->empresa ?? 'PF' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Plano Base</label>
                    <select id="plan-select" name="plan_id" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none appearance-none font-bold">
                        <option value="">Selecione o plano...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-price="{{ $plan->price }}">{{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8 border-t border-white/5">
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Valor Proposta (R$)</label>
                    <input type="number" step="0.01" id="valor-proposta" name="valor" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none font-bold">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Desconto Aplicado (R$)</label>
                    <input type="number" step="0.01" name="desconto" value="0.00" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none font-bold">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Válido Até</label>
                    <input type="date" name="validade" required value="{{ date('Y-m-d', strtotime('+15 days')) }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none">
                </div>
            </div>

            <div>
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Condições Especiais / Observações</label>
                <textarea name="observacoes" rows="4" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none resize-none" placeholder="Ex: Isenção de taxa de setup, Parcelamento diferenciado, etc..."></textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4 pb-20">
            <button type="submit" class="px-10 py-5 bg-blue-600 rounded-3xl text-xs text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/30">
                Gerar e Salvar Proposta
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('plan-select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('valor-proposta').value = price;
        }
    });
</script>
@endpush
@endsection
