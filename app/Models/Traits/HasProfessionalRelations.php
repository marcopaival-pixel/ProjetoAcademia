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
        // Administradores podem falar com qualquer um, e qualquer um com administrador
        if ($this->isAdministrator() || $otherUser->isAdministrator()) {
            return true;
        }

        // Suporte ou Financeiro via departamento
        if (in_array($otherUser->department, ['support', 'finance'])) {
            return true;
        }

        // Por padrão, não permite chat direto entre usuários comuns (alunos)
        return false;
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
