<?php

namespace App\Support;

use App\Models\Clinic;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanExercise;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class PatientAccessGuard
{
    /**
     * Admin em impersonação auditada pode aceder pacientes do tenant ativo.
     */
    public static function patientBelongsToImpersonatedTenant(User $patient): bool
    {
        if (! session()->has('impersonated_clinic_id')) {
            return false;
        }

        $clinicId = (int) session('impersonated_clinic_id');
        $companyId = session('impersonated_company_id');

        if ($patient->clinic_id && (int) $patient->clinic_id === $clinicId) {
            return true;
        }

        if (! $companyId) {
            $clinic = Clinic::find($clinicId);
            $companyId = $clinic?->academy_company_id;
        }

        return $companyId
            && $patient->academy_company_id
            && (int) $patient->academy_company_id === (int) $companyId;
    }

    public static function assertProfessionalPatientLink(User $professional, User|int $patient): User
    {
        $patientModel = $patient instanceof User
            ? $patient
            : User::findOrFail($patient);

        if ($professional->isAdministrator()) {
            if (self::patientBelongsToImpersonatedTenant($patientModel)) {
                return $patientModel;
            }

            throw new AuthorizationException('Administrador sem impersonação ativa não pode aceder a este paciente.');
        }

        if (! $professional->patients()->wherePivot('user_id', $patientModel->id)->exists()) {
            throw new AuthorizationException('Acesso não autorizado a este paciente.');
        }

        if (
            $professional->academy_company_id
            && $patientModel->academy_company_id
            && (int) $professional->academy_company_id !== (int) $patientModel->academy_company_id
        ) {
            throw new AuthorizationException('Paciente não pertence à sua organização.');
        }

        return $patientModel;
    }

    /**
     * Valida active_patient_id da sessão para profissionais.
     */
    public static function resolveActivePatientId(User $professional): ?int
    {
        $patientId = session('active_patient_id');
        if (! $patientId) {
            return null;
        }

        self::assertProfessionalPatientLink($professional, (int) $patientId);

        return (int) $patientId;
    }

    /**
     * Profissional pode aceder dados de um aluno/paciente?
     */
    public static function adminCanAccessUserData(User $admin, User $subject): bool
    {
        return $admin->isAdministrator()
            && self::patientBelongsToImpersonatedTenant($subject);
    }

    public static function canAccessStudentData(User $user, int $studentId): bool
    {
        if ((int) $user->id === $studentId) {
            return true;
        }

        if ($user->isAdministrator()) {
            $student = User::find($studentId);

            return $student !== null && self::patientBelongsToImpersonatedTenant($student);
        }

        if ($user->isProfessional() || $user->hasRole(['instructor', 'supervisor'])) {
            return $user->patients()->where('users.id', $studentId)->exists();
        }

        return false;
    }

    /**
     * Garante que o utilizador pode aceder dados do aluno indicado.
     */
    public static function assertStudentDataAccess(User $user, int $studentId): void
    {
        if (! self::canAccessStudentData($user, $studentId)) {
            throw new AuthorizationException('Acesso não autorizado aos dados deste aluno.');
        }
    }

    /**
     * Garante que o exercício do plano pertence a um plano acessível pelo utilizador.
     */
    public static function assertTrainingPlanExerciseAccess(User $user, int $trainingPlanExerciseId): TrainingPlanExercise
    {
        $planExercise = TrainingPlanExercise::with('trainingPlan')->findOrFail($trainingPlanExerciseId);
        $plan = $planExercise->trainingPlan;

        if (! $plan) {
            throw new AuthorizationException('Plano de treino não encontrado.');
        }

        if ((int) $plan->user_id === (int) $user->id) {
            return $planExercise;
        }

        if ((int) ($plan->creator_id ?? 0) === (int) $user->id) {
            return $planExercise;
        }

        if ($user->isProfessional() && $user->patients()->where('users.id', $plan->user_id)->exists()) {
            return $planExercise;
        }

        if ($user->isAdministrator()) {
            $planOwner = User::find($plan->user_id);
            if ($planOwner && self::patientBelongsToImpersonatedTenant($planOwner)) {
                return $planExercise;
            }

            throw new AuthorizationException('Administrador sem impersonação ativa não pode aceder a este plano.');
        }

        throw new AuthorizationException('Exercício não pertence a um plano autorizado.');
    }
}
