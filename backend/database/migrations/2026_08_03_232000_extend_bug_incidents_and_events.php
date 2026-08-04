<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bug_incidents', function (Blueprint $table) {
            if (! Schema::hasColumn('bug_incidents', 'fingerprint')) {
                $table->string('fingerprint', 64)->nullable()->after('incident_code')->index();
            }
            if (! Schema::hasColumn('bug_incidents', 'title')) {
                $table->string('title')->nullable()->after('fingerprint');
            }
            if (! Schema::hasColumn('bug_incidents', 'environment')) {
                $table->string('environment', 32)->default('production')->after('severity');
            }
            if (! Schema::hasColumn('bug_incidents', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('affected_role')->index();
            }
            if (! Schema::hasColumn('bug_incidents', 'clinic_id')) {
                $table->unsignedBigInteger('clinic_id')->nullable()->after('tenant_id')->index();
            }
            if (! Schema::hasColumn('bug_incidents', 'method_name')) {
                $table->string('method_name', 128)->nullable()->after('http_method');
            }
            if (! Schema::hasColumn('bug_incidents', 'first_occurred_at')) {
                $table->timestamp('first_occurred_at')->nullable()->after('stack_trace');
            }
            if (! Schema::hasColumn('bug_incidents', 'last_occurred_at')) {
                $table->timestamp('last_occurred_at')->nullable()->after('first_occurred_at');
            }
            if (! Schema::hasColumn('bug_incidents', 'occurrence_count')) {
                $table->unsignedInteger('occurrence_count')->default(1)->after('last_occurred_at');
            }
            if (! Schema::hasColumn('bug_incidents', 'affected_users_count')) {
                $table->unsignedInteger('affected_users_count')->default(1)->after('occurrence_count');
            }
            if (! Schema::hasColumn('bug_incidents', 'affected_clinics_count')) {
                $table->unsignedInteger('affected_clinics_count')->default(0)->after('affected_users_count');
            }
            if (! Schema::hasColumn('bug_incidents', 'log_reference')) {
                $table->string('log_reference')->nullable()->after('affected_clinics_count');
            }
            if (! Schema::hasColumn('bug_incidents', 'diagnosis')) {
                $table->text('diagnosis')->nullable()->after('error_summary');
            }
            if (! Schema::hasColumn('bug_incidents', 'root_cause')) {
                $table->text('root_cause')->nullable()->after('diagnosis');
            }
            if (! Schema::hasColumn('bug_incidents', 'agent_context')) {
                $table->json('agent_context')->nullable()->after('impact_analysis');
            }
            if (! Schema::hasColumn('bug_incidents', 'analysis_checklist')) {
                $table->json('analysis_checklist')->nullable()->after('agent_context');
            }
            if (! Schema::hasColumn('bug_incidents', 'test_results')) {
                $table->json('test_results')->nullable()->after('analysis_checklist');
            }
            if (! Schema::hasColumn('bug_incidents', 'production_commit')) {
                $table->string('production_commit', 64)->nullable()->after('test_results');
            }
            if (! Schema::hasColumn('bug_incidents', 'approved_commit')) {
                $table->string('approved_commit', 64)->nullable()->after('production_commit');
            }
            if (! Schema::hasColumn('bug_incidents', 'staging_status')) {
                $table->string('staging_status', 32)->nullable()->after('approval_staging');
            }
            if (! Schema::hasColumn('bug_incidents', 'production_status')) {
                $table->string('production_status', 32)->nullable()->after('approval_production');
            }
            if (! Schema::hasColumn('bug_incidents', 'monitoring_status')) {
                $table->string('monitoring_status', 32)->nullable()->after('production_status');
            }
            if (! Schema::hasColumn('bug_incidents', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('closed_at');
            }
            if (! Schema::hasColumn('bug_incidents', 'error_reproduced')) {
                $table->boolean('error_reproduced')->nullable()->after('confidence');
            }
            if (! Schema::hasColumn('bug_incidents', 'is_critical')) {
                $table->boolean('is_critical')->default(false)->after('risk_level');
            }
            if (! Schema::hasColumn('bug_incidents', 'requires_dual_approval')) {
                $table->boolean('requires_dual_approval')->default(false)->after('is_critical');
            }
            if (! Schema::hasColumn('bug_incidents', 'ignored_at')) {
                $table->timestamp('ignored_at')->nullable()->after('requires_dual_approval');
            }
        });

        if (! Schema::hasTable('bug_incident_events')) {
            Schema::create('bug_incident_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bug_incident_id')->index();
                $table->string('event_type', 64)->index();
                $table->json('payload')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_incident_events');

        Schema::table('bug_incidents', function (Blueprint $table) {
            foreach ([
                'fingerprint', 'title', 'environment', 'tenant_id', 'clinic_id', 'method_name',
                'first_occurred_at', 'last_occurred_at', 'occurrence_count', 'affected_users_count',
                'affected_clinics_count', 'log_reference', 'diagnosis', 'root_cause', 'agent_context',
                'analysis_checklist', 'test_results', 'production_commit', 'approved_commit',
                'staging_status', 'production_status', 'monitoring_status', 'resolved_at',
                'error_reproduced', 'is_critical', 'requires_dual_approval', 'ignored_at',
            ] as $col) {
                if (Schema::hasColumn('bug_incidents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
