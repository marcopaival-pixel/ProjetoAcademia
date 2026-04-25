<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internal_emails', function (Blueprint $table) {
            if (Schema::hasColumn('internal_emails', 'remetente_id')) {
                $table->renameColumn('remetente_id', 'sender_id');
            }
            if (Schema::hasColumn('internal_emails', 'destinatario_id')) {
                $table->renameColumn('destinatario_id', 'recipient_id');
            }
            if (Schema::hasColumn('internal_emails', 'mensagem')) {
                $table->renameColumn('mensagem', 'content');
            }
            if (Schema::hasColumn('internal_emails', 'assunto')) {
                $table->renameColumn('assunto', 'subject');
            }
            if (Schema::hasColumn('internal_emails', 'lida')) {
                $table->renameColumn('lida', 'is_read');
            }
            if (Schema::hasColumn('internal_emails', 'data_envio')) {
                $table->renameColumn('data_envio', 'sent_at');
            }
            if (Schema::hasColumn('internal_emails', 'data_leitura')) {
                $table->renameColumn('data_leitura', 'read_at');
            }
        });

        if (Schema::hasTable('internal_email_attachments')) {
            Schema::table('internal_email_attachments', function (Blueprint $table) {
                if (Schema::hasColumn('internal_email_attachments', 'mensagem_id')) {
                    $table->renameColumn('mensagem_id', 'email_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('internal_email_attachments')) {
            Schema::table('internal_email_attachments', function (Blueprint $table) {
                if (Schema::hasColumn('internal_email_attachments', 'email_id')) {
                    $table->renameColumn('email_id', 'mensagem_id');
                }
            });
        }

        Schema::table('internal_emails', function (Blueprint $table) {
            if (Schema::hasColumn('internal_emails', 'sender_id')) {
                $table->renameColumn('sender_id', 'remetente_id');
            }
            if (Schema::hasColumn('internal_emails', 'recipient_id')) {
                $table->renameColumn('recipient_id', 'destinatario_id');
            }
            if (Schema::hasColumn('internal_emails', 'content')) {
                $table->renameColumn('content', 'mensagem');
            }
            if (Schema::hasColumn('internal_emails', 'subject')) {
                $table->renameColumn('subject', 'assunto');
            }
            if (Schema::hasColumn('internal_emails', 'is_read')) {
                $table->renameColumn('is_read', 'lida');
            }
            if (Schema::hasColumn('internal_emails', 'sent_at')) {
                $table->renameColumn('sent_at', 'data_envio');
            }
            if (Schema::hasColumn('internal_emails', 'read_at')) {
                $table->renameColumn('read_at', 'data_leitura');
            }
        });
    }
};
