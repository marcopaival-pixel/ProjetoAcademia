<?php

namespace App\Services;

use App\Models\BibliotecaInteligente;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntelligenceLibraryService
{
    /**
     * Consulta a biblioteca inteligente antes de chamar a IA
     *
     * @param string $pergunta
     * @param string|null $modulo
     * @param string|null $categoria
     * @return BibliotecaInteligente|null
     */
    public function consultar(string $pergunta, string $modulo = null, string $categoria = null)
    {
        $query = BibliotecaInteligente::where('ativo', true)
            ->where(function ($q) use ($pergunta) {
                $q->where('pergunta', 'like', "%{$pergunta}%")
                  ->orWhere('titulo', 'like', "%{$pergunta}%")
                  ->orWhere('palavras_chave', 'like', "%{$pergunta}%");
            });

        if ($modulo) {
            $query->where('modulo', $modulo);
        }

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        // Prioriza correspondência mais exata se possível, ou mais usada
        $resultado = $query->orderBy('uso_count', 'desc')->first();

        if ($resultado) {
            $resultado->increment('uso_count');
            $resultado->touch(); // Registra data de uso (updated_at)
            return $resultado;
        }

        return null;
    }

    /**
     * Salva uma resposta da IA na biblioteca, processando listas se existirem
     *
     * @param array $respostaIA Deve conter 'titulo', 'textoCompleto' ou 'message' e opcionalmente 'listaItens'
     * @param string $modulo
     * @param string $categoria
     * @param string|null $pergunta
     * @param string|null $tipoItem Override para o tipo de item (ex: PROTOCOLO, STACK)
     * @return BibliotecaInteligente
     */
    public function salvarRespostaIA(array $respostaIA, string $modulo, string $categoria, string $pergunta = null, string $tipoItem = null)
    {
        return DB::transaction(function () use ($respostaIA, $modulo, $categoria, $pergunta, $tipoItem) {
            $textoCompleto = $respostaIA['textoCompleto'] ?? $respostaIA['message'] ?? $respostaIA['conteudo'] ?? '';
            
            // Tenta extrair lista se não for fornecida explicitamente
            if (!isset($respostaIA['listaItens']) || empty($respostaIA['listaItens'])) {
                $respostaIA['listaItens'] = $this->extrairLista($textoCompleto);
            }

            // 1) Identificar o tipo de item principal
            if (!$tipoItem) {
                $tipoItem = !empty($respostaIA['listaItens']) ? 'LISTA' : 'RESPOSTA';
            }
            
            // 2) Salvar resposta principal
            $biblioteca = BibliotecaInteligente::create([
                'modulo' => $modulo,
                'categoria' => $categoria,
                'tipo_item' => $tipoItem,
                'titulo' => $respostaIA['titulo'] ?? ($pergunta ? substr($pergunta, 0, 100) : 'Resposta IA'),
                'descricao' => $respostaIA['descricao'] ?? 'Resposta gerada automaticamente pela IA',
                'pergunta' => $pergunta,
                'palavras_chave' => $respostaIA['palavras_chave'] ?? null,
                'conteudo' => $textoCompleto,
                'origem' => 'IA',
                'visibilidade' => 'PUBLICO',
                'status' => 'ATIVO',
                'uso_count' => 1,
                'created_by' => auth()->id(),
            ]);

            // 3) Verificar se resposta contém lista e processar itens
            if (!empty($respostaIA['listaItens'])) {
                foreach ($respostaIA['listaItens'] as $item) {
                    $tituloItem = $item['nome'] ?? $item['titulo'] ?? null;
                    if (!$tituloItem) continue;

                    // Evitar duplicação usando titulo, modulo e categoria
                    $existeItem = BibliotecaInteligente::where('titulo', $tituloItem)
                        ->where('modulo', $modulo)
                        ->where('categoria', $categoria)
                        ->first();

                    if (!$existeItem) {
                        BibliotecaInteligente::create([
                            'modulo' => $modulo,
                            'categoria' => $categoria,
                            'tipo_item' => $item['tipo_item'] ?? 'ITEM_LISTA',
                            'titulo' => $tituloItem,
                            'descricao' => $item['descricao'] ?? null,
                            'conteudo' => $item['detalhes'] ?? $item['conteudo'] ?? null,
                            'origem' => 'IA',
                            'visibilidade' => 'PUBLICO',
                            'status' => 'ATIVO',
                            'uso_count' => 0,
                            'parent_id' => $biblioteca->id,
                            'created_by' => auth()->id(),
                        ]);
                    } else {
                        // Se já existir, incrementa o uso
                        $existeItem->increment('uso_count');
                    }
                }
            }

            return $biblioteca;
        });
    }

    /**
     * Tenta extrair uma lista estruturada da resposta de texto da IA
     * Identifica linhas que começam com marcadores comuns (*, -, 1., etc)
     *
     * @param string $texto
     * @return array
     */
    public function extrairLista(string $texto): array
    {
        $linhas = explode("\n", $texto);
        $itens = [];
        
        foreach ($linhas as $linha) {
            $linha = trim($linha);
            // Regex para identificar itens de lista: asterisco, traço ou número seguido de ponto e espaço
            if (preg_match('/^[\*\-\d\.]+\s+(.+)/', $linha, $matches)) {
                $itemNome = trim($matches[1]);
                
                // Ignora itens muito curtos ou que pareçam apenas pontuação
                if (strlen($itemNome) > 2) {
                    $itens[] = [
                        'nome' => $itemNome,
                        'tipo_item' => 'ITEM_LISTA'
                    ];
                }
            }
        }
        
        return $itens;
    }
}
