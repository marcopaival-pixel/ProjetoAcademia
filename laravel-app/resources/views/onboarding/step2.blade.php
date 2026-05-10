@extends('layouts.onboarding-premium')

@section('title', 'Dados Empresariais')
@section('step_title', 'Informações do Negócio')
@section('step_description', 'Precisamos dos dados legais da sua empresa ou perfil profissional para emissão de documentos e conformidade fiscal.')

@section('content')
<form action="{{ route('onboarding-premium.step.save', 2) }}" method="POST" class="space-y-12">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Nome Fantasia -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Nome Fantasia / Marca</label>
            <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
                placeholder="Ex: Clínica Viver Bem"
                class="w-full input-premium">
        </div>

        <!-- Razão Social -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">
                Razão Social
                <x-onboarding-tooltip text="O nome oficial registrado na junta comercial ou cartório." />
            </label>
            <input type="text" name="legal_name" value="{{ old('legal_name', $company->legal_name ?? '') }}"
                placeholder="Ex: Viver Bem Serviços Médicos Ltda"
                class="w-full input-premium">
        </div>

        <!-- CPF / CNPJ -->
        <div class="space-y-3" x-data="{ 
            taxId: '{{ old('tax_id', $company->tax_id ?? '') }}',
            mask() {
                let v = this.taxId.replace(/\D/g, '');
                if (v.length <= 11) {
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    v = v.replace(/^(\d{2})(\d)/, '$1.$2');
                    v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                    v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
                    v = v.replace(/(\d{4})(\d)/, '$1-$2');
                }
                this.taxId = v.substring(0, 18);
            }
        }">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">
                CPF / CNPJ / NIF
                <x-onboarding-tooltip text="Insira o documento oficial. Para profissionais liberais use o CPF. Para clínicas use o CNPJ." />
            </label>
            <input type="text" name="tax_id" x-model="taxId" @input="mask()" required
                placeholder="00.000.000/0000-00"
                class="w-full input-premium">
            <p class="text-[10px] text-zinc-600 mt-2 px-1">Detectamos automaticamente o tipo de documento.</p>
        </div>

        <!-- Inscrição Estadual -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Inscrição Estadual</label>
            <input type="text" name="state_registration" value="{{ old('state_registration', $company->state_registration ?? '') }}"
                placeholder="Isento ou Número"
                class="w-full input-premium">
        </div>

        <!-- Inscrição Municipal -->
        <div class="md:col-span-2 space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Inscrição Municipal</label>
            <input type="text" name="municipal_registration" value="{{ old('municipal_registration', $company->municipal_registration ?? '') }}"
                placeholder="Número da inscrição na prefeitura"
                class="w-full input-premium">
        </div>
    </div>

    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <a href="{{ route('onboarding-premium.step', 1) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3">
            Continuar para Contato <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
