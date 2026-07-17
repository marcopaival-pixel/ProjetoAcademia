<?php

namespace App\Enums;

enum PdfSignatureRole: string
{
    case Aluno = 'aluno';
    case Responsavel = 'responsavel';
    case Instrutor = 'instrutor';
    case Administrador = 'administrador';

    public function label(): string
    {
        return match ($this) {
            self::Aluno => 'Aluno',
            self::Responsavel => 'Responsável',
            self::Instrutor => 'Instrutor',
            self::Administrador => 'Administrador',
        };
    }
}
