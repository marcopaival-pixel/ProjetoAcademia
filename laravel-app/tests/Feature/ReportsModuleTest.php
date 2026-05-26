<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ReportsModuleTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    public function test_patient_export_pdf_redirects_to_monthly_pdf_for_premium_aluno(): void
    {
        $user = $this->userWithRole('aluno');
        $month = now()->format('Y-m');

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'export_pdf']))
            ->assertRedirect(route('report.monthly.pdf', ['month' => $month]));
    }

    public function test_patient_show_unknown_type_returns_404_when_premium(): void
    {
        $user = $this->userWithRole('aluno');

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'tipo_inexistente_xyz']))
            ->assertNotFound();
    }

    public function test_patient_show_full_history_returns_coming_soon_when_premium(): void
    {
        $user = $this->userWithRole('aluno');

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'full_history']))
            ->assertOk()
            ->assertSee('Módulo em', false);
    }

    public function test_patient_show_redirects_to_index_when_not_premium(): void
    {
        $user = $this->userWithRole('aluno', [
            'is_premium' => false,
            'premium_expires_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('patient.reports.show', ['type' => 'full_history']))
            ->assertRedirect(route('plano'));
    }

    public function test_professional_show_unknown_type_returns_404_when_premium(): void
    {
        $user = $this->userWithRole('professional');

        $this->actingAs($user)
            ->get(route('professional.reports.show', ['type' => 'invalido']))
            ->assertNotFound();
    }

    public function test_professional_export_pdf_not_in_list_returns_404(): void
    {
        $user = $this->userWithRole('professional');

        $this->actingAs($user)
            ->get(route('professional.reports.show', ['type' => 'export_pdf']))
            ->assertNotFound();
    }

    public function test_professional_show_scheduled_reports_returns_ok_when_premium(): void
    {
        if (! view()->exists('layouts.professional')) {
            $this->markTestSkipped('View layouts.professional ausente no repositório.');
        }

        $user = $this->userWithRole('professional');

        $this->actingAs($user)
            ->get(route('professional.reports.show', ['type' => 'scheduled_reports']))
            ->assertOk();
    }
}
