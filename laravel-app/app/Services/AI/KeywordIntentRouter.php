<?php

namespace App\Services\AI;

class KeywordIntentRouter
{
    /**
     * Resolve intenção por palavras-chave (sem LLM).
     * Retorna null se ambíguo — nesse caso o classificador LLM pode ser usado.
     */
    public function resolve(string $message): ?string
    {
        $text = mb_strtolower(trim($message));

        if ($text === '') {
            return null;
        }

        $rules = [
            'training'     => '/\b(treino|treinar|exercício|exercicio|série|serie|repetição|repeticao|academia|musculação|musculacao|workout|hipertrofia|leg day|supino|agachamento|ficha|halter|barra|crossfit|cardio)\b/u',
            'nutrition'    => '/\b(dieta|alimentação|alimentacao|refeição|refeicao|caloria|macro|proteína|proteina|carbo|gordura|água|agua|suplemento|whey|creatina|jantar|almoço|almoco|café da manhã|jejum|vegetariano)\b/u',
            'clinical'     => '/\b(bioimpedância|bioimpedancia|exame|laborat|bioimpedance|gordura visceral|massa magra|saúde|saude|clínico|clinico|pressão|pressao|colesterol|composição corporal)\b/u',
            'pain'         => '/\b(dor|dores|doendo|doei|machucar|machucou|lesão|lesao|lesionei|fisioterapia|fisio|eva|escala de dor|inflamação|inflamacao|torci|torção)\b/u',
            'scheduling'   => '/\b(consulta|agendar|agendamento|agenda|horário|horario|remarcar|reagendar|cancelar consulta|minha consulta|próxima consulta|proxima consulta)\b/u',
            'psychology'   => '/\b(humor|ansiedade|ansioso|depressão|depressao|estresse|estressado|sono|insônia|insonia|emocional|mindfulness|meditação|meditacao|psicólogo|psicologo|bem.estar|saúde mental)\b/u',
            'medic'        => '/\b(receita|medicamento|remédio|remedio|dosagem|bula|médico|medico|prescri|antibiótico|antibiotico|comprimido|tomando|devo tomar)\b/u',
            'shop'         => '/\b(loja|produto|comprar|comprei|pedido|entrega|rastrear|rastreio|carrinho|pontua|pontos|cashback|desconto|promoção|promocao|cupom|frete|shop)\b/u',
            'analytics'    => '/\b(relatório|relatorio|progresso|evolução|evolucao|estatística|estatistica|gráfico|grafico|desempenho|aderência|aderencia|constância|constancia|dashboard|indicador|kpi)\b/u',
            'finance'      => '/\b(pagamento|mensalidade|fatura|boleto|pix|cartão|cartao|financeiro|cobrança|cobranca|plano pago|assinatura|recibo|nota fiscal)\b/u',
            'sales'        => '/\b(comprar plano|upgrade|promoção|promocao|assinar|contratar|oferta|plano pro|premium)\b/u',
            'retention'    => '/\b(cancelar|cancelamento|desistir|desmotivado|renovar assinatura|churn|pausar conta)\b/u',
            'support'      => '/\b(ajuda|como usar|bug|erro|suporte|não consigo|nao consigo|onde fica|tutorial|configurar|senha|login|perfil|menu)\b/u',
            'workout_sheet'=> '/\b(ficha de treino|importar treino|foto do treino|planilha de treino|ocr treino)\b/u',
            'meal_photo'   => '/\b(foto da refeição|foto refeição|foto comida|analisar prato|foto do prato)\b/u',
        ];

        $matches = [];
        foreach ($rules as $intent => $pattern) {
            if (preg_match($pattern, $text)) {
                $matches[] = $intent;
            }
        }

        if (count($matches) === 1) {
            return $matches[0];
        }

        if (count($matches) > 1) {
            $priority = ['workout_sheet', 'meal_photo', 'pain', 'medic', 'psychology', 'scheduling', 'shop', 'clinical', 'training', 'nutrition', 'analytics', 'finance', 'sales', 'retention', 'support'];
            foreach ($priority as $intent) {
                if (in_array($intent, $matches, true)) {
                    return $intent;
                }
            }
        }

        return null;
    }
}
