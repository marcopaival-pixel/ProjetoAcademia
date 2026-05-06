<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditoPacote;
use App\Models\CreditoCompra;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $settings = [
            'compra_creditos_ativa' => SystemSetting::where('key', 'compra_creditos_ativa')->first()?->value === 'true',
            'pagamento_ativo' => SystemSetting::where('key', 'pagamento_ativo')->first()?->value === 'true',
            'mp_access_token' => config('projeto.mp_access_token'), // Ideally this would be in system_settings too
        ];

        $packages = CreditoPacote::orderBy('quantidade')->get();
        $purchases = CreditoCompra::with('user')->latest()->paginate(20);

        return view('admin.billing.index', compact('settings', 'packages', 'purchases'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'compra_creditos_ativa' => 'required|boolean',
            'pagamento_ativo' => 'required|boolean',
        ]);

        SystemSetting::updateOrCreate(
            ['key' => 'compra_creditos_ativa'],
            ['value' => $data['compra_creditos_ativa'] ? 'true' : 'false', 'description' => 'Ativar/desativar compra de créditos']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'pagamento_ativo'],
            ['value' => $data['pagamento_ativo'] ? 'true' : 'false', 'description' => 'Pagamento real ativo (se falso, libera automático)']
        );

        return back()->with('success', 'Configurações atualizadas com sucesso.');
    }

    public function storePackage(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0',
            'ativo' => 'required|boolean',
        ]);

        CreditoPacote::create($data);

        return back()->with('success', 'Pacote criado com sucesso.');
    }

    public function updatePackage(Request $request, CreditoPacote $package)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0',
            'ativo' => 'required|boolean',
        ]);

        $package->update($data);

        return back()->with('success', 'Pacote atualizado com sucesso.');
    }

    public function deletePackage(CreditoPacote $package)
    {
        $package->delete();
        return back()->with('success', 'Pacote excluído com sucesso.');
    }
}
