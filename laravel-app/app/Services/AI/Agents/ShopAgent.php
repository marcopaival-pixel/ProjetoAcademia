<?php

namespace App\Services\AI\Agents;

use App\Models\User;
use App\Models\ShopProduct;
use App\Models\ShopOrder;
use App\Models\ShopPointsWallet;
use App\Models\ShopRecommendation;
use App\Services\AI\AIProviderService;
use Exception;
use Illuminate\Support\Facades\File;

class ShopAgent extends BaseAgent
{
    public function __construct(
        private AIProviderService $aiProvider
    ) {}

    public function getName(): string
    {
        return 'shop';
    }

    public function execute(User $user, string $message, array $context = []): array
    {
        try {
            $shopContext = $this->getShopContext($user, $context);

            $promptFile = base_path('agentesprd/shop-agent.md');
            $instructions = File::exists($promptFile)
                ? File::get($promptFile)
                : 'Você é o Shop Specialist da NexShape. Ajude o usuário a encontrar produtos, acompanhar pedidos e usar seus pontos. NUNCA invente preços ou produtos.';

            $messages = [
                [
                    'role'    => 'system',
                    'content' => $instructions . "\n\n" . $shopContext,
                ],
                ['role' => 'user', 'content' => $message],
            ];

            $this->injectChatHistory($messages, $context);

            return $this->aiProvider->call(
                user: $user,
                messages: $messages,
                agentName: $this->getName(),
                modelType: 'fast',
                context: array_merge(['temperature' => 0.5], $context)
            );
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function getShopContext(User $user, array $context): string
    {
        $parts = [];

        // Tenant ID — isolamento multi-clínica
        $companyId = $context['clinicId'] ?? $context['clinic_id'] ?? $user->academy_company_id ?? null;

        // 1. Carteira de pontos e cashback do usuário
        $wallet = ShopPointsWallet::where('user_id', $user->id)
            ->when($companyId, fn ($q) => $q->where('academy_company_id', $companyId))
            ->first();

        if ($wallet) {
            $parts[] = "CARTEIRA DE PONTOS:\n"
                . "- Pontos disponíveis: {$wallet->balance_points} pts\n"
                . "- Cashback disponível: R$ " . number_format((float) $wallet->balance_cashback, 2, ',', '.') . "\n"
                . "- Nível de fidelidade: " . strtoupper($wallet->tier ?? 'bronze');
        } else {
            $parts[] = "CARTEIRA DE PONTOS: Nenhuma carteira encontrada para este usuário.";
        }

        // 2. Últimos pedidos (isolados por tenant)
        $orders = ShopOrder::where('user_id', $user->id)
            ->when($companyId, fn ($q) => $q->where('academy_company_id', $companyId))
            ->latest()
            ->limit(3)
            ->with('items:id,order_id,product_name,quantity,unit_price')
            ->get(['id', 'order_number', 'status', 'total', 'created_at', 'tracking_code']);

        if ($orders->isNotEmpty()) {
            $orderLines = $orders->map(fn ($o) =>
                "- #{$o->order_number} | {$o->statusLabel()} | R$ " . number_format((float) $o->total, 2, ',', '.')
                . ($o->tracking_code ? " | Rastreio: {$o->tracking_code}" : '')
            )->implode("\n");
            $parts[] = "ÚLTIMOS PEDIDOS:\n{$orderLines}";
        } else {
            $parts[] = "ÚLTIMOS PEDIDOS: Nenhum pedido realizado ainda.";
        }

        // 3. Produtos em destaque alinhados ao objetivo do usuário (isolados por tenant)
        $userGoal = $user->profile?->goal ?? null;
        $productQuery = ShopProduct::published()->inStock()->featured()
            ->when($companyId, fn ($q) => $q->where('academy_company_id', $companyId))
            ->limit(5);

        if ($userGoal) {
            $productQuery->where(function ($q) use ($userGoal) {
                $q->whereJsonContains('goal_types', $userGoal)
                  ->orWhereNull('goal_types');
            });
        }

        $featuredProducts = $productQuery->get(['id', 'name', 'type', 'price', 'sale_price', 'short_description', 'goal_types']);

        if ($featuredProducts->isNotEmpty()) {
            $productLines = $featuredProducts->map(fn ($p) =>
                "- {$p->name}"
                . ($p->isOnSale() ? " | R$ " . number_format($p->currentPrice(), 2, ',', '.') . " (em promoção)" : " | R$ " . number_format((float) $p->price, 2, ',', '.'))
                . " [{$p->type}]"
                . ($p->short_description ? " — {$p->short_description}" : '')
            )->implode("\n");
            $parts[] = "PRODUTOS EM DESTAQUE (alinhados ao objetivo do paciente):\n{$productLines}";
        }

        // 4. Recomendação ativa por IA (se existir, isolada por tenant)
        $recommendation = ShopRecommendation::where('user_id', $user->id)
            ->when($companyId, fn ($q) => $q->where('academy_company_id', $companyId))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest()
            ->first(['product_ids', 'reason']);

        if ($recommendation && !empty($recommendation->product_ids)) {
            $recProducts = ShopProduct::published()
                ->when($companyId, fn ($q) => $q->where('academy_company_id', $companyId))
                ->whereIn('id', $recommendation->product_ids)
                ->limit(3)
                ->get(['id', 'name', 'price', 'sale_price']);

            if ($recProducts->isNotEmpty()) {
                $recLines = $recProducts->map(fn ($p) =>
                    "- {$p->name} | R$ " . number_format($p->currentPrice(), 2, ',', '.')
                )->implode("\n");
                $parts[] = "RECOMENDAÇÕES PERSONALIZADAS (motivo: {$recommendation->reason}):\n{$recLines}";
            }
        }

        return "CONTEXTO DA LOJA — Paciente: {$user->name}\n\n" . implode("\n\n", $parts);
    }
}
