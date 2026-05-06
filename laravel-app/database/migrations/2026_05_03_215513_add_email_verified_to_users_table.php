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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified')) {
                $table->boolean('email_verified')->default(false)->after('email_verified_at');
            }
        });

        // Garantir que a configuração exista
        \App\Models\AdminSetting::updateOrCreate(
            ['key' => 'verificacao_email_ativa'],
            [
                'value' => 'true',
                'label' => 'Exigir verificação de e-mail',
                'type' => 'boolean'
            ]
        );
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified');
        });
    }
};
