<?php

namespace App\Services;

use App\Models\SystemError;

class SystemErrorFingerprintService
{
    public function compute(SystemError $error): string
    {
        $parts = [
            $this->exceptionType($error),
            $this->stackFile($error),
            (string) $this->stackLine($error),
            $this->stackMethod($error),
            $this->routeKey($error),
            $this->normalizeMessage($error->message),
        ];

        return hash('sha256', implode('|', $parts));
    }

    public function exceptionType(SystemError $error): string
    {
        if (preg_match('/^([A-Za-z\\\\]+Exception|Error|TypeError|ParseError)/', $error->message, $m)) {
            return class_basename($m[1]);
        }

        return $error->type ?: 'Unknown';
    }

    public function stackFile(SystemError $error): string
    {
        if ($error->stack_trace && preg_match('/\/([^\/\\\\]+\.php):(\d+)/', $error->stack_trace, $m)) {
            return $m[1];
        }

        return 'unknown.php';
    }

    public function stackLine(SystemError $error): ?int
    {
        if ($error->stack_trace && preg_match('/\.php:(\d+)/', $error->stack_trace, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    public function stackMethod(SystemError $error): string
    {
        if ($error->stack_trace && preg_match('/::(\w+)\(/', $error->stack_trace, $m)) {
            return $m[1];
        }
        if ($error->stack_trace && preg_match('/->(\w+)\(/', $error->stack_trace, $m)) {
            return $m[1];
        }

        return 'unknown';
    }

    private function routeKey(SystemError $error): string
    {
        $method = strtoupper($error->method ?? 'GET');
        $path = parse_url((string) $error->url, PHP_URL_PATH) ?: (string) $error->url;
        $path = preg_replace('/\/\d+/', '/{id}', $path) ?? $path;

        return $method . ':' . $path;
    }

    private function normalizeMessage(?string $message): string
    {
        $msg = strtolower((string) $message);
        $msg = preg_replace('/\d+/', '', $msg) ?? $msg;
        $msg = preg_replace('/\s+/', ' ', $msg) ?? $msg;

        return trim($msg);
    }
}
