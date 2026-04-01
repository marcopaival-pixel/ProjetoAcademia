<?php
declare(strict_types=1);

/** @param array<string, mixed> $config */
function mp_public_base(array $config): string
{
    $u = (string) ($config['app']['public_url'] ?? '');
    $bp = base_path($config);
    return $u . ($bp !== '' ? $bp : '');
}

/** @param array<string, mixed> $config */
function mp_absolute_url(string $path, array $config): string
{
    $base = rtrim(mp_public_base($config), '/');
    $p = '/'/* leading */ . ltrim($path, '/');
    return $base === '' ? $p : $base . $p;
}

/**
 * @param array<string, mixed>|null $body
 * @return array{ok: true, data: mixed}|array{ok: false, error: string, status: int}
 */
function mp_api_request(string $method, string $url, string $accessToken, ?array $body): array
{
    $ch = curl_init($url);
    if ($ch === false) {
        return ['ok' => false, 'error' => 'curl_init', 'status' => 0];
    }
    $headers = [
        'Accept: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 45,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
    $resp = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'error' => $err ?: 'curl_exec', 'status' => $status];
    }
    curl_close($ch);
    try {
        $decoded = json_decode($resp, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        return ['ok' => false, 'error' => 'JSON inválido na resposta MP', 'status' => $status];
    }
    if ($status >= 400) {
        $msg = is_array($decoded) && isset($decoded['message'])
            ? (string) $decoded['message']
            : 'HTTP ' . $status;
        return ['ok' => false, 'error' => $msg, 'status' => $status];
    }
    return ['ok' => true, 'data' => $decoded];
}

/** @return array{monthly: float, yearly: float} */
function mp_plan_prices(): array
{
    return ['monthly' => 19.9, 'yearly' => 149.9];
}

/**
 * @param array<string, mixed> $config
 * @return array{ok: true, init_point: string}|array{ok: false, error: string}
 */
function mp_create_checkout_preference(
    array $config,
    string $accessToken,
    int $userId,
    string $payerEmail,
    string $plan
): array {
    $prices = mp_plan_prices();
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

    $pub = mp_public_base($config);
    if ($pub === '') {
        return ['ok' => false, 'error' => 'Configure APP_PUBLIC_URL no .env.php para usar o checkout.'];
    }

    $backSuccess = mp_absolute_url('mp_return.php?collection_status=approved', $config);
    $backPending = mp_absolute_url('mp_return.php?collection_status=pending', $config);
    $backFailure = mp_absolute_url('mp_return.php?collection_status=failure', $config);
    $notify = mp_absolute_url('mp_webhook.php', $config);

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
        'external_reference' => 'pa:' . $userId . ':' . $planCode,
        'metadata' => [
            'user_id' => (string) $userId,
            'plan' => $planCode,
        ],
        'back_urls' => [
            'success' => $backSuccess,
            'pending' => $backPending,
            'failure' => $backFailure,
        ],
        'auto_return' => 'approved',
        'notification_url' => $notify,
    ];

    $r = mp_api_request('POST', 'https://api.mercadopago.com/checkout/preferences', $accessToken, $payload);
    if (!$r['ok']) {
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
 * @param array<string, mixed> $config
 * @return array{ok: true, payment: array<string, mixed>}|array{ok: false, error: string}
 */
function mp_fetch_payment(array $config, string $accessToken, string $paymentId): array
{
    $url = 'https://api.mercadopago.com/v1/payments/' . rawurlencode($paymentId);
    $r = mp_api_request('GET', $url, $accessToken, null);
    if (!$r['ok']) {
        return ['ok' => false, 'error' => $r['error']];
    }
    if (!is_array($r['data'])) {
        return ['ok' => false, 'error' => 'Pagamento inválido.'];
    }
    /** @var array<string, mixed> $pay */
    $pay = $r['data'];
    return ['ok' => true, 'payment' => $pay];
}

/**
 * @param array<string, mixed> $payment
 */
function mp_payment_expected_amount(string $plan, array $payment): bool
{
    $prices = mp_plan_prices();
    $expected = $plan === 'yearly' ? $prices['yearly'] : $prices['monthly'];
    $amt = isset($payment['transaction_amount']) ? (float) $payment['transaction_amount'] : 0.0;
    return abs($amt - $expected) < 0.02;
}

/**
 * @param array<string, mixed> $payment
 */
function mp_extract_user_and_plan(array $payment): ?array
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
    if (($userId === null || $userId < 1 || $plan === null) && !empty($payment['external_reference'])) {
        $ref = (string) $payment['external_reference'];
        if (preg_match('/^pa:(\d+):(monthly|yearly)$/', $ref, $m)) {
            $userId = (int) $m[1];
            $plan = $m[2];
        } elseif (preg_match('/^pasub:(\d+):(monthly|yearly)$/', $ref, $m)) {
            $userId = (int) $m[1];
            $plan = $m[2];
        }
    }
    if ($userId === null || $userId < 1 || !in_array($plan, ['monthly', 'yearly'], true)) {
        return null;
    }
    return ['user_id' => $userId, 'plan' => $plan];
}

