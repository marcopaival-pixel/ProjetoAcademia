<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class ApiIntegrationController extends Controller
{
    public function index()
    {
        $integrations = ApiIntegration::all();
        $types = ApiIntegration::getTypes();
        return view('admin.api-integrations.index', compact('integrations', 'types'));
    }

    public function create()
    {
        $types = ApiIntegration::getTypes();
        return view('admin.api-integrations.form', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(ApiIntegration::getTypes())),
            'base_url' => 'required|url',
            'api_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'timeout' => 'required|integer|min:1|max:120',
            'status' => 'required|in:active,inactive',
        ]);

        $integration = ApiIntegration::create($validated);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'Criou integração de API: ' . $integration->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $validated,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.api-integrations.index')->with('success', 'Integração cadastrada com sucesso.');
    }

    public function edit(ApiIntegration $apiIntegration)
    {
        $types = ApiIntegration::getTypes();
        return view('admin.api-integrations.form', [
            'integration' => $apiIntegration,
            'types' => $types
        ]);
    }

    public function update(Request $request, ApiIntegration $apiIntegration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(ApiIntegration::getTypes())),
            'base_url' => 'required|url',
            'api_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'timeout' => 'required|integer|min:1|max:120',
            'status' => 'required|in:active,inactive',
        ]);

        $apiIntegration->update($validated);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'Editou integração de API: ' . $apiIntegration->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $validated,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.api-integrations.index')->with('success', 'Integração atualizada com sucesso.');
    }

    public function toggleStatus(ApiIntegration $apiIntegration)
    {
        $apiIntegration->status = $apiIntegration->status === 'active' ? 'inactive' : 'active';
        $apiIntegration->save();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'Alterou status da API ' . $apiIntegration->name . ' para ' . $apiIntegration->status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => ['status' => $apiIntegration->status],
            'created_at' => now(),
        ]);

        return back()->with('success', 'Status alterado com sucesso.');
    }

    public function testConnection(ApiIntegration $apiIntegration)
    {
        try {
            $response = Http::timeout($apiIntegration->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiIntegration->api_key,
                    'X-Api-Key' => $apiIntegration->api_key,
                ])
                ->get($apiIntegration->base_url);

            if ($response->successful() || $response->status() === 401 || $response->status() === 403) {
                // Mesmo que seja 401/403, significa que o servidor respondeu, o que valida a URL.
                // Mas geralmente queremos 200.
                $statusMessage = "Conexão estabelecida. Status: " . $response->status();
                $isSuccess = $response->successful();
            } else {
                $statusMessage = "Falha na conexão. Status: " . $response->status();
                $isSuccess = false;
            }
        } catch (\Exception $e) {
            $statusMessage = "Erro ao conectar: " . $e->getMessage();
            $isSuccess = false;
        }

        if ($isSuccess) {
            return back()->with('success', $statusMessage);
        } else {
            return back()->with('error', $statusMessage);
        }
    }

    public function destroy(ApiIntegration $apiIntegration)
    {
        $name = $apiIntegration->name;
        $apiIntegration->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => 'Excluiu integração de API: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => ['name' => $name],
            'created_at' => now(),
        ]);

        return redirect()->route('admin.api-integrations.index')->with('success', 'Integração excluída com sucesso.');
    }
}
