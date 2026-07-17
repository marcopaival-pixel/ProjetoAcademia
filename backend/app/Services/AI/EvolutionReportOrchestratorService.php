<?php

namespace App\Services\AI;

use App\Models\BodyAssessment;
use App\Models\EvolutionPhoto;
use App\Models\User;
use App\Services\AI\Evolution\ClaimPublicationPolicy;
use App\Services\AI\Evolution\EvolutionAntiHallucinationAuditorAgent;
use App\Services\AI\Evolution\EvolutionImageValidationAgent;
use App\Services\AI\Evolution\EvolutionObjectiveDataAgent;
use App\Services\AI\Evolution\EvolutionReportPublisher;
use App\Services\AI\Evolution\EvolutionStandardizationAgent;
use App\Services\AI\Evolution\EvolutionVisualComparisonAgent;
use Illuminate\Support\Carbon;

class EvolutionReportOrchestratorService
{
    private const ANGLES = ['front', 'back', 'right_side', 'left_side'];

    public function __construct(
        private EvolutionImageValidationAgent $imageValidator,
        private EvolutionStandardizationAgent $standardizationAgent,
        private EvolutionObjectiveDataAgent $objectiveDataAgent,
        private EvolutionVisualComparisonAgent $visualComparisonAgent,
        private EvolutionAntiHallucinationAuditorAgent $auditorAgent,
        private ClaimPublicationPolicy $claimPolicy,
        private EvolutionReportPublisher $publisher,
    ) {}

    public function generate(User $user): array
    {
        if (! $user->hasPremiumAccess()) {
            return $this->blocked('nao_autorizado', ['Relatório inteligente exclusivo para membros Premium.']);
        }

        $allSessions = $this->photoSessions($user);
        $validated = $this->imageValidator->validate($allSessions->all());
        $sessions = collect($validated['approved_sessions']);

        if ($sessions->isEmpty()) {
            return $this->blocked('sem_registro_completo', [
                'Para gerar uma comparação confiável, complete as quatro fotos e registre seus dados atuais.',
            ], $validated['failures']);
        }

        $current = $sessions->first();
        $previous = $sessions->skip(1)->first();
        $standardization = $this->standardizationAgent->compare($current, $previous);

        $assessments = BodyAssessment::query()
            ->where('user_id', $user->id)
            ->orderByDesc('assessment_date')
            ->take(2)
            ->get();

        $objectiveData = $this->objectiveDataAgent->extract($assessments);
        $visualClaims = $this->claimPolicy->filter($this->visualComparisonAgent->compare($user, $current, $previous));
        $visualEvidence = $previous ? $this->publisher->visualObservations($visualClaims, ['claims' => []]) : [];
        $limitations = $this->limitations($previous, $standardization, $visualEvidence, $objectiveData);

        $report = [
            'status' => $previous ? 'aprovado_com_limitacoes' : 'linha_base_com_limitacoes',
            'nome' => 'Relatório inteligente de evolução visual',
            'periodo_comparado' => [
                'registro_anterior' => $previous['date'] ?? null,
                'registro_atual' => $current['date'],
            ],
            'modo' => $previous ? 'comparacao' : 'linha_base',
            'dados_confirmados' => $objectiveData,
            'observacoes_visuais' => $visualEvidence,
            'claims_visuais' => $visualClaims,
            'qualidade_fotos' => $this->photoQuality($current, $previous, $standardization),
            'limitacoes' => $limitations,
            'recomendacoes' => $this->recommendations($previous),
            'confianca_geral' => $this->confidence($current, $previous, $standardization, $visualEvidence, $objectiveData),
            'alertas' => [],
            'auditoria_aprovada' => true,
            'auditoria' => [
                'modelo' => 'evolution-report-orchestrator:v2',
                'agentes' => [
                    'image_validator',
                    'standardization',
                    'objective_data',
                    'visual_comparison',
                    'anti_hallucination_auditor',
                ],
                'fontes' => ['evolution_photos', 'body_assessments', 'evolution_session_analyses'],
                'falhas' => [],
            ],
        ];

        $audited = $this->auditorAgent->audit($report, $user);
        $audited['observacoes_visuais'] = $this->publisher->visualObservations(
            $visualClaims,
            ['claims' => $audited['auditoria']['claims'] ?? []]
        );

        if ($audited['observacoes_visuais'] === [] && $visualClaims !== []) {
            $audited['status'] = 'aprovado_com_limitacoes';
            $audited['limitacoes'][] = 'As observacoes visuais foram limitadas pela auditoria; o relatorio prioriza dados objetivos.';
        }

        return $audited;
    }

