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
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_type')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->integer('status_code')->nullable();
            $table->string('status_message')->nullable();
            $table->decimal('processing_time', 8, 4)->nullable();
            $table->text('error')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
    }
};
