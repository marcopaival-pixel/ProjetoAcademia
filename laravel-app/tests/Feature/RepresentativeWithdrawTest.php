<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class RepresentativeWithdrawTest extends TestCase
{
    use RefreshDatabase, SeedsRbacForTests;

    public function test_representative_can_request_withdrawal_within_available_balance(): void
    {
        $representative = $this->userWithRole('representative');

        Commission::create([
            'representative_id' => $representative->id,
            'user_id' => $representative->id,
            'base_amount' => 100,
            'commission_rate' => 10,
            'commission_amount' => 100,
            'status' => Commission::STATUS_DISPONIVEL,
        ]);

        $this->actingAs($representative)
            ->post(route('representative.withdraw.store'), [
                'amount' => 50,
                'pix_key' => 'rep@example.test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('withdrawal_requests', [
            'representative_id' => $representative->id,
            'amount' => 50,
            'pix_key' => 'rep@example.test',
            'status' => WithdrawalRequest::STATUS_PENDENTE,
        ]);
    }

    public function test_representative_cannot_withdraw_more_than_available_balance(): void
    {
        $representative = $this->userWithRole('representative');

        Commission::create([
            'representative_id' => $representative->id,
            'user_id' => $representative->id,
            'base_amount' => 30,
            'commission_rate' => 10,
            'commission_amount' => 30,
            'status' => Commission::STATUS_DISPONIVEL,
        ]);

        $this->actingAs($representative)
            ->from(route('representative.withdraw.form'))
            ->post(route('representative.withdraw.store'), [
                'amount' => 100,
                'pix_key' => 'rep@example.test',
            ])
            ->assertRedirect(route('representative.withdraw.form'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('withdrawal_requests', 0);
    }

    public function test_withdrawal_requests_are_scoped_between_representatives(): void
    {
        $repA = $this->userWithRole('representative');
        $repB = $this->userWithRole('representative');

        $requestA = WithdrawalRequest::withoutGlobalScopes()->create([
            'representative_id' => $repA->id,
            'amount' => 25,
            'pix_key' => 'a@example.test',
            'status' => WithdrawalRequest::STATUS_PENDENTE,
        ]);

        WithdrawalRequest::withoutGlobalScopes()->create([
            'representative_id' => $repB->id,
            'amount' => 40,
            'pix_key' => 'b@example.test',
            'status' => WithdrawalRequest::STATUS_PENDENTE,
        ]);

        $this->actingAs($repA);

        $visibleIds = WithdrawalRequest::pluck('id')->all();

        $this->assertSame([$requestA->id], $visibleIds);
    }
}
