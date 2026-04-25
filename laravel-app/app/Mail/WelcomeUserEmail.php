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

class WelcomeUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $roleLabel;

    public $url;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->roleLabel = $user->userRole->label ?? $user->role ?? 'Usuário';
        $this->url = url('/login');
    }

    public function envelope(): Envelope
    {
        $subject = 'Bem-vindo ao '.config('app.name');
        $tpl = EmailTemplateService::findActive(EmailTemplateType::BOAS_VINDAS, $this->user->academy_company_id);
        if ($tpl !== null) {
            $subject = $tpl->renderSubject($this->templateVars());
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $tpl = EmailTemplateService::findActive(EmailTemplateType::BOAS_VINDAS, $this->user->academy_company_id);
        if ($tpl !== null) {
            return new Content(
                htmlString: $tpl->renderBody($this->templateVars()),
            );
        }

        return new Content(
            markdown: 'emails.welcome-user',
        );
    }

    /**
     * @return array<string, string>
     */
    private function templateVars(): array
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role_label' => $this->roleLabel,
            'login_url' => $this->url,
            'app_name' => (string) config('app.name'),
        ];
    }
}
