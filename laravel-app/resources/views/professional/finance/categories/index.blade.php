@extends('layouts.app')

@section('title', 'Categorias Financeiras')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6" x-data="categoryManager()">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Financeiro</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Categorias</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Categorias <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Financeiras</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Organize seus lançamentos agrupando-os por categoria.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <button @click="openModal('create')" class="px-6 py-3 bg-blue-500 text-zinc-950 font-bold rounded-xl hover:bg-blue-400 transition-all shadow-lg flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Nova Categoria
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-3xl font-bold flex items-center gap-4 animate-bounce-subtle">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-6 rounded-3xl font-bold flex items-center gap-4 animate-bounce-subtle">
            <i data-lucide="alert-circle" class="w-6 h-6"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Receitas -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] overflow-hidden">
            <div class="p-6 border-b border-white/5 bg-emerald-500/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-emerald-400">Receitas</h2>
                        <p class="text-xs text-zinc-500 font-bold">Categorias de Entrada</p>
                    </div>
                </div>
            </div>
            <div class="p-4 space-y-2">
                @forelse($categories->where('type', 'revenue') as $category)
                    <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center gap-3">
                            <i data-lucide="tag" class="w-4 h-4 text-emerald-500"></i>
                            <span class="text-sm font-bold text-white">{{ $category->name }}</span>
                            @if($category->is_default)
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-zinc-800 text-zinc-400">Padrão</span>
                            @endif
                        </div>
                        
                        @if(!$category->is_default)
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openModal('edit', {{ $category }})" class="p-2 rounded-lg text-blue-400 hover:bg-blue-500/10 transition-all">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete('{{ route('professional.finance.categories.destroy', $category) }}')" class="p-2 rounded-lg text-rose-400 hover:bg-rose-500/10 transition-all">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-500 font-bold">Nenhuma categoria encontrada.</div>
                @endforelse
            </div>
        </div>

        <!-- Despesas -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] overflow-hidden">
            <div class="p-6 border-b border-white/5 bg-rose-500/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                        <i data-lucide="trending-down" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-rose-400">Despesas</h2>
                        <p class="text-xs text-zinc-500 font-bold">Categorias de Saída</p>
                    </div>
                </div>
            </div>
            <div class="p-4 space-y-2">
                @forelse($categories->where('type', 'expense') as $category)
                    <div class="flex items-center justify-between p-4 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-rose-500/30 transition-all group">
                        <div class="flex items-center gap-3">
                            <i data-lucide="tag" class="w-4 h-4 text-rose-500"></i>
                            <span class="text-sm font-bold text-white">{{ $category->name }}</span>
                            @if($category->is_default)
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-zinc-800 text-zinc-400">Padrão</span>
                            @endif
                        </div>
                        
                        @if(!$category->is_default)
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openModal('edit', {{ $category }})" class="p-2 rounded-lg text-blue-400 hover:bg-blue-500/10 transition-all">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete('{{ route('professional.finance.categories.destroy', $category) }}')" class="p-2 rounded-lg text-rose-400 hover:bg-rose-500/10 transition-all">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-500 font-bold">Nenhuma categoria encontrada.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal (Create/Edit) -->
    <div x-show="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-transition style="display: none;">
        <div @click.away="closeModal()" class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-md shadow-2xl p-8 relative">
            <button @click="closeModal()" class="absolute top-4 right-4 p-2 text-zinc-500 hover:text-white transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            
            <h3 class="text-xl font-black text-white mb-6" x-text="mode === 'create' ? 'Nova Categoria' : 'Editar Categoria'"></h3>
            
            <form :action="mode === 'create' ? '{{ route('professional.finance.categories.store') }}' : '/professional/finance/categories/' + form.id" method="POST">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Nome da Categoria</label>
                        <input type="text" name="name" x-model="form.name" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Tipo</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-4 rounded-2xl border border-white/5 bg-zinc-950/50 cursor-pointer hover:border-emerald-500/50 transition-all"
                                   :class="form.type === 'revenue' ? 'border-emerald-500 bg-emerald-500/10' : ''">
                                <input type="radio" name="type" value="revenue" x-model="form.type" class="hidden">
                                <i data-lucide="trending-up" class="w-5 h-5" :class="form.type === 'revenue' ? 'text-emerald-500' : 'text-zinc-600'"></i>
                                <span class="text-sm font-bold" :class="form.type === 'revenue' ? 'text-emerald-400' : 'text-zinc-400'">Receita</span>
                            </label>
                            
                            <label class="flex items-center gap-3 p-4 rounded-2xl border border-white/5 bg-zinc-950/50 cursor-pointer hover:border-rose-500/50 transition-all"
                                   :class="form.type === 'expense' ? 'border-rose-500 bg-rose-500/10' : ''">
                                <input type="radio" name="type" value="expense" x-model="form.type" class="hidden">
                                <i data-lucide="trending-down" class="w-5 h-5" :class="form.type === 'expense' ? 'text-rose-500' : 'text-zinc-600'"></i>
                                <span class="text-sm font-bold" :class="form.type === 'expense' ? 'text-rose-400' : 'text-zinc-400'">Despesa</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-4 bg-blue-500 text-zinc-950 font-black rounded-2xl hover:bg-blue-400 transition-all shadow-lg">
                        Salvar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Form -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryManager', () => ({
            modal: false,
            mode: 'create',
            form: {
                id: null,
                name: '',
                type: 'expense'
            },
            openModal(mode, data = null) {
                this.mode = mode;
                if (mode === 'edit' && data) {
                    this.form.id = data.id;
                    this.form.name = data.name;
                    this.form.type = data.type;
                } else {
                    this.form.id = null;
                    this.form.name = '';
                    this.form.type = 'expense';
                }
                this.modal = true;
                setTimeout(() => lucide.createIcons(), 50);
            },
            closeModal() {
                this.modal = false;
            },
            confirmDelete(url) {
                if (confirm('Tem certeza que deseja excluir esta categoria?')) {
                    const form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            }
        }));
    });
</script>
@endsection
