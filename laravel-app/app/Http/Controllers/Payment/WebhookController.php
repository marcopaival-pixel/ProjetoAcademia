<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $gatewayManager;

    public function __construct(PaymentGatewayManager $gatewayManager)
    {
        $this->gatewayManager = $gatewayManager;
    }

    public function __invoke(Request $request, string $gateway)
    {
        try {
            $driver = $this->gatewayManager->driver($gateway);
            
            $result = $driver->handleWebhook($request);

            // Log de depuração do processamento do webhook
            Log::info("[Webhook] Processado com sucesso para o gateway: {$gateway}", [
                'result' => $result,
                'payload' => $request->all()
            ]);

            return response()->json($result, $result['status'] ?? 200);
        } catch (\Exception $e) {
            Log::error("[Webhook] Erro ao processar webhook do gateway {$gateway}: " . $e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
