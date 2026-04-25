@extends('layouts.admin')

@section('title', 'Gerar documento PDF')

@section('content')
<div class="space-y-8 max-w-4xl animate-fade-in">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Gerar <span class="text-blue-400">PDF</span></h1>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Escolha o modelo ativo e personalize variáveis (JSON)</p>
        </div>
        @if(auth()->user()->hasPermission('pdf.templates.manage') || auth()->user()->isAdministrator())
            <a href="{{ route('admin.pdf-templates.index') }}" class="text-[10px] font-black uppercase text-amber-500 hover:text-amber-400">Modelos →</a>
        @endif
        <a href="{{ route('admin.pdf-suite.index') }}" class="text-[10px] font-black uppercase text-zinc-500">Hub PDF</a>
    </div>

    <form id="gen-form" method="post" class="bg-zinc-900/40 border border-white/5 rounded-2xl p-8 space-y-6">
        @csrf
        <div class="border border-amber-500/20 rounded-xl p-4 space-y-4 bg-amber-500/5">
            <h2 class="text-xs font-black text-amber-500 uppercase tracking-widest">Emissão oficial (numeração + QR + histórico)</h2>
            <label class="flex items-center gap-2 text-xs text-zinc-300">
                <input type="hidden" name="register_official" value="0">
                <input type="checkbox" name="register_official" value="1" id="register_official" class="rounded border-white/20">
                Registar documento oficial (numeração sequencial, código de validação, QR, ficheiro arquivado)
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="official-fields">
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-500 mb-1">Empresa</label>
                    <select name="academy_company_id" class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-xs text-white" id="academy_company_id">
                        <option value="">—</option>
                        @foreach($companies as $co)
                            <option value="{{ $co->id }}" @selected((string)$co->id === (string)(auth()->user()->academy_company_id ?? ''))>{{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-500 mb-1">Unidade (opcional)</label>
                    <select name="academy_unit_id" id="academy_unit_id" class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-xs text-white">
                        <option value="">—</option>
                        @foreach($units as $un)
                            <option value="{{ $un->id }}" data-company="{{ $un->academy_company_id }}">{{ $un->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black uppercase text-zinc-500 mb-1">Telefones WhatsApp (um por linha, reenvio manual)</label>
                    <textarea name="whatsapp_recipients_text" rows="2" placeholder="+351..." class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-xs text-zinc-300 font-mono"></textarea>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-2">Modelo</label>
            <select name="pdf_template_id" id="pdf_template_id" required
                class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-blue-500/50 outline-none">
                @php
                    $currentType = null;
                @endphp
                @foreach($templates as $tpl)
                    @if($currentType !== $tpl->document_type->value)
                        @if($currentType !== null)</optgroup>@endif
                        @php
                            $currentType = $tpl->document_type->value;
                        @endphp
                        <optgroup label="{{ $tpl->document_type->label() }}">
                    @endif
                    <option value="{{ $tpl->id }}" data-type="{{ $tpl->document_type->value }}">{{ $tpl->name }}@if($tpl->is_default) (padrão)@endif</option>
                @endforeach
                @if($templates->isNotEmpty())</optgroup>@endif
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-2">Variáveis (JSON opcional)</label>
            <p class="text-[11px] text-zinc-500 mb-2">Sobrepõe os valores de exemplo. Chaves alfanuméricas e underscore.</p>
            <textarea name="variables_json" id="variables_json" rows="12"
                class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-zinc-200 font-mono focus:border-blue-500/50 outline-none"></textarea>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" id="btn-fill-sample" class="px-4 py-2 rounded-xl bg-zinc-800 border border-white/10 text-[10px] font-black uppercase text-zinc-300 hover:bg-zinc-700 transition-all">
                Preencher exemplo
            </button>
            <button type="submit" formaction="{{ route('admin.pdf-documents.preview') }}" formtarget="_blank"
                class="px-4 py-2 rounded-xl bg-blue-600/20 border border-blue-500/40 text-[10px] font-black uppercase text-blue-400 hover:bg-blue-600/30 transition-all">
                Pré-visualizar
            </button>
            <button type="submit" formaction="{{ route('admin.pdf-documents.download') }}"
                class="px-4 py-2 rounded-xl bg-emerald-600 text-black text-[10px] font-black uppercase hover:bg-emerald-500 transition-all">
                Descarregar PDF
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
window.__pdfSampleVarsByType = @json(collect($documentTypes)->mapWithKeys(fn ($d) => [$d->value => $d->sampleVariables()]));
</script>
@verbatim
<script>
(function() {
    const select = document.getElementById('pdf_template_id');
    const ta = document.getElementById('variables_json');
    const samples = window.__pdfSampleVarsByType || {};
    const companySelect = document.getElementById('academy_company_id');
    const unitSelect = document.getElementById('academy_unit_id');
    const regOfficial = document.getElementById('register_official');

    function selectedType() {
        const opt = select.options[select.selectedIndex];
        return opt ? opt.getAttribute('data-type') : null;
    }

    function fillSample() {
        const t = selectedType();
        const obj = t ? (samples[t] || {}) : {};
        ta.value = JSON.stringify(obj, null, 2);
    }

    function filterUnits() {
        if (!unitSelect || !companySelect) return;
        const cid = companySelect.value;
        for (let i = 0; i < unitSelect.options.length; i++) {
            const opt = unitSelect.options[i];
            if (!opt.value) { opt.hidden = false; continue; }
            const oc = opt.getAttribute('data-company');
            opt.hidden = cid !== '' && oc !== cid;
        }
    }

    function toggleOfficial() {
        const on = regOfficial && regOfficial.checked;
        const box = document.getElementById('official-fields');
        if (box) box.style.opacity = on ? '1' : '0.45';
        if (companySelect) companySelect.required = !!on;
    }

    document.getElementById('btn-fill-sample').addEventListener('click', fillSample);
    select.addEventListener('change', fillSample);
    if (select.options.length) {
        fillSample();
    }
    if (companySelect) companySelect.addEventListener('change', filterUnits);
    if (regOfficial) regOfficial.addEventListener('change', toggleOfficial);
    filterUnits();
    toggleOfficial();
})();
</script>
@endverbatim
@endpush
@endsection
