<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Support\PaymentGatewayRegistry;
use App\Models\AdminLog;
use App\Models\AdminSetting;
use App\Models\PaymentWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = PaymentSetting::all()->keyBy('gateway');
        
        $gateways = PaymentGatewayRegistry::options();

        return view('admin.payment_settings.index', compact('settings', 'gateways'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gateway' => ['required', 'string', PaymentGatewayRegistry::validationRule()],
            'environment' => 'required|in:sandbox,production',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'public_key' => 'nullable|string',
            'access_token' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'webhook_url' => 'nullable|url',
            'timeout' => 'required|integer|min:5|max:120',
            'priority' => 'required|integer|min:1',
            'enable_credit_card' => 'boolean',
            'enable_pix' => 'boolean',
            'enable_boleto' => 'boolean',
            'boleto_expiration_days' => 'required|integer|min:1',
            'pix_expiration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'penalty_percent' => 'required|numeric|min:0',
            'interest_percent' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0',
            'tolerance_days' => 'required|integer|min:0',
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
        $gateway = (string) $request->input('gateway');

        if (! PaymentGatewayRegistry::isImplemented($gateway)) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway não implementado no sistema.',
            ], 422);
        }

        $setting = PaymentSetting::where('gateway', $gateway)->first();

        if (! $setting) {
            return response()->json(['success' => false, 'message' => 'Configurações não encontradas para este gateway.']);
        }

        $success = ! empty($setting->access_token);
        $message = $success
            ? "Credenciais de {$gateway} presentes (teste básico)."
            : 'Token de acesso não configurado.';

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    public function toggleGlobal(Request $request)
    {
        $newValue = $request->has('pagamento_ativo');
        
        AdminSetting::set('pagamento_ativo', $newValue ? 'true' : 'false');

        AdminLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggle_global_payment',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => ['active' => $newValue],
        ]);

        return redirect()->route('admin.settings.payments')->with('success', 'Status de faturamento global atualizado!');
    }

    public function webhooks()
    {
        $logs = PaymentWebhookLog::orderBy('created_at', 'desc')->paginate(50);
        return view('admin.payment_settings.webhooks', compact('logs'));
    }
}
