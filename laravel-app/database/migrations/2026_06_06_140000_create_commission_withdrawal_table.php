<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('commission_withdrawal')) {
            return;
        }

        Schema::create('commission_withdrawal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('withdrawal_request_id');
            $table->unsignedBigInteger('commission_id');
            $table->decimal('amount_applied', 12, 2);
            $table->timestamps();

            $table->foreign('withdrawal_request_id')
                ->references('id')
                ->on('withdrawal_requests')
                ->onDelete('cascade');

            $table->foreign('commission_id')
                ->references('id')
                ->on('commissions')
                ->onDelete('cascade');

            $table->unique(['withdrawal_request_id', 'commission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_withdrawal');
    }
};
