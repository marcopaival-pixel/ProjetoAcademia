<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('commissions')) {
            return;
        }

        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'payment_id')) {
                $table->dropForeign(['payment_id']);
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if (Schema::hasColumn('commissions', 'payment_id')) {
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE commissions MODIFY payment_id BIGINT UNSIGNED NULL');
            } elseif ($driver === 'sqlite') {
                // SQLite: recriação de coluna não suportada de forma simples; testes usam schema fresh.
            } else {
                Schema::table('commissions', function (Blueprint $table) {
                    $table->unsignedBigInteger('payment_id')->nullable()->change();
                });
            }
        }

        // Cancelar duplicatas existentes (manter a mais antiga por payment_id)
        if (Schema::hasColumn('commissions', 'payment_id')) {
            $duplicatePaymentIds = DB::table('commissions')
                ->select('payment_id')
                ->whereNotNull('payment_id')
                ->groupBy('payment_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('payment_id');

            foreach ($duplicatePaymentIds as $paymentId) {
                $keepId = DB::table('commissions')
                    ->where('payment_id', $paymentId)
                    ->orderBy('id')
                    ->value('id');

                DB::table('commissions')
                    ->where('payment_id', $paymentId)
                    ->where('id', '!=', $keepId)
                    ->update([
                        'status' => 'CANCELADO',
                        'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [Cancelada: duplicata de payment_id]')"),
                    ]);
            }
        }

        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'payment_id')) {
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
                $table->unique('payment_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('commissions') || ! Schema::hasColumn('commissions', 'payment_id')) {
            return;
        }

        Schema::table('commissions', function (Blueprint $table) {
            $table->dropUnique(['payment_id']);
            $table->dropForeign(['payment_id']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE commissions MODIFY payment_id BIGINT UNSIGNED NOT NULL');
        }
        Schema::table('commissions', function (Blueprint $table) {
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
        });
    }
};
