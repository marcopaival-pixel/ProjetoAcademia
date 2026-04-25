<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewStudentRegistrationPending extends Notification
{
    use Queueable;

    public function __construct(public User $newUser) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'registration_pending',
            'title' => 'Novo cadastro pendente',
            'body' => $this->newUser->name.' solicitou acesso à plataforma.',
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
            'action_url' => url('/admin/registrations/pending'),
        ];
    }
}
