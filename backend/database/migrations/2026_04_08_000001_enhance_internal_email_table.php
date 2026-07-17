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
        Schema::table('internal_emails', function (Blueprint $table) {
            // Support for Trash/Outbox/Sent separation per user
            $table->timestamp('excluded_at_sender')->nullable();
            $table->timestamp('excluded_at_receiver')->nullable();
            
            // Support for Drafts/Outbox (Sent is when it's actually transmitted)
            $table->enum('status', ['draft', 'outbox', 'sent', 'failed'])->default('sent');
            
            // Support for Threading/Reply/Forwarding
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('internal_emails')->onDelete('set null');
            
            // Support for System/Automatic messages
            $table->boolean('is_system')->default(false);
            
            // LGPD: Logging of interactions (Read timestamp already exists, better use full timestamps)
            $table->timestamps(); // Adds created_at and updated_at
        });

        Schema::create('internal_email_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mensagem_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, image, doc
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->foreign('mensagem_id')->references('id')->on('internal_emails')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_email_attachments');
        Schema::table('internal_emails', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'excluded_at_sender',
                'excluded_at_receiver',
                'status',
                'parent_id',
                'is_system',
                'created_at',
                'updated_at'
            ]);
        });
    }
};
