<?php

namespace App\Support;

use App\Models\AcademyCompany;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    private static ?int $activeClinicId = null;
    private static ?\App\Models\Clinic $activeClinic = null;

    /**
     * Define a clínica ativa para o contexto atual.
     */
    public static function set(?int $clinicId): void
    {
        self::$activeClinicId = $clinicId;
        self::$activeClinic = null; // Reset cached model
    }

    /**
     * Obtém o ID da clínica ativa.
     */
    public static function get(): ?int
    {
        if (self::$activeClinicId !== null) {
            return self::$activeClinicId;
        }

        if (session()->has('active_clinic_id')) {
            return session('active_clinic_id');
        }

        // Fallback para o comportamento antigo ou usuário direto
        $user = Auth::user();
        if ($user && $user->clinic_id) {
            return $user->clinic_id;
        }

        if ($user && $user->academy_company_id) {
            // Se o usuário ainda não tem clinic_id, tentamos pegar a primeira clínica da empresa
            $firstClinic = \App\Models\Clinic::where('academy_company_id', $user->academy_company_id)->first();
            if ($firstClinic) {
                return $firstClinic->id;
            }
        }

        return null;
    }

    /**
     * Obtém o modelo da clínica ativa.
     */
    public static function getClinic(): ?\App\Models\Clinic
    {
        $id = self::get();
        if (!$id) return null;

        if (self::$activeClinic === null || self::$activeClinic->id !== $id) {
            self::$activeClinic = \App\Models\Clinic::find($id);
        }

        return self::$activeClinic;
    }

    /**
     * Obtém o ID da empresa (conta principal) ativa.
     */
    public static function getCompanyId(): ?int
    {
        $clinic = self::getClinic();
        if ($clinic) {
            return $clinic->academy_company_id;
        }

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
