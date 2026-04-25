<?php

namespace App\Rules;

use App\Support\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Cpf::isValid(is_string($value) ? $value : '')) {
            $fail('O CPF informado não é válido.');
        }
    }
}
