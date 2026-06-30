<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserConsent;
use App\Services\Lgpd\LgpdDeletionWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Concerns\SeedsRbacForTests;
use Tests\TestCase;

class LgpdDeletionWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use SeedsRbacForTests;

    private function createDeletionRequest(User $user, ?string $reason = null): void
    {
        UserConsent::create([
            'user_id' => $user->id,
            'consent_type' => LgpdDeletionWorkflowService::CONSENT_DELETION_REQUEST,
            'version' => '1.0',
            'ip_address' => '127.0.0.1',
        ]);
    }

    public function test_workflow_processes_pending_deletion_request(): void
    {
        $user = $this->userWithRole('aluno', ['status' => 'active']);
        $this->createDeletionRequest($user);

        $workflow = app(LgpdDeletionWorkflowService::class);
        $this->assertSame(1, $workflow->pendingCount());

        $outcome = $workflow->processUser($user, null, 'Teste workflow');
        $this->assertSame('processed', $outcome);

        $user->refresh();
        $this->assertTrue($user->isAnonymized());
        $this->assertSame(0, $workflow->pendingCount());
    }

    public function test_command_dry_run_lists_pending_requests(): void
    {
        $user = $this->userWithRole('aluno', ['status' => 'active']);
        $this->createDeletionRequest($user);

        Artisan::call('app:lgpd:process-deletions', ['--dry-run' => true]);

        $this->assertStringContainsString((string) $user->id, Artisan::output());
    }

    public function test_platform_admin_can_process_deletion_via_panel(): void
    {
        $target = $this->userWithRole('aluno', ['status' => 'active']);
        $this->createDeletionRequest($target);

        $admin = User::factory()->administrator()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['active_role' => 'admin'])
            ->post(route('admin.lgpd.deletion-requests.process', $target));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $target->refresh();
        $this->assertTrue($target->isAnonymized());
    }

    public function test_batch_process_anonymizes_multiple_users(): void
    {
        $userA = $this->userWithRole('aluno', ['status' => 'active']);
        $userB = $this->userWithRole('aluno', ['status' => 'active']);
        $this->createDeletionRequest($userA);
        $this->createDeletionRequest($userB);

        $result = app(LgpdDeletionWorkflowService::class)->processUsers(
            [$userA->id, $userB->id],
            null,
            'Teste lote'
        );

        $this->assertSame(2, $result['processed']);
        $this->assertTrue($userA->fresh()->isAnonymized());
        $this->assertTrue($userB->fresh()->isAnonymized());
    }
}
