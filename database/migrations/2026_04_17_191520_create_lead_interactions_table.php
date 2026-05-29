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
        Schema::create('lead_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->unsignedInteger('user_id'); // Representante que fez o contato
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('tipo_contato'); // Ligação, Email, WhatsApp, Reunião, etc.
            $table->text('descricao');
            $table->timestamp('data_contato');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_interactions');
    }
};
