@extends('layouts.app')

@section('title', 'Editar Lançamento')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-4xl mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <h1 class="text-4xl font-black tracking-tight text-white leading-tight">
                Editar <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Lançamento</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('professional.finance.entries.index') }}" class="px-6 py-3 bg-zinc-900 text-zinc-300 font-bold rounded-xl hover:bg-zinc-800 transition-all border border-white/5">
                Voltar
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-6 rounded-3xl font-bold">
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('professional.finance.entries.update', $entry) }}" method="POST" class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] shadow-2xl space-y-8" x-data="{ type: '{{ old('type', $entry->type) }}', status: '{{ old('status', $entry->status) }}' }">
        @csrf
        @method('PUT')

        <!-- Tipo -->
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Tipo de Lançamento</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-3 p-4 rounded-2xl border border-white/5 bg-zinc-950/50 cursor-pointer hover:border-emerald-500/50 transition-all"
                       :class="type === 'revenue' ? 'border-emerald-500 bg-emerald-500/10' : ''">
                    <input type="radio" name="type" value="revenue" x-model="type" class="hidden">
                    <i data-lucide="trending-up" class="w-5 h-5" :class="type === 'revenue' ? 'text-emerald-500' : 'text-zinc-600'"></i>
                    <span class="text-sm font-bold" :class="type === 'revenue' ? 'text-emerald-400' : 'text-zinc-400'">Receita (Entrada)</span>
                </label>
                
                <label class="flex items-center gap-3 p-4 rounded-2xl border border-white/5 bg-zinc-950/50 cursor-pointer hover:border-rose-500/50 transition-all"
                       :class="type === 'expense' ? 'border-rose-500 bg-rose-500/10' : ''">
                    <input type="radio" name="type" value="expense" x-model="type" class="hidden">
                    <i data-lucide="trending-down" class="w-5 h-5" :class="type === 'expense' ? 'text-rose-500' : 'text-zinc-600'"></i>
                    <span class="text-sm font-bold" :class="type === 'expense' ? 'text-rose-400' : 'text-zinc-400'">Despesa (Saída)</span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Descrição</label>
                <input type="text" name="description" value="{{ old('description', $entry->description) }}" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Valor (R$)</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount', $entry->amount) }}" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Categoria</label>
                <select name="category_id" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <option value="">Sem Categoria</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" x-show="type === '{{ $category->type }}'" {{ old('category_id', $entry->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Data de Vencimento</label>
                <input type="date" name="due_date" value="{{ old('due_date', $entry->due_date ? \Carbon\Carbon::parse($entry->due_date)->format('Y-m-d') : '') }}" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Status</label>
                <select name="status" x-model="status" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <option value="pending">Pendente</option>
                    <option value="paid">Pago/Recebido</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>

            <div class="space-y-2" x-show="status === 'paid'">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Data de Pagamento/Recebimento</label>
                <input type="date" name="payment_date" value="{{ old('payment_date', $entry->payment_date ? \Carbon\Carbon::parse($entry->payment_date)->format('Y-m-d') : '') }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>

            <div class="space-y-2" x-show="status === 'paid'">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Forma de Pagamento</label>
                <input type="text" name="payment_method" value="{{ old('payment_method', $entry->payment_method) }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Observações</label>
                <textarea name="notes" rows="3" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">{{ old('notes', $entry->notes) }}</textarea>
            </div>
        </div>

        <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-2xl hover:from-blue-500 hover:to-indigo-500 transition-all shadow-2xl active:scale-[0.98] uppercase text-xs tracking-[0.2em]">
            Atualizar Lançamento
        </button>
    </form>
</div>
@endsection
