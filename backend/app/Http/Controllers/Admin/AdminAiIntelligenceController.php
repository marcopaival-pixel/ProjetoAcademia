<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiRetentionService;
use Illuminate\Http\Request;

class AdminAiIntelligenceController extends Controller
{
    protected AiRetentionService $aiRetentionService;

    public function __construct(AiRetentionService $aiRetentionService)
    {
        $this->aiRetentionService = $aiRetentionService;
    }

    /**
     * Exibe o Dashboard Principal da IA de Retenção.
     */
    public function index()
    {
        $metrics = $this->aiRetentionService->getDashboardMetrics();

        return view('admin.ai-intelligence.dashboard', compact('metrics'));
    }

    /**
     * Aciona uma ação de recuperação (ex: enviar mensagem no WhatsApp ou email).
     */
    public function recoverPatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'action_type' => 'required|string',
            'message' => 'nullable|string'
        ]);

        // Simulação do envio de notificação (Email/WhatsApp)
        // Aqui conectaríamos com os serviços existentes da plataforma.
        $patientId = $request->input('patient_id');
        $actionType = $request->input('action_type');

        // Retorna sucesso em JSON (para uso via fetch/axios no frontend).
        return response()->json([
            'success' => true,
            'message' => "Ação de recuperação ({$actionType}) acionada com sucesso para o paciente ID {$patientId}.",
        ]);
    }
}
