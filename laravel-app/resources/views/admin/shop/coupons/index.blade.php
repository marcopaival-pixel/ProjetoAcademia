@extends('layouts.admin')

@section('title', 'Cupons de Desconto')

@section('content')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('couponManager', () => ({
            editMode: false,
            actionUrl: "{{ route('admin.shop.coupons.store') }}",
            coupon: {
                id: '',
                code: '',
                description: '',
                type: 'percentage',
                discount_value: '',
                minimum_order_value: '',
                maximum_discount: '',
                max_uses_total: '',
                max_uses_per_user: '',
                starts_at: '',
                expires_at: '',
                status: 'active',
            },
            openCreate() {
                this.editMode = false;
                this.actionUrl = "{{ route('admin.shop.coupons.store') }}";
                this.coupon = {
                    id: '', code: '', description: '', type: 'percentage',
                    discount_value: '', minimum_order_value: '', maximum_discount: '',
                    max_uses_total: '', max_uses_per_user: '', starts_at: '', expires_at: '', status: 'active',
                };
            },
            openEdit(item) {
                this.editMode = true;
                this.actionUrl = `/admin/shop/coupons/${item.id}/update`;
                this.coupon = {
                    ...item,
                    discount_value: item.discount_value ?? '',
                    minimum_order_value: item.minimum_order_value ?? '',
                    maximum_discount: item.maximum_discount ?? '',
                    max_uses_total: item.max_uses_total ?? '',
                    max_uses_per_user: item.max_uses_per_user ?? '',
                    starts_at: item.starts_at ? item.starts_at.substring(0, 16) : '',
                    expires_at: item.expires_at ? item.expires_at.substring(0, 16) : '',
                };
            }
        }));
    });
</script>

<div class="space-y-10 animate-fade-in" x-data="couponManager">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Cupons do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Campanhas de desconto e promoções</p>
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
            <h3 class="text-sm font-black text-emerald-400 uppercase tracking-wider mb-5" x-text="editMode ? 'Editar Cupom' : 'Novo Cupom'"></h3>

            <form :action="actionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Código</label>
                    <input type="text" name="code" x-model="coupon.code" required maxlength="50"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm uppercase font-bold tracking-widest outline-none focus:ring-2 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Descrição</label>
                    <input type="text" name="description" x-model="coupon.description" maxlength="255"
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Tipo</label>
                        <select name="type" x-model="coupon.type" required
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                            <option value="percentage">Percentual (%)</option>
                            <option value="fixed">Valor fixo (R$)</option>
                            <option value="free_shipping">Frete grátis</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Status</label>
                        <select name="status" x-model="coupon.status"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                            <option value="active">Ativo</option>
                            <option value="paused">Pausado</option>
                            <option value="expired">Expirado</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Valor desconto</label>
                        <input type="number" name="discount_value" x-model="coupon.discount_value" step="0.01" min="0"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Pedido mínimo</label>
                        <input type="number" name="minimum_order_value" x-model="coupon.minimum_order_value" step="0.01" min="0"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Teto desconto</label>
                        <input type="number" name="maximum_discount" x-model="coupon.maximum_discount" step="0.01" min="0"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Usos totais</label>
                        <input type="number" name="max_uses_total" x-model="coupon.max_uses_total" min="1"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Início</label>
                        <input type="datetime-local" name="starts_at" x-model="coupon.starts_at"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Expira</label>
                        <input type="datetime-local" name="expires_at" x-model="coupon.expires_at"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
                    </div>
                </div>
                <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all"
                    x-text="editMode ? 'Salvar Alterações' : 'Criar Cupom'"></button>
                <button type="button" @click="openCreate()" class="w-full py-3 text-zinc-500 hover:text-white text-xs font-black uppercase tracking-widest" x-show="editMode">
                    Cancelar edição
                </button>
            </form>
        </div>

        <div class="lg:col-span-7 bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
            <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Cupons cadastrados</h3>
            @if($coupons->isEmpty())
                <p class="text-zinc-500 text-sm">Nenhum cupom cadastrado.</p>
            @else
                <div class="space-y-3">
                    @foreach($coupons as $c)
                    <div class="flex items-center justify-between gap-4 p-4 bg-zinc-950/50 border border-white/5 rounded-2xl">
                        <div class="min-w-0">
                            <p class="font-black text-emerald-400 tracking-widest">{{ $c->code }}</p>
                            <p class="text-xs text-zinc-400 mt-1">{{ $c->description ?: '—' }}</p>
                            <p class="text-[10px] text-zinc-500 mt-1 uppercase">
                                {{ $c->type }} · {{ $c->status }} · {{ $c->usages_count }} uso(s)
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" @click="openEdit(@js($c))"
                                class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl">
                                Editar
                            </button>
                            @if($c->usages_count === 0)
                            <form action="{{ route('admin.shop.coupons.destroy', $c) }}" method="POST"
                                onsubmit="return confirm('Excluir este cupom?');">
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
