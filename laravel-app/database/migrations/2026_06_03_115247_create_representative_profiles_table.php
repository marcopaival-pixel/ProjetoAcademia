<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representative_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->string('code')->unique();
            $table->decimal('commission_rate', 5, 2)->default(0.00)->comment('Percentual base de comissão');
            $table->decimal('max_discount_rate', 5, 2)->default(0.00)->comment('Percentual máximo de desconto que pode conceder');
            $table->dateTime('code_expires_at')->nullable();
            $table->integer('max_code_usages')->nullable();
            $table->integer('current_code_usages')->default(0);
            $table->text('payment_rules')->nullable()->comment('Regras financeiras (JSON ou texto)');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representative_profiles');
    }
};
