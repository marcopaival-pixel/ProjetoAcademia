<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\AdminClinicAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminClinicImpersonationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Exibe o formulário para selecionar o motivo do acesso.
     */
    public function start(AcademyCompany $company)
    {
        $this->authorizeAdmin();

        return view('admin.pdf-suite.impersonation-start', compact('company'));
    }

    /**
     * Inicia a sessão de impersonação.
     */
    public function store(Request $request, AcademyCompany $company)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'motivo_acesso' => 'required|string',
            'descricao' => 'required|string|min:10',
        ]);

        // Registrar log de entrada
        $log = AdminClinicAccessLog::create([
            'admin_user_id' => Auth::id(),
            'clinic_id' => $company->id,
            'motivo_acesso' => $data['motivo_acesso'],
            'descricao' => $data['descricao'],
            'data_hora_entrada' => now(),
            'ip' => $request->ip(),
        ]);

        // Configurar sessão
        session([
            'impersonated_clinic_id' => $company->id,
            'impersonation_log_id' => $log->id,
            'impersonation_started_at' => now()->timestamp,
        ]);

        return redirect()->route('dashboard')->with('success', "Acesso administrativo ativo na clínica: {$company->name}");
    }

    /**
     * Encerra a sessão de impersonação.
     */
    public function stop()
    {
        if (!session()->has('impersonated_clinic_id')) {
            return redirect()->route('admin.dashboard');
        }

        $logId = session('impersonation_log_id');
        $log = AdminClinicAccessLog::find($logId);

        if ($log) {
            $entrada = $log->data_hora_entrada;
            $saida = now();
            $diff = $entrada->diff($saida);
            
            $log->update([
                'data_hora_saida' => $saida,
                'duracao_acesso' => $diff->format('%H:%I:%S'),
            ]);
        }

        // Limpar sessão
        session()->forget([
            'impersonated_clinic_id',
            'impersonation_log_id',
            'impersonation_started_at',
        ]);

        return redirect()->route('admin.pdf-companies.index')->with('success', 'Sessão administrativa encerrada.');
    }

    /**
     * Verifica se o usuário tem permissão para impersonar.
     */
    protected function authorizeAdmin()
    {
        $user = Auth::user();
        
        // Conforme solicitado: ADMIN_GLOBAL ou SUPER_ADMIN
        // Se não houver esses tipos específicos, usamos is_admin como fallback seguro
        $allowedTypes = ['ADMIN_GLOBAL', 'SUPER_ADMIN'];
        
        if (!$user->is_admin && !in_array($user->user_type, $allowedTypes)) {
            abort(403, 'Acesso não autorizado para este perfil.');
        }
    }
}
