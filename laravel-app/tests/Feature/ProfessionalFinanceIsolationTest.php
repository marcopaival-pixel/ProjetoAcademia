<?php

namespace Tests\Feature;

use App\Models\ProfessionalFinanceEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class ProfessionalFinanceIsolationTest extends TestCase
{
    use RefreshDatabase, SeedsRbacForTests;

    public function test_professional_cannot_see_another_professionals_finance_entries(): void
    {
        $professionalA = $this->userWithRole('professional');
        $professionalB = $this->userWithRole('professional');

        $entryA = ProfessionalFinanceEntry::withoutGlobalScopes()->create([
            'professional_id' => $professionalA->id,
            'description' => 'Receita A',
            'amount' => 150,
            'type' => 'revenue',
            'status' => 'paid',
            'due_date' => now()->toDateString(),
        ]);

        ProfessionalFinanceEntry::withoutGlobalScopes()->create([
            'professional_id' => $professionalB->id,
            'description' => 'Receita B',
            'amount' => 200,
            'type' => 'revenue',
            'status' => 'paid',
            'due_date' => now()->toDateString(),
        ]);

        $this->actingAs($professionalA);

        $visibleIds = ProfessionalFinanceEntry::pluck('id')->all();

        $this->assertSame([$entryA->id], $visibleIds);
    }

    public function test_professional_can_create_finance_entry_via_panel(): void
    {
        $professional = $this->userWithRole('professional');

        $this->actingAs($professional)
            ->post(route('professional.finance.entries.store'), [
                'description' => 'Consulta particular',
                'amount' => 120,
                'type' => 'revenue',
                'due_date' => now()->toDateString(),
                'status' => 'pending',
            ])
            ->assertRedirect(route('professional.finance.entries.index'));

        $this->assertDatabaseHas('professional_finance_entries', [
            'professional_id' => $professional->id,
            'description' => 'Consulta particular',
            'amount' => 120,
        ]);
    }
}
