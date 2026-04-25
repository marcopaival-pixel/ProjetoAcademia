<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O índice gerado_reports_document_id_unique já foi removido manualmente ou por execução parcial
        Schema::table('generated_reports', function (Blueprint $table) {
            // Garante que o novo índice composto existe se não existir
            $indices = Schema::getConnection()->getSchemaBuilder()->getIndexListing('generated_reports');
            if (!in_array('generated_reports_document_id_version_unique', $indices)) {
                $table->unique(['document_id', 'version']);
            }
        });
    }

    public function down(): void
    {
    }
};
