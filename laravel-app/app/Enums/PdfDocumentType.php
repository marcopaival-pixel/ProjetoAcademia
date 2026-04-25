<?php

namespace App\Enums;

enum PdfDocumentType: string
{
    case Contract = 'contract';
    case Receipt = 'receipt';
    case TrainingPlan = 'training_plan';
    case PhysicalAssessment = 'physical_assessment';
    case FinancialReport = 'financial_report';
    case EnrollmentForm = 'enrollment_form';

    /** Prefixo para numeração oficial (ex.: REC-2026-000001). */
    public function numberPrefix(): string
    {
        return match ($this) {
            self::Contract => 'CTR',
            self::Receipt => 'REC',
            self::TrainingPlan => 'TRN',
            self::PhysicalAssessment => 'AVL',
            self::FinancialReport => 'FIN',
            self::EnrollmentForm => 'MAT',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Contract => 'Contrato',
            self::Receipt => 'Recibo',
            self::TrainingPlan => 'Plano de treino',
            self::PhysicalAssessment => 'Avaliação física',
            self::FinancialReport => 'Relatório financeiro',
            self::EnrollmentForm => 'Ficha de matrícula',
        };
    }

    /**
     * Variáveis sugeridas para o modelo (documentação / pré-visualização).
     *
     * @return list<string>
     */
    public function suggestedVariables(): array
    {
        return match ($this) {
            self::Contract => [
                'empresa_nome', 'empresa_documento', 'aluno_nome', 'aluno_documento',
                'data_inicio', 'data_fim', 'valor_mensal', 'clausulas',
            ],
            self::Receipt => [
                'empresa_nome', 'recibo_numero', 'data_emissao', 'aluno_nome',
                'descricao_servico', 'valor', 'forma_pagamento',
            ],
            self::TrainingPlan => [
                'aluno_nome', 'profissional_nome', 'plano_nome', 'data_emissao',
                'objetivo', 'observacoes', 'conteudo_treino',
            ],
            self::PhysicalAssessment => [
                'aluno_nome', 'data_avaliacao', 'peso', 'altura', 'imc',
                'percentual_gordura', 'observacoes', 'profissional_nome',
            ],
            self::FinancialReport => [
                'periodo', 'empresa_nome', 'total_receitas', 'total_despesas',
                'saldo', 'detalhamento',
            ],
            self::EnrollmentForm => [
                'academia_nome', 'aluno_nome', 'data_nascimento', 'telefone', 'email',
                'endereco', 'plano', 'data_matricula', 'responsavel_nome',
            ],
        };
    }

    /**
     * Valores de exemplo para pré-visualização no painel.
     *
     * @return array<string, string>
     */
    public function sampleVariables(): array
    {
        $base = [
            'empresa_nome' => 'Academia Exemplo Ltda',
            'academia_nome' => 'Academia Exemplo',
            'aluno_nome' => 'Maria Silva',
            'profissional_nome' => 'Dr. João Treinador',
            'data_emissao' => now()->format('d/m/Y'),
        ];

        return match ($this) {
            self::Contract => $base + [
                'empresa_documento' => '12.345.678/0001-90',
                'aluno_documento' => '123.456.789-00',
                'data_inicio' => now()->format('d/m/Y'),
                'data_fim' => now()->addYear()->format('d/m/Y'),
                'valor_mensal' => 'R$ 199,90',
                'clausulas' => 'Texto legal de exemplo para contrato de prestação de serviços.',
            ],
            self::Receipt => $base + [
                'recibo_numero' => '2026-0001',
                'descricao_servico' => 'Mensalidade — Plano Premium',
                'valor' => 'R$ 199,90',
                'forma_pagamento' => 'Cartão de crédito',
            ],
            self::TrainingPlan => $base + [
                'plano_nome' => 'Hipertrofia — 4x semana',
                'objetivo' => 'Ganho de massa muscular',
                'observacoes' => 'Aquecimento obrigatório. Hidratação durante o treino.',
                'conteudo_treino' => "Segunda: Peito / Tríceps\nQuarta: Costas / Bíceps\nSexta: Pernas",
            ],
            self::PhysicalAssessment => $base + [
                'data_avaliacao' => now()->format('d/m/Y'),
                'peso' => '68,5 kg',
                'altura' => '1,65 m',
                'imc' => '25,2',
                'percentual_gordura' => '22%',
                'observacoes' => 'Avaliação ilustrativa para pré-visualização do PDF.',
            ],
            self::FinancialReport => [
                'periodo' => now()->format('m/Y'),
                'empresa_nome' => 'Academia Exemplo Ltda',
                'total_receitas' => 'R$ 45.320,00',
                'total_despesas' => 'R$ 12.100,00',
                'saldo' => 'R$ 33.220,00',
                'detalhamento' => "Receitas: mensalidades, personal.\nDespesas: aluguel, equipamentos.",
            ],
            self::EnrollmentForm => $base + [
                'data_nascimento' => '01/01/1990',
                'telefone' => '(11) 98765-4321',
                'email' => 'maria@email.com',
                'endereco' => 'Rua das Flores, 100 — São Paulo / SP',
                'plano' => 'Premium Anual',
                'data_matricula' => now()->format('d/m/Y'),
                'responsavel_nome' => 'Carlos Silva',
            ],
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
