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
        Schema::create('app_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('category', ['free', 'freemium', 'premium', 'ai_credits'])->default('free');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_lock')->default(false);
            $table->boolean('show_badge')->default(false);
            $table->timestamps();
        });

        Schema::create('feature_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('app_features')->onDelete('cascade');
            $table->integer('limit_value')->default(0);
            $table->enum('limit_type', ['day', 'week', 'month', 'lifetime', 'none'])->default('none');
            $table->enum('action_type', ['block', 'popup', 'credits'])->default('block');
            $table->text('custom_popup_text')->nullable();
            $table->timestamps();
        });

        Schema::create('upgrade_popups', function (Blueprint $table) {
            $table->id();
            $table->string('feature_code')->unique();
            $table->string('title');
            $table->text('message');
            $table->json('benefits')->nullable();
            $table->string('button_text')->default('Fazer Upgrade');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upgrade_popups');
        Schema::dropIfExists('feature_limits');
        Schema::dropIfExists('app_features');
    }
};
