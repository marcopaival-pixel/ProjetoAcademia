<?php

namespace App\Services;

use DateInterval;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Role;
use App\Models\MercadoPagoSubscription;
use App\Models\MercadoPagoCredit;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\FinancialLog;
use App\Models\Commission;
use App\Services\FinancialLogService;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Services\Payment\BasePaymentGateway;
use App\Contracts\PaymentGatewayInterface;

class MercadoPagoService extends BasePaymentGateway implements PaymentGatewayInterface
{
    public function getIdentifier(): string
    {
        return 'mercadopago';
    }

    protected $token;
    protected $webhookSecret;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->token = $config['access_token'] ?? (string) config('projeto.mp_access_token');
        $this->webhookSecret = $config['webhook_secret'] ?? (string) config('projeto.mp_webhook_secret');
    }
    public function publicBase(): string
    {
        $u = (string) config('projeto.public_url');
        $bp = (string) config('projeto.base_path');

        return $u.($bp !== '' ? $bp : '');
    }

    public function absoluteUrl(string $path): string
    {
        $base = rtrim($this->publicBase(), '/');
        $p = '/'.ltrim($path, '/');

        return $base === '' ? $p : $base.$p;
    }

    /**
     * @param  array<string, mixed>|null  $body
     * @return array{ok: true, data: mixed}|array{ok: false, error: string, status: int}
     */
    public function apiRequest(string $method, string $url, string $accessToken, ?array $body): array
    {
        $req = Http::withToken($accessToken)->acceptJson()->timeout(45);
        $methodU = strtoupper($method);
        if ($methodU === 'GET') {
            $response = $req->get($url);
        } elseif ($methodU === 'POST') {
            $response = $req->asJson()->post($url, $body ?? []);
        } else {
            return ['ok' => false, 'error' => 'Unsupported HTTP method', 'status' => 0];
        }

        $status = $response->status();
        if (! $response->successful()) {
            $decoded = $response->json();
            $msg = is_array($decoded) && isset($decoded['message'])
                ? (string) $decoded['message']
                : 'HTTP '.$status;

            return ['ok' => false, 'error' => $msg, 'status' => $status];
        }

        try {
            $decoded = $response->json(throw: true);
        } catch (Exception) {
            return ['ok' => false, 'error' => 'JSON inválido na resposta MP', 'status' => $status];
        }

        return ['ok' => true, 'data' => $decoded];
    }

    /**
     * @return array{monthly: float, yearly: float, patient_monthly: float}
     */
    public function planPrices(): array
    {
        return array_merge(
            config('projeto.prices', ['monthly' => 19.9, 'yearly' => 149.9]),
            ['patient_monthly' => 14.90]
        );
    }

    /**
     * @return array{ok: true, init_point: string}|array{ok: false, error: string}
     */
    public function createCheckoutPreference(string $accessToken, int $userId, string $payerEmail, string $plan, ?Coupon $coupon = null): array
    {
        $prices = $this->planPrices();
        $user = User::find($userId);
        $isPatientOnly = $user && $user->hasRole('paciente') && !$user->hasRole('aluno');

        if ($plan === 'monthly') {
            $title = 'ProjetoAcademia Premium — mensal';
            $price = $isPatientOnly ? $prices['patient_monthly'] : $prices['monthly'];
            $planCode = 'monthly';
        } elseif ($plan === 'yearly') {
            $title = 'ProjetoAcademia Premium — anual';
            $price = $prices['yearly'];
            $planCode = 'yearly';
        } else {
            return ['ok' => false, 'error' => 'Plano inválido.'];
        }

        if ($coupon) {
            $price = $coupon->apply($price);
        }

        if ($this->publicBase() === '') {
            return ['ok' => false, 'error' => 'Configure APP_PUBLIC_URL e APP_BASE_PATH no .env para usar o checkout.'];
        }

        $payload = [
            'items' => [[
                'title' => $title,
                'description' => 'Acesso Premium: macros personalizadas e exportação CSV.',
                'category_id' => 'services',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => $price,
            ]],
            'payer' => ['email' => $payerEmail],
            'external_reference' => 'pa:'.$userId.':'.$planCode,
            'metadata' => [
                'user_id' => (string) $userId,
                'plan' => $planCode,
                'coupon_id' => $coupon ? (string) $coupon->id : null,
            ],
            'back_urls' => [
                'success' => $this->absoluteUrl('mp/return?collection_status=approved'),
                'pending' => $this->absoluteUrl('mp/return?collection_status=pending'),
                'failure' => $this->absoluteUrl('mp/return?collection_status=failure'),
            ],
            'auto_return' => 'approved',
            'notification_url' => $this->absoluteUrl('mp/webhook'),
        ];

        // Lógica de Split (Marketplace)
        if ($user && $user->academy_company_id) {
            $company = $user->academyCompany;
            if ($company && $company->mercadopago_user_id) {
                // Se a academia tem conta MP, o dinheiro vai para ela e a plataforma retém a taxa
                $fee = (float) $company->platform_fee_fixed;
                if ($company->platform_fee_percent > 0) {
                    $fee += ($price * ($company->platform_fee_percent / 100));
                }
                
                $payload['marketplace_fee'] = round($fee, 2);
                
                // Nota: Em produção, o Access Token usado deve ser o do Marketplace 
                // e o mercadopago_user_id deve ser passado se usarmos a API de Split direta.
                // Aqui estamos usando a modalidade de Marketplace Fee padrão da Preferência.
            }
        }

        $r = $this->apiRequest('POST', 'https://api.mercadopago.com/checkout/preferences', $accessToken, $payload);
        if (! $r['ok']) {
            return ['ok' => false, 'error' => $r['error']];
        }
        /** @var array<string, mixed> $d */
        $d = $r['data'];
        $init = isset($d['init_point']) ? (string) $d['init_point'] : '';
        if ($init === '') {
            return ['ok' => false, 'error' => 'Resposta sem init_point do Mercado Pago.'];
        }

        return ['ok' => true, 'init_point' => $init];
    }

    /**
     * @return array{ok: true, init_point: string}|array{ok: false, error: string}
     */
    public function createAiCreditsCheckoutPreference(string $accessToken, int $userId, int $packageId): array
    {
        $package = \App\Models\AiCreditPackage::find($packageId);
        if (!$package) {
            return ['ok' => false, 'error' => 'Pacote não encontrado.'];
        }

        $user = User::find($userId);
        if (!$user) {
            return ['ok' => false, 'error' => 'Usuário não encontrado.'];
        }

        if ($this->publicBase() === '') {
            return ['ok' => false, 'error' => 'Configure APP_PUBLIC_URL no .env para usar o checkout.'];
        }

        $payload = [
            'items' => [[
                'title' => "Pacote de Créditos IA — {$package->name}",
                'description' => "Adição de {$package->credits} créditos para uso de IA no ProjetoAcademia.",
                'category_id' => 'services',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => (float) $package->price,
            ]],
            'payer' => ['email' => $user->email],
            'external_reference' => "ai_credits:{$userId}:{$packageId}",
            'metadata' => [
                'user_id' => (string) $userId,
                'plan' => 'ai_credits',
                'package_id' => (string) $packageId,
            ],
            'back_urls' => [
                'success' => $this->absoluteUrl('mp/return?collection_status=approved'),
                'pending' => $this->absoluteUrl('mp/return?collection_status=pending'),
                'failure' => $this->absoluteUrl('mp/return?collection_status=failure'),
            ],
            'auto_return' => 'approved',
            'notification_url' => $this->absoluteUrl('mp/webhook'),
        ];

        $r = $this->apiRequest('POST', 'https://api.mercadopago.com/checkout/preferences', $accessToken, $payload);
        if (!$r['ok']) {
            return ['ok' => false, 'error' => $r['error']];
        }

        $d = $r['data'];
        $init = isset($d['init_point']) ? (string) $d['init_point'] : '';
        if ($init === '') {
            return ['ok' => false, 'error' => 'Resposta sem init_point do Mercado Pago.'];
        }

        return ['ok' => true, 'init_point' => $init];
    }

    /**
     * @param  array<string, mixed>  $payment
     */
    public function paymentExpectedAmount(string $plan, array $payment, ?Coupon $coupon = null): bool
    {
        $prices = $this->planPrices();
        $userId = $this->extractUserAndPlan($payment)['user_id'] ?? null;
        $user = $userId ? User::find($userId) : null;
        $isPatientOnly = $user && $user->hasRole('paciente') && !$user->hasRole('aluno');

        if (str_starts_with($plan, 'ai_credits:')) {
            $packageId = (int) str_replace('ai_credits:', '', $plan);
            $package = \App\Models\AiCreditPackage::find($packageId);
            $expected = $package ? (float) $package->price : 0.0;
        } elseif (str_starts_with($plan, 'credits:')) {
            $compraId = (int) str_replace('credits:', '', $plan);
            $compra = \App\Models\CreditoCompra::find($compraId);
            $expected = $compra ? (float) $compra->valor : 0.0;
        } else {
            $expected = $plan === 'yearly' ? $prices['yearly'] : ($isPatientOnly ? $prices['patient_monthly'] : $prices['monthly']);
        }
        
        if ($coupon) {
            $expected = $coupon->apply($expected);
        }
        $amt = isset($payment['transaction_amount']) ? (float) $payment['transaction_amount'] : 0.0;

        return abs($amt - $expected) < 0.02;
    }

    /**
     * @param  array<string, mixed>  $payment
     * @return array{user_id: int, plan: string}|null
     */
    public function extractUserAndPlan(array $payment): ?array
    {
        $meta = $payment['metadata'] ?? null;
        $userId = null;
        $plan = null;
        
        if (is_array($meta)) {
            if (isset($meta['user_id'])) {
                $userId = (int) $meta['user_id'];
            }
            if (isset($meta['plan']) && is_string($meta['plan'])) {
                $plan = $meta['plan'];
                if ($plan === 'ai_credits' && isset($meta['package_id'])) {
                    $plan = 'ai_credits:'.$meta['package_id'];
                }
            }
        }
        
        if (($userId === null || $userId < 1 || $plan === null) && ! empty($payment['external_reference'])) {
            $ref = (string) $payment['external_reference'];
            if (preg_match('/^pa:(\d+):(monthly|yearly)$/', $ref, $m)) {
                $userId = (int) $m[1];
                $plan = $m[2];
            } elseif (preg_match('/^pasub:(\d+):(monthly|yearly)$/', $ref, $m)) {
                $userId = (int) $m[1];
                $plan = $m[2];
            } elseif (preg_match('/^ai_credits:(\d+):(\d+)$/', $ref, $m)) {
                $userId = (int) $m[1];
                $plan = 'ai_credits:'.$m[2];
            } elseif (preg_match('/^credits:(\d+)$/', $ref, $m)) {
                $compraId = (int) $m[1];
                $compra = \App\Models\CreditoCompra::find($compraId);
                if ($compra) {
                    $userId = $compra->user_id;
                    $plan = "credits:{$compraId}";
                }
            }
        }
        
        if ($userId === null || $userId < 1 || ($plan !== 'monthly' && $plan !== 'yearly' && !str_starts_with($plan, 'ai_credits:') && !str_starts_with($plan, 'credits:'))) {
            return null;
        }

        return ['user_id' => $userId, 'plan' => $plan];
    }

    public function premiumExtendUser(int $userId, string $plan): ?Subscription
    {
        $user = User::find($userId);
        if (! $user) {
            Log::error('[MercadoPagoService] premiumExtendUser: usuário não encontrado.', ['user_id' => $userId]);
            return null;
        }

        $base = new DateTimeImmutable('now');
        if (! empty($user->premium_expires_at)) {
            try {
                $exp = new DateTimeImmutable((string) $user->premium_expires_at);
                if ($exp > $base) {
                    $base = $exp;
                }
            } catch (Exception) {
            }
        }

        $interval = $plan === 'yearly' ? new DateInterval('P1Y') : new DateInterval('P1M');
        $newExp   = $base->add($interval);

        // Usar Eloquent para respeitar casts, observers e updated_at
        $user->is_premium         = true;
        $user->premium_expires_at = $newExp->format('Y-m-d H:i:s');
        $user->save();

        // Atualizar ou criar Assinatura no novo formato
        $subscription = $this->updateSubscriptionStatus($userId, $plan, Subscription::STATUS_FIN_ATIVO);

        // Garantir que o usuário tenha o papel de "aluno"
        $alunoRole = Role::where('name', 'aluno')->first();
        if ($alunoRole) {
            $user->roles()->syncWithoutDetaching([$alunoRole->id]);
        }

        return $subscription;
    }

    /**
     * @param  array<string, mixed>  $payment
     * @return array{ok: bool, message: string}
     */
    public function tryCreditPremium(array $payment): array
    {
        $id = isset($payment['id']) ? (string) $payment['id'] : '';
        if ($id === '') {
            return ['ok' => false, 'message' => 'Sem id de pagamento.'];
        }
        $status = isset($payment['status']) ? (string) $payment['status'] : '';
        $cur = isset($payment['currency_id']) ? (string) $payment['currency_id'] : '';
        if ($cur !== '' && $cur !== 'BRL') {
            return ['ok' => false, 'message' => 'Moeda inesperada.'];
        }

        $parsed = $this->extractUserAndPlan($payment);
        
        // Mapeamento de status do Mercado Pago para o nosso sistema
        $mappedStatus = match($status) {
            'approved' => Subscription::STATUS_FIN_ATIVO,
            'pending', 'in_process' => Subscription::STATUS_FIN_AGUARDANDO,
            'rejected', 'cancelled' => Subscription::STATUS_FIN_RECUSADO,
            default => Subscription::STATUS_FIN_PENDENTE
        };

        if ($parsed === null) {
            return ['ok' => false, 'message' => 'Metadata/external_reference/preapproval inválidos.'];
        }

        $userId = $parsed['user_id'];
        $plan = $parsed['plan'];
        $amt = (float) $payment['transaction_amount'];
        
        // Extrair taxas do Split/Marketplace
        $feeAmount = (float) ($payment['marketplace_fee'] ?? 0);
        if ($feeAmount <= 0 && isset($payment['fee_details'])) {
            foreach ($payment['fee_details'] as $fee) {
                if ($fee['type'] === 'marketplace_fee' || $fee['type'] === 'application_fee') {
                    $feeAmount += (float) $fee['amount'];
                }
            }
        }
        $netAmount = $amt - $feeAmount;

        // Sempre registrar o pagamento (independente de estar aprovado ou não) para histórico
        Payment::updateOrCreate(
            ['gateway_id' => (string) $id],
            [
                'user_id' => $userId,
                'gateway' => 'mercadopago',
                'amount' => $amt,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'currency' => $cur !== '' ? $cur : 'BRL',
                'status' => $mappedStatus,
                'payload' => $payment,
            ]
        );

        // Se não estiver aprovado, apenas atualizamos o status da assinatura e retornamos
        if ($status !== 'approved') {
            $this->updateSubscriptionStatus($userId, $plan, $mappedStatus, $id);
            return ['ok' => true, 'message' => 'Status do pagamento atualizado para: '.$status];
        }
        $couponId = null;
        if ($parsed === null) {
            $preId = isset($payment['preapproval_id']) ? (string) $payment['preapproval_id'] : '';
            if ($preId !== '') {
                $subscription = MercadoPagoSubscription::find($preId);
                if ($subscription) {
                    $parsed = [
                        'user_id' => (int) $subscription->user_id,
                        'plan'    => (string) $subscription->plan_code,
                    ];
                    $couponId = $subscription->coupon_id;
                }
            }
        } else {
            $meta = $payment['metadata'] ?? null;
            if (is_array($meta) && isset($meta['coupon_id'])) {
                $couponId = (int) $meta['coupon_id'];
            }
        }

        if ($parsed === null) {
            return ['ok' => false, 'message' => 'Metadata/external_reference/preapproval inválidos.'];
        }
        $userId = $parsed['user_id'];
        $plan = $parsed['plan'];
        
        $coupon = $couponId ? Coupon::find($couponId) : null;
        
        if (! $this->paymentExpectedAmount($plan, $payment, $coupon)) {
            return ['ok' => false, 'message' => 'Valor não confere com o plano (ou cupom).'];
        }

        $mpId = (int) $payment['id'];
        $amt = (float) $payment['transaction_amount'];

        return DB::transaction(function () use ($mpId, $userId, $plan, $amt, $cur, $couponId, $id) {
            $exists = DB::table('mercadopago_payment_credits')->where('mp_payment_id', $mpId)->exists();
            if ($exists) {
                return ['ok' => true, 'message' => 'Pagamento já creditado.'];
            }
            DB::table('mercadopago_payment_credits')->insert([
                'mp_payment_id' => $mpId,
                'user_id' => $userId,
                'plan_code' => (str_starts_with($plan, 'ai_credits:') || str_starts_with($plan, 'credits:')) ? 'credits' : $plan,
                'transaction_amount' => $amt,
                'currency_id' => $cur !== '' ? $cur : 'BRL',
                'coupon_id' => $couponId,
            ]);

            if ($couponId) {
                $coupon = Coupon::find($couponId);
                if ($coupon) {
                    $coupon->markAsUsed($userId);
                }
            }

            // Registrar na nova tabela de pagamentos para auditoria
            $paymentModel = Payment::create([
                'user_id' => $userId,
                'gateway' => 'mercadopago',
                'gateway_id' => (string) $id,
                'amount' => $amt,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'currency' => $cur !== '' ? $cur : 'BRL',
                'status' => Subscription::STATUS_FIN_ATIVO,
                'payload' => $payment,
            ]);

            $user = User::find($userId);
            $subscriptionModel = null;

            if (str_starts_with($plan, 'ai_credits:')) {
                $packageId = (int) str_replace('ai_credits:', '', $plan);
                $package = \App\Models\AiCreditPackage::find($packageId);
                if ($package && $user) {
                    app(\App\Services\AiCreditService::class)->addCredits($user, $package->credits, 'purchase', [
                        'package_id' => $packageId,
                        'mp_payment_id' => $mpId
                    ]);
                }
            } elseif (str_starts_with($plan, 'credits:')) {
                $compraId = (int) str_replace('credits:', '', $plan);
                $compra = \App\Models\CreditoCompra::find($compraId);
                if ($compra && $compra->status === 'PENDENTE' && $user) {
                    $compra->update(['status' => 'PAGO', 'gateway_id' => (string) $id]);
                    $user->increment('creditos', $compra->quantidade);
                    
                    // Manter compatibilidade com ai_credits se a coluna existir
                    if (\Schema::hasColumn('users', 'ai_credits')) {
                        $user->increment('ai_credits', $compra->quantidade);
                    }
                }
            } else {
                $subscriptionModel = $this->premiumExtendUser($userId, $plan);
            }

            // Processar comissão se houver representante
            if ($user) {
                $this->processRepresentativeCommission($user, $paymentModel, $subscriptionModel);
            }

            FinancialLogService::log([
                'user_id' => $userId,
                'action' => str_starts_with($plan, 'ai_credits:') ? 'AI_CREDITS_PURCHASED' : 'PAYMENT_RECEIVED',
                'amount' => $amt,
                'transaction_id' => $id,
                'origin' => 'mercadopago',
                'payload' => ['payment_id' => $id, 'plan' => $plan]
            ]);

            return ['ok' => true, 'message' => 'Crédito aplicado com sucesso.'];
        });
    }

    /**
     * Processa a comissão do representante, se existir.
     */
    private function processRepresentativeCommission(User $user, Payment $payment, ?Subscription $subscription = null): void
    {
        $representativeId = $user->representative_id;
        if (!$representativeId) {
            return;
        }

        // Determinar a taxa de comissão
        $rate = 0.00;
        if ($subscription && $subscription->plan) {
            $rate = $subscription->plan->commission_rate;
        }

        // Se a taxa for 0, podemos ter uma taxa global padrão (ex: 10%)
        if ($rate <= 0) {
            $rate = (float) config('projeto.default_commission_rate', 10.00);
        }

        if ($rate <= 0) {
            return;
        }

        $commissionAmount = ($payment->amount * $rate) / 100;

        Commission::create([
            'representative_id' => $representativeId,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'subscription_id' => $subscription?->id,
            'base_amount' => $payment->amount,
            'commission_rate' => $rate,
            'commission_amount' => $commissionAmount,
            'status' => Commission::STATUS_PENDENTE,
            'available_at' => now()->addDays(7), // Janela de segurança de 7 dias (garantia)
            'notes' => 'Comissão gerada automaticamente via pagamento ' . $payment->gateway_id,
        ]);

        Log::info('[Representative] Comissão gerada.', [
            'representative_id' => $representativeId,
            'user_id' => $user->id,
            'amount' => $commissionAmount
        ]);
    }

    /**
     * Atualiza ou cria o registro de assinatura com o status correto.
     */
    public function updateSubscriptionStatus(int $userId, string $planCode, string $status, ?string $gatewayId = null): Subscription
    {
        $plan = \App\Models\Plan::where('name', $planCode)->first();
        if (!$plan) {
            // Tentar encontrar por slug ou outro campo se necessário
            $plan = \App\Models\Plan::where('type', $planCode === 'yearly' ? 'student' : 'student')->first(); 
        }

        return Subscription::updateOrCreate(
            ['user_id' => $userId, 'status' => $status !== Subscription::STATUS_FIN_CANCELADO ? $status : 'any'], // Simplificação
            [
                'plan_id' => $plan ? $plan->id : 1,
                'status' => $status,
                'gateway_id' => $gatewayId,
                'gateway_type' => 'mercadopago',
                'start_date' => now(),
                'end_date' => $planCode === 'yearly' ? now()->addYear() : now()->addMonth(),
            ]
        );
    }

    public function iso8601Mp(DateTimeImmutable $dt): string
    {
        return $dt->format('Y-m-d\TH:i:s').'.000'.$dt->format('P');
    }

    /**
     * @return array{ok: true, init_point: string}|array{ok: false, error: string}
     */
    public function createPreapprovalSubscription(
        string $accessToken,
        int $userId,
        string $payerEmail,
        string $plan,
        ?Coupon $coupon = null
    ): array {
        if (! in_array($plan, ['monthly', 'yearly'], true)) {
            return ['ok' => false, 'error' => 'Plano inválido.'];
        }
        if ($this->publicBase() === '') {
            return ['ok' => false, 'error' => 'Configure APP_PUBLIC_URL para usar o checkout.'];
        }

        $prices = $this->planPrices();
        $user = User::find($userId);
        $isPatientOnly = $user && $user->hasRole('paciente') && !$user->hasRole('aluno');

        $amount = $plan === 'yearly' ? $prices['yearly'] : ($isPatientOnly ? $prices['patient_monthly'] : $prices['monthly']);
        
        if ($coupon) {
            $amount = $coupon->apply($amount);
        }

        $reason = $plan === 'yearly'
            ? 'ProjetoAcademia Premium — assinatura anual'
            : 'ProjetoAcademia Premium — assinatura mensal';

        $tz = new DateTimeZone('America/Sao_Paulo');
        $start = (new DateTimeImmutable('now', $tz))->modify('+20 minutes');
        $end = $start->modify('+5 years');

        $payload = [
            'reason' => $reason,
            'external_reference' => 'pasub:'.$userId.':'.$plan,
            'payer_email' => $payerEmail,
            'auto_recurring' => [
                'frequency' => $plan === 'yearly' ? 12 : 1,
                'frequency_type' => 'months',
                'transaction_amount' => $amount,
                'currency_id' => 'BRL',
                'start_date' => $this->iso8601Mp($start),
                'end_date' => $this->iso8601Mp($end),
            ],
            'back_url' => $this->absoluteUrl('mp/sub-return'),
            'notification_url' => $this->absoluteUrl('mp/webhook'),
            'metadata' => [
                'user_id' => (string) $userId,
                'plan' => $plan,
                'coupon_id' => $coupon ? (string) $coupon->id : null,
            ],
            'status' => 'pending',
        ];

        $r = $this->apiRequest('POST', 'https://api.mercadopago.com/preapproval', $accessToken, $payload);
        if (! $r['ok']) {
            return ['ok' => false, 'error' => $r['error']];
        }
        if (! is_array($r['data'])) {
            return ['ok' => false, 'error' => 'Resposta inválida do Mercado Pago.'];
        }
        /** @var array<string, mixed> $d */
        $d = $r['data'];
        $preId = isset($d['id']) ? (string) $d['id'] : '';
        $init = isset($d['init_point']) ? (string) $d['init_point'] : '';
        if ($preId === '' || $init === '') {
            return ['ok' => false, 'error' => 'Resposta sem id or init_point do preapproval.'];
        }

        try {
            DB::table('mercadopago_subscriptions')->insert([
                'mp_preapproval_id' => $preId,
                'user_id' => $userId,
                'plan_code' => $plan,
                'status' => 'pending',
                'coupon_id' => $coupon ? $coupon->id : null,
            ]);
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Não foi possível registrar a assinatura: '.$e->getMessage()];
        }

        return ['ok' => true, 'init_point' => $init];
    }

    /**
     * @return array{ok: true, data: array<string, mixed>}|array{ok: false, error: string}
     */
    public function fetchPreapproval(string $preapprovalId): array
    {
        $url = 'https://api.mercadopago.com/preapproval/'.rawurlencode($preapprovalId);
        $r = $this->apiRequest('GET', $url, $this->token, null);
        if (! $r['ok']) {
            return ['ok' => false, 'error' => $r['error']];
        }
        if (! is_array($r['data'])) {
            return ['ok' => false, 'error' => 'Preapproval inválido.'];
        }

        /** @var array<string, mixed> $data */
        $data = $r['data'];

        return ['ok' => true, 'data' => $data];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function syncPreapprovalWebhook(string $preapprovalId): array
    {
        $f = $this->fetchPreapproval($preapprovalId);
        if (! $f['ok']) {
            return ['ok' => false, 'message' => $f['error']];
        }
        /** @var array<string, mixed> $data */
        $data = $f['data'];
        $status = isset($data['status']) ? strtolower((string) $data['status']) : '';

        $subscription = MercadoPagoSubscription::find($preapprovalId);
        if (! $subscription) {
            return ['ok' => true, 'message' => 'Preapproval não registrado localmente (ignorado).'];
        }
        $userId = (int) $subscription->user_id;

        if ($status === 'authorized') {
            $subscription->update(['status' => 'authorized']);

            return ['ok' => true, 'message' => 'Assinatura autorizada.'];
        }

        if (in_array($status, ['cancelled', 'canceled'], true)) {
            $subscription->update(['status' => 'cancelled']);

            // Usar Eloquent para respeitar casts, observers e updated_at
            $user = User::find($userId);
            if ($user) {
                $user->is_premium         = false;
                $user->premium_expires_at = null;
                $user->save();
            } else {
                Log::warning('[MercadoPagoService] syncPreapprovalWebhook: usuário não encontrado ao revogar premium.', ['user_id' => $userId]);
            }

            return ['ok' => true, 'message' => 'Assinatura cancelada; Premium revogado.'];
        }

        if ($status === 'paused') {
            $subscription->update(['status' => 'paused']);

            return ['ok' => true, 'message' => 'Assinatura pausada.'];
        }

        return ['ok' => true, 'message' => 'Status: '.$status];
    }

    /**
     * Interface Implementations
     */

    public function createCheckout(User $user, float $amount, array $options = []): array
    {
        $payload = [
            'items' => [[
                'title' => $options['title'] ?? 'Pagamento avulso',
                'description' => $options['description'] ?? 'Transação segura via ' . $this->getIdentifier(),
                'category_id' => 'services',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => (float) $amount,
            ]],
            'payer' => ['email' => $user->email],
            'external_reference' => $options['external_reference'] ?? null,
            'back_urls' => [
                'success' => $options['success_url'] ?? $this->absoluteUrl('mp/return?collection_status=approved'),
                'pending' => $options['pending_url'] ?? $this->absoluteUrl('mp/return?collection_status=pending'),
                'failure' => $options['failure_url'] ?? $this->absoluteUrl('mp/return?collection_status=failure'),
            ],
            'auto_return' => 'approved',
            'notification_url' => $this->absoluteUrl('payment/webhook/' . $this->getIdentifier()),
        ];

        return $this->apiRequest('POST', 'https://api.mercadopago.com/checkout/preferences', $this->token, $payload);
    }

    public function createSubscription(User $user, $plan, array $options = []): array
    {
        return $this->createCheckoutPreference($this->token, $user->id, $user->email, $plan, $options['coupon'] ?? null);
    }

    public function cancelSubscription($gatewaySubscriptionId): bool
    {
        $res = $this->apiRequest('PUT', "https://api.mercadopago.com/preapproval/{$gatewaySubscriptionId}", $this->token, [
            'status' => 'cancelled'
        ]);
        return $res['ok'];
    }

    public function fetchPayment(string $paymentId): array
    {
        $res = $this->apiRequest('GET', "https://api.mercadopago.com/v1/payments/{$paymentId}", $this->token, null);
        if (!$res['ok']) return ['ok' => false, 'error' => $res['error']];
        return ['ok' => true, 'payment' => $res['data']];
    }

    public function handleWebhook(Request $request): array
    {
        $start = microtime(true);
        
        if (!$this->validateSignature($request)) {
            $this->logWebhook($request, 401, 'Invalid Signature');
            return ['ok' => false, 'status' => 401, 'message' => 'Invalid Signature'];
        }

        $paymentId = null;
        $preapprovalId = null;
        $payload = $request->all();

        if (isset($payload['type'])) {
            if ($payload['type'] === 'payment') $paymentId = $payload['data']['id'] ?? null;
            if ($payload['type'] === 'subscription_preapproval') $preapprovalId = $payload['data']['id'] ?? null;
        }

        try {
            if ($preapprovalId) {
                $res = $this->syncPreapprovalWebhook($preapprovalId);
                $this->logWebhook($request, 200, $res['message'], null, microtime(true) - $start);
                return ['ok' => true, 'status' => 200];
            }

            if ($paymentId) {
                $payRes = $this->fetchPayment($paymentId);
                if ($payRes['ok']) {
                    $res = $this->tryCreditPremium($payRes['payment']);
                    $this->logWebhook($request, 200, $res['message'], null, microtime(true) - $start);
                    return ['ok' => true, 'status' => 200];
                }
            }

            $this->logWebhook($request, 200, 'Ignored (Unknown type or no ID)', null, microtime(true) - $start);
            return ['ok' => true, 'status' => 200];
        } catch (\Exception $e) {
            $this->logWebhook($request, 500, 'Error', $e->getMessage(), microtime(true) - $start);
            return ['ok' => false, 'status' => 500, 'message' => $e->getMessage()];
        }
    }

    public function validateSignature(Request $request): bool
    {
        if (empty($this->webhookSecret)) return true;

        $xSignature = (string) $request->header('x-signature', '');
        $xRequestId = (string) $request->header('x-request-id', '');

        if ($xSignature === '') return false;

        $ts = ''; $v1 = '';
        foreach (explode(';', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            if (trim($key) === 'ts') $ts = trim($value);
            if (trim($key) === 'v1') $v1 = trim($value);
        }

        if ($ts === '' || $v1 === '') return false;

        $dataId = $request->input('data.id') ?? $request->input('id') ?? '';
        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts}";
        $expected = hash_hmac('sha256', $manifest, $this->webhookSecret);

        return hash_equals($expected, $v1);
    }

    public function refund(string $paymentId, ?float $amount = null): bool
    {
        $payload = $amount ? ['amount' => $amount] : null;
        $res = $this->apiRequest('POST', "https://api.mercadopago.com/v1/payments/{$paymentId}/refunds", $this->token, $payload);
        return $res['ok'];
    }
}
