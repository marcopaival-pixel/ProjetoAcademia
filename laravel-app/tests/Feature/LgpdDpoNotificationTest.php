<?php

namespace Tests\Feature;

use App\Mail\LgpdDeletionRequestMail;
use App\Models\User;
use App\Services\Lgpd\LgpdDpoNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LgpdDpoNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletion_request_notifies_dpo_email(): void
    {
        Mail::fake();

        config(['mail.lgpd.dpo_address' => 'dpo@test.local']);

        $user = User::factory()->create([
            'name' => 'Titular Teste',
            'email' => 'titular@test.local',
        ]);

        $sent = app(LgpdDpoNotificationService::class)->notifyDeletionRequest($user, 'Motivo de teste');

        $this->assertTrue($sent);

        Mail::assertSent(LgpdDeletionRequestMail::class, function (LgpdDeletionRequestMail $mail) use ($user) {
            return $mail->user->id === $user->id && $mail->reason === 'Motivo de teste';
        });
    }

    public function test_deletion_request_via_privacy_form_triggers_dpo_notification(): void
    {
        Mail::fake();

        config(['mail.lgpd.dpo_address' => 'dpo@test.local']);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('privacy.request-deletion'), ['reason' => 'Quero apagar meus dados'])
            ->assertRedirect();

        Mail::assertSent(LgpdDeletionRequestMail::class);
    }
}
