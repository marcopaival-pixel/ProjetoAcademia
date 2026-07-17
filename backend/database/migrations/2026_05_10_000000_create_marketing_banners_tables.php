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
        Schema::create('marketing_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image_desktop')->nullable();
            $table->string('image_mobile')->nullable();
            $table->string('background_color')->default('#ffffff');
            $table->string('icon')->nullable();
            
            // Buttons
            $table->string('primary_button_text')->nullable();
            $table->string('primary_button_link')->nullable();
            $table->string('secondary_button_text')->nullable();
            $table->string('secondary_button_link')->nullable();
            
            // Schedule and Status
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            
            // Display Rules
            $table->boolean('allow_dismiss')->default(true);
            $table->boolean('dont_show_again_option')->default(false);
            $table->string('display_type')->default('always'); // once, until_closed, always, frequency
            $table->integer('frequency_days')->default(0);
            
            // Segmentation (Tenant, Plan, Language, etc.)
            $table->json('segmentation')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('marketing_banner_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id');
            $table->unsignedBigInteger('role_id'); // roles.id is bigint
            $table->timestamps();

            $table->foreign('banner_id')->references('id')->on('marketing_banners')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('marketing_banner_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id');
            $table->unsignedInteger('user_id')->nullable(); // users.id is int
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('banner_id')->references('id')->on('marketing_banners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('marketing_banner_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id');
            $table->unsignedInteger('user_id')->nullable(); // users.id is int
            $table->string('button_type')->default('primary'); // primary, secondary
            $table->timestamps();

            $table->foreign('banner_id')->references('id')->on('marketing_banners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('marketing_banner_dismissals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id');
            $table->unsignedInteger('user_id')->nullable(); // users.id is int
            $table->boolean('dont_show_again')->default(false);
            $table->timestamps();

            $table->foreign('banner_id')->references('id')->on('marketing_banners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_banner_dismissals');
        Schema::dropIfExists('marketing_banner_clicks');
        Schema::dropIfExists('marketing_banner_views');
        Schema::dropIfExists('marketing_banner_targets');
        Schema::dropIfExists('marketing_banners');
    }
};
