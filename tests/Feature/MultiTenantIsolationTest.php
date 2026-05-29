<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Clinic;
use App\Models\TrainingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class MultiTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_cannot_see_data_from_another_clinic()
    {
        // 1. Setup: Duas clínicas
        $clinicA = Clinic::create(['name' => 'Clínica A', 'slug' => 'clinica-a', 'status' => 'active']);
        $clinicB = Clinic::create(['name' => 'Clínica B', 'slug' => 'clinica-b', 'status' => 'active']);

        // 2. Dois usuários vinculados às suas clínicas
        $userA = User::factory()->create(['clinic_id' => $clinicA->id]);
        $userB = User::factory()->create(['clinic_id' => $clinicB->id]);

        // 3. Criar dados na Clínica B
        // Nota: Precisamos desativar o escopo temporariamente ou agir como B para criar
        $planB = TrainingPlan::create([
            'user_id' => $userB->id,
            'clinic_id' => $clinicB->id,
            'name' => 'Treino Secreto Clínica B',
            'status' => 'active'
        ]);

        // 4. Tentar acessar como Usuário A
        $this->actingAs($userA);
        
        // O escopo global do HasClinic deve ser ativado pelo Auth::user()
        $visiblePlans = TrainingPlan::all();

        $this->assertCount(0, $visiblePlans, 'Usuário A conseguiu ver dados da Clínica B!');
        $this->assertFalse($visiblePlans->contains($planB));
    }

    /** @test */
    public function data_is_automatically_scoped_to_the_active_tenant()
    {
        $clinic = Clinic::create(['name' => 'Clínica Alpha', 'slug' => 'alpha', 'status' => 'active']);
        $user = User::factory()->create(['clinic_id' => $clinic->id]);

        $this->actingAs($user);

        // O trait HasClinic deve capturar o clinic_id do usuário logado via TenantContext ou Auth
        $plan = TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Treino Automático',
            'status' => 'active'
        ]);

        $this->assertEquals($clinic->id, $plan->clinic_id, 'O clinic_id não foi preenchido automaticamente!');
    }
}
