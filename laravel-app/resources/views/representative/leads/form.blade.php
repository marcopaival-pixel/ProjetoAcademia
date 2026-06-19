@extends('layouts.app')

@section('title', isset($lead) ? 'Editar Lead' : 'Novo Lead')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">
                {{ isset($lead) ? 'Editar' : 'Novo' }} <span class="text-emerald-500">Lead</span>
            </h1>
            <p class="text-zinc-500 text-sm mt-1">Preencha os dados do seu prospecto.</p>
        </div>
        <a href="{{ route('representative.leads.index') }}" class="text-zinc-500 hover:text-white transition-colors text-xs font-black uppercase tracking-widest">
            Voltar
        </a>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] p-8">
        <form action="{{ isset($lead) ? route('representative.leads.update', $lead->id) : route('representative.leads.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($lead))
                @method('PUT')
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome do Contato *</label>
                    <input type="text" name="nome" value="{{ old('nome', $lead->nome ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    @error('nome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Empresa -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Clínica / Empresa</label>
                    <input type="text" name="empresa" value="{{ old('empresa', $lead->empresa ?? '') }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    @error('empresa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- E-mail -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">E-mail *</label>
                    <input type="email" name="email" value="{{ old('email', $lead->email ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Telefone / WhatsApp *</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $lead->telefone ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors mask-phone">
                    @error('telefone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Status do Lead *</label>
                    <select name="status" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors appearance-none">
                        <option value="Novo" {{ old('status', $lead->status ?? '') == 'Novo' ? 'selected' : '' }}>Novo</option>
                        <option value="Em Contato" {{ old('status', $lead->status ?? '') == 'Em Contato' ? 'selected' : '' }}>Em Contato</option>
                        <option value="Qualificado" {{ old('status', $lead->status ?? '') == 'Qualificado' ? 'selected' : '' }}>Qualificado</option>
                        <option value="Proposta Enviada" {{ old('status', $lead->status ?? '') == 'Proposta Enviada' ? 'selected' : '' }}>Proposta Enviada</option>
                        <option value="Negociação" {{ old('status', $lead->status ?? '') == 'Negociação' ? 'selected' : '' }}>Negociação</option>
                        <option value="Ganho" {{ old('status', $lead->status ?? '') == 'Ganho' ? 'selected' : '' }}>Ganho (Cliente)</option>
                        <option value="Perdido" {{ old('status', $lead->status ?? '') == 'Perdido' ? 'selected' : '' }}>Perdido</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Valor Estimado -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Valor Estimado (Mensal) R$</label>
                    <input type="number" step="0.01" name="valor_estimado" value="{{ old('valor_estimado', $lead->valor_estimado ?? '') }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    @error('valor_estimado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-800 gap-4 mt-6">
                <a href="{{ route('representative.leads.index') }}" class="px-8 py-3 rounded-xl text-zinc-500 hover:text-white hover:bg-zinc-800 text-xs font-black uppercase tracking-widest transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-zinc-950 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">
                    Salvar Lead
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Máscara básica para telefone/whatsapp se não houver um componente global
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInputs = document.querySelectorAll('.mask-phone');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
                e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            });
        });
    });
</script>
@endpush
@endsection
