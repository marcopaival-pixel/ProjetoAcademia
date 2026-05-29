<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Companies (Tenants)
        if (!Schema::hasTable('omni_companies')) {
            Schema::create('omni_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('logo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('settings')->nullable(); // welcome_message, timezone, etc.
                $table->timestamps();
            });
        } else {
            Schema::table('omni_companies', function (Blueprint $table) {
                if (!Schema::hasColumn('omni_companies', 'logo')) $table->string('logo')->nullable();
                if (!Schema::hasColumn('omni_companies', 'settings')) $table->json('settings')->nullable();
            });
        }

        // 2. Channels
        if (!Schema::hasTable('omni_channels')) {
            Schema::create('omni_channels', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('omni_companies')->onDelete('cascade');
                $table->enum('type', ['whatsapp', 'widget', 'api'])->default('widget');
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->json('config')->nullable(); // API keys, webhooks, widget styles
                $table->timestamps();
            });
        }

        // 3. Queues (Departments)
        if (!Schema::hasTable('omni_queues')) {
            Schema::create('omni_queues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('omni_companies')->onDelete('cascade');
                $table->string('name');
                $table->string('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Agents
        if (!Schema::hasTable('omni_agents')) {
            Schema::create('omni_agents', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id'); // Legacy user ID
                $table->foreignId('company_id')->constrained('omni_companies')->onDelete('cascade');
                $table->enum('status', ['online', 'offline', 'busy'])->default('offline');
                $table->integer('max_simultaneous_chats')->default(5);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 5. Conversations
        if (!Schema::hasTable('omni_conversations')) {
            Schema::create('omni_conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('omni_companies')->onDelete('cascade');
                $table->foreignId('channel_id')->constrained('omni_channels')->onDelete('cascade');
                $table->string('customer_external_id')->index(); // WhatsApp number or internal site ID
                $table->string('customer_name')->nullable();
                $table->foreignId('agent_id')->nullable()->constrained('omni_agents')->onDelete('set null');
                $table->foreignId('queue_id')->nullable()->constrained('omni_queues')->onDelete('set null');
                $table->enum('status', ['pending', 'open', 'closed', 'bot'])->default('bot');
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
            });
        }

        // 6. Messages
        if (!Schema::hasTable('omni_messages')) {
            Schema::create('omni_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('omni_conversations')->onDelete('cascade');
                $table->enum('sender_type', ['customer', 'agent', 'bot', 'system']);
                $table->unsignedInteger('sender_id')->nullable(); // Agent ID or User ID
                $table->text('content');
                $table->string('content_type')->default('text'); // text, image, file, audio
                $table->string('file_path')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // 7. Chatbot Rules & Keywords
        if (!Schema::hasTable('omni_chatbot_rules')) {
            Schema::create('omni_chatbot_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('omni_companies')->onDelete('cascade');
                $table->string('trigger_type')->default('keyword'); // keyword, welcome, ai
                $table->string('pattern')->nullable(); // word or phrase
                $table->text('response');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 8. Business Hours
        if (!Schema::hasTable('omni_business_hours')) {
            Schema::create('omni_business_hours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('omni_companies')->onDelete('cascade');
                $table->tinyInteger('day_of_week'); // 0 (Sunday) to 6 (Saturday)
                $table->time('open_time');
                $table->time('close_time');
                $table->boolean('is_closed')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('omni_business_hours');
        Schema::dropIfExists('omni_chatbot_rules');
        Schema::dropIfExists('omni_messages');
        Schema::dropIfExists('omni_conversations');
        Schema::dropIfExists('omni_agents');
        Schema::dropIfExists('omni_queues');
        Schema::dropIfExists('omni_channels');
        Schema::dropIfExists('omni_companies');
    }
};
