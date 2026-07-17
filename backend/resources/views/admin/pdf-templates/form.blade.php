@extends('layouts.admin')

@section('title', $mode === 'create' ? 'Novo modelo PDF' : 'Editar modelo PDF')

@php
    use App\Enums\PdfDocumentType;
    $typeForHints = $template->document_type instanceof PdfDocumentType
        ? $template->document_type
        : PdfDocumentType::tryFrom((string) $template->document_type) ?? PdfDocumentType::Contract;
@endphp

@section('content')
<div class="space-y-8 max-w-6xl animate-fade-in">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.pdf-templates.index') }}" class="text-[10px] font-black uppercase text-zinc-500 hover:text-amber-500 transition-colors">
            ← Voltar à lista
        </a>
    </div>

    <form action="{{ $mode === 'create' ? route('admin.pdf-templates.store') : route('admin.pdf-templates.update', $template) }}" method="post" enctype="multipart/form-data" class="space-y-8" id="pdf-template-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-2">Nome do modelo</label>
                        <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                            class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/30 outline-none transition-all">
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-2">Tipo de documento</label>
                        <select name="document_type" id="document_type" required
                            class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-amber-500/50 outline-none">
                            @foreach($documentTypes as $dt)
                                <option value="{{ $dt->value }}" @selected(old('document_type', $template->document_type instanceof \App\Enums\PdfDocumentType ? $template->document_type->value : $template->document_type) === $dt->value)>
                                    {{ $dt->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-2">Descrição (opcional)</label>
                        <input type="text" name="description" value="{{ old('description', $template->description) }}"
                            class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-amber-500/50 outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-white/5 pt-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-zinc-500 mb-2">Empresa (multi-tenant)</label>
                            <select name="academy_company_id" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
                                <option value="">— Global (todas) —</option>
                                @foreach($companies as $co)
                                    <option value="{{ $co->id }}" @selected((string)old('academy_company_id', $template->academy_company_id) === (string)$co->id)>{{ $co->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-zinc-500 mb-2">Unidade</label>
                            <select name="academy_unit_id" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-sm text-white">
                                <option value="">— Todas / matriz —</option>
                                @foreach($units as $un)
                                    <option value="{{ $un->id }}" @selected((string)old('academy_unit_id', $template->academy_unit_id) === (string)$un->id)>{{ $un->name }} ({{ $un->company->name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-zinc-500 mb-2">Rodapé HTML (opcional)</label>
                        <textarea name="footer_html" rows="4" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-3 py-2 text-xs text-zinc-200 font-mono">{{ old('footer_html', $template->footer_html) }}</textarea>
                    </div>
                    <div class="space-y-3 border-t border-white/5 pt-4">
                        <label class="flex items-center gap-2 text-xs text-zinc-300">
                            <input type="hidden" name="auto_email_enabled" value="0">
                            <input type="checkbox" name="auto_email_enabled" value="1" @checked(old('auto_email_enabled', $template->auto_email_enabled)) class="rounded border-white/20">
                            Envio automático por e-mail após emissão oficial
                        </label>
                        <textarea name="auto_email_recipients" rows="2" placeholder="email1@…, email2@…" class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-xs font-mono text-zinc-300">{{ old('auto_email_recipients', is_array($template->auto_email_recipients) ? implode(', ', $template->auto_email_recipients) : '') }}</textarea>
                        <p class="text-[11px] text-zinc-500 leading-relaxed">Se o documento estiver associado a um utilizador, o PDF é enviado para o e-mail do cadastro (<code class="text-zinc-400">users.email</code>). Os endereços acima funcionam como cópias (CC) para equipa; sem utilizador associado, são os destinatários principais.</p>
                        <label class="flex items-center gap-2 text-xs text-zinc-300">
                            <input type="hidden" name="auto_whatsapp_enabled" value="0">
                            <input type="checkbox" name="auto_whatsapp_enabled" value="1" @checked(old('auto_whatsapp_enabled', $template->auto_whatsapp_enabled)) class="rounded border-white/20">
                            Tentar WhatsApp automático (requer integração)
                        </label>
                        <textarea name="auto_whatsapp_recipients" rows="2" placeholder="+351… (um por linha)" class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-xs font-mono text-zinc-300">{{ old('auto_whatsapp_recipients', is_array($template->auto_whatsapp_recipients) ? implode("\n", $template->auto_whatsapp_recipients) : '') }}</textarea>
                        <input type="text" name="whatsapp_message_template" value="{{ old('whatsapp_message_template', $template->whatsapp_message_template) }}" placeholder="Mensagem padrão WhatsApp" class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-xs text-white">
                    </div>
                </div>

                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6 space-y-4">
                    <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest">Corpo HTML</label>
                    <p class="text-[11px] text-zinc-500">Use <code class="text-amber-500/90">{{ '{{ variavel }}' }}</code> para variáveis dinâmicas.</p>
                    <textarea name="html_body" rows="16" required
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-zinc-200 font-mono focus:border-amber-500/50 outline-none">{{ old('html_body', $template->html_body) }}</textarea>
                    @error('html_body')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                </div>

                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6 space-y-4">
                    <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest">CSS adicional</label>
                    <textarea name="css_extra" rows="8"
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-xs text-zinc-200 font-mono focus:border-amber-500/50 outline-none">{{ old('css_extra', $template->css_extra) }}</textarea>
                    <p class="text-[10px] text-zinc-600">Cores do tema também podem ser usadas via <code class="text-zinc-400">var(--pdf-primary)</code>, <code class="text-zinc-400">var(--pdf-accent)</code>.</p>
                    @error('css_extra')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6 space-y-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Marca e tema</h3>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-zinc-500 tracking-widest mb-2">Logo</label>
                        @if($template->logo_path)
                            <div class="mb-3 p-3 rounded-xl bg-zinc-950 border border-white/10">
                                <img src="{{ asset('storage/'.$template->logo_path) }}" alt="Logo" class="max-h-16 object-contain">
                            </div>
                            <label class="flex items-center gap-2 text-xs text-zinc-400 mb-2">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-white/20">
                                Remover logo atual
                            </label>
                        @endif
                        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.gif,.webp,.svg"
                            class="w-full text-xs text-zinc-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-amber-500/20 file:text-amber-500 file:text-[10px] file:font-black file:uppercase">
                        @error('logo')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-zinc-500 mb-1">Primária</label>
                            <input type="text" name="primary_color" value="{{ old('primary_color', $template->primary_color ?? '#1e293b') }}"
                                class="w-full bg-zinc-950 border border-white/10 rounded-lg px-2 py-2 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-zinc-500 mb-1">Secundária</label>
                            <input type="text" name="secondary_color" value="{{ old('secondary_color', $template->secondary_color) }}"
                                class="w-full bg-zinc-950 border border-white/10 rounded-lg px-2 py-2 text-xs text-white" placeholder="#64748b">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-zinc-500 mb-1">Destaque</label>
                            <input type="text" name="accent_color" value="{{ old('accent_color', $template->accent_color) }}"
                                class="w-full bg-zinc-950 border border-white/10 rounded-lg px-2 py-2 text-xs text-white" placeholder="#3b82f6">
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center gap-2 text-xs text-zinc-300">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active ?? true)) class="rounded border-white/20">
                            Modelo ativo
                        </label>
                        <label class="flex items-center gap-2 text-xs text-zinc-300">
                            <input type="hidden" name="is_default" value="0">
                            <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $template->is_default)) class="rounded border-white/20">
                            Definir como padrão deste tipo
                        </label>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black uppercase text-zinc-500 mb-1">Ordem</label>
                        <input type="number" name="sort_order" min="0" max="65535" value="{{ old('sort_order', $template->sort_order ?? 0) }}"
                            class="w-full bg-zinc-950 border border-white/10 rounded-lg px-3 py-2 text-sm text-white">
                    </div>
                </div>

                <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6" id="variables-panel">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-3">Variáveis sugeridas</h3>
                    <p class="text-[10px] text-zinc-500 mb-3">Inclua no HTML como <code class="text-amber-500/80">{{ '{{ nome }}' }}</code></p>
                    <ul class="text-[10px] text-zinc-400 font-mono space-y-1 max-h-48 overflow-y-auto" id="variables-list">
                        @foreach($typeForHints->suggestedVariables() as $v)
                            <li><span class="text-amber-500/70">{{ '{{ '.$v.' }}' }}</span></li>
                        @endforeach
                    </ul>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="w-full py-3 rounded-xl bg-amber-600 text-black text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all">
                        {{ $mode === 'create' ? 'Guardar modelo' : 'Atualizar modelo' }}
                    </button>
                    @if($mode === 'edit')
                        <button type="button" id="btn-preview" class="w-full py-3 rounded-xl bg-zinc-800 border border-white/10 text-zinc-200 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700 transition-all">
                            Pré-visualizar PDF
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>

    @if($mode === 'edit')
        <form id="preview-form" action="{{ route('admin.pdf-templates.preview', $template) }}" method="post" target="_blank" class="hidden">
            @csrf
            <input type="hidden" name="variables_json" id="preview-variables" value="">
        </form>
    @endif
</div>

@push('scripts')
<script>
window.__pdfSuggestedByType = @json(collect(\App\Enums\PdfDocumentType::cases())->mapWithKeys(fn ($d) => [$d->value => $d->suggestedVariables()]));
window.__pdfSampleVarsByType = @json(collect(\App\Enums\PdfDocumentType::cases())->mapWithKeys(fn ($d) => [$d->value => $d->sampleVariables()]));
</script>
@verbatim
<script>
(function() {
    const typeSelect = document.getElementById('document_type');
    const list = document.getElementById('variables-list');
    const samples = window.__pdfSuggestedByType || {};
    const open = '{{ ';
    const close = ' }}';

    function renderHints(docType) {
        const vars = samples[docType] || [];
        list.innerHTML = vars.map(function(v) {
            return '<li><span class="text-amber-500/70">' + open + v + close + '</span></li>';
        }).join('') || '<li class="text-zinc-600">—</li>';
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            renderHints(this.value);
        });
    }

    const btn = document.getElementById('btn-preview');
    if (btn && typeSelect) {
        btn.addEventListener('click', function() {
            const docType = typeSelect.value;
            const sampleObj = window.__pdfSampleVarsByType || {};
            const vars = sampleObj[docType] || {};
            document.getElementById('preview-variables').value = JSON.stringify(vars);
            document.getElementById('preview-form').submit();
        });
    }
})();
</script>
@endverbatim
@endpush
@endsection
