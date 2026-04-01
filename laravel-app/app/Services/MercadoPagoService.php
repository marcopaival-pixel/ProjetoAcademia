<?php

namespace App\Services;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
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
     * @return array{monthly: float, yearly: float}
     */
    public function planPrices(): array
    {
        return ['monthly' => 19.9, 'yearly' => 149.9];
    }

    /**
     * @return array{ok: true, init_point: string}|array{ok: false, error: string}
     */
    public function createCheckoutPreference(string $accessToken, int $userId, string $payerEmail, string $plan): array
    {
        $prices = $this->planPrices();
        if ($plan === 'monthly') {
            $title = 'ProjetoAcademia Premium — mensal';
            $price = $prices['monthly'];
            $planCode = 'monthly';
        } elseif ($plan === 'yearly') {
            $title = 'ProjetoAcademia Premium — anual';
            $price = $prices['yearly'];
            $planCode = 'yearly';
        } else {
            return ['ok' => false, 'error' => 'Plano inválido.'];
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
     * @return array{ok: true, payment: array<string, mixed>}|array{ok: false, error: string}
     */
    public function fetchPayment(string $accessToken, string $paymentId): array
    {
        $url = 'https://api.mercadopago.com/v1/payments/'.rawurlencode($paymentId);
        $r = $this->apiRequest('GET', $url, $accessToken, null);
        if (! $r['ok']) {
            return ['ok' => false, 'error' => $r['error']];
        }
        if (! is_array($r['data'])) {
            return ['ok' => false, 'error' => 'Pagamento inválido.'];
        }

        /** @var array<string, mixed> $pay */
        $pay = $r['data'];

        return ['ok' => true, 'payment' => $pay];
    }

    /**
     * @param  array<string, mixed>  $payment
     */
    public function paymentExpectedAmount(string $plan, array $payment): bool
    {
        $prices = $this->planPrices();
        $expected = $plan === 'yearly' ? $prices['yearly'] : $prices['monthly'];
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
            }
        }
        if ($userId === null || $userId < 1 || ! in_array($plan, ['monthly', 'yearly'], true)) {
            return null;
        }

        return ['user_id' => $userId, 'plan' => $plan];
    }

    public function premiumExtendUser(int $userId, string $plan): void
    {
        $row = DB::table('users')->where('id', $userId)->first();
        $base = new DateTimeImmutable('now');
        if ($row && ! empty($row->premium_expires_at)) {
            try {
                $exp = new DateTimeImmutable((string) $row->premium_expires_at);
                if ($exp > $base) {
                    $base = $exp;
                }
            } catch (Exception) {
            }
        }
        $interval = $plan === 'yearly' ? new DateInterval('P1Y') : new DateInterval('P1M');
        $newExp = $base->add($interval);
        DB::table('users')->where('id', $userId)->update([
            'is_premium' => 1,
            'premium_expires_at' => $newExp->format('Y-m-d H:i:s'),
        ]);
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
        if ($status !== 'approved') {
            return ['ok' => false, 'message' => 'Status não aprovado: '.$status];
        }
        $cur = isset($payment['currency_id']) ? (string) $payment['currency_id'] : '';
        if ($cur !== '' && $cur !== 'BRL') {
            return ['ok' => false, 'message' => 'Moeda inesperada.'];
        }

        $parsed = $this->extractUserAndPlan($payment);
        if ($parsed === null) {
            $preId = isset($payment['preapproval_id']) ? (string) $payment['preapproval_id'] : '';
            if ($preId !== '') {
                $subRow = DB::table('mercadopago_subscriptions')
                    ->where('mp_preapproval_id', $preId)
                    ->first();
                if ($subRow) {
                    $parsed = [
                        'user_id' => (int) $subRow->user_id,
                        'plan' => (string) $subRow->plan_code,
                    ];
                }
            }
        }
        if ($parsed === null) {
            return ['ok' => false, 'message' => 'Metadata/external_reference/preapproval inválidos.'];
        }
        $userId = $parsed['user_id'];
        $plan = $parsed['plan'];
        if (! $this->paymentExpectedAmount($plan, $payment)) {
            return ['ok' => false, 'message' => 'Valor não confere com o plano.'];
        }

        $mpId = (int) $payment['id'];
        $amt = (float) $payment['transaction_amount'];

        return DB::transaction(function () use ($mpId, $userId, $plan, $amt, $cur) {
            $exists = DB::table('mercadopago_payment_credits')->where('mp_payment_id', $mpId)->exists();
            if ($exists) {
                return ['ok' => true, 'message' => 'Pagamento já creditado.'];
            }
            DB::table('mercadopago_payment_credits')->insert([
                'mp_payment_id' => $mpId,
                'user_id' => $userId,
                'plan_code' => $plan,
                'transaction_amount' => $amt,
                'currency_id' => $cur !== '' ? $cur : 'BRL',
            ]);
            $this->premiumExtendUser($userId, $plan);

            return ['ok' => true, 'message' => 'Premium ativado.'];
        });
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
    ): array {
        if (! in_array($plan, ['monthly', 'yearly'], true)) {
            return ['ok' => false, 'error' => 'Plano inválido.'];
        }
        if ($this->publicBase() === '') {
            return ['ok' => false, 'error' => 'Configure APP_PUBLIC_URL para usar o checkout.'];
        }

        $prices = $this->planPrices();
        $amount = $plan === 'yearly' ? $prices['yearly'] : $prices['monthly'];
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
            return ['ok' => false, 'error' => 'Resposta sem id ou init_point do preapproval.'];
        }

        try {
            DB::table('mercadopago_subscriptions')->insert([
                'mp_preapproval_id' => $preId,
                'user_id' => $userId,
                'plan_code' => $plan,
                'status' => 'pending',
            ]);
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Não foi possível registrar a assinatura: '.$e->getMessage()];
        }

        return ['ok' => true, 'init_point' => $init];
    }

    /**
     * @return array{ok: true, data: array<string, mixed>}|array{ok: false, error: string}
     */
    public function fetchPreapproval(string $accessToken, string $preapprovalId): array
    {
        $url = 'https://api.mercadopago.com/preapproval/'.rawurlencode($preapprovalId);
        $r = $this->apiRequest('GET', $url, $accessToken, null);
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
    public function syncPreapprovalWebhook(string $accessToken, string $preapprovalId): array
    {
        $f = $this->fetchPreapproval($accessToken, $preapprovalId);
        if (! $f['ok']) {
            return ['ok' => false, 'message' => $f['error']];
        }
        /** @var array<string, mixed> $data */
        $data = $f['data'];
        $status = isset($data['status']) ? strtolower((string) $data['status']) : '';

        $row = DB::table('mercadopago_subscriptions')
            ->where('mp_preapproval_id', $preapprovalId)
            ->first();
        if (! $row) {
            return ['ok' => true, 'message' => 'Preapproval não registrado localmente (ignorado).'];
        }
        $userId = (int) $row->user_id;

        if ($status === 'authorized') {
            DB::table('mercadopago_subscriptions')
                ->where('mp_preapproval_id', $preapprovalId)
                ->update(['status' => 'authorized']);

            return ['ok' => true, 'message' => 'Assinatura autorizada.'];
        }

        if (in_array($status, ['cancelled', 'canceled'], true)) {
            DB::table('mercadopago_subscriptions')
                ->where('mp_preapproval_id', $preapprovalId)
                ->update(['status' => 'cancelled']);
            DB::table('users')->where('id', $userId)->update([
                'is_premium' => 0,
                'premium_expires_at' => null,
            ]);

            return ['ok' => true, 'message' => 'Assinatura cancelada; Premium revogado.'];
        }

        if ($status === 'paused') {
            DB::table('mercadopago_subscriptions')
                ->where('mp_preapproval_id', $preapprovalId)
                ->update(['status' => 'paused']);

            return ['ok' => true, 'message' => 'Assinatura pausada.'];
        }

        return ['ok' => true, 'message' => 'Status: '.$status];
    }
}
