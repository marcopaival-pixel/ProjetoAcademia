<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('pdf_templates', 'auto_whatsapp_recipients')) {
                $table->json('auto_whatsapp_recipients')->nullable()->after('whatsapp_message_template');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            if (Schema::hasColumn('pdf_templates', 'auto_whatsapp_recipients')) {
                $table->dropColumn('auto_whatsapp_recipients');
            }
        });
    }
};
