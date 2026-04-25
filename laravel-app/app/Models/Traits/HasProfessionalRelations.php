<?php

namespace App\Models\Traits;

use App\Models\User;
use App\Models\ProfessionalPatientRequest;

trait HasProfessionalRelations
{
    /**
     * Gera um código único para o profissional se ele tiver acesso premium.
     */
    public function generateProfessionalCode(): string
    {
        if (!$this->professional_code) {
            $code = 'PROF-' . strtoupper(substr(md5(uniqid($this->id, true)), 0, 8));
            $this->professional_code = $code;
            $this->save();
        }
        return $this->professional_code;
    }

    public function getProfessionalQrCodeUrl(): string
    {
        $code = $this->professional_code ?: $this->generateProfessionalCode();
        $url = route('professional.link', ['code' => $code]);
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($url);
    }

    /**
     * Verifica se o usuário pode enviar mensagens para outro usuário.
     */
    public function canMessage(User $otherUser): bool
    {
        // Administradores podem falar com qualquer um
        if ($this->isAdministrator()) {
            return true;
        }

        // Se o destinatário for Suporte ou Financeiro, qualquer um pode falar (inicialmente)
        if (in_array($otherUser->department, ['support', 'finance'])) {
            return true;
        }

        // Se ambos estiverem no mesmo grupo aprovado, podem falar
        $sharedGroups = $this->communicationGroups()
            ->wherePivot('status', 'approved')
            ->whereHas('users', function($q) use ($otherUser) {
                $q->where('users.id', $otherUser->id)
                  ->where('communication_group_user.status', 'approved');
            })
            ->exists();

        return $sharedGroups;
    }

    public function isSupport(): bool
    {
        return $this->department === 'support';
    }

    public function isFinance(): bool
    {
        return $this->department === 'finance';
    }
}
