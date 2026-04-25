<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gerenciamento de Bots
        if (!Schema::hasTable('omni_bots')) {
            Schema::create('omni_bots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->string('name');
                $table->string('whatsapp_phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('business_hours')->nullable(); // Horários de atendimento
                $table->text('out_of_office_message')->nullable();
                $table->timestamps();
            });
        }

        // Passos do Fluxo (Nodes)
        if (!Schema::hasTable('omni_bot_steps')) {
            Schema::create('omni_bot_steps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bot_id')->index();
                $table->string('label'); // Nome interno para organização
                $table->enum('type', ['message', 'menu', 'question', 'transfer'])->default('message');
                $table->text('content'); // O texto que o bot envia
                $table->boolean('is_start')->default(false); // Se é o passo inicial
                $table->unsignedBigInteger('next_step_id')->nullable(); // Para fluxos lineares (type=message)
                $table->timestamps();
            });
        }

        // Opções do Menu (Edges)
        if (!Schema::hasTable('omni_bot_options')) {
            Schema::create('omni_bot_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('step_id')->index();
                $table->string('trigger_value'); // Ex: "1", "preços", "Sim"
                $table->string('label'); // Texto do botão
                $table->unsignedBigInteger('destination_step_id')->index();
                $table->timestamps();
            });
        }

        // Vincular Conversas ao Passo Atual
        if (Schema::hasTable('omni_conversations')) {
            Schema::table('omni_conversations', function (Blueprint $table) {
                if (!Schema::hasColumn('omni_conversations', 'current_bot_step_id')) {
                    $table->unsignedBigInteger('current_bot_step_id')->nullable()->after('status');
                }
                if (!Schema::hasColumn('omni_conversations', 'bot_id')) {
                    $table->unsignedBigInteger('bot_id')->nullable()->after('company_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('omni_bot_options');
        Schema::dropIfExists('omni_bot_steps');
        Schema::dropIfExists('omni_bots');
    }
};
