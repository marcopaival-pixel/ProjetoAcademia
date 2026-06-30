<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Remediação auditoria BD (2026-06-29): evita CASCADE que apaga histórico
 * financeiro/clínico em hard delete, alinhado a SoftDeletes em Payment/Commission.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->replaceForeignOnDelete('payments', 'user_id', 'users', 'restrict');
        $this->replaceForeignOnDelete('commissions', 'payment_id', 'payments', 'restrict');
        $this->replaceForeignOnDelete('commissions', 'user_id', 'users', 'restrict');
        $this->replaceForeignOnDelete('commissions', 'representative_id', 'users', 'restrict');
        $this->replaceForeignOnDelete('professional_appointments', 'professional_id', 'users', 'restrict');
        $this->replaceForeignOnDelete('professional_appointments', 'patient_id', 'users', 'restrict');
        $this->replaceForeignOnDelete('body_assessments', 'user_id', 'users', 'restrict');

        if (Schema::hasTable('omnichannel_tables') && (int) DB::table('omnichannel_tables')->count() === 0) {
            Schema::drop('omnichannel_tables');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('omnichannel_tables')) {
            Schema::create('omnichannel_tables', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        $this->replaceForeignOnDelete('body_assessments', 'user_id', 'users', 'cascade');
        $this->replaceForeignOnDelete('professional_appointments', 'patient_id', 'users', 'cascade');
        $this->replaceForeignOnDelete('professional_appointments', 'professional_id', 'users', 'cascade');
        $this->replaceForeignOnDelete('commissions', 'representative_id', 'users', 'cascade');
        $this->replaceForeignOnDelete('commissions', 'user_id', 'users', 'cascade');
        $this->replaceForeignOnDelete('commissions', 'payment_id', 'payments', 'cascade');
        $this->replaceForeignOnDelete('payments', 'user_id', 'users', 'cascade');
    }

    private function replaceForeignOnDelete(
        string $table,
        string $column,
        string $references,
        string $onDelete
    ): void {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table, $column, $references, $onDelete) {
            if ($this->foreignKeyExists($table, $column)) {
                $blueprint->dropForeign([$column]);
            }

            $foreign = $blueprint->foreign($column)->references('id')->on($references);
            if ($onDelete === 'restrict') {
                $foreign->restrictOnDelete();
            } else {
                $foreign->cascadeOnDelete();
            }
        });
    }

    private function foreignKeyExists(string $table, string $column): bool
    {
        $database = Schema::getConnection()->getDatabaseName();

        $rows = DB::select(
            'SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL
             LIMIT 1',
            [$database, $table, $column]
        );

        return $rows !== [];
    }
};
