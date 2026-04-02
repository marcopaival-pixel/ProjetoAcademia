<?php

namespace Tests\Feature;

use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyReportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_pdf_forbidden_without_premium_access(): void
    {
        $user = User::factory()->create(['is_premium' => false, 'is_admin' => false]);
        $this->actingAs($user)->get(route('report.monthly.pdf', ['month' => '2026-03']))
            ->assertForbidden();
    }

    public function test_monthly_pdf_bad_month_parameter(): void
    {
        $user = User::factory()->create(['is_premium' => true, 'premium_expires_at' => now()->addYear()]);
        $this->actingAs($user)->get(route('report.monthly.pdf', ['month' => '13-2020']))
            ->assertStatus(400);
    }

    public function test_monthly_pdf_rejects_future_month(): void
    {
        $user = User::factory()->create(['is_premium' => true, 'premium_expires_at' => now()->addYear()]);
        $this->actingAs($user)->get(route('report.monthly.pdf', ['month' => now()->addYear()->format('Y-m')]))
            ->assertStatus(400);
    }

    public function test_monthly_pdf_returns_pdf_for_premium_when_dompdf_installed(): void
    {
        if (! class_exists(Dompdf::class)) {
            $this->markTestSkipped('Instale dependências: composer update (dompdf/dompdf).');
        }

        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ]);

        $this->actingAs($user)->get(route('report.monthly.pdf', ['month' => now()->format('Y-m')]))
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $this->response->headers->get('Content-Type'));
    }

    public function test_monthly_pdf_administrator_receives_pdf_when_dompdf_installed(): void
    {
        if (! class_exists(Dompdf::class)) {
            $this->markTestSkipped('Instale dependências: composer update (dompdf/dompdf).');
        }

        $admin = User::factory()->administrator()->create(['is_premium' => false]);
        $this->actingAs($admin)->get(route('report.monthly.pdf', ['month' => now()->format('Y-m')]))
            ->assertOk();
    }
}
