<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_errors', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('user_id')->nullable();
            $col->string('type', 32)->index(); // sql, system, validation, auth, integration
            $col->string('url')->nullable();
            $col->string('method', 10)->nullable();
            $col->text('message');
            $col->longText('stack_trace')->nullable();
            $col->json('payload')->nullable();
            $col->string('ip', 45)->nullable();
            $col->string('user_agent')->nullable();
            $col->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_errors');
    }
};
