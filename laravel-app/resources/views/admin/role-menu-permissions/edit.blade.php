@extends('layouts.admin')

@section('title', 'Menus por Perfil')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 py-8" x-data="{ q: '', markAll(vis, col) { const qq = (this.q || '').toLowerCase(); document.querySelectorAll('[data-menu-row]').forEach(row => { if (qq !== '' && !row.dataset.label.includes(qq)) return; const nm = 'permissions[' + row.dataset.mid + '][' + col + ']'; row.querySelectorAll('input[type=checkbox]').forEach(cb => { if (cb.name === nm) cb.checked = vis; }); }); } }">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs uppercase tracking-wider text-amber-500/80 font-semibold mb-1">Configurações → Permissões</p>
            <h1 class="text-2xl font-bold text-white">Menus por Perfil</h1>
            <p class="text-zinc-400 text-sm mt-1">Defina visibilidade e ações por menu para cada perfil. Opcional: override por empresa.</p>
        </div>
        @if(session('success'))
            <div class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-2 text-sm">{{ session('success') }}</div>
        @endif
    </div>

    <form method="get" action="{{ route('admin.settings.permissions.menus') }}" class="flex flex-wrap gap-3 items-end mb-6">
        <div>
            <label class="block text-xs text-zinc-500 mb-1">Perfil</label>
            <select name="role_id" class="bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white" onchange="this.form.submit()">
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" @selected($roleId === $r->id)>{{ $r->label }} ({{ $r->name }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-zinc-500 mb-1">Portal</label>
            <select name="portal" class="bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white" onchange="this.form.submit()">
                <option value="app" @selected($portal === 'app')>Portal (app / aluno / pro)</option>
                <option value="admin" @selected($portal === 'admin')>Painel administrativo</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-zinc-500 mb-1">Empresa (opcional)</label>
            <select name="academy_company_id" class="bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white min-w-[200px]" onchange="this.form.submit()">
                <option value="">— Padrão global —</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected($companyId === $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if(!$role)
        <p class="text-zinc-500">Nenhum perfil disponível.</p>
    @else
        <form method="post" action="{{ route('admin.settings.permissions.menus.update') }}">
            @csrf
            <input type="hidden" name="role_id" value="{{ $role->id }}">
            <input type="hidden" name="portal" value="{{ $portal }}">
            @if($companyId)
                <input type="hidden" name="academy_company_id" value="{{ $companyId }}">
            @endif

            <div class="flex flex-wrap gap-2 mb-4">
                <input type="search" x-model="q" placeholder="Buscar menu…" class="bg-zinc-900 border border-zinc-700 rounded-lg px-3 py-2 text-sm text-white flex-1 min-w-[200px] max-w-md">
                <button type="button" @click="markAll(true, 'pode_visualizar')" class="px-3 py-2 rounded-lg bg-zinc-800 text-xs text-zinc-200 border border-zinc-600 hover:bg-zinc-700">Marcar visíveis</button>
                <button type="button" @click="markAll(false, 'pode_visualizar')" class="px-3 py-2 rounded-lg bg-zinc-800 text-xs text-zinc-200 border border-zinc-600 hover:bg-zinc-700">Desmarcar visíveis</button>
                <button type="button" @click="markAll(true, 'pode_criar')" class="px-3 py-2 rounded-lg bg-zinc-800 text-xs text-zinc-200 border border-zinc-600 hover:bg-zinc-700">Marcar criar</button>
                <button type="button" @click="markAll(false, 'pode_criar')" class="px-3 py-2 rounded-lg bg-zinc-800 text-xs text-zinc-200 border border-zinc-600 hover:bg-zinc-700">Desmarcar criar</button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900/40">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-zinc-500 border-b border-zinc-800">
                            <th class="px-4 py-3">Menu</th>
                            <th class="px-2 py-3 text-center">Ver</th>
                            <th class="px-2 py-3 text-center">Criar</th>
                            <th class="px-2 py-3 text-center">Editar</th>
                            <th class="px-2 py-3 text-center">Excluir</th>
                            <th class="px-2 py-3 text-center">Exportar</th>
                            <th class="px-2 py-3 text-center">Imprimir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                            @php
                                $p = $permissionsKeyed->get($menu->id);
                            @endphp
                            <tr
                                data-menu-row
                                data-mid="{{ $menu->id }}"
                                data-label="{{ \Illuminate\Support\Str::lower($menu->label.' '.$menu->name) }}"
                                x-show="q === '' || $el.dataset.label.includes(q.toLowerCase())"
                                class="border-b border-zinc-800/80 hover:bg-zinc-800/30"
                            >
                                <td class="px-4 py-2 text-zinc-200">
                                    <div class="font-medium">{{ $menu->label }}</div>
                                    <div class="text-[11px] text-zinc-500 font-mono">{{ $menu->name }} · {{ $menu->route }}</div>
                                </td>
                                @foreach(['pode_visualizar', 'pode_criar', 'pode_editar', 'pode_excluir', 'pode_exportar', 'pode_imprimir'] as $col)
                                    <td class="px-2 py-2 text-center">
                                        <input
                                            type="checkbox"
                                            name="permissions[{{ $menu->id }}][{{ $col }}]"
                                            value="1"
                                            class="rounded border-zinc-600 bg-zinc-900"
                                            @checked($p && $p->{$col})
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-black font-semibold text-sm">
                    Salvar Permissões
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
