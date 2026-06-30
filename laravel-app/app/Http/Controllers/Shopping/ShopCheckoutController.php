<?php

namespace App\Http\Controllers\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Services\Shop\ShopCartService;
use App\Services\Shop\ShopCheckoutPaymentService;
use App\Services\Shop\ShopOrderAccess;
use App\Services\Shop\ShopOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * ShopCheckoutController
 * -----------------------------------------------------------------
 * Responsável pelo checkout das compras do shopping.
 * NÃO MODIFICA o CheckoutController existente (assinaturas da plataforma).
 * Compartilha o PaymentGatewayManager por injeção.
 */
class ShopCheckoutController extends Controller
{
    public function __construct(
        private ShopCartService $cartService,
        private ShopOrderService $orderService,
        private ShopCheckoutPaymentService $checkoutPayment,
        private ShopOrderAccess $orderAccess,
    ) {}

    /**
     * Exibe a tela de checkout com resumo do carrinho.
     */
    public function index()
    {
        $user    = Auth::user();
        $summary = $this->cartService->summary($user);

        if ($summary['cart']->isEmpty()) {
            return redirect()->route('shopping.cart.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        return view('shopping.checkout', compact('summary', 'user'));
    }

    /**
     * Processa o checkout: cria o pedido e inicia o pagamento no gateway.
     */
    public function process(Request $request)
    {
        $request->validate([
            'payment_method'  => 'required|in:pix,credit_card,points',
            'shipping_method' => 'nullable|in:correios,transportadora,pickup',
            'shipping_address.cep'       => 'required_if:shipping_method,correios,transportadora|nullable|string',
            'shipping_address.street'    => 'nullable|string',
            'shipping_address.number'    => 'nullable|string',
            'shipping_address.city'      => 'nullable|string',
            'shipping_address.state'     => 'nullable|string|max:2',
        ]);

        $user = Auth::user();

        try {
            $order = $this->orderService->createFromCart($user, [
                'payment_method'  => $request->input('payment_method'),
                'shipping_method' => $request->input('shipping_method'),
                'shipping_address' => $request->input('shipping_address'),
                'shipping_amount'  => 0, // calculado no futuro via API de frete
                'notes'           => $request->input('notes'),
            ]);

            // Inicia pagamento via gateway ativo
            $paymentResult = $this->checkoutPayment->initiate(
                $order,
                $user,
                $request->input('payment_method')
            );

            if ($paymentResult['mode'] === 'redirect' && ! empty($paymentResult['redirect_url'])) {
                return redirect()->away($paymentResult['redirect_url']);
            }

            return redirect()->route('shopping.checkout.success', $order)
                ->with('success', 'Pedido realizado com sucesso!');

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('ShopCheckoutController: erro inesperado', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocorreu um erro ao processar seu pedido. Tente novamente.');
        }
    }

    /**
     * Página de confirmação do pedido.
     */
    public function success(ShopOrder $order)
    {
        $this->orderAccess->assertOwnedBy($order);

        $order->load('items.product.images');

        return view('shopping.checkout_success', compact('order'));
    }
}