function premium_extend_user(PDO $pdo, int $userId, string $plan): void
{
    $st = $pdo->prepare('SELECT premium_expires_at FROM users WHERE id = ? LIMIT 1');
    $st->execute([$userId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    $base = new DateTimeImmutable('now');
    if ($row && !empty($row['premium_expires_at'])) {
        try {
            $exp = new DateTimeImmutable((string) $row['premium_expires_at']);
            if ($exp > $base) {
                $base = $exp;
            }
        } catch (Exception $e) {
        }
    }
    $interval = $plan === 'yearly' ? new DateInterval('P1Y') : new DateInterval('P1M');
    $newExp = $base->add($interval);
    $upd = $pdo->prepare('UPDATE users SET is_premium = 1, premium_expires_at = ? WHERE id = ?');
    $upd->execute([$newExp->format('Y-m-d H:i:s'), $userId]);
}

/**
 * Processa um pagamento aprovado (idempotente).
 *
 * @param array<string, mixed> $payment
 * @return array{ok: bool, message: string}
 */
function mp_try_credit_premium(PDO $pdo, array $payment): array
{
    $id = isset($payment['id']) ? (string) $payment['id'] : '';
    if ($id === '') {
        return ['ok' => false, 'message' => 'Sem id de pagamento.'];
    }
    $status = isset($payment['status']) ? (string) $payment['status'] : '';
    if ($status !== 'approved') {
        return ['ok' => false, 'message' => 'Status não aprovado: ' . $status];
    }
    $cur = isset($payment['currency_id']) ? (string) $payment['currency_id'] : '';
    if ($cur !== '' && $cur !== 'BRL') {
        return ['ok' => false, 'message' => 'Moeda inesperada.'];
    }

    $parsed = mp_extract_user_and_plan($payment);
    if ($parsed === null) {
        $preId = isset($payment['preapproval_id']) ? (string) $payment['preapproval_id'] : '';
        if ($preId !== '') {
            $lst = $pdo->prepare(
                'SELECT user_id, plan_code FROM mercadopago_subscriptions WHERE mp_preapproval_id = ? LIMIT 1'
            );
            $lst->execute([$preId]);
            $subRow = $lst->fetch(PDO::FETCH_ASSOC);
            if ($subRow) {
                $parsed = [
                    'user_id' => (int) $subRow['user_id'],
                    'plan' => (string) $subRow['plan_code'],
                ];
            }
        }
    }
    if ($parsed === null) {
        return ['ok' => false, 'message' => 'Metadata/external_reference/preapproval inválidos.'];
    }
    $userId = $parsed['user_id'];
    $plan = $parsed['plan'];
    if (!mp_payment_expected_amount($plan, $payment)) {
        return ['ok' => false, 'message' => 'Valor não confere com o plano.'];
    }

    $mpId = (int) $payment['id'];
    $amt = (float) $payment['transaction_amount'];
    $pdo->beginTransaction();
    try {
        $chk = $pdo->prepare('SELECT 1 FROM mercadopago_payment_credits WHERE mp_payment_id = ?');
        $chk->execute([$mpId]);
        if ($chk->fetch()) {
            $pdo->rollBack();
            return ['ok' => true, 'message' => 'Pagamento já creditado.'];
        }
        $ins = $pdo->prepare(
            'INSERT INTO mercadopago_payment_credits (mp_payment_id, user_id, plan_code, transaction_amount, currency_id)
             VALUES (?,?,?,?,?)'
        );
        $ins->execute([$mpId, $userId, $plan, $amt, $cur !== '' ? $cur : 'BRL']);
        premium_extend_user($pdo, $userId, $plan);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        return ['ok' => false, 'message' => 'Erro ao gravar: ' . $e->getMessage()];
    }
    return ['ok' => true, 'message' => 'Premium ativado.'];
}

function mp_iso8601_mp(DateTimeImmutable $dt): string
{
    return $dt->format('Y-m-d\TH:i:s') . '.000' . $dt->format('P');
}

/**
 * @param array<string, mixed> $config
 * @return array{ok: true, init_point: string}|array{ok: false, error: string}
 */
function mp_create_preapproval_subscription(
    PDO $pdo,
    array $config,
    string $accessToken,
    int $userId,
    string $payerEmail,
    string $plan
): array {
    if (!in_array($plan, ['monthly', 'yearly'], true)) {
        return ['ok' => false, 'error' => 'Plano inválido.'];
    }
    $pub = mp_public_base($config);
    if ($pub === '') {
        return ['ok' => false, 'error' => 'Configure APP_PUBLIC_URL no .env.php para usar o checkout.'];
    }

    $prices = mp_plan_prices();
    $amount = $plan === 'yearly' ? $prices['yearly'] : $prices['monthly'];
    $reason = $plan === 'yearly'
        ? 'ProjetoAcademia Premium — assinatura anual'
        : 'ProjetoAcademia Premium — assinatura mensal';

    $tz = new DateTimeZone('America/Sao_Paulo');
    $start = (new DateTimeImmutable('now', $tz))->modify('+20 minutes');
    $end = $start->modify('+5 years');

    $payload = [
        'reason' => $reason,
        'external_reference' => 'pasub:' . $userId . ':' . $plan,
        'payer_email' => $payerEmail,
        'auto_recurring' => [
            'frequency' => $plan === 'yearly' ? 12 : 1,
            'frequency_type' => 'months',
            'transaction_amount' => $amount,
            'currency_id' => 'BRL',
            'start_date' => mp_iso8601_mp($start),
            'end_date' => mp_iso8601_mp($end),
        ],
        'back_url' => mp_absolute_url('mp_sub_return.php', $config),
        'notification_url' => mp_absolute_url('mp_webhook.php', $config),
        'metadata' => [
            'user_id' => (string) $userId,
            'plan' => $plan,
        ],
        'status' => 'pending',
    ];

    $r = mp_api_request('POST', 'https://api.mercadopago.com/preapproval', $accessToken, $payload);
    if (!$r['ok']) {
        return ['ok' => false, 'error' => $r['error']];
    }
    if (!is_array($r['data'])) {
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
        $ins = $pdo->prepare(
            'INSERT INTO mercadopago_subscriptions (mp_preapproval_id, user_id, plan_code, status) VALUES (?,?,?,?)'
        );
        $ins->execute([$preId, $userId, $plan, 'pending']);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'Não foi possível registrar a assinatura: ' . $e->getMessage()];
    }

    return ['ok' => true, 'init_point' => $init];
}

/**
 * @return array{ok: true, data: array<string, mixed>}|array{ok: false, error: string}
 */
function mp_fetch_preapproval(string $accessToken, string $preapprovalId): array
{
    $url = 'https://api.mercadopago.com/preapproval/' . rawurlencode($preapprovalId);
    $r = mp_api_request('GET', $url, $accessToken, null);
    if (!$r['ok']) {
        return ['ok' => false, 'error' => $r['error']];
    }
    if (!is_array($r['data'])) {
        return ['ok' => false, 'error' => 'Preapproval inválido.'];
    }
    /** @var array<string, mixed> $data */
    $data = $r['data'];
    return ['ok' => true, 'data' => $data];
}

/**
 * Sincroniza estado da assinatura MP com o banco (webhook preapproval).
 *
 * @return array{ok: bool, message: string}
 */
function mp_sync_preapproval_webhook(PDO $pdo, string $accessToken, string $preapprovalId): array
{
    $f = mp_fetch_preapproval($accessToken, $preapprovalId);
    if (!$f['ok']) {
        return ['ok' => false, 'message' => $f['error']];
    }
    /** @var array<string, mixed> $data */
    $data = $f['data'];
    $status = isset($data['status']) ? strtolower((string) $data['status']) : '';

    $q = $pdo->prepare('SELECT user_id FROM mercadopago_subscriptions WHERE mp_preapproval_id = ? LIMIT 1');
    $q->execute([$preapprovalId]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return ['ok' => true, 'message' => 'Preapproval não registrado localmente (ignorado).'];
    }
    $userId = (int) $row['user_id'];

    if ($status === 'authorized') {
        $pdo->prepare('UPDATE mercadopago_subscriptions SET status = ? WHERE mp_preapproval_id = ?')
            ->execute(['authorized', $preapprovalId]);
        return ['ok' => true, 'message' => 'Assinatura autorizada.'];
    }

    if (in_array($status, ['cancelled', 'canceled'], true)) {
        $pdo->prepare('UPDATE mercadopago_subscriptions SET status = ? WHERE mp_preapproval_id = ?')
            ->execute(['cancelled', $preapprovalId]);
        $pdo->prepare('UPDATE users SET is_premium = 0, premium_expires_at = NULL WHERE id = ?')->execute([$userId]);
        return ['ok' => true, 'message' => 'Assinatura cancelada; Premium revogado.'];
    }

    if ($status === 'paused') {
        $pdo->prepare('UPDATE mercadopago_subscriptions SET status = ? WHERE mp_preapproval_id = ?')
            ->execute(['paused', $preapprovalId]);
        return ['ok' => true, 'message' => 'Assinatura pausada.'];
    }

    return ['ok' => true, 'message' => 'Status: ' . $status];
}
