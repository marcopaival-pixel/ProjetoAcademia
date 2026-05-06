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
        Schema::create('withdrawal_requests', function (Blueprint $blueprint) {
            $blueprint->id();
            // A tabela users usa increments('id'), que é integer unsigned.
            $blueprint->unsignedInteger('representative_id');
            $blueprint->foreign('representative_id')->references('id')->on('users')->onDelete('cascade');
            
            $blueprint->decimal('amount', 15, 2);
            $blueprint->string('pix_key')->nullable();
            $blueprint->string('bank_info')->nullable();
            $blueprint->enum('status', ['PENDENTE', 'APROVADO', 'PAGO', 'RECUSADO'])->default('PENDENTE');
            $blueprint->text('admin_notes')->nullable();
            $blueprint->timestamp('paid_at')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
