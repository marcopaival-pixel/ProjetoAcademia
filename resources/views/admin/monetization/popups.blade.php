@extends('layouts.admin')

@section('content')
<div class="p-6" x-data="{ showModal: false, selectedPopup: { feature_code: '', title: '', message: '', benefits: [], button_text: 'Fazer Upgrade', image_url: '' } }">
    <div class="mb-8">
        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
            Gestão de Popups de Upgrade
        </h1>
        <p class="text-gray-400 mt-1 uppercase text-[10px] font-black tracking-widest">Personalize as mensagens de conversão</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($features as $feature)
        @php $popup = $popups->get($feature->code); @endphp
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group overflow-hidden relative flex flex-col h-full">
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-12 h-12 bg-white/5 text-indigo-400 border border-white/10 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas fa-window-maximize"></i>
                </div>
                <div class="flex gap-2">
                    <button @click="selectedPopup = @js($popup ?? ['feature_code' => $feature->code, 'title' => '', 'message' => '', 'benefits' => [], 'button_text' => 'Fazer Upgrade', 'image_url' => '']); showModal = true" 
                            class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-indigo-600 transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="relative z-10 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-white">{{ $feature->name }}</h3>
                </div>
                <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest mb-4">{{ $feature->code }}</p>
                
                @if($popup)
                    <h4 class="text-white text-sm font-bold mb-2">{{ $popup->title }}</h4>
                    <p class="text-[10px] text-gray-400 leading-relaxed line-clamp-3 mb-4">
                        {{ $popup->message }}
                    </p>
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($popup->benefits ?? [] as $benefit)
                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[8px] font-bold uppercase tracking-widest">{{ $benefit }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 border border-dashed border-white/10 rounded-2xl text-center">
                        <span class="text-[10px] text-gray-600 font-black uppercase tracking-widest">Popup não configurado</span>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Editar Popup -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        
        <div class="bg-zinc-900 border border-white/10 w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl relative"
             @click.away="showModal = false">
            
            <form action="{{ route('admin.monetization.popups.store') }}" method="POST" class="p-10 space-y-6">
                @csrf
                <input type="hidden" name="feature_code" x-model="selectedPopup.feature_code">

                <h2 class="text-xl font-bold text-white tracking-tight">Configurar Popup de Upgrade</h2>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest" x-text="'Funcionalidade: ' + selectedPopup.feature_code"></p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Título do Popup</label>
                            <input type="text" name="title" x-model="selectedPopup.title" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Mensagem Principal</label>
                            <textarea name="message" x-model="selectedPopup.message" rows="4" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Texto do Botão</label>
                            <input type="text" name="button_text" x-model="selectedPopup.button_text" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Benefícios (JSON ou Lista)</label>
                            <div class="space-y-2">
                                <template x-for="(benefit, index) in selectedPopup.benefits" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" :name="'benefits[' + index + ']'" x-model="selectedPopup.benefits[index]" class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-white text-xs outline-none focus:border-blue-500 transition-all">
                                        <button type="button" @click="selectedPopup.benefits.splice(index, 1)" class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="if(!selectedPopup.benefits) selectedPopup.benefits = []; selectedPopup.benefits.push('')" class="w-full py-2 border border-dashed border-white/10 rounded-xl text-[9px] text-zinc-500 font-black uppercase tracking-widest hover:border-white/30 hover:text-white transition-all">
                                    <i class="fas fa-plus mr-1"></i> Adicionar Benefício
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">URL da Imagem/Ícone</label>
                            <input type="text" name="image_url" x-model="selectedPopup.image_url" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-blue-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-white/5 flex justify-end gap-4">
                    <button type="button" @click="showModal = false" class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="px-8 py-3 bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-400 hover:text-white transition-all shadow-xl">
                        Salvar Popup
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
