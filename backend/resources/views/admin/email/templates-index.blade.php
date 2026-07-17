@extends('layouts.admin')

@section('title', 'Templates de E-mail')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in pb-20">
    
    <!-- Feedback -->
    @if(session('success'))
        <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-xs font-black uppercase tracking-widest animate-bounce-subtle">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Header Context -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-4">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight italic uppercase">Templates de <span class="text-emerald-500">E-mail</span></h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Personalização de mensagens transacionais por unidade ou global</p>
        </div>
        <a href="{{ route('admin.settings.email.templates.create') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Criar Template
        </a>
    </div>

    <!-- Stats/Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card p-6 border-emerald-500/10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i data-lucide="code-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Variáveis Suportadas</p>
                    <p class="text-[10px] text-white font-bold tracking-tight">@{{name}}, @{{verification_url}}, @{{app_name}}</p>
                </div>
            </div>
        </div>
        <div class="glass-card p-6 border-emerald-500/10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Prioridade</p>
                    <p class="text-[10px] text-white font-bold tracking-tight">Unidade > Global > Default Blade</p>
                </div>
            </div>
        </div>
        <div class="glass-card p-6 border-amber-500/10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <i data-lucide="help-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Estado</p>
                    <p class="text-[10px] text-white font-bold tracking-tight">{{ $templates->where('ativo', true)->count() }} ativos / {{ $templates->count() }} total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="glass-card overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-950/80 border-b border-white/5">
                    <tr>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Identificação do Template</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Tipo / Hook</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Escopo</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($templates as $t)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-600 group-hover:text-emerald-500 group-hover:border-emerald-500/30 transition-all">
                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-white uppercase tracking-tight">{{ $t->nome_template }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($t->ativo)
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="text-[9px] font-black uppercase text-emerald-500 tracking-widest">Publicado</span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span>
                                                <span class="text-[9px] font-black uppercase text-zinc-600 tracking-widest">Rascunho</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-zinc-950 border border-white/5 rounded-lg text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $t->tipo }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @if($t->empresa)
                                    <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $t->empresa->name }}</span>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                                        <i data-lucide="globe" class="w-3 h-3 text-blue-400"></i>
                                        <span class="text-[9px] font-black uppercase text-blue-400 tracking-widest">Global</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('admin.settings.email.templates.edit', $t) }}" class="inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-zinc-900 border border-white/5 text-zinc-400 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-zinc-950 hover:border-emerald-500 transition-all active:scale-95">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center gap-4 opacity-20">
                                    <i data-lucide="layers-2" class="w-12 h-12"></i>
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em]">Nenhum template personalizado configurado</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center pt-8">
        {{ $templates->links() }}
    </div>
</div>
@endsection
