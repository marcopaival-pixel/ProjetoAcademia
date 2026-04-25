@extends('layouts.admin')

@section('title', 'Gestão de Suplementos')

@section('content')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('supplementComponent', () => ({
            showModal: false,
            editMode: false,
            supplement: {
                id: '',
                name: '',
                category: '',
                default_dosage: '',
                default_unit: 'g',
                description: '',
                benefits: '',
                side_effects: '',
                is_active: true
            },
            openCreate() {
                this.editMode = false;
                this.supplement = { id: '', name: '', category: '', default_dosage: '', default_unit: 'g', description: '', benefits: '', side_effects: '', is_active: true };
                this.showModal = true;
            },
            openEdit(item) {
                this.editMode = true;
                this.supplement = { ...item };
                this.showModal = true;
            }
        }));
    });
</script>

<div class="space-y-10 animate-fade-in" x-data="supplementComponent" x-cloak>
    <!-- Header & Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Catálogo de Suplementos</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Biblioteca global de substâncias para prescrição</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button @click="openCreate()" class="px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-3 shadow-xl shadow-emerald-600/20 group">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i> Novo Suplemento
            </button>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <form action="{{ route('admin.supplements.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou categoria..." 
                    class="w-full bg-zinc-950 border border-white/5 p-4 pl-14 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
            </div>
            <button type="submit" class="px-10 bg-zinc-800 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-700 transition-all">Filtrar</button>
            @if(request('search'))
                <a href="{{ route('admin.supplements.index') }}" class="px-6 bg-red-500/10 text-red-500 flex items-center justify-center rounded-2xl hover:bg-red-500/20 transition-all">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Grid Container -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($supplements as $item)
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-6 hover:bg-white/[0.02] transition-all group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-600/5 rounded-full blur-2xl group-hover:bg-emerald-600/10 transition-all"></div>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-white/5 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-pills text-xl"></i>
                    </div>
                    <div class="flex gap-2">
                        <button @click="openEdit(@js($item))" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-emerald-600 transition-all">
                            <i class="fas fa-edit text-[10px]"></i>
                        </button>
                        <form action="{{ route('admin.supplements.destroy', $item) }}" method="POST" onsubmit="return confirm('Excluir este suplemento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-red-600 transition-all">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-1 mb-6">
                    <h3 class="text-lg font-black text-white truncate">{{ $item->name }}</h3>
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">{{ $item->category ?? 'Sem Categoria' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-zinc-950/50 p-3 rounded-2xl border border-white/5">
                        <p class="text-[8px] text-zinc-600 font-black uppercase mb-1">Dose Padrão</p>
                        <p class="text-xs font-bold text-white">{{ $item->default_dosage }}{{ $item->default_unit }}</p>
                    </div>
                    <div class="bg-zinc-950/50 p-3 rounded-2xl border border-white/5">
                        <p class="text-[8px] text-zinc-600 font-black uppercase mb-1">Status</p>
                        <span class="text-[9px] font-black uppercase {{ $item->is_active ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $item->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('admin.supplements.toggle-status', $item) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-zinc-950 border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:bg-emerald-600 hover:text-white transition-all">
                        {{ $item->is_active ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full py-20 bg-zinc-900/20 border border-white/5 rounded-[3rem] text-center">
                <div class="flex flex-col items-center gap-4 opacity-20">
                    <i class="fas fa-box-open text-6xl"></i>
                    <p class="text-sm font-black uppercase tracking-widest">Nenhum suplemento cadastrado</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($supplements->hasPages())
        <div class="p-8 bg-zinc-900/40 border border-white/5 rounded-[2.5rem]">
            {{ $supplements->links() }}
        </div>
    @endif

    <!-- Modal Form -->
    <div class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80" 
         x-show="showModal" 
         x-cloak
         @keydown.escape.window="showModal = false">
        <div class="bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-dashboard-entry" @click.away="showModal = false">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight" x-text="editMode ? 'Editar Suplemento' : 'Novo Suplemento'"></h3>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Configuração da biblioteca de substâncias</p>
                </div>
                <button @click="showModal = false" class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/supplements') }}/' + supplement.id : '{{ route('admin.supplements.store') }}'" method="POST" class="p-8 space-y-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome da Substância</label>
                        <input type="text" name="name" x-model="supplement.name" required placeholder="Ex: Creatina Monohidratada"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Categoria</label>
                        <select name="category" x-model="supplement.category" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all appearance-none">
                            <option value="">Selecione...</option>
                            <option value="Aminoácidos">Aminoácidos</option>
                            <option value="Proteínas">Proteínas</option>
                            <option value="Vitaminas">Vitaminas</option>
                            <option value="Minerais">Minerais</option>
                            <option value="Termogênicos">Termogênicos</option>
                            <option value="Hormonais">Hormonais</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Dose Padrão</label>
                        <input type="text" name="default_dosage" x-model="supplement.default_dosage" placeholder="Ex: 5"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Unidade</label>
                        <select name="default_unit" x-model="supplement.default_unit" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all appearance-none">
                            <option value="g">Grama (g)</option>
                            <option value="mg">Miligrama (mg)</option>
                            <option value="caps">Cápsula</option>
                            <option value="ml">Mililitro (ml)</option>
                            <option value="scoop">Scoop</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status</label>
                        <select name="is_active" x-model="supplement.is_active" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all appearance-none">
                            <option :value="true">Ativo</option>
                            <option :value="false">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Descrição / Notas</label>
                    <textarea name="description" x-model="supplement.description" placeholder="Instruções gerais de uso..."
                        class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all h-24 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Benefícios</label>
                        <textarea name="benefits" x-model="supplement.benefits" 
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-[10px] outline-none focus:ring-2 focus:ring-emerald-600 transition-all h-20 resize-none"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Efeitos Colaterais</label>
                        <textarea name="side_effects" x-model="supplement.side_effects" 
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-[10px] outline-none focus:ring-2 focus:ring-emerald-600 transition-all h-20 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showModal = false" class="px-8 py-4 text-zinc-500 font-black text-[10px] uppercase tracking-widest hover:text-white transition-colors">Cancelar</button>
                    <button type="submit" class="px-10 py-4 bg-emerald-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-600/20">
                        <span x-text="editMode ? 'Salvar Alterações' : 'Criar Suplemento'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    [x-cloak] { display: none !important; }
</style>
@endsection
