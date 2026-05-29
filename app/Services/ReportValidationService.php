<?php

namespace App\Services;

use App\Models\GeneratedReport;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReportValidationService
{
    /**
     * Gera uma nova versão do relatório e retorna os dados de validação.
     */
    public function generateVersion(User $user, string $type): GeneratedReport
    {
        $lastReport = GeneratedReport::where('user_id', $user->id)
            ->where('type', $type)
            ->orderBy('version', 'desc')
            ->first();

        $newVersion = $lastReport ? $lastReport->version + 1 : 1;
        $timestamp = now()->timestamp;
        $docId = $lastReport ? $lastReport->document_id : \Illuminate\Support\Str::uuid();

        $hash = GeneratedReport::generateSecureHash($docId, $newVersion, $timestamp);

        return GeneratedReport::create([
            'document_id' => $docId,
            'user_id' => $user->id,
            'type' => $type,
            'version' => $newVersion,
            'hash' => $hash,
            'generated_at' => now(),
            'metadata' => [
                'timestamp' => $timestamp,
                'ip' => request()->ip()
            ]
        ]);
    }

    /**
     * Gera a URL do QR Code para validação.
     */
    public function getValidationUrl(GeneratedReport $report): string
    {
        $timestamp = $report->metadata['timestamp'] ?? $report->generated_at->timestamp;
        
        return route('report.validate', [
            'doc' => $report->document_id,
            'v' => $report->version,
            'token' => $report->hash,
            't' => $timestamp
        ]);
    }

    /**
     * Valida um relatório.
     */
    public function validate(string $docId, int $version, string $hash, $timestamp): bool
    {
        $report = GeneratedReport::where('document_id', $docId)
            ->where('version', $version)
            ->first();

        if (!$report) {
            return false;
        }

        // Valida se o hash bate com a versão e documento
        $expectedHash = GeneratedReport::generateSecureHash($docId, $version, $timestamp);
        
        return hash_equals($expectedHash, $hash) && hash_equals($report->hash, $hash);
    }
}
