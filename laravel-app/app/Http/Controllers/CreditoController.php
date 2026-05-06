<?php

namespace App\Http\Controllers;

use App\Models\CreditoPacote;
use App\Models\CreditoCompra;
use App\Models\SystemSetting;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class CreditoController extends Controller
{
    protected $mpService;

    public function __construct(MercadoPagoService $mpService)
    {
        $this->mpService = $mpService;
    }

    public function buy()
    {
        // Verificar se a compra de créditos está ativa
        $ativa = SystemSetting::where('key', 'compra_creditos_ativa')->first()?->value === 'true';
        if (!$ativa && !auth()->user()->isAdministrator()) {
            return redirect()->route('dashboard')->with('error', 'A compra de créditos está temporariamente desativada.');
        }

        $packages = CreditoPacote::where('ativo', true)->orderBy('quantidade')->get();
        
        return view('credits.buy', compact('packages'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:creditos_pacotes,id',
        ]);

        $package = CreditoPacote::findOrFail($request->package_id);
        $user = auth()->user();

        // Criar registro da compra PENDENTE
        $compra = CreditoCompra::create([
            'user_id' => $user->id,
            'quantidade' => $package->quantidade,
            'valor' => $package->valor,
            'status' => 'PENDENTE',
            'gateway' => 'Mercado Pago',
        ]);

        // Se pagamento_ativo for falso, libera automático (modo teste)
        $pagamentoAtivo = SystemSetting::where('key', 'pagamento_ativo')->first()?->value === 'true';
        if (!$pagamentoAtivo) {
            $this->approvePurchase($compra);
            return redirect()->route('credits.success', ['compra' => $compra->id]);
        }

        // Integrar com Mercado Pago
        $token = config('projeto.mp_access_token');
        if (!$token) {
            return back()->with('error', 'Gateway de pagamento não configurado.');
        }

        // Criar preferência no Mercado Pago
        // Vou usar o payload customizado conforme MercadoPagoService
        $payload = [
            'items' => [[
                'title' => "Pacote de Créditos — {$package->nome}",
                'description' => "Compra de {$package->quantidade} créditos.",
                'category_id' => 'services',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => (float) $package->valor,
            ]],
            'payer' => ['email' => $user->email],
            'external_reference' => "credits:{$compra->id}",
            'back_urls' => [
                'success' => route('credits.success', ['compra' => $compra->id]),
                'pending' => route('credits.pending', ['compra' => $compra->id]),
                'failure' => route('credits.buy'),
            ],
            'auto_return' => 'approved',
            'notification_url' => route('credits.webhook'),
        ];

        // Se estiver em localhost, o webhook não vai funcionar via URL real, mas para produção é necessário
        if (config('app.env') === 'local') {
            // No local, podemos simular ou usar ngrok, mas por enquanto vamos prosseguir
            unset($payload['notification_url']);
        }

        $response = $this->mpService->apiRequest('POST', 'https://api.mercadopago.com/checkout/preferences', $token, $payload);

        if (!$response['ok']) {
            return back()->with('error', 'Erro ao processar com Mercado Pago: ' . $response['error']);
        }

        $compra->update(['payment_id' => $response['data']['id']]);

        return redirect($response['data']['init_point']);
    }

    public function success(CreditoCompra $compra)
    {
        if ($compra->user_id !== auth()->id()) abort(403);
        
        return view('credits.success', compact('compra'));
    }

    public function pending(CreditoCompra $compra)
    {
        if ($compra->user_id !== auth()->id()) abort(403);
        
        return view('credits.pending', compact('compra'));
    }

    /**
     * Webhook do Mercado Pago
     */
    public function webhook(Request $request)
    {
        // Log ou processar notificação
        $topic = $request->query('topic');
        $id = $request->query('id');

        if ($topic === 'payment' || $request->has('data')) {
            $paymentId = $id ?? $request->input('data.id');
            $token = config('projeto.mp_access_token');
            
            $payment = $this->mpService->fetchPayment($token, $paymentId);
            
            if ($payment['ok']) {
                $payData = $payment['payment'];
                $ref = $payData['external_reference'] ?? '';
                
                if (str_starts_with($ref, 'credits:')) {
                    $compraId = str_replace('credits:', '', $ref);
                    $compra = CreditoCompra::find($compraId);
                    
                    if ($compra && $compra->status === 'PENDENTE' && $payData['status'] === 'approved') {
                        $this->approvePurchase($compra);
                    }
                }
            }
        }

        return response('OK', 200);
    }

    private function approvePurchase(CreditoCompra $compra)
    {
        $compra->update(['status' => 'PAGO']);
        
        $user = $compra->user;
        $user->increment('creditos', $compra->quantidade);
        
        // Também atualizar ai_credits para manter compatibilidade se necessário
        if (\Schema::hasColumn('users', 'ai_credits')) {
            $user->increment('ai_credits', $compra->quantidade);
        }
    }
}
