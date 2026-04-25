<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KBController extends Controller
{
    public function index()
    {
        $articles = KnowledgeBaseArticle::with('category')->latest()->paginate(20);
        $categories = KnowledgeBaseCategory::withCount('articles')->get();

        return view('admin.support.kb.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = KnowledgeBaseCategory::all();

        return view('admin.support.kb.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:knowledge_base_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        KnowledgeBaseArticle::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'is_published' => $request->has('is_published'),
            'tags' => $request->tags,
        ]);

        return redirect()->route('admin.kb.index')->with('success', 'Artigo criado com sucesso!');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
        ]);

        KnowledgeBaseCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Categoria criada com sucesso!');
    }
}
