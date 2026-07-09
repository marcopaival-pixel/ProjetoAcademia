<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('fake');
            $table->string('environment', 32)->default('sandbox');
            $table->string('issuer_cnpj', 20)->nullable();
            $table->string('issuer_legal_name')->nullable();
            $table->string('municipal_registration')->nullable();
            $table->string('tax_regime')->nullable();
            $table->string('cnae')->nullable();
            $table->string('municipal_service_code')->nullable();
            $table->decimal('iss_rate', 7, 4)->nullable();
            $table->string('certificate_reference')->nullable();
            $table->text('api_token')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('extra')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('fiscal_name')->nullable()->after('cnpj');
            $table->string('fiscal_document_type', 8)->nullable()->after('fiscal_name');
            $table->string('fiscal_document', 20)->nullable()->after('fiscal_document_type');
            $table->string('fiscal_email')->nullable()->after('fiscal_document');
            $table->string('fiscal_address')->nullable()->after('fiscal_email');
            $table->string('fiscal_city')->nullable()->after('fiscal_address');
            $table->string('fiscal_state', 2)->nullable()->after('fiscal_city');
            $table->string('fiscal_zip_code', 20)->nullable()->after('fiscal_state');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fiscal_name',
                'fiscal_document_type',
                'fiscal_document',
                'fiscal_email',
                'fiscal_address',
                'fiscal_city',
                'fiscal_state',
                'fiscal_zip_code',
            ]);
        });

        Schema::dropIfExists('fiscal_settings');
    }
};
