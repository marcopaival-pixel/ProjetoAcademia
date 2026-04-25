<?php

namespace App\Mail;

use App\Models\User;
use App\Services\EmailTemplateService;
use App\Support\EmailTemplateType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        $subject = 'Confirme seu email';
        $tpl = EmailTemplateService::findActive(EmailTemplateType::CONFIRMACAO_CADASTRO, $this->user->academy_company_id);
        if ($tpl !== null) {
            $subject = $tpl->renderSubject($this->templateVars());
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $tpl = EmailTemplateService::findActive(EmailTemplateType::CONFIRMACAO_CADASTRO, $this->user->academy_company_id);
        if ($tpl !== null) {
            return new Content(
                htmlString: $tpl->renderBody($this->templateVars()),
            );
        }

        $ttlHours = (int) config('email_verification.token_ttl_hours', 24);

        return new Content(
            view: 'emails.verify-email',
            with: [
                'verificationUrl' => route('verification.verify', [
                    'token' => $this->user->email_verification_token,
                ]),
                'ttlHours' => $ttlHours,
            ],
        );
    }

    /**
     * @return array<string, string>
     */
    private function templateVars(): array
    {
        $ttlHours = (int) config('email_verification.token_ttl_hours', 24);

        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'verification_url' => route('verification.verify', [
                'token' => $this->user->email_verification_token,
            ]),
            'ttl_hours' => (string) $ttlHours,
            'app_name' => (string) config('app.name'),
        ];
    }
}