    private function photoSessions(User $user)
    {
        return EvolutionPhoto::query()
            ->where('user_id', $user->id)
            ->whereIn('type', ['front', 'back', 'right_side', 'left_side', 'side'])
            ->orderByDesc('registered_date')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn (EvolutionPhoto $photo) => Carbon::parse($photo->registered_date)->toDateString())
            ->map(function ($photos, string $date) {
                $types = $photos->pluck('type')->values()->all();
                $normalizedTypes = collect($types)
                    ->flatMap(fn (string $type) => $type === 'side' ? ['right_side', 'left_side'] : [$type])
                    ->unique()
                    ->values()
                    ->all();
                $completion = collect(self::ANGLES)->filter(fn ($type) => in_array($type, $normalizedTypes, true))->count();

                return [
                    'date' => $date,
                    'completion' => $completion,
                    'photos_count' => $photos->count(),
                    'complete' => $completion >= 4,
                    'types' => $types,
                    'normalized_types' => $normalizedTypes,
                    'photo_ids' => $photos->pluck('id')->values()->all(),
                ];
            })
            ->values();
    }

    private function photoQuality(array $current, ?array $previous, array $standardization): array
    {
        return [
            'registro_atual' => [
                'data' => $current['date'],
                'angulos_capturados' => $current['completion'],
                'status' => $current['completion'] >= 4 ? 'completo' : 'incompleto',
            ],
            'registro_anterior' => $previous ? [
                'data' => $previous['date'],
                'angulos_capturados' => $previous['completion'],
                'status' => $previous['completion'] >= 4 ? 'completo' : 'incompleto',
            ] : null,
            'padronizacao' => $standardization,
        ];
    }

    private function limitations(?array $previous, array $standardization, array $visualEvidence, array $objectiveData): array
    {
        $limitations = [
            'Este relatório apoia o acompanhamento visual e não substitui avaliação de profissional de saúde.',
            'Fotos isoladas não permitem estimar percentual exato de gordura, massa muscular ou diagnóstico médico.',
        ];

        if (!$previous) {
            $limitations[] = 'Há apenas um registro completo; o sistema gera linha de base, não comparação evolutiva.';
        }
        foreach ($standardization['issues'] ?? [] as $issue) {
            $limitations[] = $issue;
        }
        if ($visualEvidence === []) {
            $limitations[] = 'Não há análise visual estruturada salva para este registro; a comparação visual fica indisponível.';
        }
        if ($objectiveData === []) {
            $limitations[] = 'Não há medidas corporais recentes suficientes para calcular variações objetivas.';
        }

        return array_values(array_unique($limitations));
    }

    private function recommendations(?array $previous): array
    {
        $items = [
            'Mantenha as quatro fotos no mesmo local, distância, altura de câmera e iluminação.',
            'Registre peso e medidas no mesmo dia das fotos para melhorar a confiança do relatório.',
        ];

        if (!$previous) {
            $items[] = 'Crie um novo registro completo no próximo ciclo para permitir comparação confiável.';
        }

        return $items;
    }

    private function confidence(array $current, ?array $previous, array $standardization, array $visualEvidence, array $objectiveData): float
    {
        $score = 0.35;
        $score += min(0.25, $current['completion'] * 0.06);
        $score += $previous ? 0.15 : 0.0;
        $score += $visualEvidence !== [] ? 0.10 : 0.0;
        $score += $objectiveData !== [] ? 0.15 : 0.0;
        $score += (float) ($standardization['confidence_modifier'] ?? 0.0);

        return round(max(0.0, min(0.95, $score)), 2);
    }

    private function blocked(string $status, array $alerts, array $failures = []): array
    {
        return [
            'status' => $status,
            'nome' => 'Relatório inteligente de evolução visual',
            'periodo_comparado' => ['registro_anterior' => null, 'registro_atual' => null],
            'dados_confirmados' => [],
            'observacoes_visuais' => [],
            'qualidade_fotos' => [],
            'limitacoes' => [],
            'recomendacoes' => [],
            'confianca_geral' => 0.0,
            'alertas' => $alerts,
            'auditoria_aprovada' => false,
            'auditoria' => [
                'modelo' => 'evolution-report-orchestrator:v2',
                'agentes' => ['image_validator'],
                'fontes' => ['evolution_photos'],
                'falhas' => array_merge($alerts, $failures),
            ],
        ];
    }
}
