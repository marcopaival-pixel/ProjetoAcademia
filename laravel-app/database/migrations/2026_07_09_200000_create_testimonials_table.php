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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name');
            $table->string('profession')->nullable();
            $table->string('avatar_path')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('testimonial');
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('tenant_id');
            $table->index('featured');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
