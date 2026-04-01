<?php
declare(strict_types=1);

/**
 * Notificações Mercado Pago:
 * - pagamentos (Checkout Pro e cobranças de assinatura) → crédito de Premium;
 * - preapproval → sincroniza cancelamento/autorização da assinatura.
 */

$config = require dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/mercadopago.php';

$token = (string) ($config['mercadopago']['access_token'] ?? '');
if ($token === '') {
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'MP não configurado';
    exit;
}

$paymentId = null;
$preapprovalNotifyId = null;

$raw = (string) file_get_contents('php://input');
if ($raw !== '') {
    try {
        $j = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        $j = null;
    }
    if (is_array($j)) {
        $type = (string) ($j['type'] ?? '');
        $topic = (string) ($j['topic'] ?? '');
        $dataArr = isset($j['data']) && is_array($j['data']) ? $j['data'] : [];
        $dataId = isset($dataArr['id']) ? (string) $dataArr['id'] : null;
        if ($dataId !== null) {
            if ($type === 'subscription_preapproval' || $type === 'preapproval'
                || $topic === 'subscription_preapproval' || $topic === 'preapproval'
            ) {
                $preapprovalNotifyId = $dataId;
            } elseif ($type === 'payment' || $topic === 'payment') {
                $paymentId = $dataId;
            } else {
                $action = isset($j['action']) ? (string) $j['action'] : '';
                if ($type === '' && $topic === '' && $action !== '' && strpos($action, 'payment.') === 0) {
                    $paymentId = $dataId;
                }
            }
        }
    }
}

$tPost = (string) ($_POST['topic'] ?? $_GET['topic'] ?? '');
$idPost = (string) ($_POST['id'] ?? $_GET['id'] ?? '');
if ($preapprovalNotifyId === null && $idPost !== '') {
    if ($tPost === 'subscription_preapproval' || $tPost === 'preapproval') {
        $preapprovalNotifyId = $idPost;
    }
    if ($paymentId === null && $tPost === 'payment') {
        $paymentId = $idPost;
    }
}

$pdo = db($config);

if ($preapprovalNotifyId !== null && $preapprovalNotifyId !== '') {
    try {
        $result = mp_sync_preapproval_webhook($pdo, $token, $preapprovalNotifyId);
        if (!$result['ok']) {
            error_log('[mp_webhook] preapproval ' . $preapprovalNotifyId . ': ' . $result['message']);
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'retry';
            exit;
        }
    } catch (Throwable $e) {
        error_log('[mp_webhook] preapproval exception: ' . $e->getMessage());
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'retry';
        exit;
    }
    http_response_code(200);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'ok';
    exit;
}

if ($paymentId === null || $paymentId === '') {
    http_response_code(200);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'ok';
    exit;
}

$payRes = mp_fetch_payment($config, $token, $paymentId);
if (!$payRes['ok']) {
    error_log('[mp_webhook] fetch payment ' . $paymentId . ': ' . $payRes['error']);
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'retry';
    exit;
}

try {
    $result = mp_try_credit_premium($pdo, $payRes['payment']);
    if (!$result['ok']) {
        error_log('[mp_webhook] payment ' . $paymentId . ': ' . $result['message']);
    }
} catch (Throwable $e) {
    error_log('[mp_webhook] payment exception: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'retry';
    exit;
}

http_response_code(200);
header('Content-Type: text/plain; charset=UTF-8');
echo 'ok';
