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
        // 1. Stickers Table
        Schema::create('community_stickers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('category')->default('General');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Posts Table
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreignId('academy_company_id')->nullable()->constrained('academy_companies')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->string('status')->default('approved'); // pending, approved, rejected, draft
            $table->string('visibility')->default('public'); // public, clinic, private
            $table->string('activity_status')->nullable(); // Muscle Emoji statuses
            $table->json('hashtags')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Post Media Table
        Schema::create('community_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->string('file_path');
            $table->string('type')->default('image'); // image, sticker
            $table->timestamps();
        });

        // 4. Comments Table
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('content');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('community_comments')->onDelete('cascade');
        });

        // 5. Reactions Table (Polymorphic)
        Schema::create('community_reactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactable'); // post or comment
            $table->unsignedInteger('user_id');
            $table->string('emoji'); // ❤️, 👍, etc.
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['reactable_type', 'reactable_id', 'user_id', 'emoji'], 'reaction_unique');
        });

        // 6. Reports Table
        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('status')->default('pending'); // pending, resolved, dismissed
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 7. Social Post Queue (Instagram)
        Schema::create('social_post_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->string('platform')->default('instagram');
            $table->string('status')->default('pending'); // pending, processing, sent, failed
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_post_queue');
        Schema::dropIfExists('community_reports');
        Schema::dropIfExists('community_reactions');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_post_media');
        Schema::dropIfExists('community_posts');
        Schema::dropIfExists('community_stickers');
    }
};
