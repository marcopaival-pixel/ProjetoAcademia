@extends('layouts.admin')

@section('title', 'Gestão de Treinamentos')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-white">Gestão da Academia</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Controle seus módulos e vídeoaulas</p>
        </div>
        <button onclick="document.getElementById('modal-module').classList.remove('hidden')" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
            Novo Módulo
        </button>
    </div>

    <!-- Modules List -->
    <div class="grid grid-cols-1 gap-6">
        @foreach($modules as $module)
        <div class="bg-zinc-900/40 border border-white/5 rounded-3xl overflow-hidden">
            <div class="p-6 bg-white/5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-500 border border-white/5">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-white">{{ $module->title }}</h3>
                        <p class="text-[10px] text-zinc-600 font-bold uppercase">{{ $module->lessons->count() }} aulas</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openLessonModal({{ $module->id }}, '{{ $module->title }}')" class="p-2 text-zinc-500 hover:text-emerald-500 transition-colors">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                    <form action="{{ route('admin.training.modules.destroy', $module) }}" method="POST"
                    data-confirm-delete
                    data-confirm-title="Excluir módulo"
                    data-confirm-message="Excluir este módulo e todas as suas aulas? Esta ação não pode ser desfeita.">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-zinc-500 hover:text-red-500 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="p-6 space-y-3">
                @forelse($module->lessons as $lesson)
                <div class="flex items-center justify-between p-4 bg-black/20 rounded-2xl border border-white/5 group">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black text-zinc-800">{{ $loop->iteration }}</span>
                        <span class="text-xs font-bold text-zinc-400">{{ $lesson->title }}</span>
                    </div>
                    <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                         <form action="{{ route('admin.training.lessons.destroy', $lesson) }}" method="POST"
                         data-confirm-delete
                         data-confirm-title="Excluir aula"
                         data-confirm-message="Excluir esta aula? Esta ação não pode ser desfeita.">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[10px] text-zinc-600 hover:text-red-500 font-black uppercase">Excluir</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-[10px] text-zinc-700 italic text-center py-4">Nenhuma aula cadastrada neste módulo</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Módulo -->
<div id="modal-module" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-zinc-900 border border-white/10 rounded-[2.5rem] w-full max-w-lg p-10 animate-scale-up">
        <h3 class="text-xl font-black text-white mb-2">Novo Módulo</h3>
        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-8">Defina uma categoria de aulas</p>
        
        <form action="{{ route('admin.training.modules.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Título do Módulo</label>
                <input type="text" name="title" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-blue-500/50 transition-all outline-none" placeholder="Ex: Primeiros Passos">
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Descrição Curta</label>
                <textarea name="description" rows="3" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-blue-500/50 transition-all outline-none" placeholder="O que o aluno vai aprender?"></textarea>
            </div>
            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" onclick="document.getElementById('modal-module').classList.add('hidden')" class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500">Cancelar</button>
                <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest">Criar Módulo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Aula -->
<div id="modal-lesson" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-zinc-900 border border-white/10 rounded-[2.5rem] w-full max-w-2xl p-10 animate-scale-up">
        <h3 class="text-xl font-black text-white mb-2">Nova Aula: <span id="module-title-display" class="text-blue-500"></span></h3>
        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-8">Adicione conteúdo em vídeo e texto</p>
        
        <form action="{{ route('admin.training.lessons.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="module_id" id="input-module-id">
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Título da Aula</label>
                    <input type="text" name="title" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-blue-500/50 transition-all outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">URL do Vídeo (YouTube/Vimeo)</label>
                    <input type="url" name="video_url" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-blue-500/50 transition-all outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Conteúdo / Descrição</label>
                    <textarea name="content" rows="6" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm text-white focus:border-blue-500/50 transition-all outline-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" onclick="document.getElementById('modal-lesson').classList.add('hidden')" class="px-6 py-4 text-[10px] font-black uppercase text-zinc-500">Cancelar</button>
                <button type="submit" class="px-10 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest">Publicar Aula</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLessonModal(id, title) {
        document.getElementById('input-module-id').value = id;
        document.getElementById('module-title-display').innerText = title;
        document.getElementById('modal-lesson').classList.remove('hidden');
    }
</script>
@endsection
