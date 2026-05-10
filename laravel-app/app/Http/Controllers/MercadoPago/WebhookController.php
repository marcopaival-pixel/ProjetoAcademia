<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Valida a assinatura HMAC-SHA256 enviada pelo Mercado Pago.
     *
     * O MP envia o header x-signature no formato: ts=<timestamp>;v1=<hash>
     * O hash é calculado sobre: "id:<data.id>;request-id:<x-request-id>;ts:<ts>"
     *
     * @see https://www.mercadopago.com.br/developers/pt/docs/your-integrations/notifications/webhooks
     */
    private function validateMpSignature(Request $request, string $secret): bool
    {
        $xSignature = (string) $request->header('x-signature', '');
        $xRequestId = (string) $request->header('x-request-id', '');

        if ($xSignature === '') {
            return false;
        }

        // Extrair ts e v1 do header x-signature (formato: ts=<n>;v1=<hash>)
        $ts = '';
        $v1 = '';
        foreach (explode(';', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            if (trim($key) === 'ts') {
                $ts = trim($value);
            }
            if (trim($key) === 'v1') {
                $v1 = trim($value);
            }
        }

        if ($ts === '' || $v1 === '') {
            return false;
        }

        // Extrair o data.id do payload para compor a string de manifesto
        $dataId = '';
        $raw = $request->getContent();
        if ($raw !== '') {
            try {
                $j = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($j) && isset($j['data']['id'])) {
                    $dataId = (string) $j['data']['id'];
                }
            } catch (\JsonException) {
                // dataId permanece vazio — incluído na string de manifesto como string vazia
            }
        }

        // Montar string de manifesto conforme documentação MP e verificar HMAC
        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts}";
        $expected  = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $v1);
    }

    public function __invoke(Request $request, MercadoPagoService $mp): \Illuminate\Http\Response
    {
        $token = config('projeto.mp_access_token');
        if ($token === '') {
            return response('MP não configurado', 503, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        // --- Validação de assinatura HMAC-SHA256 (Correção S2) ---
        $webhookSecret = (string) config('projeto.mp_webhook_secret', '');
        if ($webhookSecret !== '') {
            if (! $this->validateMpSignature($request, $webhookSecret)) {
                Log::warning('[mp_webhook] Assinatura inválida rejeitada.', [
                    'ip'           => $request->ip(),
                    'x-signature'  => $request->header('x-signature', ''),
                    'x-request-id' => $request->header('x-request-id', ''),
                ]);

                return response('Assinatura inválida', 401, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }
        } else {
            Log::debug('[mp_webhook] MP_WEBHOOK_SECRET não configurado — validação de assinatura ignorada (apenas dev).');
        }

        $paymentId           = null;
        $preapprovalNotifyId = null;

        $raw = $request->getContent();
        if ($raw !== '') {
            try {
                $j = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $j = null;
            }
            if (is_array($j)) {
                $type    = (string) ($j['type'] ?? '');
                $topic   = (string) ($j['topic'] ?? '');
                $dataArr = isset($j['data']) && is_array($j['data']) ? $j['data'] : [];
                $dataId  = isset($dataArr['id']) ? (string) $dataArr['id'] : null;
                if ($dataId !== null) {
                    if ($type === 'subscription_preapproval' || $type === 'preapproval'
                        || $topic === 'subscription_preapproval' || $topic === 'preapproval'
                    ) {
                        $preapprovalNotifyId = $dataId;
                    } elseif ($type === 'payment' || $topic === 'payment') {
                        $paymentId = $dataId;
                    } else {
                        $action = isset($j['action']) ? (string) $j['action'] : '';
                        if ($type === '' && $topic === '' && $action !== '' && str_starts_with($action, 'payment.')) {
                            $paymentId = $dataId;
                        }
                    }
                }
            }
        }

        $tPost  = (string) ($request->input('topic') ?? $request->query('topic', ''));
        $idPost = (string) ($request->input('id') ?? $request->query('id', ''));
        if ($preapprovalNotifyId === null && $idPost !== '') {
            if ($tPost === 'subscription_preapproval' || $tPost === 'preapproval') {
                $preapprovalNotifyId = $idPost;
            }
            if ($paymentId === null && $tPost === 'payment') {
                $paymentId = $idPost;
            }
        }

        if ($preapprovalNotifyId !== null && $preapprovalNotifyId !== '') {
            try {
                $result = $mp->syncPreapprovalWebhook($preapprovalNotifyId);
                if (! $result['ok']) {
                    Log::warning('[mp_webhook] preapproval '.$preapprovalNotifyId.': '.$result['message']);

                    return response('retry', 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
                }
            } catch (\Throwable $e) {
                Log::error('[mp_webhook] preapproval exception: '.$e->getMessage());

                return response('retry', 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }

            return response('ok', 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        if ($paymentId === null || $paymentId === '') {
            return response('ok', 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $payRes = $mp->fetchPayment($paymentId);
        if (! $payRes['ok']) {
            Log::warning('[mp_webhook] fetch payment '.$paymentId.': '.$payRes['error']);

            return response('retry', 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        try {
            $result = $mp->tryCreditPremium($payRes['payment']);
            if (! $result['ok']) {
                Log::info('[mp_webhook] payment '.$paymentId.': '.$result['message']);
            }
        } catch (\Throwable $e) {
            Log::error('[mp_webhook] payment exception: '.$e->getMessage());

            return response('retry', 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        return response('ok', 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
