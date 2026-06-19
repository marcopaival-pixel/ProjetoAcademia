<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Services\AI\AIProviderService;
use App\Services\KnowledgeBaseResolverService;
use Exception;

class SupportAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider,
        private KnowledgeBaseResolverService $knowledgeBase
    ) {}

    public function getName(): string
    {
        return 'support';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            if (empty($context['force_ia'])) {
                $kbDirect = $this->knowledgeBase->resolve($message);
                if ($kbDirect !== null) {
                    return $kbDirect;
                }
            }

            $kbContext = $this->fetchKnowledgeBaseContext($message);

            $systemContent = 'Você é o NexShape Support Assistant. Ajude o usuário com dúvidas sobre como usar a plataforma NexShape. Seja gentil, direto e eficiente.';
            if ($kbContext !== '') {
                $systemContent .= "\n\nArtigos relevantes da base de conhecimento:\n".$kbContext;
            }

            $messages = [
                ['role' => 'system', 'content' => $systemContent],
                ['role' => 'user', 'content' => $message],
            ];

            $this->injectChatHistory($messages, $context);

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'fast',
                context: array_merge(['temperature' => 0.3, 'max_tokens' => 800], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function fetchKnowledgeBaseContext(string $message): string
    {
        $terms = array_filter(explode(' ', mb_strtolower(trim($message))), fn ($t) => mb_strlen($t) >= 4);
        if ($terms === []) {
            return '';
        }

        $query = \App\Models\KnowledgeArticle::query()->active();

        $query->where(function ($q) use ($terms) {
            foreach (array_slice($terms, 0, 5) as $term) {
                $q->orWhere('titulo', 'like', "%{$term}%")
                    ->orWhere('conteudo', 'like', "%{$term}%");
            }
        });

        $articles = $query->limit(3)->get(['titulo', 'conteudo']);

        if ($articles->isEmpty()) {
            return '';
        }

        return $articles->map(function ($article) {
            $excerpt = mb_substr(strip_tags($article->conteudo), 0, 400);

            return "- **{$article->titulo}**: {$excerpt}";
        })->implode("\n");
    }
}
