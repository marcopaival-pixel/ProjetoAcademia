<?php

namespace App\Http\Controllers;

use App\Services\AIChatService;
use App\Services\IntelligenceLibraryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SmartQueryController extends Controller
{
    public function __construct(
        private AIChatService $aiService,
        private IntelligenceLibraryService $libraryService
    ) {}

    /**
     * Executa uma consulta inteligente: Verifica a biblioteca interna antes de chamar a IA.
     * Grava automaticamente os resultados da IA para uso futuro.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function query(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pergunta' => 'required|string|max:1000',
            'modulo' => 'nullable|string',
            'categoria' => 'nullable|string',
            'tipo_item' => 'nullable|string', // ex: PROTOCOLO, STACK, etc
            'force_ia' => 'nullable|boolean', 
        ]);

        $pergunta = $validated['pergunta'];
        $modulo = $validated['modulo'] ?? 'GERAL';
        $categoria = $validated['categoria'] ?? 'GERAL';
        $tipoItem = $validated['tipo_item'] ?? null;
        $forceIa = $validated['force_ia'] ?? false;

        // 1. Verificar biblioteca interna (se não for forçado IA)
        if (!$forceIa) {
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

        // 2. Se não encontrar ou forçado, chamar a IA
        // Nota: AIChatService pode precisar de mais contexto se for para um módulo específico
        $aiResponse = $this->aiService->chat($pergunta, [], []); 

        if (!$aiResponse['ok']) {
            return response()->json([
                'ok' => false,
                'error' => $aiResponse['error'] ?? 'Erro ao consultar IA',
            ], 500);
        }

        // 3. Salvar automaticamente na biblioteca para reutilização
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

    /**
     * Gera um título curto baseado na pergunta
     */
    private function generateTitle(string $pergunta): string
    {
        $titulo = str_replace(['?', '!', '.', ','], '', $pergunta);
        if (strlen($titulo) > 60) {
            $titulo = substr($titulo, 0, 57) . '...';
        }
        return ucwords(mb_strtolower($titulo));
    }
}
