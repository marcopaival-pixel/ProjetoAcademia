@extends('layouts.admin')

@section('title', 'Parceiros / Vendors')

@section('content')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vendorManager', () => ({
            editMode: false,
            actionUrl: "{{ route('admin.shop.vendors.store') }}",
            vendor: {
                id: '',
                name: '',
                email: '',
                phone: '',
                document: '',
                commission_rate: 0,
                status: 'active',
            },
            openCreate() {
                this.editMode = false;
                this.actionUrl = "{{ route('admin.shop.vendors.store') }}";
                this.vendor = { id: '', name: '', email: '', phone: '', document: '', commission_rate: 0, status: 'active' };
            },
            openEdit(item) {
                this.editMode = true;
                this.actionUrl = `/admin/shop/vendors/${item.id}/update`;
                this.vendor = {
                    ...item,
                    commission_rate: item.commission_rate ?? 0,
                };
            }
        }));
    });
</script>

<div class="space-y-10 animate-fade-in" x-data="vendorManager">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Parceiros do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Fornecedores e comissionamento</p>
        </div>
        <a href="{{ route('admin.shop.products.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2 w-fit">
            <i class="fas fa-arrow-left"></i> Voltar aos Produtos
        </a>
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
        <div class="lg:col-span-5 bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] h-fit">
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider mb-5" x-text="editMode ? 'Editar Parceiro' : 'Novo Parceiro'"></h3>
            <form :action="actionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Nome</label>
                    <input type="text" name="name" x-model="vendor.name" required maxlength="255"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">E-mail</label>
                        <input type="email" name="email" x-model="vendor.email" maxlength="255"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Telefone</label>
                        <input type="text" name="phone" x-model="vendor.phone" maxlength="30"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Comissão (%)</label>
                        <input type="number" name="commission_rate" x-model="vendor.commission_rate" min="0" max="100" step="0.01" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Status</label>
                        <select name="status" x-model="vendor.status" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                            <option value="pending">Pendente</option>
                            <option value="active">Ativo</option>
                            <option value="suspended">Suspenso</option>
                            <option value="rejected">Rejeitado</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Documento (CPF/CNPJ)</label>
                    <input type="text" name="document" x-model="vendor.document" maxlength="30"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                </div>
                <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all"
                    x-text="editMode ? 'Salvar Alterações' : 'Cadastrar Parceiro'"></button>
                <button type="button" @click="openCreate()" class="w-full py-3 text-zinc-500 hover:text-white text-xs font-black uppercase tracking-widest" x-show="editMode">
                    Cancelar edição
                </button>
            </form>
        </div>

        <div class="lg:col-span-7 bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
            <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Parceiros cadastrados</h3>
            @if($vendors->isEmpty())
                <p class="text-zinc-500 text-sm">Nenhum parceiro cadastrado.</p>
            @else
                <div class="space-y-3">
                    @foreach($vendors as $v)
                    <div class="flex items-center justify-between gap-4 p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <div class="min-w-0">
                            <p class="font-black text-white">{{ $v->name }}</p>
                            <p class="text-xs text-zinc-400 mt-1">{{ $v->email ?: '—' }} · {{ $v->commission_rate }}% comissão</p>
                            <p class="text-[10px] text-zinc-500 mt-1 uppercase">{{ $v->status }} · {{ $v->products_count }} produto(s)</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" @click="openEdit(@js($v))"
                                class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl">
                                Editar
                            </button>
                            @if($v->products_count === 0)
                            <form action="{{ route('admin.shop.vendors.destroy', $v) }}" method="POST"
                                onsubmit="return confirm('Excluir este parceiro?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[10px] font-black uppercase tracking-wider rounded-xl">
                                    Excluir
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
