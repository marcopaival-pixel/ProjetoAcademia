<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deploy_releases', function (Blueprint $table) {
            if (! Schema::hasColumn('deploy_releases', 'bug_incident_id')) {
                $table->unsignedBigInteger('bug_incident_id')->nullable()->after('deployed_by')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('deploy_releases', function (Blueprint $table) {
            if (Schema::hasColumn('deploy_releases', 'bug_incident_id')) {
                $table->dropColumn('bug_incident_id');
            }
        });
    }
};
