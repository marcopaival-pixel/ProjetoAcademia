@extends('layouts.onboarding-premium')

@section('title', 'Endereço')
@section('step_title', 'Localização')
@section('step_description', 'Onde sua empresa ou clínica está sediada? Estas informações serão usadas em cabeçalhos de documentos.')

@section('content')
<form action="{{ route('onboarding-premium.step.save', 4) }}" method="POST" class="space-y-12" x-data="{
    loading: false,
    zipCode: '{{ old('zip_code', $company->zip_code ?? '') }}',
    street: '{{ old('street', $company->street ?? '') }}',
    city: '{{ old('city', $company->city ?? '') }}',
    state: '{{ old('state', $company->state ?? '') }}',
    
    async lookupCep() {
        let cep = this.zipCode.replace(/\D/g, '');
        if (cep.length !== 8) return;
        
        this.loading = true;
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();
            if (!data.erro) {
                this.street = data.logradouro;
                this.city = data.localidade;
                this.state = data.uf;
            }
        } catch (e) {
            console.error('Erro ao buscar CEP');
        } finally {
            this.loading = false;
        }
    }
}">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- CEP -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">CEP</label>
            <div class="relative">
                <input type="text" name="zip_code" x-model="zipCode" @input="lookupCep()" required
                    placeholder="00000-000"
                    class="w-full input-premium">
                <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2">
                    <i class="fas fa-spinner fa-spin text-blue-500"></i>
                </div>
            </div>
        </div>

        <!-- Rua -->
        <div class="md:col-span-2 space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Rua / Logradouro</label>
            <input type="text" name="street" x-model="street" required
                placeholder="Ex: Av. Paulista"
                class="w-full input-premium">
        </div>

        <!-- Número -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Número</label>
            <input type="text" name="number" value="{{ old('number', $company->number ?? '') }}" required
                placeholder="Ex: 1000"
                class="w-full input-premium">
        </div>

        <!-- Cidade -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Cidade</label>
            <input type="text" name="city" x-model="city" required
                placeholder="Ex: São Paulo"
                class="w-full input-premium">
        </div>

        <!-- Estado -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Estado (UF)</label>
            <input type="text" name="state" x-model="state" required maxlength="2"
                placeholder="Ex: SP"
                class="w-full input-premium uppercase">
        </div>

        <!-- País -->
        <div class="md:col-span-3 space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">País</label>
            <select name="country" class="w-full input-premium bg-zinc-900">
                <option value="Brasil" {{ (old('country', $company->country ?? '') == 'Brasil') ? 'selected' : '' }}>Brasil</option>
                <option value="Portugal" {{ (old('country', $company->country ?? '') == 'Portugal') ? 'selected' : '' }}>Portugal</option>
                <option value="EUA" {{ (old('country', $company->country ?? '') == 'EUA') ? 'selected' : '' }}>EUA</option>
                <option value="Outro" {{ (old('country', $company->country ?? '') == 'Outro') ? 'selected' : '' }}>Outro</option>
            </select>
        </div>
    </div>

    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <a href="{{ route('onboarding-premium.step', 3) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3">
            Continuar para Admin <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
