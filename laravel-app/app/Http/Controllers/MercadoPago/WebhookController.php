<?php

namespace App\Http\Controllers\MercadoPago;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mp): \Illuminate\Http\Response
    {
        $token = config('projeto.mp_access_token');
        if ($token === '') {
            return response('MP não configurado', 503, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $paymentId = null;
        $preapprovalNotifyId = null;

        $raw = $request->getContent();
        if ($raw !== '') {
            try {
                $j = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
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
                        if ($type === '' && $topic === '' && $action !== '' && str_starts_with($action, 'payment.')) {
                            $paymentId = $dataId;
                        }
                    }
                }
            }
        }

        $tPost = (string) ($request->input('topic') ?? $request->query('topic', ''));
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
                $result = $mp->syncPreapprovalWebhook($token, $preapprovalNotifyId);
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

        $payRes = $mp->fetchPayment($token, $paymentId);
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
