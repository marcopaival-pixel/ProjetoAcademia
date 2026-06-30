@extends('layouts.admin')

@section('title', 'Gestão de Categorias')

@section('content')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryManager', () => ({
            editMode: false,
            actionUrl: "{{ route('admin.shop.categories.store') }}",
            category: {
                id: '',
                name: '',
                parent_id: '',
                description: '',
                icon: 'tag',
                product_type: 'physical',
                sort_order: 0,
                is_active: true
            },
            openCreate() {
                this.editMode = false;
                this.actionUrl = "{{ route('admin.shop.categories.store') }}";
                this.category = { id: '', name: '', parent_id: '', description: '', icon: 'tag', product_type: 'physical', sort_order: 0, is_active: true };
            },
            openEdit(item) {
                this.editMode = true;
                this.actionUrl = `/admin/shop/categories/${item.id}/update`;
                this.category = { ...item, is_active: !!item.is_active };
            }
        }));
    });
</script>

<div class="space-y-10 animate-fade-in" x-data="categoryManager">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Categorias do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Estruture o catálogo em categorias físicas, digitais e serviços</p>
        </div>
        <div>
            <a href="{{ route('admin.shop.products.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Voltar aos Produtos
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400"></i>
        <p class="text-emerald-400 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-rose-400"></i>
        <p class="text-rose-400 text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Formulário (Coluna Esquerda) --}}
        <div class="lg:col-span-5 bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] h-fit">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider mb-5" x-text="editMode ? 'Editar Categoria' : 'Nova Categoria'">Nova Categoria</h3>

            <form :action="actionUrl" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Nome da Categoria</label>
                    <input type="text" name="name" x-model="category.name" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Tipo de Produto</label>
                        <select name="product_type" x-model="category.product_type" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                            <option value="physical">Físicos</option>
                            <option value="digital">Digitais</option>
                            <option value="service">Serviços</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Ícone FontAwesome</label>
                        <input type="text" name="icon" x-model="category.icon" placeholder="tag"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Categoria Pai (Subcategoria de...)</label>
                    <select name="parent_id" x-model="category.parent_id" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                        <option value="">Nenhuma (Categoria Principal)</option>
                        @foreach($categories->whereNull('parent_id') as $rootCat)
                            <option :value="{{ $rootCat->id }}" x-show="category.id != {{ $rootCat->id }}">{{ $rootCat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Descrição (opcional)</label>
                    <textarea name="description" x-model="category.description" rows="3"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="category.is_active" class="w-4 h-4 accent-emerald-600">
                        <span class="text-xs font-bold text-zinc-400 uppercase">Ativa</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Ordem</label>
                        <input type="number" name="sort_order" x-model="category.sort_order"
                            class="w-20 bg-zinc-950 border border-white/5 p-2 rounded-xl text-white text-center text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button" x-show="editMode" @click="openCreate()"
                        class="px-5 py-3 bg-zinc-800 text-zinc-300 hover:text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all">Cancelar</button>
                    <button type="submit"
                        class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl shadow-emerald-500/10"
                        x-text="editMode ? 'Salvar Alterações' : 'Criar Categoria'"></button>
                </div>
            </form>
        </div>

        {{-- Listagem (Coluna Direita) --}}
        <div class="lg:col-span-7 bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
            <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Categorias Cadastradas</h3>

            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                @forelse($categories as $cat)
                <div class="flex items-center justify-between p-4 bg-zinc-950 border border-white/5 rounded-2xl hover:border-emerald-500/20 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-emerald-400">
                            <i class="fas fa-{{ $cat->icon ?? 'tag' }}"></i>
                        </div>
                        <div>
                            <p class="font-bold text-white text-sm flex items-center gap-2">
                                {{ $cat->name }}
                                @if($cat->parent)
                                    <span class="text-[9px] text-zinc-500 uppercase font-black px-2 py-0.5 bg-zinc-900 border border-white/5 rounded-full">Sub de: {{ $cat->parent->name }}</span>
                                @endif
                            </p>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5">
                                Tipo: {{ ['physical'=>'Físico','digital'=>'Digital','service'=>'Serviço'][$cat->product_type] ?? '' }} 
                                · Ordem: {{ $cat->sort_order }}
                                · @if($cat->is_active) <span class="text-emerald-500">Ativa</span> @else <span class="text-zinc-600">Inativa</span> @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="openEdit(@js($cat))"
                            class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-emerald-600 transition-all" title="Editar">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <form action="{{ route('admin.shop.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 rounded-lg bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:text-rose-500 hover:bg-rose-500/10 transition-all" title="Excluir">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-zinc-500">Nenhuma categoria cadastrada.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
