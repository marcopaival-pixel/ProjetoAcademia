<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('badge_slug'); // pioneer, strength_100, iron_10, etc.
            $table->timestamp('achieved_at')->useCurrent();
            
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'badge_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
