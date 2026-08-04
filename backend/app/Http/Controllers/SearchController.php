<?php

namespace App\Http\Controllers;

use App\Models\ExerciseCatalog;
use App\Models\User;
use App\Services\AI\OrchestratorService;
use App\Services\AiCreditService;
use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator,
        private StudentContextService $studentContext,
        private AiCreditService $aiCredits,
    ) {}

    public function search(Request $request): View
    {
        $query = trim($request->input('q'));

        if (strlen($query) < 3) {
            $query = '';
        }

        $user = auth()->user();
        $isAdmin = $user->isAdministrator();
        $category = $request->input('category');
        $muscle = $request->input('muscle');

        $results = [];

        if (! empty($query) || ! empty($category) || ! empty($muscle)) {
            if (! $category || $category === 'exercises') {
                $exercisesQuery = ExerciseCatalog::query();
                if (! $isAdmin) {
                    $exercisesQuery->where('is_active', true);
                }

                if (! empty($query)) {
                    $exercisesQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('muscle_group', 'like', "%{$query}%")
                          ->orWhere('equipment', 'like', "%{$query}%");
                    });
                }

                if (! empty($muscle)) {
                    $exercisesQuery->where('muscle_group', $muscle);
                }

                $results['exercises'] = $exercisesQuery->limit(20)->get();
            }

            if (! $category || $category === 'workouts') {
                $trainingQuery = \App\Models\TrainingPlan::query();
                if (! $isAdmin) {
                    $trainingQuery->where('user_id', $user->id);
                }

                if (! empty($query)) {
                    $trainingQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('goal', 'like', "%{$query}%");
                    });
                }

                $results['workouts'] = $trainingQuery->limit(10)->get();
            }

            if (! $category || $category === 'help') {
                $helpQuery = \App\Models\KnowledgeArticle::query()->where('ativo', true);

                if (! empty($query)) {
                    $helpQuery->where(function ($q) use ($query) {
                        $q->where('titulo', 'like', "%{$query}%")
                          ->orWhere('conteudo', 'like', "%{$query}%");
                    });
                }

                $results['help'] = $helpQuery->limit(10)->get();
            }

            if (! $category || $category === 'announcements') {
                $announcementsQuery = \App\Models\Announcement::where('is_active', true);

                if (! empty($query)) {
                    $announcementsQuery->where('content', 'like', "%{$query}%");
                }

                $results['announcements'] = $announcementsQuery->limit(5)->get();
            }

            if (! $category || $category === 'foods') {
                $foodsQuery = \App\Models\Food::query();

                if (! empty($query)) {
                    $foodsQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('brand', 'like', "%{$query}%");
                    });
                }

                $results['foods'] = $foodsQuery->limit(10)->get();
            }

            if ($isAdmin) {
                if (! $category || $category === 'users') {
                    $usersQuery = User::query();
                    if (! empty($query)) {
                        $usersQuery->where(function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%")
                              ->orWhere('email', 'like', "%{$query}%")
                              ->orWhere('username', 'like', "%{$query}%");
                        });
                    }
                    $results['users'] = $usersQuery->limit(10)->get();
                }

                if (! $category || $category === 'errors') {
                    $errorsQuery = \App\Models\SystemError::query();
                    if (! empty($query)) {
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

        $aiResponse = null;
        $aiCreditsNotice = null;

        if ($this->shouldRunAiInterpretation($query)) {
            if (! $this->aiCredits->hasCredits($user, 'ai_orchestrator')) {
                $aiCreditsNotice = [
                    'code' => 'credits_exceeded',
                    'message' => 'Creditos de IA insuficientes para interpretar esta busca com inteligencia.',
                    'plano_url' => route('plano'),
                ];
            } else {
                $intent = $this->studentContext->resolveQueryIntent($query, (string) ($category ?? ''));
                $context = [
                    'source' => 'global_search',
                    'clinic_id' => $user->clinic_id,
                    'clinicId' => $user->academy_company_id,
                    'feature_key' => 'ai_orchestrator',
                    'user_metrics' => $this->studentContext->metrics($user),
                ];

                if ($intent !== null) {
                    $context['intent'] = $intent;
                }

                $result = $this->orchestrator->run($user, $query, $context);

                if (($result['status'] ?? null) === 'success') {
                    $this->aiCredits->consume($user, 'ai_orchestrator', [
                        'source' => 'global_search',
                        'query_chars' => mb_strlen($query),
                    ], hash('sha256', implode('|', [
                        $user->id,
                        mb_strtolower(trim($query)),
                        now()->format('Y-m-d-H'),
                    ])));

                    $aiResponse = [
                        'text' => $result['message'],
                        'action' => $result['action'] ?? null,
                    ];
                }
            }
        }

        return view('search-results', [
            'query' => $query,
            'results' => $results,
            'category' => $category,
            'muscle' => $muscle,
            'muscles' => ExerciseCatalog::distinct()->pluck('muscle_group')->filter()->values(),
            'aiResponse' => $aiResponse,
            'aiCreditsNotice' => $aiCreditsNotice,
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
        $help = \App\Models\KnowledgeArticle::query()
            ->where('ativo', true)
            ->where('titulo', 'like', "%{$query}%")
            ->limit(2)
            ->get(['slug', 'titulo'])
            ->map(fn ($item) => ['label' => $item->titulo, 'category' => 'Ajuda', 'url' => route('kb.article', $item->slug)]);
        
        $suggestions = array_merge($suggestions, $help->toArray());

        // Alimentos
        $foods = \App\Models\Food::where('name', 'like', "%{$query}%")
            ->limit(2)
            ->get(['id', 'name'])
            ->map(fn($item) => ['label' => $item->name, 'category' => 'Alimento', 'url' => route('global.search', ['q' => $item->name])]);

        $suggestions = array_merge($suggestions, $foods->toArray());

        return response()->json($suggestions);
    }

    private function shouldRunAiInterpretation(string $query): bool
    {
        if ($query === '') {
            return false;
        }

        return strlen($query) > 15
            || str_contains($query, ' ')
            || (bool) preg_match('/(quero|como|meu|treino|ajuda|onde|qual)/i', $query);
    }
}
