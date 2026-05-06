@extends('layouts.admin')

@section('title', 'Gestão de Cobrança / Créditos')

@section('content')
<div class="animate-fade-in space-y-8" x-data="{ tab: 'settings' }">
    
    <!-- Cabeçalho -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-emerald-600/10 border border-emerald-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-emerald-400 text-[9px] font-black uppercase tracking-widest">Módulo de Cobrança Ativo</span>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                Cobrança / <span class="text-emerald-500">Créditos</span>
            </h1>
        </div>

        <div class="flex bg-zinc-950 p-1.5 rounded-2xl border border-white/5 shadow-2xl">
            <button @click="tab = 'settings'" :class="tab === 'settings' ? 'bg-emerald-500 text-zinc-950' : 'text-zinc-500 hover:text-white'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Configurações
            </button>
            <button @click="tab = 'packages'" :class="tab === 'packages' ? 'bg-emerald-500 text-zinc-950' : 'text-zinc-500 hover:text-white'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Pacotes
            </button>
            <button @click="tab = 'purchases'" :class="tab === 'purchases' ? 'bg-emerald-500 text-zinc-950' : 'text-zinc-500 hover:text-white'" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Histórico
            </button>
        </div>
    </div>

    <!-- Conteúdo -->
    <div class="space-y-6">
        
        <!-- Tab: Configurações -->
        <div x-show="tab === 'settings'" class="animate-fade-in space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="glass-card p-8 rounded-[2.5rem] border border-white/5 space-y-8">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tighter uppercase italic mb-2">Controle do Módulo</h3>
                        <p class="text-xs text-zinc-500 font-medium uppercase tracking-widest">Defina o comportamento global das vendas</p>
                    </div>

                    <form action="{{ route('admin.billing.settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="space-y-4">
                            <label class="flex items-center justify-between p-6 bg-zinc-950/50 border border-white/5 rounded-3xl cursor-pointer hover:border-emerald-500/20 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all">
                                        <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-black text-white uppercase tracking-widest block">Venda de Créditos</span>
                                        <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">Habilitar no portal do usuário</span>
                                    </div>
                                </div>
                                <input type="hidden" name="compra_creditos_ativa" value="0">
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="compra_creditos_ativa" value="1" {{ $settings['compra_creditos_ativa'] ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-6 bg-zinc-950/50 border border-white/5 rounded-3xl cursor-pointer hover:border-amber-500/20 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-zinc-950 transition-all">
                                        <i data-lucide="zap" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-black text-white uppercase tracking-widest block">Pagamento Ativo</span>
                                        <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">Se desligado, libera créditos automático</span>
                                    </div>
                                </div>
                                <input type="hidden" name="pagamento_ativo" value="0">
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="pagamento_ativo" value="1" {{ $settings['pagamento_ativo'] ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-emerald-500/20">
                            Salvar Configurações
                        </button>
                    </form>
                </div>

                <div class="glass-card p-8 rounded-[2.5rem] border border-white/5 space-y-8 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tighter uppercase italic mb-2">Gateway: Mercado Pago</h3>
                        <p class="text-xs text-zinc-500 font-medium uppercase tracking-widest">Configurações de integração direta</p>
                    </div>

                    <div class="p-6 bg-zinc-950/50 border border-white/5 rounded-3xl space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status da Conexão</span>
                            <span class="flex items-center gap-2 text-emerald-500 text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                Conectado
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Access Token</span>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">APP_USR-***{{ substr($settings['mp_access_token'], -4) }}</span>
                        </div>
                    </div>

                    <div class="p-6 bg-amber-500/5 border border-amber-500/10 rounded-3xl flex gap-4">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500 shrink-0"></i>
                        <p class="text-[10px] text-amber-500/80 font-bold uppercase tracking-widest leading-relaxed">
                            O Access Token do Mercado Pago é lido diretamente do arquivo .env. Para alterar, atualize a variável MP_ACCESS_TOKEN.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Pacotes -->
        <div x-show="tab === 'packages'" class="animate-fade-in space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic">Pacotes de Créditos</h3>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] mt-1">Configure o que o usuário pode comprar</p>
                </div>
                <button @click="$dispatch('open-package-modal')" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-3">
                    <i data-lucide="plus" class="w-4 h-4"></i> Novo Pacote
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($packages as $pkg)
                <div class="glass-card p-8 rounded-[2.5rem] border border-white/5 relative group hover:border-emerald-500/30 transition-all overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
                    
                    <div class="flex items-center justify-between mb-8 relative z-10">
                        <div class="w-12 h-12 bg-zinc-950 border border-white/5 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:rotate-12 transition-all">
                            <i data-lucide="package" class="w-6 h-6"></i>
                        </div>
                        <div class="flex gap-2">
                            <button @click="$dispatch('edit-package-modal', {{ json_encode($pkg) }})" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-600 hover:text-white transition-all">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
                            <form action="{{ route('admin.billing.packages.delete', $pkg) }}" method="POST" onsubmit="return confirm('Excluir este pacote?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-600 hover:text-rose-500 transition-all">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h4 class="text-2xl font-black text-white tracking-tight uppercase mb-1">{{ $pkg->nome }}</h4>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-6">{{ $pkg->quantidade }} Créditos</p>

                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block">Valor de Venda</span>
                            <span class="text-2xl font-black text-emerald-500 italic uppercase">R$ {{ number_format($pkg->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="px-3 py-1 rounded-lg {{ $pkg->ativo ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border-rose-500/20' }} border text-[8px] font-black uppercase tracking-widest">
                            {{ $pkg->ativo ? 'Ativo' : 'Inativo' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tab: Histórico -->
        <div x-show="tab === 'purchases'" class="animate-fade-in space-y-6">
            <div class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl">
                <div class="p-8 border-b border-zinc-900 flex items-center justify-between bg-zinc-950/20">
                    <div>
                        <h3 class="text-lg font-black text-white tracking-tighter uppercase italic">Vendas Recentes</h3>
                        <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] mt-1 italic italic">Monitoramento em tempo real</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-950/50">
                                <th class="px-8 py-6 text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Data / Hora</th>
                                <th class="px-8 py-6 text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Usuário</th>
                                <th class="px-8 py-6 text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Créditos</th>
                                <th class="px-8 py-6 text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Valor</th>
                                <th class="px-8 py-6 text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Gateway</th>
                                <th class="px-8 py-6 text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-900">
                            @forelse($purchases as $purchase)
                            <tr class="group hover:bg-emerald-500/[0.02] transition-all">
                                <td class="px-8 py-6">
                                    <span class="text-[10px] font-black text-white uppercase tracking-widest block">{{ $purchase->created_at->format('d/m/Y') }}</span>
                                    <span class="text-[9px] text-zinc-700 font-bold uppercase tracking-widest">{{ $purchase->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-zinc-900 shadow-xl group-hover:border-emerald-500/20 transition-all">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($purchase->user?->name ?? 'User') }}&background=09090b&color=10b981&bold=true" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <span class="text-[11px] font-black text-white uppercase tracking-widest block">{{ $purchase->user?->name ?? 'Sistema' }}</span>
                                            <span class="text-[9px] text-zinc-700 font-bold uppercase tracking-widest italic">{{ $purchase->user?->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[11px] font-black text-emerald-500 uppercase tracking-[0.2em] italic">{{ $purchase->quantidade }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[11px] font-black text-white uppercase tracking-widest">R$ {{ number_format($purchase->valor, 2, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em]">{{ strtoupper($purchase->gateway ?? 'N/A') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $statusClasses = [
                                            'PAGO' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'PENDENTE' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                            'CANCELADO' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                        ];
                                        $class = $statusClasses[$purchase->status] ?? 'bg-zinc-800 text-zinc-400';
                                    @endphp
                                    <span class="px-4 py-1.5 rounded-xl border {{ $class }} text-[9px] font-black uppercase tracking-widest italic">
                                        {{ $purchase->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="w-16 h-16 bg-zinc-950 rounded-2xl border border-white/5 flex items-center justify-center mx-auto mb-6 text-zinc-800">
                                        <i data-lucide="database" class="w-8 h-8"></i>
                                    </div>
                                    <p class="text-[10px] text-zinc-700 font-black uppercase tracking-[0.3em] italic">Nenhuma venda registrada</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($purchases->hasPages())
                <div class="p-8 border-t border-zinc-900 bg-zinc-950/20">
                    {{ $purchases->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Criar/Editar Pacote -->
<div x-data="{ 
    show: false, 
    editing: false,
    id: null,
    nome: '',
    quantidade: 100,
    valor: 0,
    ativo: 1,
    action: '{{ route('admin.billing.packages.store') }}'
}" 
x-show="show" 
x-on:open-package-modal.window="show = true; editing = false; id = null; nome = ''; quantidade = 100; valor = 0; ativo = 1; action = '{{ route('admin.billing.packages.store') }}'"
x-on:edit-package-modal.window="show = true; editing = true; id = $event.detail.id; nome = $event.detail.nome; quantidade = $event.detail.quantidade; valor = $event.detail.valor; ativo = $event.detail.ativo; action = '/admin/billing/packages/' + $event.detail.id"
class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-xl animate-fade-in" 
style="display: none;"
@keydown.escape.window="show = false">
    
    <div @click.away="show = false" class="bg-[#0b0e14] border border-white/10 w-full max-w-xl rounded-[2.5rem] overflow-hidden shadow-2xl flex flex-col animate-scale-in">
        <div class="p-8 border-b border-white/5 flex items-center justify-between bg-zinc-950/50">
            <h3 class="text-xl font-black text-white tracking-tighter uppercase italic" x-text="editing ? 'Editar Pacote' : 'Novo Pacote'"></h3>
            <button @click="show = false" class="text-zinc-500 hover:text-white transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <form :action="action" method="POST" class="p-8 space-y-6">
            @csrf
            <template x-if="editing">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Nome do Pacote</label>
                    <input type="text" name="nome" x-model="nome" required placeholder="Ex: Pacote Inicial" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder:text-zinc-800 outline-none focus:border-emerald-500/30 transition-all font-bold text-sm uppercase">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Quantidade de Créditos</label>
                        <input type="number" name="quantidade" x-model="quantidade" required min="1" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white outline-none focus:border-emerald-500/30 transition-all font-black text-lg italic">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" x-model="valor" required min="0" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white outline-none focus:border-emerald-500/30 transition-all font-black text-lg italic">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Status</label>
                    <select name="ativo" x-model="ativo" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white outline-none focus:border-emerald-500/30 transition-all font-bold uppercase tracking-widest text-xs">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-2xl shadow-emerald-500/20 mt-4">
                Confirmar Operação
            </button>
        </form>
    </div>
</div>
@endsection
