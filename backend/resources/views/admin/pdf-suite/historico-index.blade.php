@extends('layouts.admin')

@section('title', 'Histórico de PDFs')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Histórico de <span class="text-emerald-500">PDFs</span></h1>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Documentos oficiais com numeração e validação</p>
        </div>
        <a href="{{ route('admin.pdf-suite.index') }}" class="text-[10px] font-black uppercase text-amber-500">← Hub PDF</a>
    </div>

    <form method="get" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 bg-zinc-900/40 border border-white/5 rounded-2xl p-4">
        @if(auth()->user()->isAdministrator())
        <div>
            <label class="text-[9px] font-black uppercase text-zinc-500">Empresa</label>
            <select name="empresa_id" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-lg text-xs text-white px-2 py-2">
                <option value="">—</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected(request('empresa_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="text-[9px] font-black uppercase text-zinc-500">Utilizador ID</label>
            <input type="number" name="usuario_id" value="{{ request('usuario_id') }}" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-lg text-xs text-white px-2 py-2">
        </div>
        <div>
            <label class="text-[9px] font-black uppercase text-zinc-500">Tipo</label>
            <select name="tipo_documento" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-lg text-xs text-white px-2 py-2">
                <option value="">—</option>
                @foreach($documentTypes as $dt)
                    <option value="{{ $dt->value }}" @selected(request('tipo_documento') === $dt->value)>{{ $dt->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[9px] font-black uppercase text-zinc-500">Estado validação</label>
            <select name="validation_status" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-lg text-xs text-white px-2 py-2">
                <option value="">—</option>
                @foreach(\App\Enums\PdfValidationStatus::cases() as $st)
                    <option value="{{ $st->value }}" @selected(request('validation_status') === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[9px] font-black uppercase text-zinc-500">De</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-lg text-xs text-white px-2 py-2">
        </div>
        <div>
            <label class="text-[9px] font-black uppercase text-zinc-500">Até</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full mt-1 bg-zinc-950 border border-white/10 rounded-lg text-xs text-white px-2 py-2">
        </div>
        <div class="md:col-span-3 lg:col-span-6 flex gap-2">
            <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-black text-[10px] font-black uppercase">Filtrar</button>
            <a href="{{ route('admin.pdf-historico.index') }}" class="px-4 py-2 rounded-xl border border-white/10 text-zinc-400 text-[10px] font-black uppercase">Limpar</a>
        </div>
    </form>

    <div class="bg-zinc-900/40 border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-600 text-[10px] font-black uppercase border-b border-white/5">
                        <th class="px-4 py-3">Número</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Empresa</th>
                        <th class="px-4 py-3">Emitido</th>
                        <th class="px-4 py-3">Validação</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($items as $row)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-4 py-3 font-mono text-zinc-300">{{ $row->numero_oficial ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $row->document_type->label() }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $row->company?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $row->issued_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[9px] font-black uppercase @if($row->resolvedValidationStatus() === \App\Enums\PdfValidationStatus::Valid) text-emerald-400 @elseif($row->resolvedValidationStatus() === \App\Enums\PdfValidationStatus::Cancelled) text-red-400 @else text-amber-400 @endif">
                                    {{ $row->resolvedValidationStatus()->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <a href="{{ route('admin.pdf-historico.download', $row) }}" class="text-blue-400 font-bold">PDF</a>
                                @if(auth()->user()->hasPermission('pdf.document.sign') || auth()->user()->isAdministrator())
                                <button type="button" onclick="document.getElementById('sig-{{ $row->id }}').classList.toggle('hidden')" class="text-amber-400 font-bold">Assinar</button>
                                @endif
                                @if(auth()->user()->hasPermission('pdf.delivery.email') || auth()->user()->hasPermission('pdf.delivery.whatsapp') || auth()->user()->isAdministrator())
                                <form action="{{ route('admin.pdf-historico.resend', $row) }}" method="post" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-400 font-bold">Reenviar</button>
                                </form>
                                @endif
                                @if(auth()->user()->hasPermission('pdf.document.cancel') || auth()->user()->isAdministrator())
                                <form action="{{ route('admin.pdf-historico.cancel', $row) }}" method="post" class="inline"
                                data-confirm-delete
                                data-confirm-title="Cancelar validação"
                                data-confirm-message="Cancelar a validação deste documento?">
                                    @csrf
                                    <button type="submit" class="text-red-400 font-bold">Cancelar</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @if(auth()->user()->hasPermission('pdf.document.sign') || auth()->user()->isAdministrator())
                        <tr id="sig-{{ $row->id }}" class="hidden bg-zinc-950/50">
                            <td colspan="6" class="px-4 py-4">
                                <form action="{{ route('admin.pdf-historico.signatures.store', $row) }}" method="post" enctype="multipart/form-data" class="flex flex-wrap gap-4 items-end">
                                    @csrf
                                    <div>
                                        <label class="text-[9px] text-zinc-500 uppercase font-black">Papel</label>
                                        <select name="tipo_assinatura" class="block mt-1 bg-zinc-900 border border-white/10 rounded-lg text-xs px-2 py-2 text-white" required>
                                            @foreach(\App\Enums\PdfSignatureRole::cases() as $r)
                                                <option value="{{ $r->value }}">{{ $r->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[9px] text-zinc-500 uppercase font-black">Modo</label>
                                        <select name="modo" class="block mt-1 bg-zinc-900 border border-white/10 rounded-lg text-xs px-2 py-2 text-white" required>
                                            <option value="upload">Upload imagem</option>
                                            <option value="manual">Manual (imagem exportada)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[9px] text-zinc-500 uppercase font-black">Imagem</label>
                                        <input type="file" name="imagem" accept="image/*" class="block mt-1 text-xs text-zinc-400" required>
                                    </div>
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-black text-[10px] font-black uppercase">Guardar assinatura</button>
                                </form>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-500">Sem registos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/5">{{ $items->links() }}</div>
    </div>
</div>
@endsection
