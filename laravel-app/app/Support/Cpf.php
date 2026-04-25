<?php

namespace App\Support;

/**
 * CPF brasileiro: normalização e validação dos dígitos verificadores.
 */
final class Cpf
{
    public static function normalize(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return preg_replace('/\D/', '', $value) ?? '';
    }

    public static function isValid(?string $value): bool
    {
        $cpf = self::normalize($value);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$c] !== $d) {
                return false;
            }
        }

        return true;
    }
}
