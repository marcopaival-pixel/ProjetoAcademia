<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExerciseCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AI\OrchestratorService;

class SearchController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}
    public function search(Request $request): View
    {
        $query = trim($request->input('q'));
        
        // Regra de governança: não procurar palavras sem extensão (comprimento) mínima de 3 caracteres
        if (strlen($query) < 3) {
            $query = '';
        }

        $user = auth()->user();
        $isAdmin = $user->isAdministrator();
        $category = $request->input('category');
        $muscle = $request->input('muscle');
        
        $results = [];

        if (!empty($query) || !empty($category) || !empty($muscle)) {
            // 1. Catálogo de Exercícios (Todos)
            if (!$category || $category === 'exercises') {
                $exercisesQuery = ExerciseCatalog::query();
                if (!$isAdmin) {
                    $exercisesQuery->where('is_active', true);
                }
                
                if (!empty($query)) {
                    $exercisesQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('muscle_group', 'like', "%{$query}%")
                          ->orWhere('equipment', 'like', "%{$query}%");
                    });
                }

                if (!empty($muscle)) {
                    $exercisesQuery->where('muscle_group', $muscle);
                }

                $results['exercises'] = $exercisesQuery->limit(20)->get();
            }

            // 2. Planos de Treino (Relevante para o Aluno)
            if (!$category || $category === 'workouts') {
                $trainingQuery = \App\Models\TrainingPlan::query();
                if (!$isAdmin) {
                    $trainingQuery->where('user_id', $user->id);
                }
                
                if (!empty($query)) {
                    $trainingQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('goal', 'like', "%{$query}%");
                    });
                }
                
                $results['workouts'] = $trainingQuery->limit(10)->get();
            }

            // 3. Base de Conhecimento / Ajuda
            if (!$category || $category === 'help') {
                $helpQuery = \App\Models\KnowledgeBaseArticle::where('is_published', true);
                
                if (!empty($query)) {
                    $helpQuery->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('content', 'like', "%{$query}%")
                          ->orWhere('tags', 'like', "%{$query}%");
                    });
                }
                
                $results['help'] = $helpQuery->limit(10)->get();
            }

            // 4. Comunicados
            if (!$category || $category === 'announcements') {
                $announcementsQuery = \App\Models\Announcement::where('is_active', true);
                
                if (!empty($query)) {
                    $announcementsQuery->where('content', 'like', "%{$query}%");
                }
                
                $results['announcements'] = $announcementsQuery->limit(5)->get();
            }

            // 5. Alimentos
            if (!$category || $category === 'foods') {
                $foodsQuery = \App\Models\Food::query();
                
                if (!empty($query)) {
                    $foodsQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('brand', 'like', "%{$query}%");
                    });
                }
                
                $results['foods'] = $foodsQuery->limit(10)->get();
            }

            if ($isAdmin) {
                // 6. Utilizadores (Apenas Admin)
                if (!$category || $category === 'users') {
                    $usersQuery = User::query();
                    if (!empty($query)) {
                        $usersQuery->where(function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%")
                              ->orWhere('email', 'like', "%{$query}%")
                              ->orWhere('username', 'like', "%{$query}%");
                        });
                    }
                    $results['users'] = $usersQuery->limit(10)->get();
                }

                // 7. Logs de Erro (Apenas Admin)
                if (!$category || $category === 'errors') {
                    $errorsQuery = \App\Models\SystemError::query();
                    if (!empty($query)) {
                        $errorsQuery->where(function ($q) use ($query) {
                            $q->where('message', 'like', "%{$query}%")
                              ->orWhere('url', 'like', "%{$query}%")
                              ->orWhere('type', 'like', "%{$query}%");
                        });
                    }
                    $results['errors'] = $errorsQuery->limit(10)->get();
                }
            }
        }

        // IA opcional: só com ?ai=1 (evita chamadas desnecessárias em buscas SQL)
        $aiResponse = null;
        $enableAi = $request->boolean('ai') || $request->boolean('ai_assist');
        if ($enableAi && ! empty($query) && strlen($query) > 15) {
            $result = $this->orchestrator->run($user, $query, [
                'source' => 'global_search',
                'clinicId' => $user->academy_company_id,
                'clinic_id' => $user->clinic_id,
                'intent' => 'support',
                'feature_code' => 'support_ai',
            ]);

            if ($result['status'] === 'success') {
                $aiResponse = [
                    'text' => $result['message'],
                    'action' => $result['action'] ?? null
                ];
            }
        }

        return view('search-results', [
            'query' => $query,
            'results' => $results,
            'category' => $category,
            'muscle' => $muscle,
            'muscles' => ExerciseCatalog::distinct()->pluck('muscle_group')->filter()->values(),
            'aiResponse' => $aiResponse,
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = trim($request->input('q'));
        if (strlen($query) < 2) return response()->json([]);

        $user = auth()->user();
        $suggestions = [];

        // Exercícios
        $exercises = ExerciseCatalog::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name'])
            ->map(fn($item) => ['label' => $item->name, 'category' => 'Exercício', 'url' => route('global.search', ['q' => $item->name])]);
        
        $suggestions = array_merge($suggestions, $exercises->toArray());

        // Treinos
        $workouts = \App\Models\TrainingPlan::where('user_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name'])
            ->map(fn($item) => ['label' => $item->name, 'category' => 'Meu Treino', 'url' => route('global.search', ['q' => $item->name])]);

        $suggestions = array_merge($suggestions, $workouts->toArray());

        // Ajuda
        $help = \App\Models\KnowledgeBaseArticle::where('is_published', true)
            ->where('title', 'like', "%{$query}%")
            ->limit(2)
            ->get(['slug', 'title'])
            ->map(fn($item) => ['label' => $item->title, 'category' => 'Ajuda', 'url' => route('kb.article', $item->slug)]);
        
        $suggestions = array_merge($suggestions, $help->toArray());

        // Alimentos
        $foods = \App\Models\Food::where('name', 'like', "%{$query}%")
            ->limit(2)
            ->get(['id', 'name'])
            ->map(fn($item) => ['label' => $item->name, 'category' => 'Alimento', 'url' => route('global.search', ['q' => $item->name])]);

        $suggestions = array_merge($suggestions, $foods->toArray());

        return response()->json($suggestions);
    }
}
