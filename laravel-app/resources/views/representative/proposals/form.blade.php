@extends('layouts.app')

@section('title', isset($proposal) ? 'Editar Proposta' : 'Nova Proposta')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">
                {{ isset($proposal) ? 'Editar' : 'Nova' }} <span class="text-amber-500">Proposta</span>
            </h1>
            <p class="text-zinc-500 text-sm mt-1">Preencha os dados da proposta comercial.</p>
        </div>
        <a href="{{ route('representative.proposals.index') }}" class="text-zinc-500 hover:text-white transition-colors text-xs font-black uppercase tracking-widest">
            Voltar
        </a>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] p-8">
        <form action="{{ isset($proposal) ? route('representative.proposals.update', $proposal->id) : route('representative.proposals.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($proposal))
                @method('PUT')
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Lead -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Lead (Cliente) *</label>
                    <select name="lead_id" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors appearance-none">
                        <option value="">Selecione um Lead</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}" {{ old('lead_id', $proposal->lead_id ?? '') == $lead->id ? 'selected' : '' }}>
                                {{ $lead->nome }} {{ $lead->empresa ? '('.$lead->empresa.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('lead_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Plano -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Plano Oferecido *</label>
                    <select name="plan_id" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors appearance-none">
                        <option value="">Selecione um Plano</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $proposal->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Valor da Mensalidade -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Valor da Mensalidade (R$) *</label>
                    <input type="number" step="0.01" name="valor" value="{{ old('valor', $proposal->valor ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('valor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Desconto -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Desconto Aplicado (R$)</label>
                    <input type="number" step="0.01" name="desconto" value="{{ old('desconto', $proposal->desconto ?? '') }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('desconto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Validade -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Validade da Proposta *</label>
                    <input type="date" name="validade" value="{{ old('validade', isset($proposal) ? $proposal->validade->format('Y-m-d') : now()->addDays(7)->format('Y-m-d')) }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('validade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Dados da Clínica -->
                <div class="md:col-span-2 mt-4 pt-4 border-t border-zinc-800">
                    <h3 class="text-white font-bold mb-4">Dados da Clínica</h3>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome da Clínica *</label>
                    <input type="text" name="clinic_name" value="{{ old('clinic_name', $proposal->clinic_name ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('clinic_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">CNPJ *</label>
                    <input type="text" name="clinic_cnpj" value="{{ old('clinic_cnpj', $proposal->clinic_cnpj ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors" placeholder="00.000.000/0000-00">
                    @error('clinic_cnpj') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Responsável *</label>
                    <input type="text" name="clinic_contact" value="{{ old('clinic_contact', $proposal->clinic_contact ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('clinic_contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Telefone/WhatsApp *</label>
                    <input type="text" name="clinic_phone" value="{{ old('clinic_phone', $proposal->clinic_phone ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('clinic_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Cidade *</label>
                    <input type="text" name="clinic_city" value="{{ old('clinic_city', $proposal->clinic_city ?? '') }}" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('clinic_city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Estado (UF) *</label>
                    <input type="text" name="clinic_state" value="{{ old('clinic_state', $proposal->clinic_state ?? '') }}" required maxlength="2" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                    @error('clinic_state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 mt-4 pt-4 border-t border-zinc-800">
                    <h3 class="text-white font-bold mb-4">Status e Observações</h3>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Status *</label>
                    <select name="status" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors appearance-none">
                        <option value="Ativa" {{ old('status', $proposal->status ?? '') == 'Ativa' ? 'selected' : '' }}>🟢 Ativa</option>
                        <option value="Aguardando Cadastro" {{ old('status', $proposal->status ?? '') == 'Aguardando Cadastro' ? 'selected' : '' }}>🟡 Aguardando Cadastro</option>
                        <option value="Em Análise" {{ old('status', $proposal->status ?? '') == 'Em Análise' ? 'selected' : '' }}>🔵 Em Análise</option>
                        <option value="Convertida em Cliente" {{ old('status', $proposal->status ?? '') == 'Convertida em Cliente' ? 'selected' : '' }}>🟣 Convertida em Cliente</option>
                        <option value="Expirada" {{ old('status', $proposal->status ?? '') == 'Expirada' ? 'selected' : '' }}>🔴 Expirada</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Observações -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Observações Adicionais</label>
                    <textarea name="observacoes" rows="4" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-colors">{{ old('observacoes', $proposal->observacoes ?? '') }}</textarea>
                    @error('observacoes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-800 gap-4 mt-6">
                <a href="{{ route('representative.proposals.index') }}" class="px-8 py-3 rounded-xl text-zinc-500 hover:text-white hover:bg-zinc-800 text-xs font-black uppercase tracking-widest transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-zinc-950 px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">
                    Salvar Proposta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
