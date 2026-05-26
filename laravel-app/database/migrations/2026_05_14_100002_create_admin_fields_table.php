<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_entity_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Column name
            $table->string('label');
            $table->string('type')->default('text'); // text, number, select, etc.
            $table->boolean('is_required')->default(false);
            $table->boolean('is_readonly')->default(false);
            $table->boolean('is_searchable')->default(true);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_sortable')->default(true);
            $table->boolean('is_visible_list')->default(true);
            $table->boolean('is_visible_form')->default(true);
            $table->string('default_value')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->string('validation_rules')->nullable();
            $table->json('options')->nullable(); // For select/multiselect or relationship settings
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['admin_entity_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_fields');
    }
};
