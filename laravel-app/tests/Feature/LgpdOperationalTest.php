<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Fluxos operacionais LGPD (não substitui revisão jurídica).
 *
 * @group release
 */
class LgpdOperationalTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_download_personal_data_json(): void
    {
        $user = User::factory()->create([
            'name' => 'Titular LGPD',
            'email' => 'lgpd-smoke@example.test',
        ]);

        $response = $this->actingAs($user)->get(route('privacy.download'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('Titular LGPD', $response->streamedContent());
        $this->assertStringContainsString('lgpd-smoke@example.test', $response->streamedContent());
    }

    public function test_authenticated_user_can_request_account_deletion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('privacy.request-deletion'), ['reason' => 'Teste homologação'])
            ->assertRedirect();

        $this->assertDatabaseHas('admin_logs', [
            'user_id' => $user->id,
        ]);

        $log = DB::table('admin_logs')->where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('exclus', (string) $log->action);
    }

    public function test_legal_pages_are_public(): void
    {
        $this->get(route('legal.privacy'))->assertOk();
        $this->get(route('legal.terms'))->assertOk();
        $this->get(route('legal.cookies'))->assertOk();
    }
}
