<?php

namespace App\Support;

use App\Models\AcademyCompany;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    private static ?int $activeClinicId = null;

    /**
     * Define a clínica ativa para o contexto atual.
     */
    public static function set(int $clinicId): void
    {
        self::$activeClinicId = $clinicId;
    }

    /**
     * Obtém o ID da clínica ativa.
     * Prioriza o valor setado manualmente (ex: via Middleware), 
     * depois tenta a sessão, e por fim o academy_company_id do usuário.
     */
    public static function get(): ?int
    {
        if (self::$activeClinicId !== null) {
            return self::$activeClinicId;
        }

        if (session()->has('active_clinic_id')) {
            return session('active_clinic_id');
        }

        // Fallback para o comportamento antigo se for admin/profissional de uma clínica única
        $user = Auth::user();
        if ($user && $user->academy_company_id) {
            return $user->academy_company_id;
        }

        return null;
    }

    /**
     * Verifica se existe um contexto de clínica ativo.
     */
    public static function has(): bool
    {
        return self::get() !== null;
    }
}
