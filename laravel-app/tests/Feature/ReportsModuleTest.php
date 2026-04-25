<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsModuleTest extends TestCase
{
    use RefreshDatabase;

    private function userWithProfile(string $profileName, array $userOverrides = []): User
    {
        $profile = Profile::query()->firstOrCreate(
            ['name' => $profileName],
            ['label' => ucfirst($profileName), 'description' => 'Test profile']
        );

        return User::factory()->create(array_merge([
            'profile_id' => $profile->id,
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ], $userOverrides));
    }

    public function test_patient_export_pdf_redirects_to_monthly_pdf_for_premium_aluno(): void
    {
        $user = $this->userWithProfile('aluno');
        $month = now()->format('Y-m');

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'export_pdf']))
            ->assertRedirect(route('report.monthly.pdf', ['month' => $month]));
    }

    public function test_patient_show_unknown_type_returns_404_when_premium(): void
    {
        $user = $this->userWithProfile('aluno');

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'tipo_inexistente_xyz']))
            ->assertNotFound();
    }

    public function test_patient_show_full_history_returns_coming_soon_when_premium(): void
    {
        $user = $this->userWithProfile('aluno');

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'full_history']))
            ->assertOk()
            ->assertSee('Módulo em', false);
    }

    public function test_patient_show_redirects_to_index_when_not_premium(): void
    {
        $user = $this->userWithProfile('aluno', [
            'is_premium' => false,
            'premium_expires_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'full_history']))
            ->assertRedirect(route('patient.reports.index'))
            ->assertSessionHas('premium_required', true);
    }

    public function test_professional_show_unknown_type_returns_404_when_premium(): void
    {
        $user = $this->userWithProfile('professional');

        $this->actingAs($user)
            ->get(route('professional.reports.show', ['type' => 'invalido']))
            ->assertNotFound();
    }

    public function test_professional_export_pdf_not_in_list_returns_404(): void
    {
        $user = $this->userWithProfile('professional');

        $this->actingAs($user)
            ->get(route('professional.reports.show', ['type' => 'export_pdf']))
            ->assertNotFound();
    }

    public function test_professional_show_complete_analytics_returns_coming_soon_when_premium(): void
    {
        $user = $this->userWithProfile('professional');

        $this->actingAs($user)
            ->get(route('professional.reports.show', ['type' => 'complete_analytics']))
            ->assertOk()
            ->assertSee('Módulo em', false);
    }
}
