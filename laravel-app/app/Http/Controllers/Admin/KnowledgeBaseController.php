<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $articles = KnowledgeArticle::with('category')->latest()->paginate(20);
        $categories = KnowledgeCategory::withCount('articles')->get();

        return view('admin.kb.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = KnowledgeCategory::all();
        $types = ['ALUNO', 'PACIENTE', 'CLINICA', 'ADMIN', 'FINANCEIRO'];

        return view('admin.kb.create', compact('categories', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'categoria_id' => 'required|exists:knowledge_categories,id',
            'tipo_usuario' => 'required|string',
            'ativo' => 'boolean',
        ]);

        KnowledgeArticle::create([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo . '-' . $request->tipo_usuario),
            'conteudo' => $request->conteudo,
            'categoria_id' => $request->categoria_id,
            'tipo_usuario' => $request->tipo_usuario,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('admin.kb.index')->with('success', 'Artigo criado com sucesso!');
    }

    public function edit(KnowledgeArticle $knowledgeArticle)
    {
        $categories = KnowledgeCategory::all();
        $types = ['ALUNO', 'PACIENTE', 'CLINICA', 'ADMIN', 'FINANCEIRO'];

        return view('admin.kb.edit', [
            'article' => $knowledgeArticle,
            'categories' => $categories,
            'types' => $types
        ]);
    }

    public function update(Request $request, KnowledgeArticle $knowledgeArticle)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'categoria_id' => 'required|exists:knowledge_categories,id',
            'tipo_usuario' => 'required|string',
        ]);

        $knowledgeArticle->update([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo . '-' . $request->tipo_usuario),
            'conteudo' => $request->conteudo,
            'categoria_id' => $request->categoria_id,
            'tipo_usuario' => $request->tipo_usuario,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('admin.kb.index')->with('success', 'Artigo atualizado com sucesso!');
    }

    public function destroy(KnowledgeArticle $knowledgeArticle)
    {
        $knowledgeArticle->delete();
        return redirect()->route('admin.kb.index')->with('success', 'Artigo excluído com sucesso!');
    }

    // Category Methods
    public function storeCategory(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_usuario' => 'required|string',
        ]);

        KnowledgeCategory::create([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome . '-' . $request->tipo_usuario),
            'descricao' => $request->descricao,
            'tipo_usuario' => $request->tipo_usuario,
            'ativo' => true,
        ]);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    public function updateCategory(Request $request, KnowledgeCategory $category)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_usuario' => 'required|string',
        ]);

        $category->update([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome . '-' . $request->tipo_usuario),
            'descricao' => $request->descricao,
            'tipo_usuario' => $request->tipo_usuario,
            'ativo' => $request->has('ativo'),
        ]);

        return back()->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroyCategory(KnowledgeCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Categoria excluída com sucesso!');
    }
}
