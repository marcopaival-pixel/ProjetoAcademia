<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseArticle;

class KBController extends Controller
{
    public function index()
    {
        $categories = KnowledgeBaseCategory::withCount('articles')->orderBy('order')->get();
        $popularArticles = KnowledgeBaseArticle::where('is_published', true)->orderBy('views', 'desc')->take(5)->get();
        return view('support.kb.index', compact('categories', 'popularArticles'));
    }

    public function category(KnowledgeBaseCategory $category)
    {
        $articles = $category->articles()->where('is_published', true)->paginate(15);
        return view('support.kb.category', compact('category', 'articles'));
    }

    public function article($slug)
    {
        $article = KnowledgeBaseArticle::where('slug', $slug)->where('is_published', true)->with('category')->firstOrFail();
        $article->increment('views');
        
        $relatedArticles = KnowledgeBaseArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->take(3)
            ->get();

        return view('support.kb.article', compact('article', 'relatedArticles'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $articles = KnowledgeBaseArticle::where('is_published', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('tags', 'like', "%{$query}%");
            })
            ->with('category')
            ->paginate(15);

        return view('support.kb.search', compact('articles', 'query'));
    }
}
