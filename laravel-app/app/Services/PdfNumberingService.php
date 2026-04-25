<?php

namespace App\Services;

use App\Enums\PdfDocumentType;
use App\Models\AcademyCompany;
use App\Models\PdfNumberSequence;
use Illuminate\Support\Facades\DB;

class PdfNumberingService
{
    /**
     * Gera número único por empresa, tipo e ano (ex.: REC-2026-000001).
     */
    public function nextOfficialNumber(AcademyCompany $company, PdfDocumentType $type, int $year): string
    {
        return DB::transaction(function () use ($company, $type, $year) {
            $row = PdfNumberSequence::query()
                ->where('academy_company_id', $company->id)
                ->where('tipo_documento', $type->value)
                ->where('ano', $year)
                ->lockForUpdate()
                ->first();

            if ($row === null) {
                PdfNumberSequence::create([
                    'academy_company_id' => $company->id,
                    'tipo_documento' => $type->value,
                    'ano' => $year,
                    'sequencia_atual' => 0,
                ]);
                $row = PdfNumberSequence::query()
                    ->where('academy_company_id', $company->id)
                    ->where('tipo_documento', $type->value)
                    ->where('ano', $year)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $row->increment('sequencia_atual');
            $row->refresh();

            $seq = str_pad((string) $row->sequencia_atual, 6, '0', STR_PAD_LEFT);

            return $type->numberPrefix().'-'.$year.'-'.$seq;
        });
    }
}
