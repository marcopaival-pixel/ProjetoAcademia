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
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('identifier')->index()->comment('Identificador único do formulário ou registro');
            $table->json('payload')->comment('Os dados do rascunho (pode ser criptografado se necessário)');
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
            
            // Um usuário pode ter apenas um rascunho por identificador ao mesmo tempo
            $table->unique(['user_id', 'identifier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
