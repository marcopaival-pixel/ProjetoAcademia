<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1StudentGamificationTest extends TestCase
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
        $user->profile()->create([
            'birth_date' => '1995-05-15',
            'sex' => 'M',
            'water_target_ml' => 2000,
        ]);

        $permission = Permission::firstOrCreate(
            ['name' => 'portal.access'],
            ['label' => 'Acesso ao Portal Usuário']
        );
        $user->permissions()->syncWithoutDetaching([$permission->id]);

        return $user;
    }

    public function test_gamification_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/student/gamification')->assertUnauthorized();
    }

    public function test_student_can_fetch_gamification_data(): void
    {
        $student = $this->studentUser();
        $student->forceFill([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonth(),
        ])->save();
        Sanctum::actingAs($student);

        $response = $this->getJson('/api/v1/student/gamification')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'rankings' => [
                        'consistency',
                        'strength',
                        'nutrition',
                        'elite',
                    ],
                    'badges' => [
                        '*' => [
                            'code',
                            'title',
                            'description',
                            'meta',
                            'current',
                            'is_unlocked',
                            'color',
                        ]
                    ]
                ]
            ]);

        $response->assertJsonCount(3, 'data.badges');
    }
}
