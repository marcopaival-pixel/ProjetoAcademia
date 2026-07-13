<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\ProfessionalPatient;
use App\Models\HealthPermission;
use App\Models\AdminLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1StudentProfessionalsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'aluno'], ['label' => 'Aluno']);
    }

    private function studentUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('aluno');

        $permission = Permission::firstOrCreate(
            ['name' => 'portal.access'],
            ['label' => 'Acesso ao Portal Usuário']
        );
        $user->permissions()->syncWithoutDetaching([$permission->id]);

        return $user;
    }

    public function test_professionals_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/student/professionals')->assertUnauthorized();
    }

    public function test_student_can_list_professionals_with_permissions(): void
    {
        $student = $this->studentUser();
        $professional = User::factory()->create();

        $link = ProfessionalPatient::create([
            'user_id' => $student->id,
            'profissional_id' => $professional->id,
            'status' => 'Sim',
            'patient_permissions' => [
                'psychology_session_notes' => true,
                'restricted_notes' => false,
            ],
        ]);

        HealthPermission::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'view',
        ]);

        HealthPermission::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'data_type' => 'restricted_notes',
            'access_level' => 'none',
        ]);

        Sanctum::actingAs($student);

        $response = $this->getJson('/api/v1/student/professionals')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'professionals' => [
                        '*' => [
                            'id',
                            'link_id',
                            'name',
                            'email',
                            'permissions' => [
                                'psychology_session_notes',
                                'restricted_notes',
                            ]
                        ]
                    ]
                ]
            ]);

        $response->assertJsonPath('data.professionals.0.link_id', $link->id);
        $response->assertJsonPath('data.professionals.0.permissions.psychology_session_notes', true);
        $response->assertJsonPath('data.professionals.0.permissions.restricted_notes', false);
    }

    public function test_student_can_update_permissions(): void
    {
        $student = $this->studentUser();
        $professional = User::factory()->create();

        $link = ProfessionalPatient::create([
            'user_id' => $student->id,
            'profissional_id' => $professional->id,
            'status' => 'Sim',
            'patient_permissions' => [
                'psychology_session_notes' => false,
                'restricted_notes' => false,
            ],
        ]);

        Sanctum::actingAs($student);

        $response = $this->postJson("/api/v1/student/professionals/links/{$link->id}/permissions", [
            'permissions' => [
                'psychology_session_notes' => true,
                'restricted_notes' => false,
            ],
        ])->assertOk();

        $this->assertDatabaseHas('pacientes', [
            'id' => $link->id,
            'patient_permissions' => json_encode([
                'psychology_session_notes' => true,
                'restricted_notes' => false,
            ]),
        ]);

        $this->assertDatabaseHas('health_permissions', [
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'view',
        ]);

        $this->assertDatabaseHas('health_permissions', [
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'data_type' => 'restricted_notes',
            'access_level' => 'none',
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'user_id' => $student->id,
            'action' => 'PATIENT_UPDATED_PERMISSIONS',
        ]);
    }

    public function test_student_cannot_update_others_permissions(): void
    {
        $student = $this->studentUser();
        $otherStudent = $this->studentUser();
        $professional = User::factory()->create();

        $link = ProfessionalPatient::create([
            'user_id' => $otherStudent->id,
            'profissional_id' => $professional->id,
            'status' => 'Sim',
        ]);

        Sanctum::actingAs($student);

        $this->postJson("/api/v1/student/professionals/links/{$link->id}/permissions", [
            'permissions' => [
                'psychology_session_notes' => true,
                'restricted_notes' => false,
            ],
        ])->assertStatus(403);
    }

    public function test_student_can_revoke_link(): void
    {
        $student = $this->studentUser();
        $professional = User::factory()->create();

        $link = ProfessionalPatient::create([
            'user_id' => $student->id,
            'profissional_id' => $professional->id,
            'status' => 'Sim',
        ]);

        HealthPermission::create([
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'view',
        ]);

        Sanctum::actingAs($student);

        $this->postJson("/api/v1/student/professionals/links/{$link->id}/revoke")->assertOk();

        $this->assertDatabaseHas('pacientes', [
            'id' => $link->id,
            'status' => 'Não',
        ]);

        $this->assertDatabaseHas('health_permissions', [
            'patient_id' => $student->id,
            'professional_id' => $professional->id,
            'data_type' => 'psychology_session_notes',
            'access_level' => 'none',
        ]);

        $this->assertDatabaseHas('admin_logs', [
            'user_id' => $student->id,
            'action' => 'PATIENT_REVOKED_PROFESSIONAL_LINK',
        ]);
    }
}
