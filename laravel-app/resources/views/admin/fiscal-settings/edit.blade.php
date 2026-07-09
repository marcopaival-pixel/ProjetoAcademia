@extends('layouts.admin')

@section('title', 'Configuração Fiscal')

@section('content')
<div class="max-w-5xl space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Financeiro</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Configuração Fiscal</h1>
        </div>
        <a href="{{ route('admin.financial.fiscal-invoices.index') }}" class="px-4 py-2 rounded-xl bg-zinc-900 border border-white/10 text-xs font-black uppercase text-zinc-300">Notas fiscais</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-300">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.financial.fiscal-settings.update') }}" class="bg-[#11141b] border border-white/5 rounded-2xl p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        @csrf
        <label class="space-y-2">
            <span class="text-[10px] uppercase font-black text-zinc-500">Provedor</span>
            <select name="provider" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white">
                <option value="fake" @selected(old('provider', $setting->provider) === 'fake')>Fake homologação interna</option>
            </select>
        </label>
        <label class="space-y-2">
            <span class="text-[10px] uppercase font-black text-zinc-500">Ambiente</span>
            <select name="environment" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white">
                <option value="sandbox" @selected(old('environment', $setting->environment) === 'sandbox')>Homologação</option>
                <option value="production" @selected(old('environment', $setting->environment) === 'production')>Produção</option>
            </select>
        </label>
        @foreach([
            'issuer_cnpj' => 'CNPJ emitente',
            'issuer_legal_name' => 'Razão social',
            'municipal_registration' => 'Inscrição municipal',
            'tax_regime' => 'Regime tributário',
            'cnae' => 'CNAE',
            'municipal_service_code' => 'Código de serviço municipal',
            'iss_rate' => 'Alíquota ISS (%)',
            'certificate_reference' => 'Referência do certificado',
        ] as $field => $label)
            <label class="space-y-2">
                <span class="text-[10px] uppercase font-black text-zinc-500">{{ $label }}</span>
                <input name="{{ $field }}" value="{{ old($field, $setting->{$field}) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white">
                @error($field)<span class="text-xs text-rose-400">{{ $message }}</span>@enderror
            </label>
        @endforeach
        <label class="space-y-2 md:col-span-2">
            <span class="text-[10px] uppercase font-black text-zinc-500">Token/API</span>
            <textarea name="api_token" rows="3" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white">{{ old('api_token', $setting->api_token) }}</textarea>
        </label>
        <label class="flex items-center gap-3 md:col-span-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $setting->is_active))>
            <span class="text-sm text-zinc-300">Configuração fiscal ativa</span>
        </label>
        <div class="md:col-span-2 flex justify-end">
            <button class="px-6 py-3 rounded-xl bg-emerald-600 text-white text-xs font-black uppercase">Guardar</button>
        </div>
    </form>
</div>
@endsection
