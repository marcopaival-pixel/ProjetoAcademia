<?php

namespace App\Services;

use App\Models\KnowledgeArticle;

class KnowledgeBaseResolverService
{
    /**
     * Tenta resolver a mensagem via base de conhecimento (sem LLM).
     *
     * @return array{ok: bool, message: string, tokens: int, cost: int, model: string, source: string}|null
     */
    public function resolve(string $message): ?array
    {
        $articles = $this->findArticles($message);

        if ($articles->isEmpty()) {
            return null;
        }

        $body = $articles->map(function ($article) {
            $excerpt = mb_substr(strip_tags($article->conteudo), 0, 600);

            return "**{$article->titulo}**\n{$excerpt}";
        })->implode("\n\n");

        if (mb_strlen($body) < 80) {
            return null;
        }

        return [
            'ok' => true,
            'message' => $body,
            'tokens' => 0,
            'cost' => 0,
            'model' => 'knowledge_base',
            'source' => 'knowledge_base',
        ];
    }

    private function findArticles(string $message)
    {
        $terms = array_values(array_filter(
            explode(' ', mb_strtolower(trim($message))),
            fn ($t) => mb_strlen($t) >= 4
        ));

        if ($terms === []) {
            return collect();
        }

        $query = KnowledgeArticle::query()->active();

        $query->where(function ($q) use ($terms) {
            foreach (array_slice($terms, 0, 5) as $term) {
                $q->orWhere('titulo', 'like', "%{$term}%")
                    ->orWhere('conteudo', 'like', "%{$term}%");
            }
        });

        return $query->limit(2)->get(['titulo', 'conteudo']);
    }
}
