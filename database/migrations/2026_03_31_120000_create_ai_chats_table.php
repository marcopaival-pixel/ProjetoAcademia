<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ai_chats');
        Schema::create('ai_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('role'); // 'user' ou 'assistant'
            $table->longText('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chats');
    }
};
