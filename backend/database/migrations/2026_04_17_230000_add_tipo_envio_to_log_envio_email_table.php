<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_envio_email', function (Blueprint $table) {
            if (! Schema::hasColumn('log_envio_email', 'tipo_envio')) {
                $table->string('tipo_envio', 64)->nullable()->after('usuario_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('log_envio_email', function (Blueprint $table) {
            if (Schema::hasColumn('log_envio_email', 'tipo_envio')) {
                $table->dropColumn('tipo_envio');
            }
        });
    }
};
