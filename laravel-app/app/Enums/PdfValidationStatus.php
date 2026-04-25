<?php

namespace App\Enums;

enum PdfValidationStatus: string
{
    case Valid = 'valid';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'Válido',
            self::Cancelled => 'Cancelado',
            self::Expired => 'Expirado',
        };
    }
}
