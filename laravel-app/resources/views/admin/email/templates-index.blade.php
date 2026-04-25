@extends('layouts.admin')

@section('title', 'Templates de e-mail')

@section('content')
<div class="space-y-10 animate-fade-in max-w-6xl mx-auto">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-bold">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Templates de e-mail</h2>
            <p class="text-zinc-500 text-sm mt-1">Substituem o conteúdo padrão quando ativos. Variáveis: <code class="text-zinc-400">@{{name}}, @{{verification_url}}, @{{app_name}}</code> (conforme o tipo).</p>
        </div>
        <a href="{{ route('admin.settings.email.templates.create') }}" class="px-6 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all">Novo template</a>
    </div>

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2rem] overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-950/80 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                <tr>
                    <th class="px-8 py-5">Nome</th>
                    <th class="px-8 py-5">Tipo</th>
                    <th class="px-8 py-5">Empresa</th>
                    <th class="px-8 py-5">Estado</th>
                    <th class="px-8 py-5 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($templates as $t)
                    <tr class="hover:bg-white/[0.02]">
                        <td class="px-8 py-5 text-white font-bold">{{ $t->nome_template }}</td>
                        <td class="px-8 py-5 text-zinc-400 text-xs">{{ $t->tipo }}</td>
                        <td class="px-8 py-5 text-zinc-400">{{ $t->empresa?->name ?? 'Global' }}</td>
                        <td class="px-8 py-5">
                            @if($t->ativo)
                                <span class="text-[10px] font-black uppercase text-emerald-500">Ativo</span>
                            @else
                                <span class="text-[10px] font-black uppercase text-zinc-500">Inativo</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('admin.settings.email.templates.edit', $t) }}" class="inline-flex px-4 py-2 rounded-xl bg-blue-600/20 text-blue-400 text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-zinc-500">Nenhum template. O sistema usa as vistas Blade padrão.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $templates->links() }}
    </div>
</div>
@endsection
