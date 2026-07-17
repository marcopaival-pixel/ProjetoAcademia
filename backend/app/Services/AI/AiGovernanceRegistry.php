<?php

namespace App\Services\AI;

use App\Exceptions\UnmappedAiFeatureException;
use App\Models\AiAgentRegistry;
use App\Models\AiFeatureRoute;
use Illuminate\Support\Facades\Schema;

class AiGovernanceRegistry
{
    public function hasAgent(string $agentKey): bool
    {
        if (! Schema::hasTable('ai_agent_registry')) {
            return true;
        }

        return AiAgentRegistry::query()
            ->where('agent_key', $agentKey)
            ->where('active', true)
            ->exists();
    }

    public function hasRoute(string $featureKey): bool
    {
        if (! Schema::hasTable('ai_feature_routes')) {
            return true;
        }

        return AiFeatureRoute::query()
            ->where('feature_key', $featureKey)
            ->where('active', true)
            ->exists();
    }

    public function assertAgent(string $agentKey): void
    {
        if (! $this->hasAgent($agentKey)) {
            throw new UnmappedAiFeatureException("Agente de IA nao registrado ou inativo: {$agentKey}");
        }
    }

    public function agentFor(string $agentKey): ?AiAgentRegistry
    {
        if (! Schema::hasTable('ai_agent_registry')) {
            return null;
        }

        return AiAgentRegistry::query()
            ->where('agent_key', $agentKey)
            ->where('active', true)
            ->first();
    }

    public function assertRoute(string $featureKey): void
    {
        if (! $this->hasRoute($featureKey)) {
            throw new UnmappedAiFeatureException("Funcionalidade de IA sem rota registrada: {$featureKey}");
        }
    }

    public function routeFor(string $featureKey): ?AiFeatureRoute
    {
        if (! Schema::hasTable('ai_feature_routes')) {
            return null;
        }

        return AiFeatureRoute::query()
            ->where('feature_key', $featureKey)
            ->where('active', true)
            ->first();
    }
}
