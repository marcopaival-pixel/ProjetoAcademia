@extends('layouts.admin')

@section('title', 'Configuração de PDF')

@section('content')
<div class="space-y-10 max-w-5xl animate-fade-in">
    <div>
        <h1 class="text-2xl font-black text-white tracking-tight">Documentos <span class="text-amber-500">PDF</span></h1>
        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Modelos, histórico, empresas, validação e integrações</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @if(auth()->user()->hasPermission('pdf.templates.manage') || auth()->user()->isAdministrator())
        <a href="{{ route('admin.pdf-templates.index') }}" class="block p-6 rounded-2xl bg-zinc-900/50 border border-white/10 hover:border-amber-500/30 transition-all">
            <h2 class="text-sm font-black text-white">Templates</h2>
            <p class="text-xs text-zinc-500 mt-2">Modelos por empresa/unidade, rodapé, envio automático.</p>
        </a>
        @endif
        @if(auth()->user()->hasPermission('pdf.documents.generate') || auth()->user()->isAdministrator())
        <a href="{{ route('admin.pdf-documents.generate') }}" class="block p-6 rounded-2xl bg-zinc-900/50 border border-white/10 hover:border-blue-500/30 transition-all">
            <h2 class="text-sm font-black text-white">Gerar documento</h2>
            <p class="text-xs text-zinc-500 mt-2">Emissão simples ou oficial com QR e numeração.</p>
        </a>
        @endif
        @if(auth()->user()->hasPermission('pdf.history.view') || auth()->user()->isAdministrator())
        <a href="{{ route('admin.pdf-historico.index') }}" class="block p-6 rounded-2xl bg-zinc-900/50 border border-white/10 hover:border-emerald-500/30 transition-all">
            <h2 class="text-sm font-black text-white">Histórico</h2>
            <p class="text-xs text-zinc-500 mt-2">PDFs gerados, download, cancelar, reenviar, assinar.</p>
        </a>
        @endif
        @if(auth()->user()->hasPermission('pdf.templates.manage') || auth()->user()->isAdministrator())
        <a href="{{ route('admin.pdf-templates.logs') }}" class="block p-6 rounded-2xl bg-zinc-900/50 border border-white/10 hover:border-zinc-500/30 transition-all">
            <h2 class="text-sm font-black text-white">Logs técnicos</h2>
            <p class="text-xs text-zinc-500 mt-2">Pré-visualizações e descargas (auditoria técnica).</p>
        </a>
        @endif
        @if(auth()->user()->hasPermission('pdf.companies.manage') || auth()->user()->isAdministrator())
        <a href="{{ route('admin.pdf-companies.index') }}" class="block p-6 rounded-2xl bg-zinc-900/50 border border-white/10 hover:border-purple-500/30 transition-all">
            <h2 class="text-sm font-black text-white">Empresas e unidades</h2>
            <p class="text-xs text-zinc-500 mt-2">Multi-empresa, marca d&apos;água, isolamento.</p>
        </a>
        @endif
        <div class="block p-6 rounded-2xl bg-zinc-900/30 border border-white/5">
            <h2 class="text-sm font-black text-zinc-400">Validação pública</h2>
            <p class="text-xs text-zinc-600 mt-2 mb-3">URL base: <code class="text-amber-600/80">{{ url('/'.trim(config('pdf.validation_path_segment'), '/')) }}</code></p>
            <p class="text-xs text-zinc-600">Cada PDF oficial inclui QR com link único por código.</p>
        </div>
        @if(auth()->user()->hasPermission('pdf.integrations.view') || auth()->user()->isAdministrator())
        <a href="{{ route('admin.api-integrations.index') }}" class="block p-6 rounded-2xl bg-zinc-900/50 border border-white/10 hover:border-cyan-500/30 transition-all">
            <h2 class="text-sm font-black text-white">Integrações</h2>
            <p class="text-xs text-zinc-500 mt-2">APIs externas, gateways e credenciais.</p>
            <p class="text-xs text-zinc-600 mt-2">E-mail oficial: fila Laravel (<code class="text-zinc-500">queue:work</code>). WhatsApp: <code class="text-zinc-500">WHATSAPP_*</code> no <code class="text-zinc-500">.env</code>.</p>
        </a>
        @else
        <div class="block p-6 rounded-2xl bg-zinc-900/30 border border-white/5">
            <h2 class="text-sm font-black text-zinc-400">Integrações</h2>
            <p class="text-xs text-zinc-600 mt-2">E-mail: fila Laravel (execute <code class="text-zinc-500">queue:work</code>).</p>
            <p class="text-xs text-zinc-600 mt-1">WhatsApp: <code class="text-zinc-500">WHATSAPP_DRIVER</code> / <code class="text-zinc-500">WHATSAPP_API_URL</code> em <code class="text-zinc-500">.env</code>.</p>
        </div>
        @endif
    </div>
</div>
@endsection
