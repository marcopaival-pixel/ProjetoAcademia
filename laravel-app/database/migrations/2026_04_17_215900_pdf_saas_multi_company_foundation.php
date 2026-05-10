<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('legal_name')->nullable();
            $table->string('tax_id', 64)->nullable()->index();
            $table->json('pdf_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academy_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_company_id')->constrained('academy_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 64)->nullable()->index();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pdf_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_company_id')->constrained('academy_companies')->cascadeOnDelete();
            $table->string('tipo_documento', 64);
            $table->unsignedSmallInteger('ano');
            $table->unsignedInteger('sequencia_atual')->default(0);
            $table->timestamps();
            $table->unique(['academy_company_id', 'tipo_documento', 'ano'], 'pdf_number_sequences_company_type_year');
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'academy_company_id')) {
                $table->foreignId('academy_company_id')->nullable()->after('role_id')->constrained('academy_companies')->nullOnDelete();
            }
        });

        Schema::table('pdf_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('pdf_templates', 'academy_company_id')) {
                $table->foreignId('academy_company_id')->nullable()->after('id')->constrained('academy_companies')->nullOnDelete();
            }
            if (! Schema::hasColumn('pdf_templates', 'academy_unit_id')) {
                $table->foreignId('academy_unit_id')->nullable()->after('academy_company_id')->constrained('academy_units')->nullOnDelete();
            }
            if (! Schema::hasColumn('pdf_templates', 'footer_html')) {
                $table->longText('footer_html')->nullable()->after('accent_color');
            }
            if (! Schema::hasColumn('pdf_templates', 'auto_email_enabled')) {
                $table->boolean('auto_email_enabled')->default(false)->after('footer_html');
            }
            if (! Schema::hasColumn('pdf_templates', 'auto_email_recipients')) {
                $table->json('auto_email_recipients')->nullable()->after('auto_email_enabled');
            }
            if (! Schema::hasColumn('pdf_templates', 'auto_whatsapp_enabled')) {
                $table->boolean('auto_whatsapp_enabled')->default(false)->after('auto_email_recipients');
            }
            if (! Schema::hasColumn('pdf_templates', 'whatsapp_message_template')) {
                $table->string('whatsapp_message_template', 500)->nullable()->after('auto_whatsapp_enabled');
            }
            if (! Schema::hasColumn('pdf_templates', 'duplicated_from_id')) {
                $table->foreignId('duplicated_from_id')->nullable()->after('whatsapp_message_template')->constrained('pdf_templates')->nullOnDelete();
            }
        });

        Schema::create('historico_pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_company_id')->nullable()->constrained('academy_companies')->nullOnDelete();
            $table->foreignId('academy_unit_id')->nullable()->constrained('academy_units')->nullOnDelete();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('pdf_template_id')->nullable()->constrained('pdf_templates')->nullOnDelete();
            $table->string('document_type', 64)->index();
            $table->string('related_document_type', 64)->nullable();
            $table->unsignedBigInteger('related_document_id')->nullable()->index();
            $table->string('numero_oficial', 64)->nullable();
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo', 512);
            $table->string('codigo_validacao', 64)->unique();
            $table->string('validation_status', 32)->default('valid')->index();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('generation_status', 32)->default('complete')->index();
            $table->json('source_variables')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['academy_company_id', 'numero_oficial'], 'historico_pdfs_company_numero_idx');
        });

        Schema::create('pdf_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historico_pdf_id')->constrained('historico_pdfs')->cascadeOnDelete();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('signer_name', 191)->nullable();
            $table->string('tipo_assinatura', 32)->index();
            $table->string('modo', 32)->default('upload');
            $table->string('imagem_assinatura', 512);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('data_assinatura')->useCurrent();
            $table->timestamps();
        });

        Schema::create('pdf_signature_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historico_pdf_id')->constrained('historico_pdfs')->cascadeOnDelete();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('evento', 64);
            $table->text('detalhe')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('pdf_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historico_pdf_id')->constrained('historico_pdfs')->cascadeOnDelete();
            $table->string('channel', 32)->index();
            $table->string('email_destinatario', 191)->nullable();
            $table->string('telefone_destinatario', 64)->nullable();
            $table->timestamp('data_envio')->nullable();
            $table->string('status_envio', 32)->default('pending')->index();
            $table->unsignedSmallInteger('tentativas')->default(0);
            $table->text('ultimo_erro')->nullable();
            $table->timestamps();
        });

        Schema::table('pdf_generation_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('pdf_generation_logs', 'historico_pdf_id')) {
                $table->foreignId('historico_pdf_id')->nullable()->after('pdf_template_id')->constrained('historico_pdfs')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pdf_generation_logs', function (Blueprint $table) {
            if (Schema::hasColumn('pdf_generation_logs', 'historico_pdf_id')) {
                $table->dropForeign(['historico_pdf_id']);
                $table->dropColumn('historico_pdf_id');
            }
        });

        Schema::dropIfExists('pdf_delivery_logs');
        Schema::dropIfExists('pdf_signature_audit_logs');
        Schema::dropIfExists('pdf_signatures');

        Schema::dropIfExists('historico_pdfs');

        Schema::table('pdf_templates', function (Blueprint $table) {
            foreach ([
                'duplicated_from_id', 'whatsapp_message_template', 'auto_whatsapp_enabled',
                'auto_email_recipients', 'auto_email_enabled', 'footer_html',
                'academy_unit_id', 'academy_company_id',
            ] as $col) {
                if (Schema::hasColumn('pdf_templates', $col)) {
                    if (in_array($col, ['academy_company_id', 'academy_unit_id', 'duplicated_from_id'], true)) {
                        $table->dropForeign([$col]);
                    }
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'academy_company_id')) {
                $table->dropForeign(['academy_company_id']);
                $table->dropColumn('academy_company_id');
            }
        });

        Schema::dropIfExists('pdf_number_sequences');
        Schema::dropIfExists('academy_units');
        Schema::dropIfExists('academy_companies');
    }
};
