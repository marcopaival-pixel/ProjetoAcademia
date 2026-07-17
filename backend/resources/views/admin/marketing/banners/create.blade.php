@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.marketing.banners.index') }}" class="w-10 h-10 rounded-xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-500 hover:text-white transition-all">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Novo Banner</h1>
                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-1">Configure um novo banner promocional</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.marketing.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Conteúdo -->
                <div class="bg-zinc-900/50 border border-white/5 p-8 rounded-[2.5rem] backdrop-blur-xl space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-600/10 flex items-center justify-center text-blue-500">
                            <i class="fas fa-edit text-xs"></i>
                        </div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Conteúdo do Banner</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Título</label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition-all" placeholder="Ex: Grande Lançamento NexShape App">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Subtítulo</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition-all" placeholder="Ex: Disponível agora nas lojas">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Descrição</label>
                            <textarea name="description" rows="4" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition-all resize-none" placeholder="Conte mais detalhes sobre a promoção...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Cor de Fundo</label>
                            <div class="flex items-center gap-3 bg-zinc-950 border border-white/5 rounded-2xl px-4 py-2">
                                <input type="color" name="background_color" value="{{ old('background_color', '#09090b') }}" class="w-10 h-10 bg-transparent border-none cursor-pointer">
                                <input type="text" value="{{ old('background_color', '#09090b') }}" readonly class="bg-transparent border-none text-zinc-400 text-xs font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Ícone (FontAwesome)</label>
                            <input type="text" name="icon" value="{{ old('icon', 'fas fa-rocket') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/50 transition-all" placeholder="Ex: fas fa-rocket">
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="bg-zinc-900/50 border border-white/5 p-8 rounded-[2.5rem] backdrop-blur-xl space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600/10 flex items-center justify-center text-emerald-500">
                            <i class="fas fa-mouse-pointer text-xs"></i>
                        </div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Botões e Ações</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <p class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Botão Primário</p>
                            <input type="text" name="primary_button_text" value="{{ old('primary_button_text') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm" placeholder="Texto do botão">
                            <input type="text" name="primary_button_link" value="{{ old('primary_button_link') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm" placeholder="URL do botão">
                        </div>
                        <div class="space-y-4">
                            <p class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.2em]">Botão Secundário</p>
                            <input type="text" name="secondary_button_text" value="{{ old('secondary_button_text') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm" placeholder="Texto do botão">
                            <input type="text" name="secondary_button_link" value="{{ old('secondary_button_link') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm" placeholder="URL do botão">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral -->
            <div class="space-y-8">
                <!-- Visibilidade -->
                <div class="bg-zinc-900/50 border border-white/5 p-8 rounded-[2.5rem] backdrop-blur-xl space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-600/10 flex items-center justify-center text-purple-500">
                            <i class="fas fa-eye text-xs"></i>
                        </div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Exibição</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Exibir nos Painéis</label>
                            <div class="bg-zinc-950 border border-white/5 rounded-2xl p-4 max-h-48 overflow-y-auto space-y-2 custom-scrollbar">
                                @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-2 hover:bg-white/[0.02] rounded-xl cursor-pointer transition-all">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="w-4 h-4 rounded border-white/10 bg-zinc-900 text-blue-600 focus:ring-offset-zinc-950">
                                    <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider">
                                        @if($role->name === 'admin') Painel Administrativo
                                        @elseif($role->name === 'professional') Painel do Profissional
                                        @elseif($role->name === 'aluno') Painel do Aluno
                                        @elseif($role->name === 'paciente') Painel do Paciente
                                        @elseif($role->name === 'instructor') Painel do Instrutor
                                        @elseif($role->name === 'manager') Painel do Gestor
                                        @elseif($role->name === 'receptionist') Painel da Recepção
                                        @elseif($role->name === 'finance') Painel Financeiro
                                        @else Painel {{ $role->label ?? $role->name }}
                                        @endif
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-widest mt-2 ml-1">* O banner sempre será exibido na página principal (Dashboard/Home) dos painéis selecionados.</p>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Prioridade</label>
                            <input type="number" name="priority" value="{{ old('priority', 0) }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm" placeholder="0">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Tipo de Exibição</label>
                            <select name="display_type" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm appearance-none">
                                <option value="always" {{ old('display_type') == 'always' ? 'selected' : '' }}>Sempre exibir</option>
                                <option value="once" {{ old('display_type') == 'once' ? 'selected' : '' }}>Apenas uma vez</option>
                                <option value="until_closed" {{ old('display_type') == 'until_closed' ? 'selected' : '' }}>Até o usuário fechar</option>
                                <option value="frequency" {{ old('display_type') == 'frequency' ? 'selected' : '' }}>Frequência Customizada</option>
                            </select>
                        </div>

                        <div id="frequency_days_container" class="{{ old('display_type') == 'frequency' ? '' : 'hidden' }}">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Reaparecer após (dias)</label>
                            <input type="number" name="frequency_days" value="{{ old('frequency_days', 7) }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm" placeholder="7">
                        </div>
                    </div>

                    <div class="pt-4 space-y-3 border-t border-white/5">
                        <label class="flex items-center justify-between p-2 cursor-pointer group">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-all">Ativar Banner</span>
                            <input type="checkbox" name="is_active" value="1" checked class="w-10 h-5 rounded-full border-white/10 bg-zinc-900 text-blue-600 focus:ring-offset-zinc-950">
                        </label>
                        <label class="flex items-center justify-between p-2 cursor-pointer group">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-all">Permitir Fechar</span>
                            <input type="checkbox" name="allow_dismiss" value="1" checked class="w-10 h-5 rounded-full border-white/10 bg-zinc-900 text-blue-600 focus:ring-offset-zinc-950">
                        </label>
                        <label class="flex items-center justify-between p-2 cursor-pointer group">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest group-hover:text-white transition-all">Opção "Não mostrar novamente"</span>
                            <input type="checkbox" name="dont_show_again_option" value="1" class="w-10 h-5 rounded-full border-white/10 bg-zinc-900 text-blue-600 focus:ring-offset-zinc-950">
                        </label>
                    </div>
                </div>

                <!-- Imagens -->
                <div class="bg-zinc-900/50 border border-white/5 p-8 rounded-[2.5rem] backdrop-blur-xl space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-600/10 flex items-center justify-center text-orange-500">
                            <i class="fas fa-image text-xs"></i>
                        </div>
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Imagens</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Imagem Desktop</label>
                            <input type="file" name="image_desktop" class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1 mb-2 block">Imagem Mobile</label>
                            <input type="file" name="image_mobile" class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-8">
            <a href="{{ route('admin.marketing.banners.index') }}" class="px-8 py-4 bg-zinc-900 text-zinc-400 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-800 transition-all">Cancelar</a>
            <button type="submit" class="px-12 py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 active:scale-95">Salvar Banner</button>
        </div>
    </form>
</div>

<script>
    document.querySelector('select[name="display_type"]').addEventListener('change', function() {
        const container = document.getElementById('frequency_days_container');
        if (this.value === 'frequency') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    });
</script>
@endsection
