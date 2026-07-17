<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('generated_reports');
        
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('document_id'); // Identificador da série do documento (não único sozinho)
            $table->unsignedInteger('user_id');
            $table->string('type');
            $table->integer('version')->default(1);
            $table->string('hash');
            $table->timestamp('generated_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // A combinação de Document ID e Versão é que deve ser única
            $table->unique(['document_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
