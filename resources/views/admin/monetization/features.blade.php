@extends('layouts.admin')

@section('content')
<div class="p-6" x-data="{ showModal: false, selectedFeature: null }">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                Gestão de Funcionalidades
            </h1>
            <p class="text-gray-400 mt-1 uppercase text-[10px] font-black tracking-widest">Cadastre e categorize os recursos do sistema</p>
        </div>
        <button @click="showModal = true; selectedFeature = null" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="fas fa-plus"></i> Nova Funcionalidade
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($features as $feature)
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group overflow-hidden relative flex flex-col h-full">
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-12 h-12 bg-white/5 text-blue-400 border border-white/10 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas @if($feature->category === 'ai_credits') fa-robot @else fa-rocket @endif"></i>
                </div>
                <div class="flex gap-2">
                    <button @click="selectedFeature = @js($feature); showModal = true" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-blue-600 transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="relative z-10 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-white">{{ $feature->name }}</h3>
                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest bg-white/5 text-gray-400 border border-white/10">
                        {{ $feature->code }}
                    </span>
                </div>
                
                <div class="mt-2 flex flex-wrap gap-2">
                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest 
                        @if($feature->category === 'free') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                        @elseif($feature->category === 'freemium') bg-blue-500/10 text-blue-400 border border-blue-500/20
                        @elseif($feature->category === 'premium') bg-purple-500/10 text-purple-400 border border-purple-500/20
                        @elseif($feature->category === 'ai_credits') bg-amber-500/10 text-amber-400 border border-amber-500/20
                        @endif">
                        {{ strtoupper($feature->category) }}
                    </span>
                    
                    @if($feature->show_lock)
                    <span class="px-2 py-1 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 text-[9px] font-black uppercase tracking-widest">
                        <i class="fas fa-lock mr-1"></i> Cadeado
                    </span>
                    @endif

                    @if($feature->show_badge)
                    <span class="px-2 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[9px] font-black uppercase tracking-widest">
                        <i class="fas fa-certificate mr-1"></i> Badge Premium
                    </span>
                    @endif
                </div>

                <p class="text-[10px] text-gray-500 mt-4 leading-relaxed line-clamp-2">
                    {{ $feature->description ?? 'Sem descrição.' }}
                </p>

                <div class="flex items-center justify-between pt-6 border-t border-white/10 mt-6">
                    <div class="flex flex-col">
                        <span class="text-[9px] text-gray-500 uppercase font-black">Status</span>
                        <span class="text-xs font-bold {{ $feature->is_active ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $feature->is_active ? 'ATIVO' : 'INATIVO' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal CRUD Funcionalidade -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        
        <div class="bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl relative"
             @click.away="showModal = false">
            
            <form :action="selectedFeature ? '{{ url('admin/monetization/features') }}/' + selectedFeature.id : '{{ route('admin.monetization.features.store') }}'" method="POST" class="p-10 space-y-6">
                @csrf
                <h2 class="text-xl font-bold text-white tracking-tight" x-text="selectedFeature ? 'Editar Funcionalidade' : 'Nova Funcionalidade'"></h2>
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nome da Funcionalidade</label>
                        <input type="text" name="name" x-model="selectedFeature ? selectedFeature.name : ''" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                    </div>

                    <div class="space-y-2" x-show="!selectedFeature">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Código Interno (Slug)</label>
                        <input type="text" name="code" x-model="selectedFeature ? selectedFeature.code : ''" :required="!selectedFeature" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all" placeholder="ex: ai_chat">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Categoria</label>
                        <select name="category" x-model="selectedFeature ? selectedFeature.category : 'free'" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                            <option value="free">Free</option>
                            <option value="freemium">Freemium</option>
                            <option value="premium">Premium</option>
                            <option value="ai_credits">Créditos de IA</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Descrição</label>
                        <textarea name="description" x-model="selectedFeature ? selectedFeature.description : ''" rows="3" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all"></textarea>
                    </div>

                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative w-10 h-5 bg-white/10 rounded-full transition-all group-hover:bg-white/20">
                                <input type="checkbox" name="is_active" value="1" x-model="selectedFeature ? selectedFeature.is_active : true" class="sr-only peer">
                                <div class="absolute top-1 left-1 w-3 h-3 bg-white rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-blue-500"></div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Ativo</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative w-10 h-5 bg-white/10 rounded-full transition-all group-hover:bg-white/20">
                                <input type="checkbox" name="show_lock" value="1" x-model="selectedFeature ? selectedFeature.show_lock : false" class="sr-only peer">
                                <div class="absolute top-1 left-1 w-3 h-3 bg-white rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-blue-500"></div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Exibir Cadeado</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative w-10 h-5 bg-white/10 rounded-full transition-all group-hover:bg-white/20">
                                <input type="checkbox" name="show_badge" value="1" x-model="selectedFeature ? selectedFeature.show_badge : false" class="sr-only peer">
                                <div class="absolute top-1 left-1 w-3 h-3 bg-white rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-blue-500"></div>
                            </div>
                            <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Badge Premium</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-white/5 flex justify-end gap-4">
                    <button type="button" @click="showModal = false" class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="px-8 py-3 bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-400 hover:text-white transition-all shadow-xl">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
