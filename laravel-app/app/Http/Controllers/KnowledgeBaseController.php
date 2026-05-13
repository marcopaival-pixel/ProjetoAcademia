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
        if (!$user) return 'ALUNO';

        $activeRole = session('active_role');

        // Prioridade 1: Papel Ativo na Sessão (Respeita a troca de perfil)
        if ($activeRole === 'admin') return 'ADMIN';
        if ($activeRole === 'finance') return 'FINANCEIRO';
        if (in_array($activeRole, ['professional', 'instructor', 'manager', 'receptionist', 'supervisor'])) return 'CLINICA';
        if (in_array($activeRole, ['paciente', 'aluno'])) return 'ALUNO';

        // Prioridade 2: Fallback para is_admin se não houver papel ativo
        if ($user->is_admin) return 'ADMIN';
        
        // Prioridade 3: Fallback baseado nos papéis do usuário
        if ($user->hasRole('finance')) return 'FINANCEIRO';
        if ($user->hasRole(['professional', 'instructor', 'manager'])) return 'CLINICA';
        if ($user->hasRole(['paciente', 'aluno'])) return 'ALUNO';

        return 'ALUNO';
    }
}
