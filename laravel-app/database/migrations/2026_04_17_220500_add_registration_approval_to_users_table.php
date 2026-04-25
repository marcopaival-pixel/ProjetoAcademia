<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_approval_status', 24)->default('approved')->after('status');
            $table->timestamp('registration_reviewed_at')->nullable()->after('registration_approval_status');
            $table->text('registration_rejection_note')->nullable()->after('registration_reviewed_at');
        });

        DB::table('users')->update(['registration_approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'registration_approval_status',
                'registration_reviewed_at',
                'registration_rejection_note',
            ]);
        });
    }
};
