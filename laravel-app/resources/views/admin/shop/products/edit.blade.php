@extends('layouts.admin')

@section('title', 'Editar Produto: ' . $product->name)

@section('content')
<div class="max-w-[900px] mx-auto space-y-10 animate-fade-in" 
     x-data="{ manageStock: {{ $product->manage_stock ? 'true' : 'false' }}, type: '{{ $product->type }}' }">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.shop.products.index') }}" class="w-10 h-10 rounded-xl bg-zinc-800 border border-white/5 flex items-center justify-center text-zinc-400 hover:text-white transition-all">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Editar Produto</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Modifique as configurações de "{{ $product->name }}"</p>
        </div>
    </div>

    @if($errors->any())
    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl">
        <p class="text-rose-500 text-sm font-bold mb-2">Erros de validação encontrados:</p>
        <ul class="list-disc pl-5 text-xs text-rose-400 space-y-1">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.shop.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('POST') {{-- Web routes define post para update por simplificação --}}

        {{-- Bloco Principal --}}
        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Informações Gerais</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Nome do Produto</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Categoria</label>
                    <select name="category_id" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Vendedor (Parceiro)</label>
                    <select name="vendor_id" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $product->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Fornecedor (opcional)</label>
                    <select name="supplier_id" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                        <option value="">Nenhum</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Tipo de Produto</label>
                    <select name="type" x-model="type" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                        <option value="physical">Físico</option>
                        <option value="digital">Digital</option>
                        <option value="service">Serviço</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">SKU (opcional)</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Descrição Curta (opcional)</label>
                    <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Descrição Completa</label>
                    <textarea name="description" rows="5"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all resize-none">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Bloco Financeiro e Preços --}}
        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Preços & Finanças</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Preço de Venda (R$)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Preço Promocional (R$)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Preço de Custo (R$)</label>
                    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
            </div>
        </div>

        {{-- Bloco Estoque & Configurações Físicas --}}
        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5" x-show="type === 'physical'">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Estoque & Logística</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="manage_stock" value="1" x-model="manageStock" class="w-4 h-4 accent-emerald-600">
                    <span class="text-xs font-bold text-zinc-400">Controlar Estoque</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-show="manageStock" x-transition>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Quantidade em Estoque</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Alerta de Estoque Mínimo</label>
                    <input type="number" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold ?? 5) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Peso (kg)</label>
                    <input type="number" step="0.001" name="weight" value="{{ old('weight', $product->weight) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
            </div>
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-4">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Objetivos de Treino</h3>
            <div class="flex flex-wrap gap-4">
                @php $selectedGoals = old('goal_types', $product->goal_types ?? []); @endphp
                @foreach(['emagrecimento' => 'Emagrecimento', 'hipertrofia' => 'Hipertrofia', 'performance' => 'Performance', 'saude' => 'Saúde'] as $value => $label)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="goal_types[]" value="{{ $value }}" {{ in_array($value, $selectedGoals, true) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-600">
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-4">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Tags IA (recomendações)</h3>
            <p class="text-xs text-zinc-500">Separadas por vírgula. Ex.: emagrecimento, termogenico, whey</p>
            @php $aiTagsValue = old('ai_tags', is_array($product->ai_tags) ? implode(', ', $product->ai_tags) : ''); @endphp
            <input type="text" name="ai_tags" value="{{ $aiTagsValue }}"
                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Imagens do Produto</h3>
            @if($product->images->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($product->images as $image)
                <div class="relative rounded-xl overflow-hidden border {{ $image->is_primary ? 'border-emerald-500' : 'border-white/10' }}">
                    <img src="{{ $image->url() }}" alt="{{ $image->alt }}" class="w-full h-28 object-cover">
                    <div class="absolute inset-x-0 bottom-0 p-2 bg-zinc-950/90 flex items-center justify-between gap-1">
                        @if($image->is_primary)
                            <span class="text-[9px] font-black uppercase text-emerald-400">Principal</span>
                        @else
                            <form action="{{ route('admin.shop.products.images.primary', [$product, $image]) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[9px] font-black uppercase text-zinc-400 hover:text-emerald-400">Definir principal</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.shop.products.images.destroy', [$product, $image]) }}" method="POST" onsubmit="return confirm('Remover esta imagem?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:text-rose-300"><i class="fas fa-trash-alt text-xs"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-zinc-500">Nenhuma imagem cadastrada.</p>
            @endif
            <div>
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Adicionar imagens</label>
                <input type="file" name="product_images[]" accept="image/*" multiple
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm">
            </div>
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5" x-show="type === 'digital'" x-cloak>
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Arquivo Digital</h3>
            @if($product->downloadable_file)
            <p class="text-xs text-zinc-500">Ficheiro atual: <span class="text-emerald-400 font-mono">{{ basename($product->downloadable_file) }}</span></p>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Substituir ficheiro</label>
                    <input type="file" name="downloadable_file" accept=".pdf,.zip,.epub,.mp4,.mp3"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Limite de downloads</label>
                    <input type="number" name="download_limit" value="{{ old('download_limit', $product->download_limit) }}" min="1" max="100"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Validade (dias)</label>
                    <input type="number" name="download_expiry_days" value="{{ old('download_expiry_days', $product->download_expiry_days) }}" min="1" max="3650"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                </div>
            </div>
        </div>

        {{-- Bloco Opções e Status --}}
        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider">Status & Visibilidade</h3>
            <div class="flex flex-wrap gap-6 items-center">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-600">
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Produto em Destaque</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 accent-emerald-600">
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Ativo para Venda</span>
                </label>
                <div class="flex items-center gap-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Status</label>
                    <select name="status" required class="bg-zinc-950 border border-white/5 p-3 rounded-xl text-zinc-400 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                        <option value="published" {{ old('status', $product->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.shop.products.index') }}" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white font-black text-xs uppercase tracking-widest rounded-2xl transition-all">Cancelar</a>
            <button type="submit" class="px-10 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-emerald-500/10">Atualizar Produto</button>
        </div>
    </form>
</div>
@endsection
