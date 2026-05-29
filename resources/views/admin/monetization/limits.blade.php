@extends('layouts.admin')

@section('content')
<div class="p-6" x-data="{ showModal: false, selectedLimit: { plan_id: '', feature_id: '', limit_value: 0, limit_type: 'none', action_type: 'block', custom_popup_text: '' } }">
    <div class="mb-8">
        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
            Configuração de Limites de Uso
        </h1>
        <p class="text-gray-400 mt-1 uppercase text-[10px] font-black tracking-widest">Defina as restrições por plano e funcionalidade</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Funcionalidade</th>
                        <th class="px-6 py-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest text-center">Plano Free (Visitante)</th>
                        @foreach($plans as $plan)
                        <th class="px-6 py-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest text-center">{{ $plan->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($features as $feature)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white/5 rounded-lg flex items-center justify-center text-blue-400 text-xs">
                                    <i class="fas @if($feature->category === 'ai_credits') fa-robot @else fa-rocket @endif"></i>
                                </div>
                                <div>
                                    <span class="text-white text-sm font-bold block">{{ $feature->name }}</span>
                                    <span class="text-[9px] text-gray-500 font-black uppercase tracking-widest">{{ $feature->code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Coluna Free -->
                        <td class="px-6 py-4 text-center">
                            @php $limit = $limits->get(null)?->firstWhere('feature_id', $feature->id); @endphp
                            <button @click="selectedLimit = { plan_id: '', feature_id: '{{ $feature->id }}', limit_value: '{{ $limit?->limit_value ?? 0 }}', limit_type: '{{ $limit?->limit_type ?? 'none' }}', action_type: '{{ $limit?->action_type ?? 'block' }}', custom_popup_text: '{{ $limit?->custom_popup_text ?? '' }}' }; showModal = true"
                                    class="px-3 py-2 rounded-xl border {{ $limit ? 'bg-blue-500/10 border-blue-500/20 text-blue-400' : 'bg-white/5 border-white/5 text-gray-500' }} text-[10px] font-black uppercase tracking-widest hover:border-blue-500/50 transition-all">
                                @if($limit && $limit->limit_type !== 'none')
                                    {{ $limit->limit_value }} / {{ $limit->limit_type }}
                                @elseif($limit)
                                    Ilimitado
                                @else
                                    Configurar
                                @endif
                            </button>
                        </td>

                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center">
                            @php $limit = $limits->get($plan->id)?->firstWhere('feature_id', $feature->id); @endphp
                            <button @click="selectedLimit = { plan_id: '{{ $plan->id }}', feature_id: '{{ $feature->id }}', limit_value: '{{ $limit?->limit_value ?? 0 }}', limit_type: '{{ $limit?->limit_type ?? 'none' }}', action_type: '{{ $limit?->action_type ?? 'block' }}', custom_popup_text: '{{ $limit?->custom_popup_text ?? '' }}' }; showModal = true"
                                    class="px-3 py-2 rounded-xl border {{ $limit ? 'bg-blue-500/10 border-blue-500/20 text-blue-400' : 'bg-white/5 border-white/5 text-gray-500' }} text-[10px] font-black uppercase tracking-widest hover:border-blue-500/50 transition-all">
                                @if($limit && $limit->limit_type !== 'none')
                                    {{ $limit->limit_value }} / {{ $limit->limit_type }}
                                @elseif($limit)
                                    Ilimitado
                                @else
                                    Configurar
                                @endif
                            </button>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Configurar Limite -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        
        <div class="bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl relative"
             @click.away="showModal = false">
            
            <form action="{{ route('admin.monetization.limits.store') }}" method="POST" class="p-10 space-y-6">
                @csrf
                <input type="hidden" name="plan_id" x-model="selectedLimit.plan_id">
                <input type="hidden" name="feature_id" x-model="selectedLimit.feature_id">

                <h2 class="text-xl font-bold text-white tracking-tight">Configurar Limite</h2>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest" x-text="selectedLimit.plan_id ? 'Plano ID: ' + selectedLimit.plan_id : 'Plano Free'"></p>
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Qtd. Permitida</label>
                            <input type="number" name="limit_value" x-model="selectedLimit.limit_value" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Período</label>
                            <select name="limit_type" x-model="selectedLimit.limit_type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                                <option value="none">Ilimitado</option>
                                <option value="day">Por Dia</option>
                                <option value="week">Por Semana</option>
                                <option value="month">Por Mês</option>
                                <option value="lifetime">Vitalício</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Ação ao Atingir</label>
                        <select name="action_type" x-model="selectedLimit.action_type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                            <option value="block">Bloquear Acesso</option>
                            <option value="popup">Exibir Popup de Upgrade</option>
                            <option value="credits">Permitir Compra de Créditos</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Texto Personalizado (Popup)</label>
                        <textarea name="custom_popup_text" x-model="selectedLimit.custom_popup_text" rows="2" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all" placeholder="Deixe vazio para usar o padrão"></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-white/5 flex justify-end gap-4">
                    <button type="button" @click="showModal = false" class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="px-8 py-3 bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-400 hover:text-white transition-all shadow-xl">
                        Salvar Limite
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
