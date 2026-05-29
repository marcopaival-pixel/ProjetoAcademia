<?php

namespace App\Http\Controllers;

use App\Models\CreditoPacote;
use App\Models\CreditoCompra;
use App\Models\SystemSetting;
use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\Request;

class CreditoController extends Controller
{
    protected $gateway;

    public function __construct(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
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
            'gateway' => $this->gateway->getIdentifier(),
        ]);

        // Se pagamento_ativo for falso, libera automático (modo teste)
        $pagamentoAtivo = SystemSetting::where('key', 'pagamento_ativo')->first()?->value === 'true';
        if (!$pagamentoAtivo) {
            $this->approvePurchase($compra);
            return redirect()->route('credits.success', ['compra' => $compra->id]);
        }

        // Criar preferência/checkout no Gateway Ativo
        $response = $this->gateway->createCheckout($user, (float) $package->valor, [
            'title' => "Pacote de Créditos — {$package->nome}",
            'description' => "Compra de {$package->quantidade} créditos.",
            'external_reference' => "credits:{$compra->id}",
            'success_url' => route('credits.success', ['compra' => $compra->id]),
            'pending_url' => route('credits.pending', ['compra' => $compra->id]),
            'failure_url' => route('credits.buy'),
        ]);

        if (!$response['ok']) {
            return back()->with('error', 'Erro ao processar com o gateway: ' . $response['error']);
        }

        $compra->update(['payment_id' => $response['id'] ?? $response['data']['id']]);

        return redirect($response['init_point']);
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
            $payment = $this->gateway->fetchPayment($paymentId);
            
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
