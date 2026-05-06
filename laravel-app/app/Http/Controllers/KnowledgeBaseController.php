<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $userType = $this->getUserKnowledgeType();
        
        $categories = KnowledgeCategory::active()
            ->forUserType($userType)
            ->with(['articles' => function($q) {
                $q->active();
            }])
            ->get();

        $search = $request->input('search');
        $articles = null;

        if ($search) {
            $articles = KnowledgeArticle::active()
                ->forUserType($userType)
                ->where(function($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                      ->orWhere('conteudo', 'like', "%{$search}%");
                })
                ->paginate(12);
        }

        return view('kb.index', compact('categories', 'articles', 'search', 'userType'));
    }

    public function show($slug)
    {
        $userType = $this->getUserKnowledgeType();
        
        $article = KnowledgeArticle::active()
            ->forUserType($userType)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('kb.show', compact('article'));
    }

    private function getUserKnowledgeType()
    {
        $user = Auth::user();
        
        if (!$user) return 'ALUNO'; // Default for public if allowed, but we'll use auth middleware

        if ($user->is_admin) return 'ADMIN';
        
        if ($user->hasRole('finance')) return 'FINANCEIRO';
        if ($user->hasRole(['professional', 'manager'])) return 'CLINICA';
        if ($user->hasRole('paciente')) return 'PACIENTE';
        if ($user->hasRole('aluno')) return 'ALUNO';

        return 'ALUNO'; // Fallback
    }
}
