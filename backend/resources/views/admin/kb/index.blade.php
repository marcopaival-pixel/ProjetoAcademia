@extends('layouts.admin')

@section('title', 'Gestão Base de Conhecimento')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white">Base de Conhecimento</h1>
            <p class="text-zinc-500 font-medium">Gerencie categorias e artigos de suporte.</p>
        </div>
        <div class="flex gap-4">
            <button onclick="document.getElementById('modal-category').classList.remove('hidden')" class="px-6 py-3 bg-zinc-800 text-white font-black rounded-xl border border-white/5 hover:bg-zinc-700 transition-all text-xs">
                NOVA CATEGORIA
            </button>
            <a href="{{ route('admin.kb.create') }}" class="px-6 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-500 transition-all text-xs shadow-lg shadow-blue-500/20">
                NOVO ARTIGO
            </a>
        </div>
    </div>

    <!-- Categories Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach($categories as $cat)
        <div class="bg-zinc-900/60 border border-white/5 p-6 rounded-3xl group">
            <div class="flex justify-between items-start mb-4">
                <span class="px-2 py-1 bg-blue-500/10 text-blue-400 text-[8px] font-black rounded uppercase tracking-widest border border-blue-500/10">{{ $cat->tipo_usuario }}</span>
                <div class="flex gap-2">
                    <button onclick="editCategory({{ $cat->toJson() }})" class="text-zinc-600 hover:text-white transition-colors"><i class="fas fa-edit text-xs"></i></button>
                    <form action="{{ route('admin.kb.category.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Excluir categoria e todos os artigos vinculados?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-zinc-600 hover:text-rose-500 transition-colors"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                </div>
            </div>
            <h4 class="text-white font-black text-lg leading-tight">{{ $cat->nome }}</h4>
            <p class="text-zinc-500 text-[10px] font-bold uppercase mt-2">{{ $cat->articles_count }} ARTIGOS</p>
        </div>
        @endforeach
    </div>

    <!-- Articles Table -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5 bg-white/5">
                    <th class="p-6">ARTIGO</th>
                    <th class="p-6">CATEGORIA / TIPO</th>
                    <th class="p-6">STATUS</th>
                    <th class="p-6">DATA</th>
                    <th class="p-6 text-right">AÇÕES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($articles as $article)
                <tr class="hover:bg-white/5 transition-all">
                    <td class="p-6">
                        <p class="text-white font-black">{{ $article->titulo }}</p>
                        <p class="text-zinc-600 text-[10px] mt-1">{{ Str::limit(strip_tags($article->conteudo), 50) }}</p>
                    </td>
                    <td class="p-6">
                        <span class="text-zinc-400 font-bold text-xs">{{ $article->category->nome }}</span>
                        <div class="text-[9px] font-black text-zinc-600 mt-1">{{ $article->tipo_usuario }}</div>
                    </td>
                    <td class="p-6">
                        @if($article->ativo)
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black rounded border border-emerald-500/10">ATIVO</span>
                        @else
                            <span class="px-2 py-1 bg-zinc-800 text-zinc-500 text-[9px] font-black rounded border border-white/5">INATIVO</span>
                        @endif
                    </td>
                    <td class="p-6 text-zinc-500 text-xs">
                        {{ $article->created_at->format('d/m/Y') }}
                    </td>
                    <td class="p-6 text-right space-x-2">
                        <a href="{{ route('admin.kb.edit', $article->id) }}" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-white transition-all"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.kb.destroy', $article->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 bg-zinc-800 text-zinc-400 rounded-lg hover:text-rose-500 transition-all" onclick="return confirm('Deseja excluir este artigo?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-6 border-t border-white/5">
            {{ $articles->links() }}
        </div>
    </div>
</div>

<!-- Modal Category -->
<div id="modal-category" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-white/10 w-full max-w-md rounded-[2.5rem] shadow-2xl p-10">
        <h3 id="cat-modal-title" class="text-2xl font-black text-white italic uppercase mb-6">Nova Categoria</h3>
        <form id="cat-form" action="{{ route('admin.kb.category.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="_method" id="cat-method" value="POST">
            <div>
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Nome da Categoria</label>
                <input type="text" name="nome" id="cat-nome" required class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Tipo de Usuário</label>
                <select name="tipo_usuario" id="cat-tipo" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all">
                    <option value="ALUNO">ALUNO</option>
                    <option value="PACIENTE">PACIENTE</option>
                    <option value="CLINICA">CLÍNICA/PRO</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="FINANCEIRO">FINANCEIRO</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Descrição</label>
                <textarea name="descricao" id="cat-desc" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-blue-500 transition-all" rows="3"></textarea>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('modal-category').classList.add('hidden')" class="flex-1 py-4 bg-zinc-800 text-white font-black rounded-xl hover:bg-zinc-700 transition-all text-xs uppercase tracking-widest">Cancelar</button>
                <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-500 transition-all text-xs uppercase tracking-widest shadow-lg shadow-blue-500/20">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('cat-modal-title').innerText = 'Editar Categoria';
    document.getElementById('cat-form').action = `/admin/kb/category/${cat.id}`;
    document.getElementById('cat-method').value = 'PUT';
    document.getElementById('cat-nome').value = cat.nome;
    document.getElementById('cat-tipo').value = cat.tipo_usuario;
    document.getElementById('cat-desc').value = cat.descricao;
    document.getElementById('modal-category').classList.remove('hidden');
}
</script>
@endsection
