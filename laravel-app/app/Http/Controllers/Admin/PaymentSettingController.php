<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = PaymentSetting::all()->keyBy('gateway');
        
        // Default gateways if not existing in DB
        $gateways = ['mercadopago', 'pagseguro', 'asaas', 'stripe'];
        
        return view('admin.payment_settings.index', compact('settings', 'gateways'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gateway' => 'required|string|in:mercadopago,pagseguro,asaas,stripe',
            'environment' => 'required|in:sandbox,production',
            'public_key' => 'nullable|string',
            'access_token' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'enable_credit_card' => 'boolean',
            'enable_pix' => 'boolean',
            'enable_boleto' => 'boolean',
            'boleto_expiration_days' => 'required|integer|min:1',
            'pix_expiration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        // If this gateway is being set to active, set others to inactive
        if ($validated['status'] === 'active') {
            PaymentSetting::where('gateway', '!=', $validated['gateway'])->update(['status' => 'inactive']);
        }

        $setting = PaymentSetting::updateOrCreate(
            ['gateway' => $validated['gateway']],
            $validated
        );

        // Register Log
        AdminLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_payment_settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'gateway' => $setting->gateway,
                'environment' => $setting->environment,
                'status' => $setting->status,
            ],
        ]);

        return redirect()->back()->with('success', 'Configurações de pagamento atualizadas com sucesso!');
    }

    public function testConnection(Request $request)
    {
        $gateway = $request->input('gateway');
        $setting = PaymentSetting::where('gateway', $gateway)->first();

        if (!$setting) {
            return response()->json(['success' => false, 'message' => 'Configurações não encontradas para este gateway.']);
        }

        // Mocking connection test logic
        // In a real scenario, we would make a request to the gateway API
        
        $success = false;
        $message = 'Falha na conexão com o gateway.';

        try {
            switch ($gateway) {
                case 'mercadopago':
                    // Example: check if access token is valid via MP API
                    // $response = Http::withToken($setting->access_token)->get('https://api.mercadopago.com/v1/payment_methods');
                    // $success = $response->successful();
                    $success = !empty($setting->access_token); 
                    break;
                case 'stripe':
                    $success = !empty($setting->access_token);
                    break;
                case 'asaas':
                    $success = !empty($setting->access_token);
                    break;
                case 'pagseguro':
                    $success = !empty($setting->access_token);
                    break;
            }
            
            if ($success) {
                $message = "Conexão com {$gateway} estabelecida com sucesso!";
            }
        } catch (\Exception $e) {
            $message = "Erro ao testar conexão: " . $e->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}
