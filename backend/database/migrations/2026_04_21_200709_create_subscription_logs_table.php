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
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('event'); // created, status_change, payment_attempt, payment_success, payment_failure, refund, cancelled
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->json('payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};
