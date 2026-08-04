<?php

namespace App\Services;

class BugLogMaskingService
{
    /** @var array<int, array{pattern: string, replacement: string}> */
    private array $rules = [
        ['pattern' => '/"(password|password_confirmation|senha|current_password)"\s*:\s*"[^"]*"/i', 'replacement' => '"$1":"[REMOVIDA]"'],
        ['pattern' => '/"(token|api_token|access_token|refresh_token|secret)"\s*:\s*"[^"]*"/i', 'replacement' => '"$1":"[REMOVIDO]"'],
        ['pattern' => '/Bearer\s+[A-Za-z0-9\-._~+\/]+=*/i', 'replacement' => 'Bearer [REMOVIDO]'],
        ['pattern' => '/Authorization:\s*[^\s,]+/i', 'replacement' => 'Authorization: [REMOVIDO]'],
        ['pattern' => '/Cookie:\s*[^\n\r]+/i', 'replacement' => 'Cookie: [REMOVIDO]'],
        ['pattern' => '/\b\d{3}\.\d{3}\.\d{3}-\d{2}\b/', 'replacement' => '***.***.***-**'],
        ['pattern' => '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/', 'replacement' => '[EMAIL_MASCARADO]'],
        ['pattern' => '/\(\d{2}\)\s?\d{4,5}-\d{4}/', 'replacement' => '(***) *****-****'],
        ['pattern' => '/"(cpf|cnpj|rg|crm|prontuario|diagnostico|diagnóstico|laudo)"\s*:\s*"[^"]*"/iu', 'replacement' => '"$1":"[DADO_SENSÍVEL]"'],
    ];

    public function mask(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        foreach ($this->rules as $rule) {
            $text = preg_replace($rule['pattern'], $rule['replacement'], $text) ?? $text;
        }

        return $this->maskEmailsInText($text);
    }

    /** @param  mixed  $data */
    public function maskPayload(mixed $data): mixed
    {
        if (is_string($data)) {
            return $this->mask($data);
        }

        if (is_array($data)) {
            $out = [];
            foreach ($data as $key => $value) {
                if ($this->isSensitiveKey((string) $key)) {
                    $out[$key] = '[REMOVIDO]';
                    continue;
                }
                $out[$key] = $this->maskPayload($value);
            }

            return $out;
        }

        return $data;
    }

    private function isSensitiveKey(string $key): bool
    {
        return (bool) preg_match('/password|senha|token|secret|authorization|cookie|cpf|cnpj|prontuario|diagnostico|laudo|crm|rg/i', $key);
    }

    private function maskEmailsInText(string $text): string
    {
        return preg_replace_callback(
            '/\b([A-Za-z0-9._%+-])[A-Za-z0-9._%+-]*@([A-Za-z0-9.-]+\.[A-Za-z]{2,})\b/',
            static fn (array $m) => $m[1] . '***@' . $m[2],
            $text
        ) ?? $text;
    }
}
