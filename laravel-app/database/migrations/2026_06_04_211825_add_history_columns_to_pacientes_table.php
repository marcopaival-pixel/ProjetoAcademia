<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (!Schema::hasColumn('pacientes', 'data_fim')) {
                $table->timestamp('data_fim')->nullable()->after('data_cadastro');
            }
            if (!Schema::hasColumn('pacientes', 'motivo_desvinculacao')) {
                $table->text('motivo_desvinculacao')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['data_fim', 'motivo_desvinculacao']);
        });
    }
};
