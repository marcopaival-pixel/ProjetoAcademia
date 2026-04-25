<?php

namespace App\Services;

use App\Models\EmailTemplate;

class EmailTemplateService
{
    public static function findActive(string $tipo, ?int $empresaId): ?EmailTemplate
    {
        if ($empresaId) {
            $local = EmailTemplate::query()
                ->where('tipo', $tipo)
                ->where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->first();
            if ($local !== null) {
                return $local;
            }
        }

        return EmailTemplate::query()
            ->where('tipo', $tipo)
            ->whereNull('empresa_id')
            ->where('ativo', true)
            ->first();
    }
}
