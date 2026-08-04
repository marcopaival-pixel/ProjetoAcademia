<?php

namespace App\Http\Controllers;

use App\Services\AI\OrchestratorService;
use App\Services\AiCreditService;
use App\Services\IntelligenceLibraryService;
use App\Services\StudentContextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartQueryController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator,
        private IntelligenceLibraryService $libraryService,
        private StudentContextService $studentContext,
        private AiCreditService $aiCredits,
    ) {}

    /**
     * Executa uma consulta inteligente: Verifica a biblioteca interna antes de chamar a IA.
     * Grava automaticamente os resultados da IA para uso futuro.
     */
    public function query(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pergunta' => 'required|string|max:1000',
            'modulo' => 'nullable|string',
            'categoria' => 'nullable|string',
            'tipo_item' => 'nullable|string',
            'force_ia' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $pergunta = $validated['pergunta'];
        $modulo = $validated['modulo'] ?? 'GERAL';
        $categoria = $validated['categoria'] ?? 'GERAL';
        $tipoItem = $validated['tipo_item'] ?? null;
        $forceIa = $validated['force_ia'] ?? false;

        if (! $forceIa) {
            $resultadoInterno = $this->libraryService->consultar($pergunta, $modulo, $categoria);

            if ($resultadoInterno) {
                return response()->json([
                    'ok' => true,
                    'origem' => 'BIBLIOTECA',
                    'conteudo' => $resultadoInterno->conteudo,
                    'titulo' => $resultadoInterno->titulo,
                    'tipo_item' => $resultadoInterno->tipo_item,
                    'uso_count' => $resultadoInterno->uso_count,
                    'last_used' => $resultadoInterno->updated_at->diffForHumans(),
                ]);
            }
        }

        if (! $this->aiCredits->hasCredits($user, 'ai_orchestrator')) {
            return response()->json([
                'ok' => false,
                'code' => 'credits_exceeded',
                'error' => 'Creditos de IA insuficientes para consultar a biblioteca inteligente.',
            ], 402);
        }

        $intent = $this->studentContext->resolveQueryIntent($modulo, $categoria);
        $context = [
            'source' => 'smart_query',
            'modulo' => $modulo,
            'categoria' => $categoria,
            'clinic_id' => $user->clinic_id,
            'clinicId' => $user->academy_company_id,
            'feature_key' => 'ai_orchestrator',
            'user_metrics' => $this->studentContext->metrics($user),
        ];

        if ($intent !== null) {
            $context['intent'] = $intent;
        }

        $aiResponse = $this->orchestrator->run($user, $pergunta, $context);

        if ($aiResponse['status'] !== 'success') {
            return response()->json([
                'ok' => false,
                'error' => $aiResponse['error'] ?? $aiResponse['message'] ?? 'Erro ao consultar IA',
            ], ($aiResponse['status'] ?? null) === 'limit_reached' ? 403 : 500);
        }

        $referenceId = hash('sha256', implode('|', [
            $user->id,
            mb_strtolower(trim($pergunta)),
            $modulo,
            $categoria,
            now()->format('Y-m-d-H'),
        ]));

        $this->aiCredits->consume($user, 'ai_orchestrator', [
            'source' => 'smart_query',
            'modulo' => $modulo,
            'categoria' => $categoria,
        ], $referenceId);

        $biblioteca = $this->libraryService->salvarRespostaIA([
            'message' => $aiResponse['message'],
            'titulo' => $this->generateTitle($pergunta),
        ], $modulo, $categoria, $pergunta, $tipoItem);

        return response()->json([
            'ok' => true,
            'origem' => 'IA',
            'conteudo' => $aiResponse['message'],
            'titulo' => $biblioteca->titulo,
            'tipo_item' => $biblioteca->tipo_item,
            'biblioteca_id' => $biblioteca->id,
            'itens_extraidos' => $biblioteca->children()->count(),
        ]);
    }

    private function generateTitle(string $pergunta): string
    {
        $titulo = str_replace(['?', '!', '.', ','], '', $pergunta);
        if (strlen($titulo) > 60) {
            $titulo = substr($titulo, 0, 57).'...';
        }

        return ucwords(mb_strtolower($titulo));
    }
}
