<?php

namespace App\Observers;

use App\Models\ProfessionalFinanceEntry;
use App\Services\FinancialLogService;

class ProfessionalFinanceEntryObserver
{
    public function created(ProfessionalFinanceEntry $entry): void
    {
        $this->log('PROFESSIONAL_FINANCE_ENTRY_CREATED', $entry);
    }

    public function updated(ProfessionalFinanceEntry $entry): void
    {
        $this->log('PROFESSIONAL_FINANCE_ENTRY_UPDATED', $entry, [
            'changes' => $entry->getChanges(),
        ]);
    }

    public function deleted(ProfessionalFinanceEntry $entry): void
    {
        $this->log('PROFESSIONAL_FINANCE_ENTRY_DELETED', $entry);
    }

    private function log(string $action, ProfessionalFinanceEntry $entry, array $extra = []): void
    {
        FinancialLogService::log([
            'user_id' => $entry->professional_id,
            'action' => $action,
            'amount' => $entry->amount,
            'status_before' => $entry->getOriginal('status'),
            'status_after' => $entry->status,
            'origin' => 'professional_finance',
            'payload' => array_merge([
                'entry_id' => $entry->id,
                'type' => $entry->type,
                'description' => $entry->description,
                'category_id' => $entry->category_id,
            ], $extra),
        ]);
    }
}
