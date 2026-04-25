@extends('layouts.admin')

@section('title', $mode === 'create' ? 'Nova empresa' : 'Editar empresa')

@section('content')
<div class="max-w-xl space-y-8 animate-fade-in">
    <a href="{{ route('admin.pdf-companies.index') }}" class="text-[10px] font-black uppercase text-zinc-500">← Lista</a>

    <form method="post" action="{{ $mode === 'create' ? route('admin.pdf-companies.store') : route('admin.pdf-companies.update', $company) }}" class="space-y-6 bg-zinc-900/40 border border-white/5 rounded-2xl p-6">
        @csrf
        <div>
            <label class="text-[10px] font-black uppercase text-zinc-500">Nome</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
        </div>
        <div>
            <label class="text-[10px] font-black uppercase text-zinc-500">Razão social</label>
            <input type="text" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
        </div>
        <div>
            <label class="text-[10px] font-black uppercase text-zinc-500">NIF / Documento</label>
            <input type="text" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
        </div>
        <div class="border-t border-white/5 pt-4">
            <h3 class="text-xs font-black text-white uppercase tracking-widest mb-3">Marca d&apos;água (PDF)</h3>
            <div>
                <label class="text-[10px] font-black uppercase text-zinc-500">Texto</label>
                <input type="text" name="watermark_text" value="{{ old('watermark_text', $watermarkText ?? '') }}" placeholder="CONFIDENCIAL" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
            </div>
            <div class="mt-3">
                <label class="text-[10px] font-black uppercase text-zinc-500">Opacidade (0.02–1)</label>
                <input type="number" step="0.01" min="0.02" max="1" name="watermark_opacity" value="{{ old('watermark_opacity', $watermarkOpacity ?? 0.12) }}" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
            </div>
        </div>
        <button type="submit" class="w-full py-3 rounded-xl bg-purple-600 text-white text-[10px] font-black uppercase">{{ $mode === 'create' ? 'Criar' : 'Guardar' }}</button>
    </form>

    @if($mode === 'edit')
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6">
            <h3 class="text-xs font-black text-white uppercase tracking-widest mb-4">Unidades</h3>
            <ul class="space-y-2 mb-6">
                @foreach($company->units as $u)
                    <li class="text-sm text-zinc-300">{{ $u->name }} @if($u->code)<span class="text-zinc-600">({{ $u->code }})</span>@endif</li>
                @endforeach
            </ul>
            <form method="post" action="{{ route('admin.pdf-companies.units.store', $company) }}" class="flex flex-wrap gap-2 items-end">
                @csrf
                <div>
                    <label class="text-[9px] text-zinc-500 uppercase font-black">Nova unidade</label>
                    <input type="text" name="name" required placeholder="Academia Centro" class="mt-1 bg-zinc-950 border border-white/10 rounded-lg px-2 py-2 text-xs text-white">
                </div>
                <div>
                    <label class="text-[9px] text-zinc-500 uppercase font-black">Código</label>
                    <input type="text" name="code" placeholder="CTR" class="mt-1 bg-zinc-950 border border-white/10 rounded-lg px-2 py-2 text-xs text-white">
                </div>
                <button type="submit" class="px-3 py-2 rounded-lg bg-zinc-800 text-[10px] font-black uppercase text-white">Adicionar</button>
            </form>
        </div>
    @endif
</div>
@endsection
