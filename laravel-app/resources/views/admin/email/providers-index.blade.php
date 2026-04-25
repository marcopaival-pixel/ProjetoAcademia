@extends('layouts.admin')

@section('title', 'Provedores de e-mail por empresa')

@section('content')
<div class="space-y-10 animate-fade-in max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Provedor de e-mail</h2>
            <p class="text-zinc-500 text-sm mt-1">Uma configuração por empresa (Academia). O fallback global continua em <a href="{{ route('admin.settings') }}" class="text-blue-400 hover:underline">Configurações gerais</a>.</p>
        </div>
    </div>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2rem] overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-950/80 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                <tr>
                    <th class="px-8 py-5">Empresa</th>
                    <th class="px-8 py-5">Provedor</th>
                    <th class="px-8 py-5">Estado</th>
                    <th class="px-8 py-5 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($companies as $c)
                    @php($cfg = $c->configuracaoEmail)
                    <tr class="hover:bg-white/[0.02]">
                        <td class="px-8 py-5 text-white font-bold">{{ $c->name }}</td>
                        <td class="px-8 py-5 text-zinc-400">{{ $cfg?->nome_provedor ?? '—' }}</td>
                        <td class="px-8 py-5">
                            @if(!$cfg)
                                <span class="text-[10px] font-black uppercase text-zinc-500">Não configurado</span>
                            @elseif($cfg->ativo)
                                <span class="text-[10px] font-black uppercase text-emerald-500">Ativo</span>
                            @else
                                <span class="text-[10px] font-black uppercase text-amber-500">Desativado</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('admin.settings.email.providers.edit', $c) }}" class="inline-flex px-4 py-2 rounded-xl bg-blue-600/20 text-blue-400 text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-zinc-500">Nenhuma empresa cadastrada. Crie empresas em Documentos PDF / multi-empresa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
