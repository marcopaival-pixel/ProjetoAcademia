@extends('layouts.clinic-onboarding')

@section('title', 'Cadastro da Clínica')

@section('content')
<form action="{{ route('admin.clinic-onboarding.step.save', [$company, 1]) }}" method="POST" class="space-y-8">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Nome da Clínica (Fantasia)</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>
        
        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Razão Social</label>
            <input type="text" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}"
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">CNPJ / Identificação Fiscal</label>
            <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" required
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Nome do Responsável</label>
            <input type="text" name="responsible_name" value="{{ old('responsible_name', $company->responsible_name) }}" required
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Email do Responsável</label>
            <input type="email" name="responsible_email" value="{{ old('responsible_email', $company->responsible_email) }}" required
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Telefone / WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all"
                placeholder="(11) 99999-9999" oninput="maskPhone(this)">
        </div>

        <div class="md:col-span-2 space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Endereço Completo</label>
            <input type="text" name="address" value="{{ old('address', $company->address) }}"
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-semibold text-zinc-400">Cidade</label>
            <input type="text" name="city" value="{{ old('city', $company->city) }}"
                class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-zinc-400">Estado (UF)</label>
                <input type="text" name="state" value="{{ old('state', $company->state) }}" maxlength="2"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all uppercase">
            </div>
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-zinc-400">CEP</label>
                <input type="text" name="zip_code" value="{{ old('zip_code', $company->zip_code) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 text-white focus:outline-none focus:border-blue-500 transition-all">
            </div>
        </div>
    </div>

    <div class="pt-8 border-t border-white/5 flex justify-end">
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Salvar e Continuar
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </div>
</form>
@endsection
