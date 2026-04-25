@extends('layouts.admin')

@section('title', 'Modelos de PDF')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Modelos de <span class="text-amber-500">PDF</span></h1>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">HTML, CSS, logo e variáveis dinâmicas por tipo de documento</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->hasPermission('pdf.templates.manage') || auth()->user()->hasPermission('pdf.documents.generate') || auth()->user()->hasPermission('pdf.history.view') || auth()->user()->hasPermission('pdf.companies.manage') || auth()->user()->hasPermission('pdf.integrations.view') || auth()->user()->isAdministrator())
                <a href="{{ route('admin.pdf-suite.index') }}" class="px-4 py-2 rounded-xl bg-zinc-800 border border-amber-500/30 text-amber-400 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700 transition-all">
                    Hub PDF
                </a>
            @endif
            @if(auth()->user()->hasPermission('pdf.documents.generate') || auth()->user()->isAdministrator())
                <a href="{{ route('admin.pdf-documents.generate') }}" class="px-4 py-2 rounded-xl bg-blue-600/20 border border-blue-500/30 text-blue-400 text-[10px] font-black uppercase tracking-widest hover:bg-blue-600/30 transition-all">
                    Gerar documento
                </a>
            @endif
            <a href="{{ route('admin.pdf-templates.logs') }}" class="px-4 py-2 rounded-xl bg-zinc-800 border border-white/10 text-zinc-300 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700 transition-all">
                Logs de geração
            </a>
            <a href="{{ route('admin.pdf-templates.create') }}" class="px-4 py-2 rounded-xl bg-amber-600 text-black text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 transition-all">
                Novo modelo
            </a>
        </div>
    </div>

    @foreach($documentTypes as $docType)
        @php
            $group = $templatesByType->get($docType->value, collect());
        @endphp
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                <h2 class="text-sm font-black text-white tracking-tight">{{ $docType->label() }}</h2>
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $group->count() }} modelo(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.15em] border-b border-white/5">
                            <th class="px-6 py-3">Nome</th>
                            <th class="px-6 py-3">Empresa / Unidade</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3">Padrão</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($group as $tpl)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-zinc-200">{{ $tpl->name }}</p>
                                    @if($tpl->description)
                                        <p class="text-[10px] text-zinc-600 mt-1">{{ Str::limit($tpl->description, 80) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[11px] text-zinc-500">
                                    @if($tpl->company)
                                        <span class="text-zinc-300">{{ $tpl->company->name }}</span>
                                    @else
                                        <span class="text-zinc-600">Global</span>
                                    @endif
                                    @if($tpl->unit)
                                        <span class="block text-[10px] text-zinc-600 mt-0.5">{{ $tpl->unit->name }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($tpl->is_active)
                                        <span class="text-[9px] font-black uppercase text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/20">Ativo</span>
                                    @else
                                        <span class="text-[9px] font-black uppercase text-zinc-500 bg-zinc-800 px-2 py-1 rounded-lg border border-white/5">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($tpl->is_default)
                                        <span class="text-[9px] font-black uppercase text-amber-400">Sim</span>
                                    @else
                                        <span class="text-[9px] text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <form action="{{ route('admin.pdf-templates.toggle-active', $tpl) }}" method="post" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg border border-white/10 text-zinc-400 hover:text-white hover:border-white/20 transition-all">
                                                {{ $tpl->is_active ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.pdf-templates.edit', $tpl) }}" class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 hover:bg-amber-500/20 transition-all">Editar</a>
                                        <form action="{{ route('admin.pdf-templates.duplicate', $tpl) }}" method="post" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg border border-white/10 text-zinc-400 hover:text-white hover:border-white/20 transition-all">Duplicar</button>
                                        </form>
                                        <form action="{{ route('admin.pdf-templates.destroy', $tpl) }}" method="post" class="inline"
                                        data-confirm-delete
                                        data-confirm-title="Remover modelo"
                                        data-confirm-message="Remover este modelo de PDF? Esta ação não pode ser desfeita.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500/10 transition-all">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-zinc-500">Nenhum modelo para este tipo. <a href="{{ route('admin.pdf-templates.create') }}" class="text-amber-500 font-bold hover:underline">Criar</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
