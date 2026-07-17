<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_import_logs', function (Blueprint $col) {
            $col->id();
            $col->unsignedInteger('user_id');
            $col->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $col->string('image_path')->nullable();
            $col->text('raw_ocr_text')->nullable();
            $col->json('structured_json')->nullable();
            $col->string('status')->default('pending'); // pending, processing, completed, failed
            $col->text('error_message')->nullable();
            $col->float('ai_confidence')->nullable();
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_import_logs');
    }
};
